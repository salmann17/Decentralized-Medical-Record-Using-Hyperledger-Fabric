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
        'name', 'email', 'password',
    ];

    protected $hidden = ['password'];

    // Relasi
    public function patient()
    {
        return $this->hasOne(Patient::class, 'idpatient', 'idusers');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'iddoctor', 'idusers');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'idadmin', 'idusers');
    }

    // Helper methods untuk mengecek role
    public function isPatient()
    {
        return $this->patient()->exists();
    }

    public function isDoctor()
    {
        return $this->doctor()->exists();
    }

    public function isAdmin()
    {
        return $this->admin()->exists();
    }

    public function getRole()
    {
        if ($this->isAdmin()) return 'admin';
        if ($this->isDoctor()) return 'doctor';
        if ($this->isPatient()) return 'patient';
        return null;
    }
}
