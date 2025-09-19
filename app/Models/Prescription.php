<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescriptions';
    protected $primaryKey = 'prescription_id';
    public $timestamps = false;

    protected $fillable = ['item','dosage','frequency','duration'];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'prescription_id', 'prescription_id');
    }
}

