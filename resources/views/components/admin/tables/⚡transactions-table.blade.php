<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payments;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshPayments' => 'refreshTransactions'];

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function payments()
    {
        return Payments::with('appointment.patient', 'reviewer')
            ->where('archived', 0)
            ->when(
                str_contains($this->sortBy, 'patient'),
                function ($query) {
                    // Sort by patient first_name if needed
                    $query->join('appointments', 'payments.appointment_id', '=', 'appointments.id')->join('patients', 'appointments.patient_id', '=', 'patients.id')->orderBy('patients.first_name', $this->sortDirection)->select('payments.*');
                },
                function ($query) {
                    $query->orderBy($this->sortBy, $this->sortDirection);
                },
            )
            ->paginate(5);
    }

    public function refreshTransactions() {}
};
?>

<div class="bg-white dark:bg-gray-800 p-6 border border-gray-100 shadow-md rounded-md">
    <livewire:admin.modals.edit-payment-modal wire:key="edit-payment-modal" />
    <livewire:admin.modals.archive-payment-modal wire:key="archive-payment-modal" />
    <flux:table :paginate="$this->payments">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                wire:click="sort('id')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'patient_name'" :direction="$sortDirection"
                wire:click="sort('patient_name')">Patient Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'appointment_date'" :direction="$sortDirection"
                wire:click="sort('appointment_date')">Appointment Date</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection"
                wire:click="sort('amount')">Amount</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'payment_status'" :direction="$sortDirection"
                wire:click="sort('payment_status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'reviewed_by'" :direction="$sortDirection"
                wire:click="sort('reviewed_by')">Reviewed By</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->payments as $payment)
                <flux:table.row :key="$payment->id">
                    <flux:table.cell>{{ $payment->id }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $payment->appointment->patient->first_name ?? '' }}
                        {{ $payment->appointment->patient->middle_name ?? '' }}
                        {{ $payment->appointment->patient->last_name ?? '' }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $payment->appointment->appointment_date ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ number_format($payment->amount, 2) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm"
                            color="{{ $payment->payment_status === 'approved' ? 'green' : ($payment->payment_status === 'rejected' ? 'red' : 'yellow') }}">
                            {{ ucfirst($payment->payment_status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $payment->reviewer->name ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ \Carbon\Carbon::parse($payment->created_at)->format('F d, Y h:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon:trailing="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item icon="pencil"
                                    wire:click="$dispatch('editPaymentModal', { paymentId: {{ $payment->id }} })">
                                    Edit</flux:menu.item>
                                <flux:menu.item variant="danger" icon="trash"
                                    wire:click="$dispatch('archivePaymentModal', { paymentId: {{ $payment->id }} })">
                                    Archive</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
