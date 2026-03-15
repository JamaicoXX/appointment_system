<x-layouts::app :title="__('Business Days Editor')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Business Days Editor</flux:heading>
        </div>
        <livewire:admin.tables.business-days-table />
    </div>
</x-layouts::app>
