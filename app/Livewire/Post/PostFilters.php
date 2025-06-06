<?php

namespace App\Livewire\Post;

use App\Models\Language;
use Livewire\Component;
use Livewire\Attributes\On;
use Monarobase\CountryList\CountryListFacade as CountryList;

class PostFilters extends Component
{
    // Filter properties
    public $filterDestinationCountry = '';
    public $filterDestinationCity = '';
    public $filterFromDate = '';
    public $filterToDate = '';
    public $filterUserNationality = '';
    public $filterMinAge = '';
    public $filterMaxAge = '';
    public ?string $filterPostLanguage = null;

    // Country list for TomSelect
    public $countryList;

    public function mount()
    {
        // Initialize country list
        $this->countryList = CountryList::getList(app()->getLocale());
        // Optional: preload selected language from query string
        $this->language = request()->query('language', 'all');
    }

    // This method will be called when any of the filter properties change
    // We emit an event to the parent component (PostList)
    public function updated(string $property): void
    {
        // We only care about filter properties here
        if (
            in_array($property, [
                'filterDestinationCountry',
                'filterDestinationCity',
                'filterFromDate',
                'filterToDate',
                'filterUserNationality',
                'filterMinAge',
                'filterMaxAge',
                'filterPostLanguage',
            ])
        ) {
            $this->applyFilters();
        }
    }

    public function applyFilters()
    {
        $this->dispatch('filters-updated', [
            'destinationCountry' => $this->filterDestinationCountry,
            'destinationCity' => $this->filterDestinationCity,
            'fromDate' => $this->filterFromDate,
            'toDate' => $this->filterToDate,
            'userNationality' => $this->filterUserNationality,
            'minAge' => $this->filterMinAge,
            'maxAge' => $this->filterMaxAge,
            'postLanguage' => $this->filterPostLanguage,
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'filterDestinationCountry',
            'filterDestinationCity',
            'filterFromDate',
            'filterToDate',
            'filterUserNationality',
            'filterMinAge',
            'filterMaxAge',
            'filterPostLanguage',
        ]);

        // Emit events to clear TomSelect instances
        $this->dispatch('reset-destination-select');
        $this->dispatch('reset-nationality-select');

        $this->applyFilters(); // Apply empty filters
    }

    public function render()
    {
        return view('livewire.post.post-filters');
    }
}