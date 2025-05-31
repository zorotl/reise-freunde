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
    public bool $filterVerified = false;
    public bool $filterTrusted = false;

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
        $this->filterLanguages = $filters['spokenLanguages'] ?? [];
        $this->filterHobbies = $filters['hobbies'] ?? [];
        $this->filterTravelStyles = $filters['travelStyles'] ?? [];
        $this->filterVerified = $filters['verified'] ?? false;
        $this->filterTrusted = $filters['trusted'] ?? false;

        $this->resetPage();
    }

    public function render()
    {
        $query = User::query()
            ->where('id', '!=', auth()->id())
            ->where('status', 'approved')
            ->whereHas('additionalInfo', function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%');
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
                'spokenLanguages',
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

        // Verified filter
        $query->when($this->filterVerified, function ($q) {
            $q->whereHas('verification', function ($sub) {
                $sub->where('status', 'accepted');
            });
        });

        // Trusted filter (at least 3 confirmations)
        $query->when($this->filterTrusted, function ($q) {
            $q->whereIn('id', function ($sub) {
                $sub->selectRaw('user_id')->from(function ($inner) {
                    $inner->selectRaw('requester_id as user_id')->from('user_confirmations')->where('status', 'accepted')
                        ->unionAll(
                            \DB::table('user_confirmations')->selectRaw('confirmer_id as user_id')->where('status', 'accepted')
                        );
                }, 'merged')->groupBy('user_id')->havingRaw('COUNT(*) >= 1');
            });
        });

        return view('livewire.user.search', [
            'users' => $query->paginate(15),
        ])->layout('components.layouts.app');
    }
}
