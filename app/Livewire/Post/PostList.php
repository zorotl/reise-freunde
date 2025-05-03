<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use App\Models\User; // Import User model
use Illuminate\Support\Carbon;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder; // Import Builder
use Livewire\Attributes\Url; // Import Url attribute

class PostList extends Component
{
    use WithPagination; // Use pagination

    // Existing properties
    //public $entries;
    public Carbon $now;
    public string $show = 'all';

    // Keep existing filters (optional, can be adapted)
    #[Url(as: 'country', history: true, keep: true)]
    public ?string $filterDestinationCountry = null;
    #[Url(as: 'city', history: true, keep: true)]
    public ?string $filterDestinationCity = null;
    #[Url(as: 'from', history: true, keep: true)]
    public ?string $filterFromDate = null;
    #[Url(as: 'to', history: true, keep: true)]
    public ?string $filterToDate = null;

    // New Filters
    #[Url(as: 'nation', history: true, keep: true)]
    public ?string $filterUserNationality = null;
    #[Url(as: 'min_age', history: true, keep: true)]
    public ?int $filterMinAge = null;
    #[Url(as: 'max_age', history: true, keep: true)]
    public ?int $filterMaxAge = null;

    // Country list for dropdowns
    public array $countryList = [];

    public function mount()
    {
        $this->now = Carbon::now();
        // Load country list for both destination and user nationality filters
        $this->countryList = Countries::getList('en', 'php');
        // Initial load is handled by render() with pagination
    }

    // Reset pagination when filters change
    public function updating($property): void
    {
        if (
            in_array($property, [
                'filterDestinationCountry',
                'filterDestinationCity',
                'filterFromDate',
                'filterToDate',
                'filterUserNationality',
                'filterMinAge',
                'filterMaxAge'
            ])
        ) {
            $this->resetPage();
        }
    }

    // Method to reset all filters
    public function resetFilters(): void
    {
        $this->reset([
            'filterDestinationCountry',
            'filterDestinationCity',
            'filterFromDate',
            'filterToDate',
            'filterUserNationality',
            'filterMinAge',
            'filterMaxAge'
        ]);
        $this->resetPage();
        // Dispatch event for TomSelect to clear itself (if needed)
        $this->dispatch('reset-nationality-select');
    }


    public function render()
    {
        $query = Post::query()
            ->with('user.additionalInfo') // Eager load user->additionalInfo
            ->where('is_active', true)
            ->where(function ($query) { // Filter out expired posts
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', $this->now);
            });

        // Apply Destination Country Filter
        $query->when($this->filterDestinationCountry, function (Builder $q, $countryCode) {
            $q->where('country', $countryCode);
        });

        // Apply Destination City Filter
        $query->when($this->filterDestinationCity, function (Builder $q, $city) {
            // Use ILIKE for case-insensitive search if using PostgreSQL, otherwise use LIKE
            $operator = config('database.default') === 'pgsql' ? 'ILIKE' : 'LIKE';
            $q->where('city', $operator, '%' . $city . '%');
        });

        // Apply From Date Filter
        $query->when($this->filterFromDate, function (Builder $q, $date) {
            $q->whereDate('from_date', '>=', $date);
        });

        // Apply To Date Filter
        $query->when($this->filterToDate, function (Builder $q, $date) {
            $q->whereDate('to_date', '<=', $date);
        });

        // Apply User Nationality Filter
        $query->when($this->filterUserNationality, function (Builder $q, $nationality) {
            $q->whereHas('user.additionalInfo', function (Builder $subQuery) use ($nationality) {
                $subQuery->where('nationality', $nationality);
            });
        });

        // Apply Minimum Age Filter
        $query->when($this->filterMinAge, function (Builder $q, $minAge) {
            // Calculate latest possible birthday for someone who is at least minAge
            $latestBirthday = now()->subYears($minAge)->endOfDay()->toDateString();
            $q->whereHas('user.additionalInfo', function (Builder $subQuery) use ($latestBirthday) {
                $subQuery->where('birthday', '<=', $latestBirthday);
            });
        });

        // Apply Maximum Age Filter
        $query->when($this->filterMaxAge, function (Builder $q, $maxAge) {
            // Calculate earliest possible birthday for someone who is at most maxAge
            // Need to subtract maxAge + 1 years to get the correct boundary
            $earliestBirthday = now()->subYears($maxAge + 1)->startOfDay()->toDateString();
            $q->whereHas('user.additionalInfo', function (Builder $subQuery) use ($earliestBirthday) {
                $subQuery->where('birthday', '>', $earliestBirthday);
            });
        });

        $entries = $query->latest()->paginate(15); // Adjust per page number as needed

        return view('livewire.post.post-list', [
            'entries' => $entries, // Pass the paginator object directly to the view
        ]);
    }
}