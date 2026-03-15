<?php

use Livewire\Component;
use App\Models\MedicalRecords;

new class extends Component {
    public $recordId;

    protected $listeners = [
        'archiveMedicalRecordModal' => 'openModal',
    ];

    public function openModal($recordId)
    {
        $this->recordId = $recordId;
        $this->modal('archive-medical-record-modal')->show();
    }

    public function archive()
    {
        $record = MedicalRecords::findOrFail($this->recordId);
        $record->update([
            'archived' => 1,
        ]);

        $this->modal('archive-medical-record-modal')->close();
        $this->dispatch('showAlertModal', ['message' => 'Medical record archived successfully.']);
        $this->dispatch('refreshMedicalRecords');
    }
};
?>

<flux:modal name="archive-medical-record-modal" class="min-w-[22rem]">
    <div class="space-y-4">
        <div>
            <flux:heading size="lg">Archive Medical Record</flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to archive this medical record? This action is irreversible.
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" wire:click="archive">
                Archive
            </flux:button>
        </div>
    </div>
</flux:modal>
