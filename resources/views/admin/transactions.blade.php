<x-layouts::app :title="__('Transactions')">
    {{-- Main Div --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="flex w-full justify-between">
            <flux:heading size="xl" level="1">Transactions</flux:heading>
            <flux:modal.trigger name="add-payment-modal">
                <flux:button variant="primary" icon="plus">Add new payment</flux:button>
            </flux:modal.trigger>
        </div>
        <livewire:admin.tables.transactions-table />
    </div>
    <livewire:admin.modals.add-payment-modal wire:key="add-payment-modal" />
</x-layouts::app>
