<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    public $timestamps = false;

    protected $fillable = [
        'nik', 'birthdate', 'gender', 'blood', 'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'patient_id', 'idusers');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id', 'patient_id');
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class, 'patient_id', 'patient_id');
    }
}
