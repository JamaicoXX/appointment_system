<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\Payments;
use App\Models\DoctorSchedule;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

    // Appointment
    public $appointment_date;
    public $appointment_time;
    public $notes;

    // Payment
    public $gcash_reference_number;
    public $gcash_number;
    public $receipt_image;

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

        $schedule = DoctorSchedule::where([
            'day_of_week' => $day,
            'is_active' => 1,
        ])->first();

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required',
            'contact_number' => 'required',
            'email' => 'required|email',

            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',

            'services_availed' => 'required|array|min:1',

            'gcash_reference_number' => 'required',
            'gcash_number' => 'required',
            'receipt_image' => 'required|image|max:2048',
        ]);

        // Check if logged-in user already has patient record
        $patient = Patients::where('user_id', Auth::id())->first();

        if (!$patient) {
            // Create patient linked to the user
            $patient = Patients::create([
                'user_id' => Auth::id(),
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
            'status' => 'pending',
        ]);

        // Upload receipt
        $path = $this->receipt_image->store('receipts', 'public');

        // Create payment
        Payments::create([
            'appointment_id' => $appointment->id,
            'amount' => 0,
            'payment_method' => 'gcash',
            'gcash_number' => $this->gcash_number,
            'gcash_reference_number' => $this->gcash_reference_number,
            'receipt_image' => $path,
            'payment_status' => 'pending',
        ]);

        $this->reset();

        $this->modal('add-appointment-modal')->close();

        $this->dispatch('showAlertModal');
    }
};
?>

<flux:modal name="add-appointment-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">

    <form wire:submit="save" class="space-y-8">

        <div>
            <flux:heading size="lg">Book Appointment</flux:heading>
            <flux:text class="mt-2">
                Enter your information, schedule, and payment details.
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

                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        First Name <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the patient's given name.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input wire:model="first_name" />
                </div>

                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Middle Name <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter the patient's middle name.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input wire:model="middle_name" />
                </div>

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
                        <option value="prefer not to say">Prefer not to say</option>
                    </flux:select>
                </div>

                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        Contact Number <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content>
                                Enter an active mobile number where the clinic can contact the patient.
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input wire:model="contact_number" />
                </div>

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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pl-3 mt-6">

                <flux:input label="Appointment Date" type="date" wire:model.live="appointment_date" />

                <flux:select wire:model="appointment_time" label="Appointment Time">
                    <option value="">Select time</option>

                    @foreach ($this->availableSlots as $slot)
                        <option value="{{ $slot }}">
                            {{ \Carbon\Carbon::parse($slot)->format('h:i A') }}
                        </option>
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

            {{-- <flux:text class="text-sm text-gray-500">
                Upload your payment receipt and reference number.
            </flux:text> --}}

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-3 mt-6">
                <div class="space-y-1">
                    <flux:heading class="flex items-center gap-2 text-sm font-medium">
                        GCash Reference Number <span class="text-red-500">*</span>

                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost" />
                            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                <p>Enter the reference number from your GCash transaction receipt.</p>
                                <p>You can find it in your GCash app under <strong>Transaction Details</strong>.</p>
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>

                    <flux:input wire:model="gcash_reference_number" />
                </div>
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
                Book Appointment
            </flux:button>
        </div>

    </form>

</flux:modal>
