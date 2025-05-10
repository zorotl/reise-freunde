<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\TravelStyle;
use App\Models\Hobby;
use App\Models\UserAdditionalInfo;
use Illuminate\Support\Facades\Auth;

class TravelStylePreferences extends Component
{
    public array $selectedTravelStyles = [];
    public array $selectedHobbies = [];
    public array $customTravelStyle = [];
    public array $customHobby = [];

    public function mount()
    {
        $user = Auth::user();

        // Load selected predefined items
        $this->selectedTravelStyles = $user->travelStyles->pluck('id')->toArray();
        $this->selectedHobbies = $user->hobbies->pluck('id')->toArray();

        // Load custom preferences from user_additional_infos
        $this->customTravelStyle = $user->additionalInfo->custom_travel_styles ?? [];
        $this->customHobby = $user->additionalInfo->custom_hobbies ?? [];
    }

    public function toggleTravelStyle($id)
    {
        if (in_array($id, $this->selectedTravelStyles)) {
            $this->selectedTravelStyles = array_diff($this->selectedTravelStyles, [$id]);
        } else {
            $this->selectedTravelStyles[] = $id;
        }
    }

    public function toggleHobby($id)
    {
        if (in_array($id, $this->selectedHobbies)) {
            $this->selectedHobbies = array_diff($this->selectedHobbies, [$id]);
        } else {
            $this->selectedHobbies[] = $id;
        }
    }

    public function save()
    {
        $this->validate([
            'customTravelStyle' => 'array|max:5',
            'customHobby' => 'array|max:5',
        ]);

        $user = Auth::user();

        // Sync many-to-many relations
        $user->travelStyles()->sync($this->selectedTravelStyles);
        $user->hobbies()->sync($this->selectedHobbies);

        // Ensure the additionalInfo model exists and is correctly linked
        $userInfo = $user->additionalInfo;
        if (!$userInfo) {
            $userInfo = new UserAdditionalInfo([
                'custom_travel_styles' => [],
                'custom_hobbies' => [],
            ]);
            $user->additionalInfo()->save($userInfo); // This sets the user_id properly
        }

        // Save custom free-text preferences
        $userInfo->custom_travel_styles = array_filter($this->customTravelStyle);
        $userInfo->custom_hobbies = array_filter($this->customHobby);
        $userInfo->save();

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
