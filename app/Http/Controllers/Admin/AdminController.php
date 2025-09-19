<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Hospital;
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
        
        // Get hospital data for this admin
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        // Get statistics for this hospital
        $doctorsCount = Doctor::where('hospital_id', $user->idusers)->count();
        $patientsCount = Patient::whereHas('medicalRecords', function($query) use ($user) {
            $query->where('hospital_id', $user->idusers);
        })->count();
        $medicalRecordsCount = MedicalRecord::where('hospital_id', $user->idusers)->count();
        
        // Get recent doctors
        $recentDoctors = Doctor::where('hospital_id', $user->idusers)
            ->with('user')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'hospital',
            'doctorsCount',
            'patientsCount', 
            'medicalRecordsCount',
            'recentDoctors'
        ));
    }
}