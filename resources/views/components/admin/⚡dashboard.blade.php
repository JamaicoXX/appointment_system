<?php

use Livewire\Component;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\MedicalRecords;
use App\Models\Payments;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';
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

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function recentAppointments()
    {
        return Appointments::with('patient')
            ->where('archived', 0)
            ->when($this->sortBy, function ($query) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            })
            ->paginate(5);
    }
};
?>

<div class="space-y-6">

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Patients -->
        <flux:card class="flex gap-4 items-center p-4">
            <div class="p-3 bg-sky-600 rounded-xl flex-shrink-0">
                <flux:icon name="users" class="text-white w-6 h-6" />
            </div>
            <div>
                <flux:heading>Patients</flux:heading>
                <span class="text-3xl font-bold">{{ $totalPatients }}</span>
            </div>
        </flux:card>

        <!-- Appointments -->
        <flux:card class="flex gap-4 items-center p-4">
            <div class="p-3 bg-green-500 rounded-xl flex-shrink-0">
                <flux:icon name="calendar" class="text-white w-6 h-6" />
            </div>
            <div>
                <flux:heading>Appointments</flux:heading>
                <span class="text-3xl font-bold">{{ $totalAppointments }}</span>
            </div>
        </flux:card>

        <!-- Medical Records -->
        <flux:card class="flex gap-4 items-center p-4">
            <div class="p-3 bg-yellow-500 rounded-xl flex-shrink-0">
                <flux:icon name="clipboard-document" class="text-white w-6 h-6" />
            </div>
            <div>
                <flux:heading>Medical Records</flux:heading>
                <span class="text-3xl font-bold">{{ $totalMedicalRecords }}</span>
            </div>
        </flux:card>

        <!-- Payments -->
        <flux:card class="flex gap-4 items-center p-4">
            <div class="p-3 bg-red-500 rounded-xl flex-shrink-0">
                <flux:icon name="credit-card" class="text-white w-6 h-6" />
            </div>
            <div>
                <flux:heading>Payments</flux:heading>
                <span class="text-3xl font-bold">{{ $totalPayments }}</span>
            </div>
        </flux:card>

    </div>

    {{-- Recent Appointments Table --}}
    <div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
        <flux:heading size="lg">Recent Appointments</flux:heading>
        <flux:table :paginate="$this->recentAppointments" class="mt-3">
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Patient Name</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->recentAppointments as $appointment)
                    <flux:table.row :key="$appointment->id">
                        <flux:table.cell>{{ $appointment->id }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $appointment->patient->first_name ?? '' }}
                            {{ $appointment->patient->last_name ?? '' }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $appointment->appointment_date }}</flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="green" inset="top bottom">
                                {{ ucfirst($appointment->status) }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

</div>
