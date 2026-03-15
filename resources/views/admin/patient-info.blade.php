<x-layouts::app :title="__('Patient Info')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Patient Info</flux:heading>
            <flux:modal.trigger name="add-patient-info-modal">
                <flux:button variant="primary" icon="plus">Add new patient</flux:button>
            </flux:modal.trigger>
        </div>
        <livewire:admin.tables.patient-info-table />
    </div>
    <livewire:admin.modals.add-patient-info-modal wire:key="add-patient-info-modal" />
</x-layouts::app>
