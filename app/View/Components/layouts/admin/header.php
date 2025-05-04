<?php

namespace App\View\Components\layouts\admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class header extends Component
{
    public string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title = 'Admin')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.admin.header');
    }
}
