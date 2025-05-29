<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Title('All Posts')]
class PostList extends Component
{
    use WithPagination;

    public Carbon $now;
    public string $show = 'all';

    #[Url(as: 'country', history: true, keep: true)]
    public ?string $filterDestinationCountry = null;
    #[Url(as: 'city', history: true, keep: true)]
    public ?string $filterDestinationCity = null;
    #[Url(as: 'from', history: true, keep: true)]
    public ?string $filterFromDate = null;
    #[Url(as: 'to', history: true, keep: true)]
    public ?string $filterToDate = null;
    #[Url(as: 'nation', history: true, keep: true)]
    public ?string $filterUserNationality = null;
    #[Url(as: 'min_age', history: true, keep: true)]
    public ?int $filterMinAge = null; // Type-hinted as ?int
    #[Url(as: 'max_age', history: true, keep: true)]
    public ?int $filterMaxAge = null; // Type-hinted as ?int
    #[Url(as: 'lang', history: true, keep: true)]
    public ?string $filterPostLanguage = null;

    public function mount()
    {
        $this->now = Carbon::now();
    }

    #[On('filters-updated')]
    public function updateFilters(array $filters): void
    {
        $this->filterDestinationCountry = $filters['destinationCountry'] ?? null;
        $this->filterDestinationCity = $filters['destinationCity'] ?? null;
        $this->filterFromDate = $filters['fromDate'] ?? null;
        $this->filterToDate = $filters['toDate'] ?? null;
        $this->filterUserNationality = $filters['userNationality'] ?? null;
        $this->filterPostLanguage = $filters['postLanguage'] ?? null;

        // Corrected lines for filterMinAge and filterMaxAge:
        // Convert empty strings to null, otherwise cast to int.
        $this->filterMinAge = !empty($filters['minAge']) ? (int) $filters['minAge'] : null;
        $this->filterMaxAge = !empty($filters['maxAge']) ? (int) $filters['maxAge'] : null;

        $this->resetPage();
    }

    public function render()
    {
        $query = Post::query()
            ->with('user.additionalInfo')
            ->withCount('likes')
            ->where('posts.is_active', true)
            ->where(function ($query) {
                $query->whereNull('posts.expiry_date')
                    ->orWhere('posts.expiry_date', '>', $this->now);
            });

        $query->when($this->filterDestinationCountry, function (Builder $q, $countryCode) {
            $q->where('posts.country', $countryCode);
        });

        $query->when($this->filterDestinationCity, function (Builder $q, $city) {
            $operator = config('database.default') === 'pgsql' ? 'ILIKE' : 'LIKE';
            $q->where('posts.city', $operator, '%' . $city . '%');
        });

        $query->when($this->filterFromDate, function (Builder $q, $date) {
            $formattedDate = Carbon::parse($date)->format('Y-m-d');
            $q->whereDate('posts.from_date', '>=', $formattedDate);
        });

        $query->when($this->filterToDate, function (Builder $q, $date) {
            $formattedDate = Carbon::parse($date)->format('Y-m-d');
            $q->whereDate('posts.to_date', '<=', $formattedDate);
        });

        $query->when($this->filterUserNationality, function (Builder $q, $nationality) {
            $q->whereHas('user.additionalInfo', function (Builder $subQuery) use ($nationality) {
                $subQuery->whereRaw('LOWER(nationality) = ?', [strtolower($nationality)]);
            });
        });

        $query->when($this->filterPostLanguage, function (Builder $q, $langCode) {
            $q->where('language_code', $langCode);
        });


        if ($this->filterMinAge !== null && $this->filterMinAge >= 0) {
            $minAge = (int) $this->filterMinAge;
            $latestBirthday = now()->subYears($minAge)->endOfDay()->toDateString();
            $query->whereHas('user.additionalInfo', function (Builder $subQuery) use ($latestBirthday) {
                $subQuery->whereNotNull('birthday')
                    ->whereDate('birthday', '<=', $latestBirthday);
            });
        }

        if ($this->filterMaxAge !== null && $this->filterMaxAge >= 0) {
            $maxAge = (int) $this->filterMaxAge;
            $earliestBirthday = now()->subYears($maxAge + 1)->startOfDay()->toDateString();
            $query->whereHas('user.additionalInfo', function (Builder $subQuery) use ($earliestBirthday) {
                $subQuery->whereNotNull('birthday')
                    ->whereDate('birthday', '>', $earliestBirthday);
            });
        }

        $entries = $query->latest('posts.created_at')->paginate(15);

        return view('livewire.post.post-list', [
            'entries' => $entries,
        ]);
    }
}