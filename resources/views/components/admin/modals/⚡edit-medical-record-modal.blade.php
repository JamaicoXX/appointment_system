<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MedicalRecords;
use App\Models\Patients;
use Carbon\Carbon;

new class extends Component {
    use WithFileUploads;

    public $recordId;
    public $record;

    public $doctor_name;
    public $chief_complaint;
    public $findings;
    public $diagnosis;
    public $treatment_plan;
    public $prescription;
    public $follow_up_date;

    public $attachment; // New upload
    public $existing_attachment; // Existing file path

    protected $listeners = [
        'editMedicalRecordModal' => 'openModal'
    ];

    public function openModal($recordId)
    {
        $this->recordId = $recordId;
        $this->loadRecord();
        $this->modal('edit-medical-record-modal')->show();
    }

    public function loadRecord()
    {
        $this->record = MedicalRecords::findOrFail($this->recordId);

        $this->doctor_name = $this->record->doctor_name;
        $this->chief_complaint = $this->record->chief_complaint;
        $this->findings = $this->record->findings;
        $this->diagnosis = $this->record->diagnosis;
        $this->treatment_plan = $this->record->treatment_plan;
        $this->prescription = $this->record->prescription;
        $this->follow_up_date = $this->record->follow_up_date;
        $this->existing_attachment = $this->record->attachments ? json_decode($this->record->attachments)[0] ?? null : null;
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function save()
    {
        $this->validate([
            'doctor_name' => 'nullable|string|max:255',
            'chief_complaint' => 'nullable|string',
            'findings' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'prescription' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $attachmentPath = $this->existing_attachment;

        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('medical_records', 'public');
        }

        $this->record->update([
            'doctor_name' => $this->doctor_name,
            'chief_complaint' => $this->chief_complaint,
            'findings' => $this->findings,
            'diagnosis' => $this->diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'prescription' => $this->prescription,
            'attachments' => json_encode([$attachmentPath]),
        ]);
    }
       
};
?>

<flux:modal name="edit-medical-record-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">
    <form wire:submit.prevent="save" class="space-y-8">

        <div>
            <flux:heading size="lg">Edit Medical Record</flux:heading>
            <flux:text class="mt-2">
                Update patient medical record details below.
            </flux:text>
            <flux:text class="text-sm text-gray-500">
                Fields marked with <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <div class="space-y-4">
            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">Medical Record Details</flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">
                <flux:input label="Doctor Name" wire:model="doctor_name" />
                <flux:textarea label="Chief Complaint" wire:model="chief_complaint" />
                <flux:textarea label="Findings" wire:model="findings" />
                <flux:textarea label="Diagnosis" wire:model="diagnosis" />
                <flux:textarea label="Treatment Plan" wire:model="treatment_plan" />
                <flux:textarea label="Prescription" wire:model="prescription" />
                <flux:input type="date" label="Follow-up Date" wire:model="follow_up_date" />

                <!-- Attachment -->
                <div class="col-span-1 md:col-span-3">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Attachment
                    </flux:heading>

                    <flux:input type="file" wire:model="attachment" />

                    <!-- New upload preview -->
                    @if ($attachment)
                        <div class="flex items-center gap-2 mt-2 bg-gray-100 px-3 py-2 rounded text-sm w-fit">
                            <span>{{ $attachment->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeAttachment"
                                class="text-red-500 font-bold hover:text-red-700">✕</button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $attachment->temporaryUrl() }}" class="w-32 rounded shadow" />
                        </div>
                    @endif

                    <!-- Existing attachment -->
                    @if ($existing_attachment && !$attachment)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $existing_attachment) }}" target="_blank"
                                class="text-blue-600 underline">
                                {{ basename($existing_attachment) }}
                            </a>
                            <img src="{{ asset('storage/' . $existing_attachment) }}" class="w-32 rounded shadow mt-1" />
                        </div>
                    @endif
                </div>
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
