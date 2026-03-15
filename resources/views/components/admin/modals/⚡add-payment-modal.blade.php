<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Appointments;
use App\Models\Payments;

new class extends Component {
    use WithFileUploads;

    public $appointment_id;
    public $amount;
    public $gcash_number;
    public $gcash_reference_number;
    public $receipt_image;
    public $payment_status = 'pending';

    public $appointments = [];

    public function mount()
    {
        // Fetch all non-archived appointments to select from
        $this->appointments = Appointments::with('patient')->where('archived', false)->orderBy('appointment_date', 'desc')->get();
    }

    public function removeReceipt()
    {
        $this->receipt_image = null;
    }

    public function save()
    {
        $this->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'gcash_number' => 'nullable|string|max:255',
            'gcash_reference_number' => 'nullable|string|max:255',
            'receipt_image' => 'nullable|image|max:2048',
            'payment_status' => 'required|in:pending,approved,rejected,declined',
        ]);

        $path = $this->receipt_image ? $this->receipt_image->store('payments', 'public') : null;

        $auth_user= auth()->check() ? auth()->id() : null;

        Payments::create([
            'appointment_id' => $this->appointment_id,
            'amount' => $this->amount,
            'gcash_number' => $this->gcash_number,
            'gcash_reference_number' => $this->gcash_reference_number,
            'receipt_image' => $path,
            'payment_status' => $this->payment_status,
            'reviewed_by' => $auth_user
        ]);

        $this->reset();
        $this->modal('add-payment-modal')->close();
        $this->dispatch('refreshPayments');
    }
};
?>


<flux:modal name="add-payment-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">
    <form wire:submit.prevent="save" class="space-y-6">

        <div>
            <flux:heading size="lg">Add Payment</flux:heading>
            <flux:text class="mt-2">
                Select an appointment and enter payment details.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Appointment Selector -->
            <flux:select label="Select Appointment *" wire:model="appointment_id">
                <option value="">-- Select Appointment --</option>
                @foreach ($appointments as $appointment)
                    <option value="{{ $appointment->id }}">
                        {{ $appointment->id }} - {{ $appointment->patient->first_name }}
                        {{ $appointment->patient->last_name }}
                        ({{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }})
                    </option>
                @endforeach
            </flux:select>

            <flux:input label="Amount *" type="number" step="0.01" wire:model="amount" />
            <flux:input label="GCash Number *" wire:model="gcash_number" />
            <flux:input label="GCash Reference Number *" wire:model="gcash_reference_number" />

            <!-- Receipt -->
            <div class="col-span-1 md:col-span-2">
                <flux:heading>Upload Receipt</flux:heading>
                <flux:input type="file" wire:model="receipt_image" />

                @if ($receipt_image)
                    <div class="flex items-center gap-2 mt-2 bg-gray-100 px-3 py-2 rounded text-sm w-fit">
                        <span>{{ $receipt_image->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeReceipt"
                            class="text-red-500 hover:text-red-700 font-bold">✕</button>
                    </div>
                    <div class="mt-2">
                        <img src="{{ $receipt_image->temporaryUrl() }}" class="w-32 rounded shadow" />
                    </div>
                @endif
            </div>

            <flux:select label="Payment Status" wire:model="payment_status">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="declined">Declined</option>
            </flux:select>

        </div>

        <div class="flex pt-6">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save Payment</flux:button>
        </div>

    </form>
</flux:modal>
