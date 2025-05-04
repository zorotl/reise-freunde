<?php

use Livewire\Volt\Component;
use Livewire\Attribute\Middleware;
use Livewire\Attributes\Title;

new 
#[Title('Dashboard')]
#[Middleware(['auth'])]
class extends Component {
    //
}; ?>

<div>
    <livewire:dashboard.overview />
</div>