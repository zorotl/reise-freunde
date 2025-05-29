<?php

namespace App\Livewire\User;

use Livewire\Component;
use Monarobase\CountryList\CountryListFacade as CountryList;
use App\Models\Hobby;
use App\Models\Language;
use App\Models\TravelStyle;

class UserFilters extends Component
{
    // Filter values
    public ?string $filterUserNationality = null;
    public ?string $filterGender = null;
    public $filterMinAge = null;
    public $filterMaxAge = null;
    public array $filterLanguages = [];
    public array $filterHobbies = [];
    public array $filterTravelStyles = [];

    public $countryList;
    public $allLanguages;
    public $allHobbies;
    public $allTravelStyles;

    public function mount()
    {
        $this->countryList = CountryList::getList(app()->getLocale());
        $this->allLanguages = Language::orderBy('name_en')->get();
        $this->allHobbies = Hobby::orderBy('name')->get();
        $this->allTravelStyles = TravelStyle::orderBy('name')->get();
    }

    public function setFilterMinAge($value): void
    {
        $this->filterMinAge = is_numeric($value) ? (int) $value : null;
    }

    public function setFilterMaxAge($value): void
    {
        $this->filterMaxAge = is_numeric($value) ? (int) $value : null;
    }

    public function updated($property, $value)
    {
        // Always force-cast age fields to int or null
        if ($property === 'filterMinAge') {
            $this->filterMinAge = is_numeric($value) ? (int) $value : null;
        } elseif ($property === 'filterMaxAge') {
            $this->filterMaxAge = is_numeric($value) ? (int) $value : null;
        }

        $this->dispatch('filters-updated', [
            'userNationality' => $this->filterUserNationality,
            'gender' => $this->filterGender,
            'minAge' => $this->filterMinAge,
            'maxAge' => $this->filterMaxAge,
            'spokenLanguages' => $this->filterLanguages,
            'hobbies' => $this->filterHobbies,
            'travelStyles' => $this->filterTravelStyles,
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'filterUserNationality',
            'filterGender',
            'filterMinAge',
            'filterMaxAge',
            'filterLanguages',
            'filterHobbies',
            'filterTravelStyles',
        ]);

        $this->dispatch('reset-user-filter-selects');

        $this->dispatch('filters-updated', [
            'userNationality' => $this->filterUserNationality,
            'gender' => $this->filterGender,
            'minAge' => $this->filterMinAge,
            'maxAge' => $this->filterMaxAge,
            'spokenLanguages' => $this->filterLanguages,
            'hobbies' => $this->filterHobbies,
            'travelStyles' => $this->filterTravelStyles,
        ]);
    }

    public function render()
    {
        // dd(
        //     $this->allLanguages->map(fn($l) => ['value' => $l->code, 'label' => $l->name_en])->toArray()
        // );


        return view('livewire.user.user-filters');
    }
}
