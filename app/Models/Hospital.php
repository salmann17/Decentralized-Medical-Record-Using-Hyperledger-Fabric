<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $table = 'hospitals';
    protected $primaryKey = 'hospital_id';
    public $timestamps = false;

    protected $fillable = ['name','address','type'];

    public function doctors()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctor_hospital',
            'hospital_id',
            'doctor_id'
        )->withTimestamps();
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'hospital_id', 'hospital_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'hospital_id', 'idusers');
    }
}