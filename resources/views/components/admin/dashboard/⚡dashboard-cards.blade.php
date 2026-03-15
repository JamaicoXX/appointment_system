<?php

use Livewire\Component;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\MedicalRecords;
use App\Models\Payments;
use Livewire\Attributes\Computed;

new class extends Component {
    public $totalPatients;
    public $totalAppointments;
    public $totalMedicalRecords;
    public $totalPayments;

    public function mount()
    {
        $this->totalPatients = Patients::count();
        $this->totalAppointments = Appointments::count();
        $this->totalMedicalRecords = MedicalRecords::count();
        $this->totalPayments = Payments::count();
    }

    #[Computed]
    public function recentAppointments()
    {
        return Appointments::with('patient')->orderBy('created_at', 'desc')->take(5)->get(); // Collection
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'recentAppointments' => $this->recentAppointments,
        ]);
    }
};
?>

<div class="p-6 space-y-6">

    <flux:heading size="xl">Admin Dashboard</flux:heading>
    <flux:text>Welcome back! Here's a quick overview of your system.</flux:text>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <flux:card>
            <flux:card.header>Patients</flux:card.header>
            <flux:card.body>
                <span class="text-3xl font-bold">{{ $totalPatients }}</span>
            </flux:card.body>
        </flux:card>

        <flux:card>
            <flux:card.header>Appointments</flux:card.header>
            <flux:card.body>
                <span class="text-3xl font-bold">{{ $totalAppointments }}</span>
            </flux:card.body>
        </flux:card>

        <flux:card>
            <flux:card.header>Medical Records</flux:card.header>
            <flux:card.body>
                <span class="text-3xl font-bold">{{ $totalMedicalRecords }}</span>
            </flux:card.body>
        </flux:card>

        <flux:card>
            <flux:card.header>Payments</flux:card.header>
            <flux:card.body>
                <span class="text-3xl font-bold">{{ $totalPayments }}</span>
            </flux:card.body>
        </flux:card>

    </div>

    {{-- Recent Appointments Table --}}
    <div class="mt-6">
        <flux:heading size="lg">Recent Appointments</flux:heading>
        <flux:table :paginate="$recentAppointments">
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Patient Name</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($recentAppointments as $appointment)
                    <flux:table.row :key="$appointment->id">
                        <flux:table.cell>{{ $appointment->id }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $appointment->patient->first_name ?? '' }}
                            {{ $appointment->patient->last_name ?? '' }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $appointment->appointment_date }}</flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                        </flux:table.cell>
                        <flux:table.cell>{{ ucfirst($appointment->status) }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

</div>
