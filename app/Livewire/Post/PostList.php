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
use Illuminate\Pagination\LengthAwarePaginator;

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
        $this->countryList = Countries::getList('en', 'php');
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
        $this->dispatch('reset-nationality-select');
    }

    public function render()
    {
        // *** Using whereHas approach ***
        $query = Post::query()
            ->with('user.additionalInfo') // Eager load needed data
            ->withCount('likes')
            ->where('posts.is_active', true) // Qualify table name
            ->where(function ($query) {
                $query->whereNull('posts.expiry_date') // Qualify table name
                    ->orWhere('posts.expiry_date', '>', $this->now);
            });

        // Apply Destination Country Filter
        $query->when($this->filterDestinationCountry, function (Builder $q, $countryCode) {
            $q->where('posts.country', $countryCode);
        });

        // Apply Destination City Filter
        $query->when($this->filterDestinationCity, function (Builder $q, $city) {
            $operator = config('database.default') === 'pgsql' ? 'ILIKE' : 'LIKE';
            $q->where('posts.city', $operator, '%' . $city . '%');
        });

        // Apply From Date Filter
        $query->when($this->filterFromDate, function (Builder $q, $date) {
            $q->whereDate('posts.from_date', '>=', $date);
        });

        // Apply To Date Filter
        $query->when($this->filterToDate, function (Builder $q, $date) {
            $q->whereDate('posts.to_date', '<=', $date);
        });

        // Apply User Nationality Filter (using whereHas with whereRaw)
        $query->when($this->filterUserNationality, function (Builder $q, $nationality) {
            $q->whereHas('user.additionalInfo', function (Builder $subQuery) use ($nationality) {
                // Using LOWER ensures case-insensitivity across DBs
                $subQuery->whereRaw('LOWER(nationality) = ?', [strtolower($nationality)]);
            });
        });

        // Apply Minimum Age Filter (using whereHas)
        if ($this->filterMinAge !== null && $this->filterMinAge >= 0) {
            $minAge = (int) $this->filterMinAge;
            $latestBirthday = now()->subYears($minAge)->endOfDay()->toDateString();
            $query->whereHas('user.additionalInfo', function (Builder $subQuery) use ($latestBirthday) {
                $subQuery->whereNotNull('birthday')
                    ->whereDate('birthday', '<=', $latestBirthday);
            });
        }

        // Apply Maximum Age Filter (using whereHas)
        if ($this->filterMaxAge !== null && $this->filterMaxAge >= 0) {
            $maxAge = (int) $this->filterMaxAge;
            $earliestBirthday = now()->subYears($maxAge + 1)->startOfDay()->toDateString();
            $query->whereHas('user.additionalInfo', function (Builder $subQuery) use ($earliestBirthday) {
                $subQuery->whereNotNull('birthday')
                    ->whereDate('birthday', '>', $earliestBirthday);
            });
        }

        // *** END Query Logic ***

        $entries = $query->latest('posts.created_at')->paginate(15);

        return view('livewire.post.post-list', [
            'entries' => $entries,
        ]);
    }
}