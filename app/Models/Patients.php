<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Appointments;
use App\Models\User;

class Patients extends Model
{
    protected $table = 'patients';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function appointments()
    // {
    //     return $this->hasMany(Appointments::class);
    // }
}
