<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Services;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshServices' => 'refreshServices'];

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
    public function services()
    {
        return Services::where('archived', 0)->when($this->sortBy, fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))->paginate(5);
    }

    public function refreshServices() {}
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
    <livewire:admin.modals.edit-services-modal wire:key="edit-services-modal" />
    <livewire:admin.modals.archive-services-modal wire:key="archive-services-modal" />
    <flux:table :paginate="$this->services">

        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                wire:click="sort('id')">ID</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Service</flux:table.column>

            <flux:table.column>Description</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection"
                wire:click="sort('price')">Price</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'discount_price'" :direction="$sortDirection"
                wire:click="sort('discount_price')">Discount</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'duration'" :direction="$sortDirection"
                wire:click="sort('duration')">Duration</flux:table.column>

            <flux:table.column>Status</flux:table.column>

            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->services as $service)
                <flux:table.row :key="$service->id">

                    <flux:table.cell>{{ $service->id }}</flux:table.cell>

                    <flux:table.cell class="font-semibold">
                        {{ $service->name }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $service->description ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        ₱{{ number_format($service->price, 2) }}
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($service->discount_price)
                            <span class="text-green-600 font-medium">
                                ₱{{ number_format($service->discount_price, 2) }}
                            </span>
                        @else
                            -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $service->duration ? $service->duration . ' min' : '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        @if (!$service->archived)
                            <flux:badge color="green" size="sm">Active</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Archived</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon:trailing="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item icon="pencil"
                                    wire:click="$dispatch('editServiceModal', { serviceId: {{ $service->id }} })">
                                    Edit
                                </flux:menu.item>

                                <flux:menu.item variant="danger" icon="trash"
                                    wire:click="$dispatch('archiveServiceModal', { serviceId: {{ $service->id }} })">
                                    Archive
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>

    </flux:table>

</div>
