<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MultiSelect extends Component
{
    public string $entangle;
    public array $options;
    public string $label;

    public function __construct(string $entangle, array $options, string $label = '')
    {
        $this->entangle = $entangle;
        $this->options = $options;
        $this->label = $label;
    }

    public function render()
    {
        return view('components.multi-select');
    }
}

