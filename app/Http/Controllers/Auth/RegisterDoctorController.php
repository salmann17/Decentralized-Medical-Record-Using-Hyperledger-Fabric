<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterDoctorController extends Controller
{
    /**
     * Show the doctor registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register-doctor');
    }

    /**
     * Handle doctor registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:135',
            'email' => 'required|string|email|max:135|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'license_number' => 'required|numeric|unique:doctors,license_number',
            'spesialization' => 'required|string|max:45',
        ], [
            'license_number.unique' => 'Nomor lisensi ini sudah terdaftar dalam sistem. Satu nomor lisensi hanya dapat mendaftar satu akun.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login jika Anda sudah memiliki akun.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Doctor::create([
                'iddoctor' => $user->idusers,
                'license_number' => $request->license_number,
                'spesialization' => $request->spesialization,
            ]);

            Auth::login($user);

            return redirect()->route('doctor.dashboard')->with('success', 'Registrasi dokter berhasil! Selamat datang.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])->withInput();
        }
    }
}
