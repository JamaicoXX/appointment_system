<x-layouts::app :title="__('Medical Records')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Medical Records</flux:heading>
            <flux:modal.trigger name="add-medical-record-modal">
                <flux:button variant="primary" icon="plus">Add new record</flux:button>
            </flux:modal.trigger>
        </div>
        <livewire:admin.tables.medical-records-table />
    </div>
    <livewire:admin.modals.add-medical-record-modal wire:key="add-medical-record-modal" />
</x-layouts::app>
