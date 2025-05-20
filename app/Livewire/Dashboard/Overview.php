<?php

namespace App\Livewire\Dashboard;

use App\Models\Post; // Make sure necessary models are imported
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\On; // Import On attribute if using listeners

class Overview extends Component
{
    // Middleware is applied to the component class
    protected $middleware = ['auth'];

    // Properties to hold data loaded in this component and passed to children
    public User $user;
    public Collection $feedPosts;
    public int $followerCount = 0;
    public int $followingCount = 0;
    public Collection $pendingRequests; // Data for NotificationSection
    public Collection $suggestedUsers; // Data for SuggestedUsersSection
    public string $show = 'feed';

    /**
     * Mount the component. This is where initial data is loaded.
     */
    public function mount(): void
    {
        $this->user = Auth::user(); // Get the authenticated user
        $this->loadData(); // Load all data needed for the dashboard sections
    }

    /**
     * Load all necessary data for the dashboard components.
     * This method can be called to refresh data.
     */
    // Example of listening for an event to refresh data
    // #[On('data-refreshed')]
    public function loadData(): void
    {
        $this->loadCounts();
        $this->loadFeedPosts();
        $this->loadPendingRequests();
        $this->loadSuggestedUsers();
    }

    /**
     * Load follower and following counts.
     */
    public function loadCounts(): void
    {
        // Eager load the counts on the user model
        $this->user->loadCount(['followers', 'following']);
        $this->followerCount = $this->user->followers_count;
        $this->followingCount = $this->user->following_count;
    }

    /**
     * Load posts for the feed (own posts + posts from followed users).
     * Limits to the latest 15 posts.
     */
    public function loadFeedPosts(): void
    {
        // Get IDs of users being followed
        $followingIds = $this->user->following()->pluck('users.id');
        // Add own ID to the list and ensure uniqueness
        $userIds = $followingIds->push($this->user->id)->unique();

        $this->feedPosts = Post::whereIn('user_id', $userIds)
            ->with([
                'user' => function ($query) {
                    $query->with('additionalInfo'); // Eager load user info for display
                }
            ])
            ->withCount('likes')
            ->where('is_active', true) // Only show active posts
            ->where(function (Builder $query) { // Only show non-expired posts
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->latest() // Order by newest first
            ->take(15) // Limit the number of posts initially loaded
            ->get();
    }

    /**
     * Load pending follower requests for the notification section preview.
     */
    public function loadPendingRequests(): void
    {
        // Fetch a limited number of pending follower requests
        $this->pendingRequests = $this->user->pendingFollowerRequests()
            ->with('additionalInfo') // Load info for display
            ->orderBy('user_follower.created_at', 'desc') // Order by request time
            ->take(5) // Limit notifications displayed
            ->get();
    }

    /**
     * Load user suggestions based on shared hobbies/travel styles.
     */
    public function loadSuggestedUsers(): void
    {
        $currentUser = $this->user;
        // Eager load only IDs of current user's interests
        $currentUser->load(['hobbies:id', 'travelStyles:id']);

        // Get IDs to exclude (current user, users already followed, users with pending requests)
        $followingIds = $currentUser->following()->pluck('users.id')->toArray();
        $pendingRequestIds = $currentUser->pendingFollowingRequests()->pluck('users.id')->toArray();
        $excludeIds = array_merge($followingIds, $pendingRequestIds, [$currentUser->id]);

        $currentUserHobbyIds = $currentUser->hobbies->pluck('id');
        $currentUserTravelStyleIds = $currentUser->travelStyles()->pluck('travel_styles.id');

        // Query for users who are not excluded and share at least one interest
        $this->suggestedUsers = User::query()
            ->whereNotIn('id', $excludeIds)
            ->where(function (Builder $query) use ($currentUserHobbyIds, $currentUserTravelStyleIds) {
                $query->whereHas('hobbies', function (Builder $q) use ($currentUserHobbyIds) {
                    $q->whereIn('hobbies.id', $currentUserHobbyIds);
                })
                    ->orWhereHas('travelStyles', function (Builder $q) use ($currentUserTravelStyleIds) {
                        $q->whereIn('travel_styles.id', $currentUserTravelStyleIds);
                    });
            })
            ->with('additionalInfo') // Eager load for display
            ->withCount([
                'hobbies as shared_hobbies_count' => function (Builder $query) use ($currentUserHobbyIds) {
                    $query->whereIn('hobbies.id', $currentUserHobbyIds);
                },
                'travelStyles as shared_travel_styles_count' => function (Builder $query) use ($currentUserTravelStyleIds) {
                    $query->whereIn('travel_styles.id', $currentUserTravelStyleIds);
                }
            ])
            ->orderByRaw('(shared_hobbies_count + shared_travel_styles_count) DESC')
            ->inRandomOrder()
            ->take(5)
            ->get();
    }


    /**
     * Handle the action to follow a user.
     * Triggered from the SuggestedUsersSection view.
     */
    public function followUser(int $userIdToFollow)
    {
        $userToFollow = User::find($userIdToFollow);
        if ($userToFollow) {
            Auth::user()->follow($userToFollow);
            // Refresh relevant data after a follow action
            $this->loadData(); // Reload all data
            // Set a flash message
            session()->flash('status', $userToFollow->isPrivate() ? 'Follow request sent to ' . $userToFollow->name : 'Now following ' . $userToFollow->name);
        }
    }


    /**
     * Render the dashboard overview view.
     */
    public function render()
    {
        // Pass the loaded data to the overview Blade view
        return view('livewire.dashboard.overview', [
            'user' => $this->user, // Pass the authenticated user
            'feedPosts' => $this->feedPosts, // Pass feed posts to FeedSection
            'pendingRequests' => $this->pendingRequests, // Pass requests to NotificationSection
            'suggestedUsers' => $this->suggestedUsers, // Pass suggestions to SuggestedUsersSection
            'followerCount' => $this->followerCount, // Pass counts to NetworkStats
            'followingCount' => $this->followingCount, // Pass counts to NetworkStats
            'show' => $this->show, // Pass the current view mode to the FeedSection
        ]);
    }
}