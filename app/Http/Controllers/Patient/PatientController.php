<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AccessRequest;
use App\Models\MedicalRecord;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Show the patient dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        // Get incoming access requests
        $incomingRequests = AccessRequest::where('patient_id', $patient->patient_id)
            ->where('status', 'pending')
            ->with(['doctor.user', 'doctor.hospital'])
            ->orderBy('requested_at', 'desc')
            ->take(5)
            ->get();
        
        // Get recent audit trail (access history)
        $auditTrail = AuditTrail::where('patient_id', $patient->patient_id)
            ->with('user')
            ->orderBy('timestamp', 'desc')
            ->take(5)
            ->get();
        
        // Get medical records
        $medicalRecords = MedicalRecord::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital'])
            ->orderBy('visit_date', 'desc')
            ->take(5)
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'incomingRequests',
            'auditTrail',
            'medicalRecords'
        ));
    }
}