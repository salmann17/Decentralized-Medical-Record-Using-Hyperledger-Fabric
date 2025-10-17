<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use SoftDeletes;

    protected $table = 'medical_records';
    protected $primaryKey = 'idmedicalrecord';

    protected $fillable = [
        'patient_id','doctor_id','admin_id','visit_date',
        'blood_pressure','heart_rate','temperature','respiratory_rate',
        'chief_complaint','history_present_illness','physical_examination',
        'diagnosis_code','diagnosis_desc','treatment','notes',
        'status','version', 'previous_id'
    ];

    protected $dates = ['deleted_at'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'idpatient');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'iddoctor');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'idadmin');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class, 'medicalrecord_id', 'idmedicalrecord')
            ->latestOfMany('idprescription');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'medicalrecord_id', 'idmedicalrecord');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'medicalrecord_id', 'idmedicalrecord');
    }
}

