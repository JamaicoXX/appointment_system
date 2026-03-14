<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patients;

class Appointments extends Model
{
    protected $table = 'appointments';
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }
}
