<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointments;
use Livewire\Attributes\Computed;

new class extends Component {
    public $sortBy = 'id';
    public $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function appointments()
    {
        return Appointments::with('patient')
            ->when($this->sortBy, fn ($query) =>
                $query->orderBy($this->sortBy, $this->sortDirection)
            )
            ->paginate(5);
    }
};
?>

<div class="bg-white p-6 border border-gray-100 shadow-md rounded-md">
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
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'notes'" :direction="$sortDirection"
                wire:click="sort('notes')">Notes</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->appointments as $appointment)
                <flux:table.row :key="$appointment->id">
                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->patient->first_name . ' ' . $appointment->patient->last_name }}</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->appointment_time }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="yellow" inset="top bottom">
                            {{ ucfirst($appointment->status) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="green" inset="top bottom">
                            {{ ucfirst($appointment->payment_status) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->notes }}</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $appointment->created_at }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
