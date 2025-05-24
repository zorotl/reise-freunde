<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Followable;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Search extends Component
{
    use WithPagination, Followable;

    #[Url(except: '')]
    public string $search = '';

    /**
     * Listen for follow state changes and refresh.
     * We don't need to do anything specific here; the #[On] attribute
     * and the parent 'refreshData' will trigger a re-render,
     * which re-fetches users with updated states.
     */
    #[On('userFollowStateChanged')]
    public function refreshSearchList(int $userId): void
    {
        // Simply calling $this->refreshData() or letting Livewire re-render
        // should be enough, as the `render` method fetches fresh data.
        $this->refreshData();
    }

    public function render()
    {
        $users = User::query()
            ->where(function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
            })
            ->where('id', '!=', auth()->id()) // Exclude self
            ->where('status', 'approved') // Only show approved users
            ->paginate(15);

        return view('livewire.user.search', [
            'users' => $users,
        ])->layout('components.layouts.app');
    }
}