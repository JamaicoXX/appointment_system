<?php

use Livewire\Component;
use App\Models\Services;

new class extends Component {
    public $serviceId;
    public $service;

    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $duration;

    protected $listeners = [
        'editServiceModal' => 'openModal',
    ];

    public function openModal($serviceId)
    {
        $this->serviceId = $serviceId;
        $this->loadService();
        $this->modal('edit-service-modal')->show();
    }

    public function loadService()
    {
        $this->service = Services::findOrFail($this->serviceId);

        $this->name = $this->service->name;
        $this->description = $this->service->description;
        $this->price = $this->service->price;
        $this->discount_price = $this->service->discount_price;
        $this->duration = $this->service->duration;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
        ]);

        $this->service->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'duration' => $this->duration,
        ]);

        $this->modal('edit-service-modal')->close();

        $this->dispatch('showAlertModal', [
            'message' => 'Service updated successfully.',
        ]);

        $this->dispatch('refreshServices');
    }
};
?>

<flux:modal name="edit-service-modal" class="md:w-[60%] !bg-sky-50" flyout position="left">

    <form wire:submit.prevent="save" class="space-y-6 p-6">

        <div>
            <flux:heading size="lg">Edit Service</flux:heading>
            <flux:text class="mt-2">
                Update service details below. Fields marked with
                <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Service Name -->
            <flux:input label="Service Name *" wire:model="name" />

            <!-- Duration -->
            <flux:input type="number" label="Duration (minutes)" wire:model="duration" />

            <!-- Price -->
            <flux:input type="number" step="0.01" label="Price (₱)" wire:model="price" />

            <!-- Discount Price -->
            <flux:input type="number" step="0.01" label="Discount Price (₱)" wire:model="discount_price" />

            <!-- Description -->
            <div class="col-span-1 md:col-span-2">
                <flux:textarea label="Description" wire:model="description" />
            </div>

        </div>

        <div class="flex pt-6">
            <flux:spacer />

            <flux:button type="submit" variant="primary">
                Save Changes
            </flux:button>
        </div>

    </form>

</flux:modal>
