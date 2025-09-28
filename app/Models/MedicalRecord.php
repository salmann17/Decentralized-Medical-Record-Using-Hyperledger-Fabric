<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';
    protected $primaryKey = 'medicalrecord_id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id','hospital_id','doctor_id','visit_date',
        'blood_pressure','heart_rate','temperature','respiratory_rate',
        'chief_complaint','history_present_illness','physical_examination',
        'diagnosis_code','diagnosis_desc','treatment','notes',
        'status','prescription_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'hospital_id');
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id', 'prescription_id');
    }
}

