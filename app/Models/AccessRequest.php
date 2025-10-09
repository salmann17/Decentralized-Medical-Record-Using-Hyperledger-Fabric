<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessRequest extends Model
{
    use SoftDeletes;

    protected $table = 'access_request';
    protected $primaryKey = 'idrequest';

    protected $fillable = ['patient_id','admin_id','status','requested_at','responded_at'];

    protected $dates = ['deleted_at'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'idpatient');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'idadmin');
    }
}

