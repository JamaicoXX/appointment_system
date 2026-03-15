<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patients;
use App\Models\Payments;

class Appointments extends Model
{
    protected $table = 'appointments';
    protected $guarded = [];
    protected $casts = [
        'services_availed' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function payment()
    {
        return $this->hasOne(Payments::class, 'appointment_id');
    }
}
