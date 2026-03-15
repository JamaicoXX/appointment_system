<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicalRecords;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'medical_records.id';
    public $sortDirection = 'desc';
    public $search = '';
    protected $listeners = ['refreshMedicalRecords' => 'refreshMedicalRecords'];

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
    public function records()
    {
        $sub = MedicalRecords::selectRaw('MAX(id) as latest_id')->groupBy('patient_id');

        return MedicalRecords::with('patient')
            ->joinSub($sub, 'latest_records', function ($join) {
                $join->on('medical_records.id', '=', 'latest_records.latest_id');
            })
            ->leftJoin('patients', 'medical_records.patient_id', '=', 'patients.id')
            ->select('medical_records.*')
            ->where('medical_records.archived', 0)

            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('patients.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('patients.middle_name', 'like', '%' . $this->search . '%')
                        ->orWhere('patients.last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('medical_records.id', 'like', '%' . $this->search . '%')
                        ->orWhere('medical_records.diagnosis', 'like', '%' . $this->search . '%');
                });
            })

            ->when($this->sortBy === 'patients.first_name', fn($query) => $query->orderBy('patients.first_name', $this->sortDirection), fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))

            ->paginate(5);
    }

    public function refreshMedicalRecords(){

    }
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
    <div class="flex mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search patient or diagnosis..."
            icon="magnifying-glass" class="w-full md:w-1/2" autocomplete="off" />
    </div>
    <livewire:admin.modals.edit-medical-record-modal wire:key="edit-medical-record-modal" />
        <livewire:admin.modals.archive-medical-record-modal wire:key="archive-medical-record-modal" />
    <flux:table :paginate="$this->records">

        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'medical_records.id'" :direction="$sortDirection"
                wire:click="sort('medical_records.id')">ID</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'patients.first_name'" :direction="$sortDirection"
                wire:click="sort('patients.first_name')">Patient Name</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'doctor_name'" :direction="$sortDirection"
                wire:click="sort('doctor_name')">Doctor</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'follow_up_date'" :direction="$sortDirection"
                wire:click="sort('follow_up_date')">Follow Up</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column>

            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->records as $record)
                <flux:table.row :key="$record->id">
                    <flux:table.cell>{{ $record->id }}</flux:table.cell>

                    <flux:table.cell>
                        {{ $record->patient->first_name ?? '' }}
                        {{ $record->patient->middle_name ?? '' }}
                        {{ $record->patient->last_name ?? '' }}
                    </flux:table.cell>

                    <flux:table.cell>{{ $record->doctor_name }}</flux:table.cell>

                    <flux:table.cell>
                        {{ $record->follow_up_date ? \Carbon\Carbon::parse($record->follow_up_date)->format('F d, Y') : '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ \Carbon\Carbon::parse($record->created_at)->format('F d, Y h:i A') }}
                    </flux:table.cell>


                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon:trailing="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item icon="eye">
                                    View</flux:menu.item>
                                <flux:menu.item icon="pencil" wire:click="$dispatch('editMedicalRecordModal', { recordId: {{ $record->id }} })">
                                    Edit</flux:menu.item>
                                <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('archiveMedicalRecordModal', { recordId: {{ $record->id }} })">
                                    Archive</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>

    </flux:table>
</div>
