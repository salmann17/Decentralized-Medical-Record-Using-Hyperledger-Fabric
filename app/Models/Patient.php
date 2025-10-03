<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $table = 'patients';
    protected $primaryKey = 'idpatient';

    protected $fillable = [
        'nik', 'birthdate', 'gender', 'blood', 'address'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'idpatient', 'idusers');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id', 'idpatient');
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class, 'patient_id', 'idpatient');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'patient_id', 'idpatient');
    }
}
