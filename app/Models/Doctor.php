<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';
    public $timestamps = false;

    protected $fillable = ['license_number', 'specialization'];

    public function user()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'idusers');
    }

    public function hospitals()
    {
        return $this->belongsToMany(
            Hospital::class,
            'doctor_hospital',
            'doctor_id',
            'hospital_id'
        )->withTimestamps();
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'doctor_id');
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class, 'doctor_id', 'doctor_id');
    }
}