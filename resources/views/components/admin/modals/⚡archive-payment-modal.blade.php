<?php

use Livewire\Component;
use App\Models\Payments;

new class extends Component {
    public $paymentId;

    protected $listeners = [
        'archivePaymentModal' => 'openModal',
    ];

    public function openModal($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->modal('archive-payment-modal')->show();
    }

    public function archive()
    {
        $payment = Payments::find($this->paymentId);

        if ($payment) {
            $payment->archived = 1;
            $payment->save();
        }

        $this->modal('archive-payment-modal')->close();

        // Optionally, trigger a SPA alert or refresh
        $this->dispatch('showAlertModal');
        $this->dispatch('refreshPayments');
    }
};
?>

<flux:modal name="archive-payment-modal" class="min-w-[22rem]">
    <div class="space-y-4">
        <div>
            <flux:heading size="lg">Archive Payment</flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to archive this payment? This action cannot be undone.
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" wire:click="archive">
                Archive
            </flux:button>
        </div>
    </div>
</flux:modal>
