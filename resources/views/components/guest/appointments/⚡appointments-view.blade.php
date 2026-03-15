<?php

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointments;
use App\Models\Patients;
use App\Models\Services;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $allServices = [];

    public function mount()
    {
        // Load all active services once for displaying in appointments
        $this->allServices = Services::where('archived', false)->pluck('name', 'id')->toArray();
    }

    #[Computed]
    public function appointments()
    {
        // Find the patient linked to the current authenticated user
        $patient = Patients::where('user_id', Auth::id())->first();

        if (!$patient) {
            // Return empty pagination if no patient is found
            return Appointments::whereRaw('1 = 0')->paginate(5);
        }

        // Load appointments with payment relation to avoid N+1 queries
        return Appointments::where('patient_id', $patient->id)
            ->where('archived', 0)
            ->with('payment') // ensure Appointments model has: public function payment() { return $this->hasOne(Payments::class); }
            ->orderBy('appointment_date', 'desc')
            ->paginate(5);
    }
};
?>

<div class="bg-white p-6 border border-gray-100 shadow-md rounded-md">
    <livewire:user.modals.add-appointment-modal wire:key="add-appointment-modal-user" />

    <div class="flex items-center mb-4">
        <flux:heading size="lg">My Appointments</flux:heading>
        <flux:spacer />
        <flux:modal.trigger name="add-appointment-modal">
            <flux:button variant="primary" icon="plus">Book Appointment</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:separator class="my-4" />

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 p-4">
        @foreach ($this->appointments as $appointment)
            <div class="bg-sky-50 border border-sky-100 rounded-xl p-5 shadow-sm">
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">Appointment Date</p>
                            <p class="font-semibold text-lg text-gray-800">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                            </p>
                        </div>

                        <flux:badge color="blue">
                            {{ ucfirst($appointment->status) }}
                        </flux:badge>
                    </div>

                    <!-- Payment Status -->
                    <div class="mt-3">
                        <p class="text-sm text-gray-500">Payment</p>
                        @php
                            $paymentStatus = $appointment->payment?->payment_status ?? 'No Payment';

                            $paymentColor = match (strtolower($paymentStatus)) {
                                'paid' => 'green',
                                'pending' => 'yellow',
                                'failed', 'declined' => 'red',
                                default => 'gray',
                            };
                        @endphp

                        <flux:badge color="{{ $paymentColor }}" size="sm">
                            {{ ucfirst($paymentStatus) }}
                        </flux:badge>
                    </div>

                    <!-- Services -->
                    <div class="mt-3">
                        <p class="text-sm text-gray-500">Services</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach ($appointment->services_availed ?? [] as $serviceId)
                                @if (isset($allServices[$serviceId]))
                                    <flux:badge size="sm" color="sky">
                                        {{ $allServices[$serviceId] }}
                                    </flux:badge>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($appointment->notes)
                        <div class="mt-3">
                            <p class="text-sm text-gray-500">Notes</p>
                            <p class="text-sm text-gray-700">{{ $appointment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->appointments->links() }}
    </div>
</div>
