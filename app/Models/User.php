<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idusers';

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = ['password'];

    // Relasi
    public function patient()
    {
        return $this->hasOne(Patient::class, 'patient_id', 'idusers');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'doctor_id', 'idusers');
    }
}
