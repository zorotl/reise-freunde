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
        $user = Auth::user();

        // Sync many-to-many relations
        $user->travelStyles()->sync($this->selectedTravelStyles);
        $user->hobbies()->sync($this->selectedHobbies);

        // Get or create user additional info record
        $userInfo = $user->additionalInfo ?? $user->additionalInfo()->create([]);

        // Save custom preferences
        $userInfo->custom_travel_styles = array_filter($this->customTravelStyle);
        $userInfo->custom_hobbies = array_filter($this->customHobby);
        // dd([
        //     'custom_travel_styles' => $userInfo->custom_travel_styles,
        //     'custom_hobbies' => $userInfo->custom_hobbies,
        // ]);
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
