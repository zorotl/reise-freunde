<?php

use Livewire\Volt\Component;
use Livewire\Attribute\Middleware;

new 
#[Middleware(['auth'])]
class extends Component {
    //
}; ?>

<div>
    <livewire:dashboard.overview />
</div>