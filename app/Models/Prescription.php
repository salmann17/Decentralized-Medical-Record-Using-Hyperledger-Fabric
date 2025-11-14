<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use SoftDeletes;

    protected $table = 'prescriptions';
    protected $primaryKey = 'idprescription';

    protected $fillable = [
        'medicalrecord_id',
        'type',
        'name',
        'dosage',
        'frequency',
        'duration',
        'description'
    ];

    protected $dates = ['deleted_at'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medicalrecord_id', 'idmedicalrecord');
    }

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id', 'idprescription');
    }

    // Legacy - for backward compatibility
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'prescription_id', 'prescription_id');
    }
}
