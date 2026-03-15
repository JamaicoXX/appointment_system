<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointments;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $search = '';
    protected $listeners = ['showAlertModal' => 'showModal', 'refreshAppointment' => 'refreshPage'];

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
    public function appointments()
    {
        return Appointments::with(['patient', 'payment'])
            ->where('archived', 0)

            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhereHas('patient', function ($p) {
                            $p->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        });
                });
            })

            ->when($this->sortBy, fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))

            ->paginate(5);
    }

    public function showModal()
    {
        $this->modal('alert-modal')->show();
    }

    public function refreshPage() {}
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
    <livewire:admin.modals.edit-appointment-modal wire:key="edit-appointment-modal" />
    <livewire:admin.modals.archive-appointment-modal wire:key="archive-appointment-modal" />
    <div class="flex mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search patient..." icon="magnifying-glass"
            class="w-full md:w-1/2" autocomplete="off" />
    </div>

    <flux:table :paginate="$this->appointments">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                wire:click="sort('id')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'patient->first_name'" :direction="$sortDirection"
                wire:click="sort('patient->first_name')">Patient Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'appointment_date'" :direction="$sortDirection"
                wire:click="sort('appointment_date')">Date</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'appointment_time'" :direction="$sortDirection"
                wire:click="sort('appointment_time')">Time</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'payment.payment_status'" :direction="$sortDirection"
                wire:click="sort('payment.payment_status')">Payment Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'services_availed'" :direction="$sortDirection"
                wire:click="sort('services_availed')">Services Availed</flux:table.column>
            {{-- <flux:table.column sortable :sorted="$sortBy === 'notes'" :direction="$sortDirection"
                wire:click="sort('notes')">Notes</flux:table.column> --}}
            {{-- <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column> --}}
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->appointments as $appointment)
                <flux:table.row :key="$appointment->id">
                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ trim($appointment->patient->first_name . ' ' . $appointment->patient->middle_name . ' ' . $appointment->patient->last_name) }}
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('F d, Y') }}</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</flux:table.cell>


                    <flux:table.cell>
                        <flux:badge size="sm" color="green" inset="top bottom">
                            {{ ucfirst($appointment->payment->payment_status) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="yellow" inset="top bottom">
                            {{ ucfirst($appointment->status) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        @foreach ($appointment->services_availed ?? [] as $serviceId)
                            <flux:badge size="sm" color="blue" inset="top bottom">
                                {{ \App\Models\Services::find($serviceId)?->name }}
                            </flux:badge>
                        @endforeach
                    </flux:table.cell>

                    {{-- <flux:table.cell class="whitespace-nowrap">{{ $appointment->notes }}</flux:table.cell> --}}

                    {{-- <flux:table.cell class="whitespace-nowrap">{{ $appointment->created_at }}</flux:table.cell> --}}

                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon:trailing="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item icon="pencil"
                                    wire:click="$dispatch('editAppointmentModal', { appointmentId: {{ $appointment->id }} })">
                                    Edit</flux:menu.item>
                                <flux:menu.item variant="danger" icon="trash"
                                    wire:click="$dispatch('archiveAppointmentModal', { appointmentId: {{ $appointment->id }} })">
                                    Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
