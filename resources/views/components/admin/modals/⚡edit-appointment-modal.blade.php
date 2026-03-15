<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\Payments;
use App\Models\Services;

new class extends Component {
    use WithFileUploads;

    public $appointmentId;

    // Patient info
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

    // Appointment info
    public $appointment_date;
    public $appointment_time;
    public $services = [];
    public $services_availed = [];
    public $notes;
    public $status;

    // Payment info
    public $gcash_reference_number;
    public $gcash_number;
    public $receipt_image;
    public $existing_receipt;
    public $payment_status;

    public $appointment;

    protected $listeners = ['editAppointmentModal' => 'openEditModal'];

    public function mount()
    {
        $this->services = Services::where('archived', false)->get();
    }

    public function openEditModal($appointmentId)
    {
        $this->appointmentId = $appointmentId;

        // Load appointment with relationships
        $this->appointment = Appointments::with(['patient'])->findOrFail($appointmentId);

        $patient = $this->appointment->patient;
        $payment = $this->appointment->payment;

        // Load patient info
        $this->first_name = $patient->first_name;
        $this->middle_name = $patient->middle_name;
        $this->last_name = $patient->last_name;
        $this->birthdate = $patient->birthdate;
        $this->gender = strtolower($patient->gender);
        $this->contact_number = $patient->contact_number;
        $this->email = $patient->email;
        $this->address = $patient->address;
        $this->emergency_contact_name = $patient->emergency_contact_name;
        $this->emergency_contact_number = $patient->emergency_contact_number;

        // Load appointment info
        $this->appointment_date = $this->appointment->appointment_date;
        $this->appointment_time = $this->appointment->appointment_time;
        $this->services_availed = $this->appointment->services_availed;
        $this->status = strtolower($this->appointment->status);
        $this->notes = $this->appointment->notes;

        // Load payment info if exists
        if ($payment) {
            $this->gcash_reference_number = $payment->gcash_reference_number;
            $this->gcash_number = $payment->gcash_number;
            $this->payment_status = strtolower($payment->payment_status);
            $this->existing_receipt = $payment->receipt_image;
        } else {
            $this->gcash_reference_number = '';
            $this->gcash_number = '';
            $this->payment_status = 'pending';
            $this->existing_receipt = null;
        }

        // Open the modal
        $this->modal('edit-appointment-modal-admin')->show();
    }

    public function save()
    {
        $this->validate([
            // Patient validation
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required',
            'contact_number' => 'required',
            'email' => 'required|email',

            // Appointment validation
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',

            // Payment validation
            'gcash_reference_number' => 'required',
            'gcash_number' => 'required',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $appointment = Appointments::findOrFail($this->appointmentId);
        $patient = $appointment->patient;

        // Update patient
        $patient->update([
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

        // Update appointment
        $appointment->update([
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'services_availed' => $this->services_availed,
            'notes' => $this->notes,
            'status' => strtolower($this->status),
        ]);

        // Update payment
        $paymentData = [
            'gcash_number' => $this->gcash_number,
            'gcash_reference_number' => $this->gcash_reference_number,
            'payment_status' => strtolower($this->payment_status),
        ];

        if ($this->receipt_image) {
            $paymentData['receipt_image'] = $this->receipt_image->store('receipts', 'public');
        }

        if ($appointment->payment) {
            $appointment->payment->update($paymentData);
        } else {
            $paymentData['appointment_id'] = $appointment->id;
            $paymentData['amount'] = 0;
            $paymentData['payment_method'] = 'gcash';
            Payments::create($paymentData);
        }

        $this->dispatch('showAlertModal');
        $this->dispatch('refreshAppointment');
        $this->modal('edit-appointment-modal-admin')->close();
        $this->reset();
    }
};
?>

<flux:modal name="edit-appointment-modal-admin" class="md:w-[80%] !bg-sky-50" flyout position="bottom">

    <form wire:submit="save" class="space-y-8">

        <div>
            <flux:heading size="lg">Edit Appointment (Admin)</flux:heading>
            <flux:text class="mt-2">
                Update patient's information, appointment details, and payment status.
            </flux:text>
        </div>

        <flux:separator />

        <!-- PATIENT INFORMATION -->
        <div class="space-y-4">
            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">Patient Information</flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <flux:input label="First Name" wire:model="first_name" />
                <flux:input label="Middle Name" wire:model="middle_name" />
                <flux:input label="Last Name" wire:model="last_name" />
                <flux:input label="Birthdate" type="date" wire:model="birthdate" />
                <flux:select label="Gender" wire:model="gender">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </flux:select>
                <flux:input label="Contact Number" wire:model="contact_number" />
                <flux:input label="Email Address" wire:model="email" />
                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <flux:textarea label="Address" wire:model="address" />
                </div>
                <flux:input label="Emergency Contact Name" wire:model="emergency_contact_name" />
                <flux:input label="Emergency Contact Number" wire:model="emergency_contact_number" />
            </div>
        </div>

        <flux:separator />

        <!-- APPOINTMENT DETAILS -->
        <div class="space-y-4">
            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">Appointment Details</flux:heading>
                <flux:spacer />
            </div>

            <div class="col-span-1 md:col-span-2 lg:col-span-3 py-6">

                <flux:checkbox.group wire:model="services_availed" label="Services availed:" variant="pills">

                    @foreach ($services as $service)
                        <flux:checkbox value="{{ $service->id }}" label="{{ $service->name }}" class="bg-white"
                            description="₱{{ $service->discount_price ?? $service->price }}" />
                    @endforeach

                </flux:checkbox.group>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <flux:input label="Appointment Date" type="date" wire:model="appointment_date" />
                <flux:input label="Appointment Time" type="time" wire:model="appointment_time" />
                <flux:select label="Appointment Status" wire:model="status">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </flux:select>
                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <flux:textarea label="Notes" placeholder="Describe your concern" wire:model="notes" />
                </div>
            </div>
        </div>

        <flux:separator />

        <!-- PAYMENT -->
        <div class="space-y-4">
            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">GCash Payment</flux:heading>
                <flux:spacer />
            </div>

            <flux:text class="text-sm text-gray-500">
                Update patient's payment info. Upload a new receipt if needed.
            </flux:text>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <flux:input label="GCash Reference Number" wire:model="gcash_reference_number" />
                <flux:input label="GCash Number" wire:model="gcash_number" />
                <flux:input label="Upload Receipt" type="file" wire:model="receipt_image" />
                @if ($existing_receipt)
                    <flux:text class="text-sm text-gray-500">Current receipt: {{ $existing_receipt }}</flux:text>
                @endif
                <flux:select label="Payment Status" wire:model="payment_status">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="declined">Declined</option>
                </flux:select>
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
