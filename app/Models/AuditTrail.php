<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditTrail extends Model
{
    use SoftDeletes;

    protected $table = 'audit_trails';
    protected $primaryKey = 'idaudit';

    protected $fillable = ['patient_id','doctor_id','medicalrecord_id','action','timestamp','blockchain_hash'];

    protected $dates = ['deleted_at'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'idpatient');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'iddoctor');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medicalrecord_id', 'idmedicalrecord');
    }

    // Legacy - for backward compatibility with existing code
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'idusers');
    }
}
