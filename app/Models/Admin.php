<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;

    protected $table = 'admins';
    protected $primaryKey = 'idadmin';

    protected $fillable = [
        'name',
        'address',
        'type'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'idadmin', 'idusers');
    }

    public function doctors()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctors_admins',
            'admin_id',
            'doctor_id',
            'idadmin',
            'iddoctor'
        )->withTimestamps()->withPivot('deleted_at')->wherePivot('deleted_at', null);
    }

    public function doctorsWithTrashed()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctors_admins',
            'admin_id',
            'doctor_id',
            'idadmin',
            'iddoctor'
        )->withTimestamps()->withPivot('deleted_at');
    }

    public function doctorsOnlyTrashed()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctors_admins',
            'admin_id',
            'doctor_id',
            'idadmin',
            'iddoctor'
        )->withTimestamps()->withPivot('deleted_at')->wherePivotNotNull('deleted_at');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'admin_id', 'idadmin');
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class, 'admin_id', 'idadmin');
    }
}