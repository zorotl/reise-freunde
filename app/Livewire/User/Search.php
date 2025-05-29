<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Followable;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

class Search extends Component
{
    use WithPagination, Followable;

    public ?string $filterUserNationality = null;
    public ?string $filterGender = null;
    public ?int $filterMinAge = null;
    public ?int $filterMaxAge = null;
    public array $filterLanguages = [];
    public array $filterHobbies = [];
    public array $filterTravelStyles = [];

    #[Url(except: '')]
    public string $search = '';

    #[On('userFollowStateChanged')]
    public function refreshSearchList(int $userId): void
    {
        $this->refreshData();
    }

    #[On('filters-updated')]
    public function updateFilters(array $filters): void
    {
        $this->filterUserNationality = $filters['userNationality'] ?? null;
        $this->filterGender = $filters['gender'] ?? null;
        $this->filterMinAge = !empty($filters['minAge']) ? (int) $filters['minAge'] : null;
        $this->filterMaxAge = !empty($filters['maxAge']) ? (int) $filters['maxAge'] : null;
        $this->filterLanguages = $filters['languages'] ?? [];
        $this->filterHobbies = $filters['hobbies'] ?? [];
        $this->filterTravelStyles = $filters['travelStyles'] ?? [];

        $this->resetPage();
    }

    public function render()
    {
        $query = User::query()
            ->where('id', '!=', auth()->id())
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
            });

        $query->when(
            $this->filterUserNationality,
            fn($q, $nation) =>
            $q->whereHas(
                'additionalInfo',
                fn($sub) =>
                $sub->whereRaw('LOWER(nationality) = ?', [strtolower($nation)])
            )
        );

        $query->when(
            $this->filterGender,
            fn($q, $gender) =>
            $q->where('gender', $gender)
        );

        $query->when($this->filterMinAge !== null, function ($q) {
            $latestBirthday = now()->subYears($this->filterMinAge)->endOfDay()->toDateString();
            $q->whereHas(
                'additionalInfo',
                fn($sub) =>
                $sub->whereNotNull('birthday')
                    ->whereDate('birthday', '<=', $latestBirthday)
            );
        });

        $query->when($this->filterMaxAge !== null, function ($q) {
            $earliestBirthday = now()->subYears($this->filterMaxAge + 1)->startOfDay()->toDateString();
            $q->whereHas(
                'additionalInfo',
                fn($sub) =>
                $sub->whereNotNull('birthday')
                    ->whereDate('birthday', '>', $earliestBirthday)
            );
        });

        $query->when(
            $this->filterLanguages,
            fn($q, $langs) =>
            $q->whereHas(
                'languages',
                fn($sub) =>
                $sub->whereIn('languages.code', $langs)
            )
        );

        $query->when(
            $this->filterHobbies,
            fn($q, $hobbies) =>
            $q->whereHas(
                'hobbies',
                fn($sub) =>
                $sub->whereIn('hobbies.id', $hobbies)
            )
        );

        $query->when(
            $this->filterTravelStyles,
            fn($q, $styles) =>
            $q->whereHas(
                'travelStyles',
                fn($sub) =>
                $sub->whereIn('travel_styles.id', $styles)
            )
        );

        return view('livewire.user.search', [
            'users' => $query->paginate(15),
        ])->layout('components.layouts.app');
    }
}
