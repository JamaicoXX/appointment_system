<?php

use Livewire\Component;

new class extends Component {

};
?>

<flux:modal name="alert-modal" class="md:w-96">
    <div class="p-6 text-center space-y-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
        </svg>

        <!-- Title -->
        <flux:heading size="lg" class="text-red-600">Alert</flux:heading>

        <!-- Message -->
        <flux:text class="text-gray-700" wire:model="alertMessage">
            You have successfully submitted your appointment! Please check your email if confirmation is received. Thank you.
        </flux:text>

        <!-- Action Button -->
        <div class="flex justify-center pt-4">
            <flux:button variant="danger" wire:click="$emit('closeModal', 'alert-modal')">
                OK
            </flux:button>
        </div>
    </div>
</flux:modal>
