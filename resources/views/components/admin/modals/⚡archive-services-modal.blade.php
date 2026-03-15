<?php

use Livewire\Component;
use App\Models\Services;

new class extends Component {
    public $serviceId;
    public $service;

    protected $listeners = [
        'archiveServiceModal' => 'openModal',
    ];

    public function openModal($serviceId)
    {
        $this->serviceId = $serviceId;
        $this->service = Services::findOrFail($this->serviceId);

        $this->modal('archive-service-modal')->show();
    }

    public function archive()
    {
        $this->service->update([
            'archived' => 1,
        ]);

        $this->modal('archive-service-modal')->close();

        // SPA alert
        $this->dispatch('showAlertModal', [
            'message' => 'Service has been archived.',
        ]);

        $this->dispatch('refreshServices');
    }
};
?>

<flux:modal name="archive-service-modal" class="min-w-[22rem]">
    <div class="space-y-4">

        <div>
            <flux:heading size="lg">Archive Service</flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to archive
                <strong>{{ optional($service)->name }}</strong>?
                This action will mark the service as archived.
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">
                    Cancel
                </flux:button>
            </flux:modal.close>

            <flux:button variant="danger" wire:click="archive">
                Archive
            </flux:button>
        </div>

    </div>
</flux:modal>
