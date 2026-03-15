<?php

use Livewire\Component;
use App\Models\Patients;

new class extends Component
{
    public $patientId;
    public $patient;

    protected $listeners = [
        'archivePatientModal' => 'openModal'
    ];

    public function openModal($patientId)
    {
        $this->patientId = $patientId;
        $this->patient = Patients::findOrFail($this->patientId);
        $this->modal('archive-patient-info-modal')->show();
    }

    public function archive()
    {
        $this->patient->update([
            'archived' => 1
        ]);

        $this->modal('archive-patient-info-modal')->close();

        // SPA alert
        $this->dispatch('showAlertModal', ['message' => 'Patient has been archived.']);
        $this->dispatch('refreshPatientInfo');
    }
};
?>

<flux:modal name="archive-patient-info-modal" class="min-w-[22rem]">
    <div class="space-y-4">

        <div>
            <flux:heading size="lg">Archive Patient</flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to archive <strong>{{ optional($patient)->first_name }} {{ optional($patient)->last_name }}</strong>? This action will mark the patient as archived.
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