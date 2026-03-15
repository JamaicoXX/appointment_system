<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Payments;
use App\Models\Appointments;

new class extends Component {
    use WithFileUploads;

    public $paymentId;
    public $payment;

    public $appointment_id;
    public $amount;
    public $gcash_number;
    public $gcash_reference_number;
    public $receipt_image; // new upload
    public $existing_receipt; // existing file path
    public $payment_status = 'pending';

    public $appointments;

    protected $listeners = [
        'editPaymentModal' => 'openModal',
    ];

    public function mount()
    {
        $this->appointments = Appointments::with('patient')->get();
    }

    public function openModal($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->loadPayment();
        $this->modal('edit-payment-modal')->show();
    }

    public function loadPayment()
    {
        $this->payment = Payments::findOrFail($this->paymentId);

        $this->appointment_id = $this->payment->appointment_id;
        $this->amount = $this->payment->amount;
        $this->gcash_number = $this->payment->gcash_number;
        $this->gcash_reference_number = $this->payment->gcash_reference_number;
        $this->payment_status = $this->payment->payment_status;
        $this->existing_receipt = $this->payment->receipt_image;
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
            'receipt_image' => 'nullable|file|max:2048',
            'payment_status' => 'required|in:pending,approved,rejected,declined',
        ]);

        $receiptPath = $this->existing_receipt;

        if ($this->receipt_image) {
            $receiptPath = $this->receipt_image->store('payments', 'public');
        }

        $this->payment->update([
            'appointment_id' => $this->appointment_id,
            'amount' => $this->amount,
            'gcash_number' => $this->gcash_number,
            'gcash_reference_number' => $this->gcash_reference_number,
            'receipt_image' => $receiptPath,
            'payment_status' => $this->payment_status,
        ]);

        $this->modal('edit-payment-modal')->close();

        // Dispatch events for SPA updates
        $this->dispatch('refreshPayments');
        // $this->dispatch('showAlertModal', ['message' => 'Payment updated successfully.']);
    }
};
?>

<flux:modal name="edit-payment-modal" class="md:w-[80%] !bg-sky-50" flyout position="left">
    <form wire:submit.prevent="save" class="space-y-6">

        <div>
            <flux:heading size="lg">Edit Payment</flux:heading>
            <flux:text class="mt-2">
                Update payment details for the selected appointment.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <flux:select label="Appointment *" wire:model="appointment_id">
                <option value="">Select Appointment</option>
                @foreach ($appointments as $appt)
                    <option value="{{ $appt->id }}">
                        {{ $appt->patient->first_name }} {{ $appt->patient->last_name }} -
                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
                        ({{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }})
                    </option>
                @endforeach
            </flux:select>

            <flux:input label="Amount *" type="number" step="0.01" wire:model="amount" />

            <flux:input label="GCash Number" wire:model="gcash_number" />
            <flux:input label="GCash Reference Number" wire:model="gcash_reference_number" />

            <flux:select label="Payment Status" wire:model="payment_status">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="declined">Declined</option>
            </flux:select>

            <!-- Receipt -->
            <div class="col-span-1 md:col-span-3">
                <flux:heading class="flex items-center gap-2 text-sm font-medium">Receipt</flux:heading>
                <flux:input type="file" wire:model="receipt_image" />

                <!-- New upload preview -->
                @if ($receipt_image)
                    <div class="flex items-center gap-2 mt-2 bg-gray-100 px-3 py-2 rounded text-sm w-fit">
                        <span>{{ $receipt_image->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeReceipt"
                            class="text-red-500 font-bold hover:text-red-700">✕</button>
                    </div>
                    <div class="mt-2">
                        <img src="{{ $receipt_image->temporaryUrl() }}" class="w-32 rounded shadow" />
                    </div>
                @endif

                <!-- Existing receipt -->
                @if ($existing_receipt && !$receipt_image)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $existing_receipt) }}" target="_blank"
                            class="text-blue-600 underline">
                            {{ basename($existing_receipt) }}
                        </a>
                        <img src="{{ asset('storage/' . $existing_receipt) }}" class="w-32 rounded shadow mt-1" />
                    </div>
                @endif
            </div>

        </div>

        <div class="flex pt-6">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save Changes</flux:button>
        </div>

    </form>
</flux:modal>
