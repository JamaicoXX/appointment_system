<?php

use Livewire\Component;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

new class extends Component {
    public $scheduleId;

    public $day_of_week;
    public $start_time;
    public $end_time;
    public $slot_limit;
    public $is_active = true;

    protected $listeners = [
        'editDoctorScheduleModal' => 'openModal',
    ];

    public function openModal($scheduleId)
    {
        $this->scheduleId = $scheduleId;
        $this->loadSchedule();
        $this->modal('edit-business-day-modal')->show();
    }

    public function loadSchedule()
    {
        $schedule = DoctorSchedule::findOrFail($this->scheduleId);

        $this->day_of_week = $schedule->day_of_week;
        $this->start_time = $schedule->start_time;
        $this->end_time = $schedule->end_time;
        $this->slot_limit = $schedule->slot_limit;
        $this->is_active = $schedule->is_active;
    }

    public function save()
    {
        $this->validate([
            'day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_limit' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            $this->addError('end_time', 'End time must be after start time.');
            return;
        }

        $schedule = DoctorSchedule::findOrFail($this->scheduleId);

        $schedule->update([
            'day_of_week' => $this->day_of_week,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'slot_limit' => $this->slot_limit,
            'is_active' => $this->is_active,
        ]);

        $this->modal('edit-business-day-modal')->close();
        $this->dispatch('showAlertModal');
        $this->dispatch('refreshSchedule');
    }
};
?>

<flux:modal name="edit-business-day-modal" class="md:w-[50%] !bg-sky-50" flyout position="left">

    <form wire:submit.prevent="save" class="space-y-6">

        <div>
            <flux:heading size="lg">Edit Business Day</flux:heading>
            <flux:text class="mt-2">
                Update the schedule details for this business day.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <flux:select label="Day of Week" wire:model="day_of_week" disabled>
                <option value="">Select a day</option>
                <option value="Sunday">Sunday</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
            </flux:select>

            <flux:input label="Start Time" type="time" wire:model="start_time" />

            <flux:input label="End Time" type="time" wire:model="end_time" />

            <flux:input label="Slot Limit" type="number" min="1" wire:model="slot_limit" />

            <flux:select label="Active" wire:model="is_active">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </flux:select>

        </div>

        <div class="flex pt-6">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save Changes</flux:button>
        </div>

    </form>

</flux:modal>
