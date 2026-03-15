<x-layouts::app :title="__('Appointments')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Appointments</flux:heading>
            <flux:modal.trigger name="add-appointment-modal-admin">
                <flux:button variant="primary" icon="plus">Add new appointment</flux:button>
            </flux:modal.trigger>
        </div>
        <livewire:admin.tables.appointments-table />
    </div>
    <livewire:admin.modals.add-appointment-modal wire:key="add-appointment-modal-admin" />
</x-layouts::app>
