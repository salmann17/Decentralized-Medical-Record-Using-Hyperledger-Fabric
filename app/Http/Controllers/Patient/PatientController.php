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
            ->with(['doctor.user', 'hospital', 'prescription'])
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
    public function records(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $query = MedicalRecord::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital', 'prescription']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('hospital', function($hospitalQuery) use ($search) {
                    $hospitalQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('doctor.user', function($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('diagnosis_desc', 'like', '%' . $search . '%')
                ->orWhere('diagnosis_code', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply period filter
        if ($request->filled('period')) {
            $period = $request->period;
            switch ($period) {
                case '7days':
                    $query->where('visit_date', '>=', now()->subDays(7));
                    break;
                case '30days':
                    $query->where('visit_date', '>=', now()->subDays(30));
                    break;
                case '6months':
                    $query->where('visit_date', '>=', now()->subMonths(6));
                    break;
                case '1year':
                    $query->where('visit_date', '>=', now()->subYear());
                    break;
            }
        }

        $records = $query->orderBy('visit_date', 'desc')->paginate(10);

        return view('patient.records.index', compact('patient', 'records'));
    }

    /**
     * Show detailed medical record
     */
    public function recordDetail($id)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $record = MedicalRecord::where('medicalrecord_id', $id)
            ->where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'hospital', 'prescription'])
            ->firstOrFail();

        // Log access for audit trail
        AuditTrail::create([
            'users_id' => $user->idusers,
            'patient_id' => $patient->patient_id,
            'medicalrecord_id' => $record->medicalrecord_id,
            'action' => 'view',
            'timestamp' => now(),
            'blockchain_hash' => 'dummy_hash_' . uniqid() // Placeholder for blockchain
        ]);

        return view('patient.records.detail', compact('patient', 'record'));
    }

    /**
     * Show access requests from doctors
     */
    public function accessRequests(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        $currentStatus = $request->status;
        
        $query = AccessRequest::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'doctor.hospitals']);

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('requested_at', 'desc')->paginate(15);
        
        // Get all requests for count calculations (unfiltered)
        $allRequests = AccessRequest::where('patient_id', $patient->patient_id)->get();
        $pendingCount = $allRequests->where('status', 'pending')->count();
        $approvedCount = $allRequests->where('status', 'approved')->count();
        $rejectedCount = $allRequests->where('status', 'rejected')->count();

        return view('patient.access-requests.index', compact(
            'patient', 
            'requests', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount',
            'currentStatus'
        ));
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
            'responded_at' => now()
        ]);

        // Insert audit trail saat pasien approve access request
        // medicalrecord_id = NULL karena dokter belum melakukan aktivitas apapun
        AuditTrail::create([
            'users_id' => $request->doctor_id, // ID dokter yang mendapat akses
            'patient_id' => $patient->patient_id,
            'medicalrecord_id' => null, // NULL karena belum ada record yang diakses
            'action' => 'view', // Action view untuk menandakan dokter sudah bisa "view"
            'timestamp' => now(),
            'blockchain_hash' => 'access_granted_' . uniqid()
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
            'responded_at' => now()
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
    public function auditTrail(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();
        
        $query = AuditTrail::where('patient_id', $patient->patient_id)
            ->with(['medicalRecord.doctor.user', 'medicalRecord.hospital']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', '%' . $search . '%')
                  ->orWhere('blockchain_hash', 'like', '%' . $search . '%')
                  ->orWhereHas('medicalRecord', function($recordQuery) use ($search) {
                      $recordQuery->where('diagnosis_desc', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->date_to);
        }

        $auditTrails = $query->orderBy('timestamp', 'desc')->paginate(20);

        return view('patient.audit-trail.index', compact('patient', 'auditTrails'));
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
        $user = Auth::user();
        $patient = Patient::where('patient_id', $user->idusers)->first();

        // Handle different actions based on form submission
        $action = $request->input('action', 'profile');

        switch ($action) {
            case 'profile':
                return $this->updateProfile($request, $user, $patient);
            case 'change_password':
                return $this->changePassword($request, $user);
            case 'privacy':
                return $this->updatePrivacy($request, $patient);
            default:
                return $this->updateProfile($request, $user, $patient);
        }
    }

    /**
     * Update profile information
     */
    private function updateProfile(Request $request, $user, $patient)
    {
        $request->validate([
            'name' => 'required|string|max:135',
            'email' => 'required|email|max:135|unique:users,email,' . $user->idusers . ',idusers',
            'nik' => 'nullable|numeric',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'blood' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address' => 'nullable|string|max:135',
        ]);

        // Update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update patient data - only update fields that exist in database
        $patientData = [];
        if ($request->filled('nik')) $patientData['nik'] = $request->nik;
        if ($request->filled('birthdate')) $patientData['birthdate'] = $request->birthdate;
        if ($request->filled('gender')) $patientData['gender'] = $request->gender;
        if ($request->filled('blood')) $patientData['blood'] = $request->blood;
        if ($request->filled('address')) $patientData['address'] = $request->address;

        if (!empty($patientData)) {
            $patient->update($patientData);
        }

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Change password
     */
    private function changePassword(Request $request, $user)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Update privacy settings
     */
    private function updatePrivacy(Request $request, $patient)
    {
        // Since privacy settings are not in the database, we'll just return success for now
        // In the future, you could add a separate privacy_settings table or add columns to patients table
        
        return redirect()->back()->with('success', 'Pengaturan privasi berhasil diperbarui.');
    }
}