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
        return $this->hasMany(Doctor::class, 'hospital_id', 'hospital_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'hospital_id', 'hospital_id');
    }
}

