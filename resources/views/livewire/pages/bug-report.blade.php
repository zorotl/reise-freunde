<?php
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.app')]
#[Title('Bug Report')]
class extends Component {};
?>

<div class="max-w-xl mx-auto py-10 px-4">    
    <livewire:bug-report-form />
</div>
