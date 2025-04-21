<?php

namespace App\Livewire\User;

use App\Models\Hobby;
use Livewire\Component;
use App\Models\TravelStyle;
use Illuminate\Support\Facades\Auth;

class TravelStylePreferences extends Component
{
    public $selectedTravelStyles = [];
    public $selectedHobbies = [];
    public $customTravelStyle = '';
    public $customHobby = '';

    public function mount()
    {
        $user = Auth::user();
        $this->selectedTravelStyles = $user->travelStyles->pluck('id')->toArray();
        $this->selectedHobbies = $user->hobbies->pluck('id')->toArray();
    }

    public function save()
    {
        $user = Auth::user();
        $user->travelStyles()->sync($this->selectedTravelStyles);
        $user->hobbies()->sync($this->selectedHobbies);

        if ($this->customTravelStyle) {
            $newStyle = TravelStyle::firstOrCreate(['name' => $this->customTravelStyle]);
            $user->travelStyles()->attach($newStyle);
        }

        if ($this->customHobby) {
            $newHobby = Hobby::firstOrCreate(['name' => $this->customHobby]);
            $user->hobbies()->attach($newHobby);
        }

        session()->flash('success', 'Preferences saved successfully!');
    }

    public function render()
    {
        return view('livewire.user.travel-style-preferences', [
            'travelStyles' => TravelStyle::all(),
            'hobbies' => Hobby::all(),
        ]);
    }
}

