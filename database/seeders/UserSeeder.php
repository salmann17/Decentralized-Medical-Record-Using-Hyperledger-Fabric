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
            ['name' => 'RS Jakarta Medical Center', 'address' => 'Jl. Sudirman No. 1, Jakarta', 'type' => 'Rumah Sakit'],
            ['name' => 'RS Siloam Hospitals', 'address' => 'Jl. Gatot Subroto No. 2, Jakarta', 'type' => 'Rumah Sakit'],
            ['name' => 'RSUP Dr. Sardjito', 'address' => 'Jl. Kesehatan No. 1, Yogyakarta', 'type' => 'Rumah Sakit'],
        ];

        $hospitalModels = [];
        foreach ($hospitals as $i => $h) {
            $user = User::create([
                'name' => $h['name'],
                'email' => 'admin' . ($i + 1) . '@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]);

            $hospital = Hospital::create([
                'hospital_id' => $user->idusers,
                'name' => $h['name'],
                'address' => $h['address'],
                'type' => $h['type']
            ]);

            $hospitalModels[] = $hospital;
        }

        $doctors = [
            ['name' => 'Dr. Ahmad Sutanto', 'email' => 'doctor1@mail.com', 'license_number' => '12345', 'specialization' => 'Kardiologi'],
            ['name' => 'Dr. Sari Indah', 'email' => 'doctor2@mail.com', 'license_number' => '12346', 'specialization' => 'Neurologi'],
            ['name' => 'Dr. Budi Prasetyo', 'email' => 'doctor3@mail.com', 'license_number' => '12347', 'specialization' => 'Orthopedi'],
            ['name' => 'Dr. Maya Sari', 'email' => 'doctor4@mail.com', 'license_number' => '12348', 'specialization' => 'Pediatri'],
        ];

        foreach ($doctors as $i => $d) {
            $user = User::create([
                'name' => $d['name'],
                'email' => $d['email'],
                'password' => Hash::make('password123'),
                'role' => 'dokter'
            ]);

            $doctor = Doctor::create([
                'doctor_id' => $user->idusers,
                'license_number' => $d['license_number'],
                'specialization' => $d['specialization']
            ]);

            if ($i == 0) {
                $doctor->hospitals()->attach([$hospitalModels[0]->hospital_id, $hospitalModels[1]->hospital_id]);
            } elseif ($i == 1) {
                $doctor->hospitals()->attach([$hospitalModels[0]->hospital_id, $hospitalModels[2]->hospital_id]);
            } elseif ($i == 2) {
                $doctor->hospitals()->attach([
                    $hospitalModels[0]->hospital_id, 
                    $hospitalModels[1]->hospital_id, 
                    $hospitalModels[2]->hospital_id
                ]);
            } else {
                $doctor->hospitals()->attach([$hospitalModels[1]->hospital_id]);
            }
        }

        $patients = [
            ['name' => 'John Doe', 'email' => 'patient1@mail.com', 'nik' => '1234567890123456', 'birthdate' => '1990-01-01', 'gender' => 'male', 'blood' => 'A+', 'address' => 'Jl. Merdeka No. 1'],
            ['name' => 'Jane Smith', 'email' => 'patient2@mail.com', 'nik' => '1234567890123457', 'birthdate' => '1985-05-15', 'gender' => 'female', 'blood' => 'B+', 'address' => 'Jl. Sudirman No. 2'],
            ['name' => 'Bob Johnson', 'email' => 'patient3@mail.com', 'nik' => '1234567890123458', 'birthdate' => '1992-08-20', 'gender' => 'male', 'blood' => 'O+', 'address' => 'Jl. Thamrin No. 3'],
        ];

        foreach ($patients as $i => $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('password123'),
                'role' => 'pasien'
            ]);

            Patient::create([
                'patient_id' => $user->idusers,
                'nik' => $p['nik'],
                'birthdate' => $p['birthdate'],
                'gender' => $p['gender'],
                'blood' => $p['blood'],
                'address' => $p['address']
            ]);
        }
    }
}