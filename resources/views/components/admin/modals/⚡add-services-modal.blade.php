<?php

use Livewire\Component;
use App\Models\Services;

new class extends Component {
    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $duration;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
        ]);

        Services::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'duration' => $this->duration,
            'archived' => false,
        ]);

        $this->reset();
        $this->modal('add-service-modal')->close();

        $this->dispatch('refreshServices');
    }
};
?>

<flux:modal name="add-service-modal" class="md:w-[60%] !bg-sky-50" flyout position="left">

    <form wire:submit.prevent="save" class="space-y-6 p-6">

        <div>
            <flux:heading size="lg">Add New Service</flux:heading>
            <flux:text class="mt-2">
                Enter service details below. Fields marked with
                <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Service Name -->
            <flux:input label="Service Name *" placeholder="Example: Tooth Extraction" wire:model="name" />

            <!-- Duration -->
            <flux:input type="number" label="Duration (minutes)" placeholder="Example: 30" wire:model="duration" />

            <!-- Price -->
            <flux:input type="number" step="0.01" label="Price (₱)" placeholder="Example: 1500"
                wire:model="price" />

            <!-- Discount Price -->
            <flux:input type="number" step="0.01" label="Discount Price (₱)" placeholder="Optional"
                wire:model="discount_price" />

            <!-- Description -->
            <div class="col-span-1 md:col-span-2">
                <flux:textarea label="Description" placeholder="Brief description of the service..."
                    wire:model="description" />
            </div>

        </div>

        <div class="flex pt-6">
            <flux:spacer />

            <flux:button type="submit" variant="primary">
                Save Service
            </flux:button>
        </div>

    </form>

</flux:modal>
