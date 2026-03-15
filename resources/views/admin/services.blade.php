<x-layouts::app :title="__('Services Table')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Services Table</flux:heading>
            <flux:modal.trigger name="add-service-modal">
                <flux:button variant="primary" icon="plus">Add new service</flux:button>
            </flux:modal.trigger>
        </div>
        <livewire:admin.tables.services-table />
    </div>
    <livewire:admin.modals.add-services-modal wire:key="add-services-modal" />
</x-layouts::app>
