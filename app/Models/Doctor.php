<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $table = 'doctors';
    protected $primaryKey = 'iddoctor';

    protected $fillable = ['license_number', 'spesialization'];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'iddoctor', 'idusers');
    }

    public function admins()
    {
        return $this->belongsToMany(
            Admin::class,
            'doctors_admins',
            'doctor_id',
            'admin_id',
            'iddoctor',
            'idadmin'
        )->withTimestamps()->withPivot('deleted_at')->wherePivot('deleted_at', null);
    }

    public function adminsWithTrashed()
    {
        return $this->belongsToMany(
            Admin::class,
            'doctors_admins',
            'doctor_id',
            'admin_id',
            'iddoctor',
            'idadmin'
        )->withTimestamps()->withPivot('deleted_at');
    }

    public function adminsOnlyTrashed()
    {
        return $this->belongsToMany(
            Admin::class,
            'doctors_admins',
            'doctor_id',
            'admin_id',
            'iddoctor',
            'idadmin'
        )->withTimestamps()->withPivot('deleted_at')->wherePivotNotNull('deleted_at');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'iddoctor');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'doctor_id', 'iddoctor');
    }

}