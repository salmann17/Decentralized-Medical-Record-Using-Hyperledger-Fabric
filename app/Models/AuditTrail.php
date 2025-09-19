<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audit_trail';
    protected $primaryKey = 'audit_id';
    public $timestamps = false;

    protected $fillable = ['users_id','patient_id','medicalrecord_id','action','timestamp','blockchain_hash'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'idusers');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medicalrecord_id', 'medicalrecord_id');
    }
}
