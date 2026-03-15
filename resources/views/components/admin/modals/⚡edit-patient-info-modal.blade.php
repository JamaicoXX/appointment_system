<?php

use Livewire\Component;
use App\Models\Patients;

new class extends Component {
    public $patientId;
    public $patient;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $birthdate;
    public $gender;
    public $contact_number;
    public $email;
    public $address;
    public $emergency_contact_name;
    public $emergency_contact_number;

    protected $listeners = [
        'editPatientModal' => 'openModal',
    ];

    public function openModal($patientId)
    {
        $this->patientId = $patientId;
        $this->loadPatient();
        $this->modal('edit-patient-modal')->show();
    }

    public function loadPatient()
    {
        $this->patient = Patients::findOrFail($this->patientId);

        $this->first_name = $this->patient->first_name;
        $this->middle_name = $this->patient->middle_name;
        $this->last_name = $this->patient->last_name;
        $this->birthdate = $this->patient->birthdate;
        $this->gender = $this->patient->gender;
        $this->contact_number = $this->patient->contact_number;
        $this->email = $this->patient->email;
        $this->address = $this->patient->address;
        $this->emergency_contact_name = $this->patient->emergency_contact_name;
        $this->emergency_contact_number = $this->patient->emergency_contact_number;
    }

    public function save()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
            'email' => 'required|email|unique:patients,email,' . $this->patientId,
            'contact_number' => 'nullable|string|max:20',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
        ]);

        $this->patient->update([
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_number' => $this->emergency_contact_number,
        ]);

        $this->modal('edit-patient-modal')->close();

        // SPA alert
        $this->dispatch('showAlertModal', ['message' => 'Patient info updated successfully.']);
        $this->dispatch('refreshPatientInfo');
    }
};
?>

<flux:modal name="edit-patient-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">
    <form wire:submit.prevent="save" class="space-y-6 p-6">

        <div>
            <flux:heading size="lg">Edit Patient Information</flux:heading>
            <flux:text class="mt-2">
                Update patient details below. Fields marked with <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- First Name -->
            <flux:input label="First Name *" wire:model="first_name" />

            <!-- Middle Name -->
            <flux:input label="Middle Name" wire:model="middle_name" />

            <!-- Last Name -->
            <flux:input label="Last Name *" wire:model="last_name" />

            <!-- Birthdate -->
            <flux:input type="date" label="Birthdate *" wire:model="birthdate" />

            <!-- Gender -->
            <flux:select label="Gender *" wire:model="gender">
                <option value="">Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </flux:select>

            <!-- Contact Number -->
            <flux:input label="Contact Number" wire:model="contact_number" />

            <!-- Email -->
            <flux:input label="Email Address *" wire:model="email" />

            <!-- Address -->
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                <flux:textarea label="Address" wire:model="address" />
            </div>

            <!-- Emergency Contact -->
            <flux:input label="Emergency Contact Name" wire:model="emergency_contact_name" />
            <flux:input label="Emergency Contact Number" wire:model="emergency_contact_number" />

        </div>

        <div class="flex pt-6">
            <flux:spacer />
            <flux:button type="submit" variant="primary">
                Save Changes
            </flux:button>
        </div>
    </form>
</flux:modal>
