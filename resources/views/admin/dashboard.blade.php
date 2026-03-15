<x-layouts::app :title="__('Dashboard')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Dashboard</flux:heading>
        </div>
        <livewire:admin.dashboard />
    </div>
</x-layouts::app>
