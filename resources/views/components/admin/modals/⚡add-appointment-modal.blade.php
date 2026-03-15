<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\Payments;
use App\Models\DoctorSchedule;
use App\Models\Services;
use Carbon\Carbon;

new class extends Component {
    use WithFileUploads;

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
    public $notes;
    public $status = 'pending'; // default

    // Payment info
    public $gcash_reference_number;
    public $gcash_number;
    public $receipt_image;
    public $payment_status = 'pending'; // default

    public $availableSlots = [];
    public $services = [];
    public $services_availed = [];

    public function mount()
    {
        $this->services = Services::where('archived', false)->get();
    }

    public function updatedAppointmentDate()
    {
        $this->availableSlots = [];
        $this->appointment_time = null;

        if (!$this->appointment_date) {
            return;
        }

        $day = Carbon::parse($this->appointment_date)->format('l');

        $schedule = DoctorSchedule::where('day_of_week', $day)->where('is_active', 1)->first();

        if (!$schedule) {
            return;
        }

        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        while ($start < $end) {
            $slot = $start->format('H:i');

            $count = Appointments::whereDate('appointment_date', $this->appointment_date)->where('appointment_time', $slot)->count();

            if ($count < $schedule->slot_limit) {
                $this->availableSlots[] = $slot;
            }

            $start->addHour(1);
        }
    }

    public function removeAttachment()
    {
        $this->receipt_image = null;
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

            'services_availed' => 'required|array|min:1',

            // Payment validation
            'gcash_reference_number' => 'required',
            'gcash_number' => 'required',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        // Find existing patient
        $patient = Patients::where('first_name', $this->first_name)
            ->where('last_name', $this->last_name)
            ->where('birthdate', $this->birthdate)
            ->where(function ($query) {
                $query->where('middle_name', $this->middle_name)->orWhereNull('middle_name');
            })
            ->first();

        // Create patient if not exists
        if (!$patient) {
            $patient = Patients::create([
                'user_id' => null,
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
        }

        // Create appointment
        $appointment = Appointments::create([
            'patient_id' => $patient->id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'services_availed' => $this->services_availed,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);

        // Upload receipt
        $path = null;

        if ($this->receipt_image) {
            $path = $this->receipt_image->store('receipts', 'public');
        }

        // Create payment
        Payments::create([
            'appointment_id' => $appointment->id,
            'amount' => 0,
            'payment_method' => 'gcash',
            'gcash_number' => $this->gcash_number,
            'gcash_reference_number' => $this->gcash_reference_number,
            'receipt_image' => $path,
            'payment_status' => $this->payment_status,
        ]);

        // Reset form
        $this->reset();

        // Close modal
        $this->modal('add-appointment-modal-admin')->close();

        // Trigger SPA alert
        $this->dispatch('showAlertModal');
        $this->dispatch('refreshAppointment');
    }
};
?>

<flux:modal name="add-appointment-modal-admin" class="md:w-[80%] !bg-sky-50" flyout position="left">

    <form wire:submit="save" class="space-y-8">

        <div>
            <flux:heading size="lg">Add Appointment (Admin)</flux:heading>
            <flux:text class="mt-2">
                Enter patient information, appointment schedule, and payment details.
            </flux:text>
            <flux:text class="text-sm text-gray-500">
                Fields marked with <span class="text-red-500">*</span> are required.
            </flux:text>
        </div>

        <flux:separator />

        <!-- PATIENT INFORMATION -->
        <div class="space-y-4">

            <div class="flex items-center">
                <flux:heading size="xl" class="text-sky-600 font-bold">Patient Information</flux:heading>
                <flux:spacer />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <!-- FIRST NAME -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        First Name <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the patient's first name.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input wire:model="first_name" />
                </div>


                <!-- MIDDLE NAME -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Middle Name

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the patient's middle name (optional).
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input wire:model="middle_name" />
                </div>


                <!-- LAST NAME -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Last Name <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the patient's last name.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input wire:model="last_name" />
                </div>


                <!-- BIRTHDATE -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Birthdate <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Select the patient's date of birth.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input type="date" wire:model="birthdate" />
                </div>


                <!-- GENDER -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Gender <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Select the patient's gender.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:select wire:model="gender">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </flux:select>
                </div>


                <!-- CONTACT -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Contact Number <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter patient's active contact number.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input wire:model="contact_number" />
                </div>


                <!-- EMAIL -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Email Address <span class="text-red-500">*</span>
                    </flux:heading>

                    <flux:input wire:model="email" />
                </div>


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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <flux:input label="Appointment Date *" type="date" wire:model.live="appointment_date" />

                <flux:select label="Appointment Time" wire:model="appointment_time">
                    <option value="">Select time</option>
                    @foreach ($availableSlots as $slot)
                        <option value="{{ $slot }}">{{ \Carbon\Carbon::parse($slot)->format('h:i A') }}</option>
                    @endforeach
                </flux:select>

                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-6">

                    <flux:checkbox.group wire:model="services_availed" label="Services you want to avail:"
                        variant="pills">

                        @foreach ($services as $service)
                            <flux:checkbox value="{{ $service->id }}" label="{{ $service->name }}" class="bg-white"
                                description="₱{{ $service->discount_price ?? $service->price }}" />
                        @endforeach

                    </flux:checkbox.group>

                </div>


                <!-- STATUS -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Appointment Status
                    </flux:heading>

                    <flux:select wire:model="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </flux:select>

                </div>


                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <flux:textarea label="Notes" wire:model="notes" />
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">

                <!-- REFERENCE -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        GCash Reference Number

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the GCash transaction reference number.
                            </flux:tooltip.content>
                        </flux:tooltip>

                    </flux:heading>

                    <flux:input wire:model="gcash_reference_number" />
                </div>


                <!-- NUMBER -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        GCash Number <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the GCash number used for the payment.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input wire:model="gcash_number" />
                </div>


                <!-- PAYMENT STATUS -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Payment Status

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Select the Payment Status for this appointment.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:select wire:model="payment_status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="declined">Declined</option>
                    </flux:select>

                </div>

                <!-- RECEIPT -->
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Upload Receipt <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Upload a screenshot of your GCash payment receipt. Maximum size: 2MB.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input type="file" wire:model="receipt_image" />

                    @if ($receipt_image)
                        <div class="flex items-center gap-2 mt-3 bg-gray-100 px-3 py-2 rounded text-sm w-fit">

                            <span>
                                {{ $receipt_image->getClientOriginalName() }}
                            </span>



                            <button type="button" wire:click="removeAttachment"
                                class="text-red-500 hover:text-red-700 font-bold">
                                ✕
                            </button>

                        </div>
                        <img src="{{ $receipt_image->temporaryUrl() }}" class="w-40 mt-2 rounded shadow">
                    @endif
                </div>

            </div>

        </div>


        <div class="flex pt-6">
            <flux:spacer />
            <flux:button type="submit" variant="primary">
                Save Appointment
            </flux:button>
        </div>


    </form>

</flux:modal>
