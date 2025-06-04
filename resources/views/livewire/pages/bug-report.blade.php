<?php
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
#[Layout('components.layouts.app')]
#[Title('Bug Report')]
class extends Component {};
?>

<div class="max-w-xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-semibold mb-6">{{ __('Report a bug') }}</h1>
    <livewire:bug-report-form />
</div>
