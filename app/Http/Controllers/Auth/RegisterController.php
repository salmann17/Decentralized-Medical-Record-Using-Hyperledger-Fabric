<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:135',
            'email' => 'required|string|email|max:135|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'nik' => 'required|numeric|digits:16|unique:patients',
            'birthdate' => 'required|date',
            'gender' => 'required|in:male,female',
            'blood' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address' => 'required|string|max:135',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create user without role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create patient profile
            Patient::create([
                'idpatient' => $user->idusers,
                'nik' => $request->nik,
                'birthdate' => $request->birthdate,
                'gender' => $request->gender,
                'blood' => $request->blood,
                'address' => $request->address,
            ]);

            // Auto-login the user
            Auth::login($user);

            return redirect()->route('patient.dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])->withInput();
        }
    }
}