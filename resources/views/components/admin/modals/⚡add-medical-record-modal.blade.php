<?php

use Livewire\Component;

use Livewire\WithFileUploads;
use App\Models\MedicalRecords;
use App\Models\Patients;

new class extends Component {
    use WithFileUploads;

    public $patient_id;

    public $doctor_name;
    public $chief_complaint;
    public $findings;
    public $diagnosis;
    public $treatment_plan;
    public $prescription;

    public $follow_up_date;

    public $attachments = [];

    public function save()
    {
        $this->validate([
            'patient_id' => 'required',
            'doctor_name' => 'nullable|string|max:255',
            'chief_complaint' => 'nullable|string',
            'findings' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'prescription' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:2048',
        ]);

        $attachments = [];

        if ($this->attachments) {
            foreach ($this->attachments as $file) {
                $attachments[] = $file->store('medical_records', 'public');
            }
        }

        MedicalRecords::create([
            'patient_id' => $this->patient_id,
            'doctor_name' => $this->doctor_name,
            'chief_complaint' => $this->chief_complaint,
            'findings' => $this->findings,
            'diagnosis' => $this->diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'prescription' => $this->prescription,
            'attachments' => json_encode($attachments),
            'follow_up_date' => $this->follow_up_date,
        ]);

        $this->reset();

        $this->modal('add-medical-record-modal')->close();

        $this->dispatch('showAlertModal');
        $this->dispatch('refreshMedicalRecords');
    }

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);

        // reindex array to prevent Livewire issues
        $this->attachments = array_values($this->attachments);
    }

    public function patients()
    {
        return Patients::orderBy('first_name')->get();
    }
};
?>

<flux:modal name="add-medical-record-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">

    <form wire:submit="save" class="space-y-8">

        <div>
            <flux:heading size="lg">Add Medical Record</flux:heading>
            <flux:text class="mt-2">
                Enter patient medical record details.
            </flux:text>
            <flux:text class="text-sm text-gray-500">
                Fields marked with <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <!-- PATIENT -->
        <div class="space-y-4">

            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">
                    Patient Information
                </flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <flux:select wire:model="patient_id" label="Patient *">
                    <option value="">Select patient</option>

                    @foreach ($this->patients() as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </option>
                    @endforeach

                </flux:select>

                <flux:input label="Doctor Name" wire:model="doctor_name" />

            </div>

        </div>

        <flux:separator />

        <!-- MEDICAL DETAILS -->

        <div class="space-y-4">

            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">
                    Medical Details
                </flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <div class="col-span-3">
                    <flux:textarea label="Chief Complaint" wire:model="chief_complaint" />
                </div>

                <div class="col-span-3">
                    <flux:textarea label="Findings" wire:model="findings" />
                </div>

                <div class="col-span-3">
                    <flux:textarea label="Diagnosis" wire:model="diagnosis" />
                </div>

                <div class="col-span-3">
                    <flux:textarea label="Treatment Plan" wire:model="treatment_plan" />
                </div>

                <div class="col-span-3">
                    <flux:textarea label="Prescription" wire:model="prescription" />
                </div>

                <flux:input type="date" label="Follow Up Date" wire:model="follow_up_date" />

            </div>

        </div>

        <flux:separator />

        <!-- ATTACHMENTS -->

        <div class="space-y-4">

            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">
                    Attachments
                </flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <div class="col-span-2">

                    <flux:input type="file" wire:model="attachments" multiple />

                    <flux:text class="text-xs text-gray-500 mt-2">
                        Upload lab results, scans, or related files. Max 2MB each.
                    </flux:text>

                    @if ($attachments)

                        <div class="flex gap-3 mt-3 flex-wrap">

                            @foreach ($attachments as $index => $file)
                                <div class="flex items-center gap-2 px-3 py-2 border rounded bg-white text-xs shadow">

                                    <span>
                                        {{ $file->getClientOriginalName() }}
                                    </span>

                                    <button type="button" wire:click="removeAttachment({{ $index }})"
                                        class="text-red-500 hover:text-red-700 font-bold">

                                        ✕

                                    </button>

                                </div>
                            @endforeach

                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div class="flex pt-6">
            <flux:spacer />

            <flux:button type="submit" variant="primary">
                Save Medical Record
            </flux:button>

        </div>

    </form>

</flux:modal>
