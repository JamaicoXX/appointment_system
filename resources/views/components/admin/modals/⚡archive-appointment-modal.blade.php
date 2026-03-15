<?php

use Livewire\Component;
use App\Models\Appointments;

new class extends Component {
    public $appointmentId;

    // Patient info
    public $first_name;
    public $last_name;

    public $appointment;
    public $patient;
    public $payment;

    protected $listeners = ['archiveAppointmentModal' => 'openArchiveModal'];

    public function openArchiveModal($appointmentId)
    {
        $this->appointmentId = $appointmentId;

        $this->appointment = Appointments::with(['patient'])->findOrFail($appointmentId);
        $this->patient = $this->appointment->patient;
        $this->payment = $this->appointment->payment;
        $this->first_name = $this->first_name;
        $this->last_name = $this->last_name;
        $this->modal('archive-appointment-modal-admin')->show();
    }

    public function archive()
    {
        $this->appointment->update([
            'archived' => 1,
        ]);

        $this->patient->update([
            'archived' => 1,
        ]);

        $this->payment->update([
            'archived' => 1,
        ]);

        $this->modal('archive-appointment-modal-admin')->close();
        $this->reset();
        $this->dispatch('refreshAppointment');
    }
};
?>

<flux:modal name="archive-appointment-modal-admin" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Archive appointment?</flux:heading>
            <flux:text class="mt-2">
                You're about to archive the appointment of {{ $first_name . ' ' . $last_name }}.<br>
                This action cannot be reversed.
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="danger" wire:click="archive">Archive appointment</flux:button>
        </div>
    </div>
</flux:modal>
