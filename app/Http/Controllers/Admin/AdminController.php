<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $doctorCount = $hospital ? $hospital->doctors()->count() : 0;
        $patientCount = Patient::count(); 
        $recordCount = $hospital ? $hospital->medicalRecords()->count() : 0;
        
        $recentDoctors = $hospital ? $hospital->doctors()->with('user')->latest('doctor_hospital.created_at')->take(5)->get() : collect();

        return view('admin.dashboard', compact(
            'hospital',
            'doctorCount',
            'patientCount', 
            'recordCount',
            'recentDoctors'
        ));
    }

    /**
     * Show doctors management page
     */
    public function doctors()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $hospitalDoctors = $hospital ? $hospital->doctors()->with('user')->get() : collect();
        
        $availableDoctors = Doctor::with('user')
            ->whereDoesntHave('hospitals', function($query) use ($hospital) {
                $query->where('hospital_id', $hospital->hospital_id);
            })
            ->get();

        return view('admin.doctors', compact('hospital', 'hospitalDoctors', 'availableDoctors'));
    }

    /**
     * Assign doctor to hospital
     */
    public function assignDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,doctor_id'
        ]);

        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        $doctor = Doctor::find($request->doctor_id);

        // Check if already assigned
        if (!$hospital->doctors()->where('doctor_id', $doctor->doctor_id)->exists()) {
            $hospital->doctors()->attach($doctor->doctor_id);
            return redirect()->back()->with('success', 'Dokter berhasil ditambahkan ke rumah sakit.');
        }

        return redirect()->back()->with('error', 'Dokter sudah terdaftar di rumah sakit ini.');
    }

    /**
     * Remove doctor from hospital
     */
    public function removeDoctor(Request $request, $doctorId)
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $hospital->doctors()->detach($doctorId);
        
        return redirect()->back()->with('success', 'Dokter berhasil dihapus dari rumah sakit.');
    }
}