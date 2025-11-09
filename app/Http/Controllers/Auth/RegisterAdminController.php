<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterAdminController extends Controller
{
    /**
     * Show the admin registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register-admin');
    }

    /**
     * Handle admin registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:135',
            'email' => 'required|string|email|max:135|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'admin_name' => 'required|string|max:135|unique:admins,name',
            'address' => 'required|string|max:135',
            'type' => 'required|string|in:Rumah Sakit,Klinik,Puskesmas',
        ], [
            'admin_name.unique' => 'Nama rumah sakit/klinik ini sudah terdaftar dalam sistem. Silakan gunakan nama lain.',
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

            Admin::create([
                'idadmin' => $user->idusers,
                'name' => $request->admin_name,
                'address' => $request->address,
                'type' => $request->type,
            ]);

            Auth::login($user);

            return redirect()->route('admin.dashboard')->with('success', 'Registrasi admin berhasil! Selamat datang.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])->withInput();
        }
    }
}
