<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;

class UserSeeder extends Seeder
{
    public function run()
    {
        $hospitals = [
            ['name' => 'RS Harapan Sehat', 'address' => 'Jl. Merdeka No.1', 'type' => 'Rumah Sakit'],
            ['name' => 'Klinik Pratama', 'address' => 'Jl. Melati No.2', 'type' => 'Klinik'],
            ['name' => 'Puskesmas Kota', 'address' => 'Jl. Kenanga No.3', 'type' => 'Puskesmas'],
        ];

        foreach ($hospitals as $i => $h) {
            $user = User::create([
                'name' => $h['name'],
                'email' => 'hospital' . ($i+1) . '@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);

            Hospital::create([
                'hospital_id' => $user->idusers,
                'name' => $h['name'],
                'address' => $h['address'],
                'type' => $h['type'],
            ]);
        }

        $doctors = [
            ['name' => 'Dr. Andi', 'license_number' => 12345, 'specialization' => 'Umum', 'hospital_id' => 1],
            ['name' => 'Dr. Budi', 'license_number' => 67890, 'specialization' => 'Anak', 'hospital_id' => 2],
            ['name' => 'Dr. Clara', 'license_number' => 54321, 'specialization' => 'Bedah', 'hospital_id' => 3],
        ];

        foreach ($doctors as $i => $d) {
            $user = User::create([
                'name' => $d['name'],
                'email' => 'doctor' . ($i+1) . '@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
            ]);

            Doctor::create([
                'doctor_id' => $user->idusers,
                'license_number' => $d['license_number'],
                'specialization' => $d['specialization'],
                'hospital_id' => $d['hospital_id'],
            ]);
        }

        $patients = [
            ['name' => 'Ahmad', 'nik' => 111111, 'birthdate' => '1990-01-01', 'gender' => 'male', 'blood' => 'A+', 'address' => 'Jl. Mawar No.1'],
            ['name' => 'Siti', 'nik' => 222222, 'birthdate' => '1992-02-02', 'gender' => 'female', 'blood' => 'B+', 'address' => 'Jl. Anggrek No.2'],
            ['name' => 'Joko', 'nik' => 333333, 'birthdate' => '1995-03-03', 'gender' => 'male', 'blood' => 'O-', 'address' => 'Jl. Melati No.3'],
        ];

        foreach ($patients as $i => $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => 'patient' . ($i+1) . '@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'pasien',
            ]);

            Patient::create([
                'patient_id' => $user->idusers,
                'nik' => $p['nik'],
                'birthdate' => $p['birthdate'],
                'gender' => $p['gender'],
                'blood' => $p['blood'],
                'address' => $p['address'],
            ]);
        }
    }
}
