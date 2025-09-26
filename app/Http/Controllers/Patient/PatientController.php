<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AccessRequest;
use App\Models\MedicalRecord;
use App\Models\AuditTrail;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Show the patient dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        // Get statistics
        $totalRecords = MedicalRecord::where('patient_id', $patient->patient_id)->count();
        $pendingRequests = AccessRequest::where('patient_id', $patient->patient_id)
            ->where('status', 'pending')->count();
        $activeDoctors = AccessRequest::where('patient_id', $patient->patient_id)
            ->where('status', 'approved')->distinct('doctor_id')->count();
        
        // Get recent access requests (pending)
        $recentRequests = AccessRequest::where('patient_id', $patient->patient_id)
            ->where('status', 'pending')
            ->with(['doctor.user', 'doctor.hospitals'])
            ->orderBy('requested_at', 'desc')
            ->take(5)
            ->get();
        
        // Get recent medical records
        $recentRecords = MedicalRecord::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital'])
            ->orderBy('visit_date', 'desc')
            ->take(5)
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'totalRecords',
            'pendingRequests', 
            'activeDoctors',
            'recentRequests',
            'recentRecords'
        ));
    }

    /**
     * Show patient medical records
     */
    public function records()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $records = MedicalRecord::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital'])
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('patient.records.index', compact('patient', 'records'));
    }

    /**
     * Show detailed medical record
     */
    public function recordDetail($id)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $record = MedicalRecord::where('medical_record_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital'])
            ->firstOrFail();

        // Log access for audit trail
        AuditTrail::create([
            'user_id' => $user->idusers,
            'medical_record_id' => $record->medical_record_id,
            'action' => 'view',
            'access_time' => now(),
            'ip_address' => request()->ip(),
            'blockchain_hash' => 'dummy_hash_' . uniqid() // Placeholder for blockchain
        ]);

        return view('patient.records.detail', compact('patient', 'record'));
    }

    /**
     * Show access requests from doctors
     */
    public function accessRequests()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $requests = AccessRequest::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'doctor.hospitals'])
            ->orderBy('requested_at', 'desc')
            ->paginate(15);

        return view('patient.access-requests.index', compact('patient', 'requests'));
    }

    /**
     * Approve access request
     */
    public function approveAccess($id)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $request = AccessRequest::where('request_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update([
            'status' => 'approved',
            'response_date' => now()
        ]);

        // TODO: Add blockchain transaction here
        // BlockchainService::approveAccess($request);

        return redirect()->back()->with('success', 'Akses dokter berhasil disetujui.');
    }

    /**
     * Reject access request  
     */
    public function rejectAccess($id)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $request = AccessRequest::where('request_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update([
            'status' => 'rejected',
            'response_date' => now()
        ]);

        // TODO: Add blockchain transaction here
        // BlockchainService::rejectAccess($request);

        return redirect()->back()->with('success', 'Akses dokter berhasil ditolak.');
    }

    /**
     * Show doctors with active access
     */
    public function activeDoctors()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $activeDoctors = AccessRequest::where('patient_id', $patient->patient_id)
            ->where('status', 'approved')
            ->with(['doctor.user', 'doctor.hospitals'])
            ->orderBy('response_date', 'desc')
            ->paginate(10);

        return view('patient.active-doctors.index', compact('patient', 'activeDoctors'));
    }

    /**
     * Revoke doctor access
     */
    public function revokeAccess($id)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $request = AccessRequest::where('request_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $request->update([
            'status' => 'revoked',
            'revoked_at' => now()
        ]);

        // TODO: Add blockchain transaction here
        // BlockchainService::revokeAccess($request);

        return redirect()->back()->with('success', 'Akses dokter berhasil dicabut.');
    }

    /**
     * Show audit trail
     */
    public function auditTrail()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $auditLogs = AuditTrail::whereHas('medicalRecord', function($query) use ($patient) {
                $query->where('patient_id', $patient->patient_id);
            })
            ->with(['user', 'medicalRecord.doctor.user', 'medicalRecord.hospital'])
            ->orderBy('access_time', 'desc')
            ->paginate(20);

        return view('patient.audit-trail.index', compact('patient', 'auditLogs'));
    }

    /**
     * Show patient settings
     */
    public function settings()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();

        return view('patient.settings.index', compact('patient', 'user'));
    }

    /**
     * Update patient settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id() . ',idusers',
            'nik' => 'required|string|max:16',
            'birthdate' => 'required|date',
            'gender' => 'required|in:male,female',
            'blood' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address' => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();

        // Update user data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $userData['password'] = Hash::make($request->new_password);
        }

        $user->update($userData);

        // Update patient data
        $patient->update([
            'nik' => $request->nik,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'blood' => $request->blood,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}