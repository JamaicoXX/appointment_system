<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecords extends Model
{
    protected $table = 'medical_records';
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }
}
