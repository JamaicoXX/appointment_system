<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DoctorSchedule;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $search = '';

    protected $listeners = [
        'refreshSchedule' => 'refreshPage',
    ];

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
    public function schedules()
    {
        return DoctorSchedule::where('archived', 0)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('day_of_week', 'like', '%' . $this->search . '%')
                        ->orWhere('start_time', 'like', '%' . $this->search . '%')
                        ->orWhere('end_time', 'like', '%' . $this->search . '%')
                        ->orWhere('slot_limit', 'like', '%' . $this->search . '%')
                        ->orWhere('is_active', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->sortBy, fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);
    }

    public function refreshPage() {}
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">

    <!-- Modals -->
    <livewire:admin.modals.edit-business-day-modal wire:key="edit-business-day-modal" />

    <!-- Search -->
    <div class="flex mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search schedules..." icon="magnifying-glass"
            class="w-full md:w-1/2" autocomplete="off" />
    </div>

    <!-- Table -->
    <flux:table :paginate="$this->schedules">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                wire:click="sort('id')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'day_of_week'" :direction="$sortDirection"
                wire:click="sort('day_of_week')">Day</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'start_time'" :direction="$sortDirection"
                wire:click="sort('start_time')">Start Time</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'end_time'" :direction="$sortDirection"
                wire:click="sort('end_time')">End Time</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'slot_limit'" :direction="$sortDirection"
                wire:click="sort('slot_limit')">Slots</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection"
                wire:click="sort('is_active')">Active</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->schedules as $schedule)
                <flux:table.row :key="$schedule->id">
                    <flux:table.cell class="whitespace-nowrap">{{ $schedule->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $schedule->day_of_week }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $schedule->slot_limit }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $schedule->is_active ? 'green' : 'red' }}"
                            inset="top bottom">
                            {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>

                        <flux:button icon="pencil-square"
                            wire:click="$dispatch('editDoctorScheduleModal', { scheduleId: {{ $schedule->id }} })">
                            Edit</flux:button>

                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
