<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
use App\Services\RouteConflictChecker;

class RouteConflicts extends Component
{
    public function render()
    {
        $conflicts = RouteConflictChecker::getConflicts();

        return view('livewire.admin.widgets.route-conflicts', [
            'conflicts' => $conflicts,
        ]);
    }
}
