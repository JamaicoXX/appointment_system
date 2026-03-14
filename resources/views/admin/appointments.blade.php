<x-layouts::app :title="__('Appointments')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Appointments</flux:heading>
            <flux:button variant="primary" icon="plus">Add new appointment</flux:button>
        </div>
        <livewire:admin.tables.appointments-table />
    </div>
</x-layouts::app>
