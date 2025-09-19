<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\AccessRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    /**
     * Show the doctor dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_id', $user->idusers)->with('hospital')->first();
        
        // Get access requests for this doctor
        $accessRequests = AccessRequest::where('doctor_id', $doctor->doctor_id)
            ->with(['patient.user'])
            ->orderBy('requested_at', 'desc')
            ->take(5)
            ->get();
        
        // Get patients this doctor has access to
        $myPatients = Patient::whereHas('accessRequests', function($query) use ($doctor) {
            $query->where('doctor_id', $doctor->doctor_id)
                  ->where('status', 'approved');
        })->with('user')->take(5)->get();

        return view('doctor.dashboard', compact(
            'doctor',
            'accessRequests',
            'myPatients'
        ));
    }
}