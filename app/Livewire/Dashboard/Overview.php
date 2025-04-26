<?php

namespace App\Livewire\Dashboard;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection; // Import Collection

class Overview extends Component
{
    public User $user;
    public Collection $feedPosts;
    public int $followerCount = 0;
    public int $followingCount = 0;
    public Collection $pendingRequests; // For notifications
    public Collection $suggestedUsers; // For suggestions

    /**
     * Mount the component and load initial data.
     */
    public function mount(): void
    {
        $this->user = Auth::user();
        $this->loadData();
    }

    /**
     * Load all necessary data for the dashboard.
     */
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
        // Eager load counts for efficiency
        $this->user->loadCount(['followers', 'following']);
        $this->followerCount = $this->user->followers_count;
        $this->followingCount = $this->user->following_count;
    }

    /**
     * Load posts for the feed (own posts + posts from followed users).
     * Limits to the latest 15 posts for performance.
     */
    public function loadFeedPosts(): void
    {
        $followingIds = $this->user->following()->pluck('users.id'); // Get IDs of users being followed
        $userIds = $followingIds->push($this->user->id)->unique(); // Add own ID and ensure uniqueness

        $this->feedPosts = Post::whereIn('user_id', $userIds)
            ->with([
                'user' => function ($query) {
                    $query->with('additionalInfo'); // Eager load user info for display
                }
            ])
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
     * Load pending follower requests for the notification section.
     */
    public function loadPendingRequests(): void
    {
        // Reuse the existing relationship, limit to a few recent ones
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
        $currentUser->load(['hobbies:id', 'travelStyles:id']); // Eager load only IDs

        $followingIds = $currentUser->following()->pluck('users.id')->toArray();
        $pendingRequestIds = $currentUser->pendingFollowingRequests()->pluck('users.id')->toArray();
        $excludeIds = array_merge($followingIds, $pendingRequestIds, [$currentUser->id]); // Exclude self, already followed, and pending requests

        $currentUserHobbyIds = $currentUser->hobbies->pluck('id');
        $currentUserTravelStyleIds = $currentUser->travelStyles->pluck('id');

        // Find users with at least one shared hobby OR travel style, excluding the current user and those already followed/requested
        $this->suggestedUsers = User::query()
            ->whereNotIn('id', $excludeIds) // Exclude specified users
            ->where(function (Builder $query) use ($currentUserHobbyIds, $currentUserTravelStyleIds) {
                // Must have at least one shared hobby OR travel style
                $query->whereHas('hobbies', function (Builder $q) use ($currentUserHobbyIds) {
                    $q->whereIn('hobbies.id', $currentUserHobbyIds); // Check hobbies table directly
                })
                    ->orWhereHas('travelStyles', function (Builder $q) use ($currentUserTravelStyleIds) {
                    $q->whereIn('travel_styles.id', $currentUserTravelStyleIds); // Check travel_styles table directly
                });
            })
            ->with('additionalInfo') // Eager load for display
            ->withCount([
                // Count shared hobbies
                'hobbies as shared_hobbies_count' => function (Builder $query) use ($currentUserHobbyIds) {
                    $query->whereIn('hobbies.id', $currentUserHobbyIds);
                },
                // Count shared travel styles
                'travelStyles as shared_travel_styles_count' => function (Builder $query) use ($currentUserTravelStyleIds) {
                    $query->whereIn('travel_styles.id', $currentUserTravelStyleIds);
                }
            ])
            // Order by the number of shared interests (descending), then randomly
            ->orderByRaw('(shared_hobbies_count + shared_travel_styles_count) DESC')
            ->inRandomOrder() // Add randomness for users with same number of shared interests
            ->take(5) // Limit suggestions
            ->get();
    }

    /**
     * Follow a suggested user.
     * This reuses the follow logic from UserProfile, slightly adapted.
     */
    public function followUser(int $userIdToFollow)
    {
        $userToFollow = User::find($userIdToFollow);
        if ($userToFollow) {
            $this->user->follow($userToFollow);
            // Refresh suggestions after following
            $this->loadSuggestedUsers();
            // Optionally refresh feed if needed immediately, or rely on next page load
            // $this->loadFeedPosts();
            // Dispatch an event or show a success message
            session()->flash('status', 'Follow request sent to ' . $userToFollow->name); // Or 'Now following...' for public profiles
        }
    }

    /**
     * Render the dashboard view.
     */
    public function render()
    {
        // Pass the loaded data to the view
        return view('livewire.dashboard.overview');
    }
}