<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriptionItem extends Model
{
    use SoftDeletes;

    protected $table = 'prescription_items';
    protected $primaryKey = 'iditem';

    protected $fillable = [
        'prescription_id',
        'name',
        'dosage',
        'frequency',
        'duration',
        'notes'
    ];

    protected $dates = ['deleted_at'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id', 'idprescription');
    }
}