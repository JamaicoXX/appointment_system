<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patients;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshPatientInfo' => 'refreshPatientInfo'];

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
    public function patients()
    {
        return Patients::withCount('appointments') // Get count of appointments
            ->where('archived', 0)
            ->when($this->sortBy, fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(5);
    }

    public function refreshPatientInfo() {}
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
    <livewire:admin.modals.edit-patient-info-modal wire:key="edit-patient-info-modal" />
    <livewire:admin.modals.archive-patient-info-modal wire:key="archive-patient-info-modal" />
    <flux:table :paginate="$this->patients">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                wire:click="sort('id')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'first_name'" :direction="$sortDirection"
                wire:click="sort('first_name')">First Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'last_name'" :direction="$sortDirection"
                wire:click="sort('last_name')">Last Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'birthdate'" :direction="$sortDirection"
                wire:click="sort('birthdate')">Birthdate</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'gender'" :direction="$sortDirection"
                wire:click="sort('gender')">Gender</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'contact_number'" :direction="$sortDirection"
                wire:click="sort('contact_number')">Contact</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection"
                wire:click="sort('email')">Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'appointments_count'" :direction="$sortDirection"
                wire:click="sort('appointments_count')">Appointments</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->patients as $patient)
                <flux:table.row :key="$patient->id">
                    <flux:table.cell>{{ $patient->id }}</flux:table.cell>
                    <flux:table.cell>{{ $patient->first_name }}</flux:table.cell>
                    <flux:table.cell>{{ $patient->last_name }}</flux:table.cell>
                    <flux:table.cell>{{ \Carbon\Carbon::parse($patient->birthdate)->format('F d, Y') }}
                    </flux:table.cell>
                    <flux:table.cell>{{ ucfirst($patient->gender) }}</flux:table.cell>
                    <flux:table.cell>{{ $patient->contact_number ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ $patient->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="blue" size="sm">
                            {{ $patient->appointments_count }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon:trailing="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item icon="pencil" wire:click="$dispatch('editPatientModal', { patientId: {{ $patient->id }} })">
                                    Edit</flux:menu.item>
                                <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('archivePatientModal', { patientId: {{ $patient->id }} })">
                                    Archive</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
