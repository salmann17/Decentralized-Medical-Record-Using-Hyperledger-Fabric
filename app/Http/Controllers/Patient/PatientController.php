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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientController extends Controller
{
    /**
     * Show the patient dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $totalRecords = MedicalRecord::where('patient_id', $patient->idpatient)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            })
            ->count();
            
        $pendingRequests = AccessRequest::where('patient_id', $patient->idpatient)
            ->where('status', 'pending')->count();
            
        $activeDoctors = AccessRequest::where('patient_id', $patient->idpatient)
            ->where('status', 'approved')->distinct('doctor_id')->count();
        
        $recentRequests = AccessRequest::where('patient_id', $patient->idpatient)
            ->where('status', 'pending')
            ->with(['doctor.user', 'doctor.admins'])
            ->orderBy('requested_at', 'desc')
            ->take(5)
            ->get();
        
        $recentRecords = MedicalRecord::where('patient_id', $patient->idpatient)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            })
            ->with(['doctor.user', 'admin', 'prescription'])
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
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $query = MedicalRecord::where('patient_id', $patient->idpatient)
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            })
            ->with(['doctor.user', 'admin', 'prescription', 'auditTrails' => function ($q) {
                $q->whereNotNull('blockchain_hash')
                    ->where('blockchain_hash', '!=', '')
                    ->orderBy('timestamp', 'desc')
                    ->limit(1);
            }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('admin', function($adminQuery) use ($search) {
                    $adminQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('doctor.user', function($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('diagnosis_desc', 'like', '%' . $search . '%')
                ->orWhere('diagnosis_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $record = MedicalRecord::where('idmedicalrecord', $id)
            ->where('patient_id', $patient->idpatient)
            ->with(['doctor.user', 'admin', 'prescription'])
            ->firstOrFail();

        return view('patient.records.detail', compact('patient', 'record'));
    }

    /**
     * Show access requests from doctors
     */
    public function accessRequests(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        $currentStatus = $request->status;
        
        $query = AccessRequest::where('patient_id', $patient->idpatient)
            ->with(['doctor.user', 'doctor.admins']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('requested_at', 'desc')->paginate(15);
        
        $allRequests = AccessRequest::where('patient_id', $patient->idpatient)->get();
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
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $request = AccessRequest::where('idrequest', $id)
            ->where('patient_id', $patient->idpatient)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update([
            'status' => 'approved',
            'responded_at' => now()
        ]);

        return redirect()->back()->with('success', 'Akses dokter berhasil disetujui.');
    }

    /**
     * Reject access request  
     */
    public function rejectAccess($id)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $request = AccessRequest::where('idrequest', $id)
            ->where('patient_id', $patient->idpatient)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);

        return redirect()->back()->with('success', 'Akses dokter berhasil ditolak.');
    }

    /**
     * Show doctors with active access
     */
    public function activeDoctors(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $query = AccessRequest::where('patient_id', $patient->idpatient)
            ->where('status', 'approved')
            ->with(['doctor.user', 'doctor.admins']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('doctor.user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('doctor.admins', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $accessRequests = $query->orderBy('responded_at', 'desc')->paginate(10);
        
        $doctors = $accessRequests->map(function($accessRequest) {
            $doctor = $accessRequest->doctor;
            $doctor->accessRequest = $accessRequest;
            return $doctor;
        });
        
        $doctors = new LengthAwarePaginator(
            $doctors,
            $accessRequests->total(),
            $accessRequests->perPage(),
            $accessRequests->currentPage(),
            ['path' => $request->url(), 'pageName' => 'page']
        );

        $totalRecords = MedicalRecord::where('patient_id', $patient->idpatient)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            })
            ->count();

        return view('patient.active-doctors.index', compact('patient', 'doctors', 'totalRecords'));
    }

    /**
     * Revoke doctor access
     */
    public function revokeAccess($id)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $request = AccessRequest::where('idrequest', $id)
            ->where('patient_id', $patient->idpatient)
            ->where('status', 'approved')
            ->firstOrFail();

        $request->update([
            'status' => 'revoked',
            'responded_at' => now()
        ]);

        return redirect()->back()->with('success', 'Akses dokter berhasil dicabut.');
    }

    /**
     * Show audit trail
     */
    public function auditTrail(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();
        
        $query = AuditTrail::where('patient_id', $patient->idpatient)
            ->with(['doctor.user', 'medicalRecord']);

        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $query->orderBy('timestamp', 'desc');

        $perPage = $request->input('per_page', 10);
        $auditTrails = $query->paginate($perPage)->appends($request->except('page'));

        $totalAudits = AuditTrail::where('patient_id', $patient->idpatient)->count();
        $uniqueDoctors = AuditTrail::where('patient_id', $patient->idpatient)
            ->whereNotNull('doctor_id')
            ->distinct('doctor_id')
            ->count('doctor_id');
        $recordsAccessed = AuditTrail::where('patient_id', $patient->idpatient)
            ->where('action', 'view')
            ->count();
        $blockchainVerified = AuditTrail::where('patient_id', $patient->idpatient)
            ->whereNotNull('blockchain_hash')
            ->where('blockchain_hash', '!=', '')
            ->where('blockchain_hash', 'NOT LIKE', 'INVALID_%')
            ->where('blockchain_hash', 'NOT LIKE', 'NOT_FOUND_%')
            ->distinct('medicalrecord_id')
            ->count('medicalrecord_id');

        return view('patient.audit-trail.index', compact(
            'patient', 
            'auditTrails', 
            'totalAudits', 
            'uniqueDoctors', 
            'recordsAccessed', 
            'blockchainVerified'
        ));
    }

    /**
     * Show patient settings
     */
    public function settings()
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();

        return view('patient.settings.index', compact('patient', 'user'));
    }

    /**
     * Update patient settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('idpatient', $user->idusers)->first();

        $action = $request->input('action', 'profile');

        switch ($action) {
            case 'profile':
                return $this->updateProfile($request, $user, $patient);
            case 'privacy':
                return $this->updatePrivacy($request, $patient);
            default:
                return $this->updateProfile($request, $user, $patient);
        }
    }

    /**
     * Update patient password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah');
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

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

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

    private function updatePrivacy(Request $request, $patient)
    {
        return redirect()->back()->with('success', 'Pengaturan privasi berhasil diperbarui.');
    }
    
    /**
     * Verifikasi rekam medis di blockchain
     */
    public function verifyBlockchain($id)
    {
        try {
            $user = Auth::user();
            $patient = Patient::where('idpatient', $user->idusers)->first();
            
            $record = MedicalRecord::where('idmedicalrecord', $id)
                ->where('patient_id', $patient->idpatient)
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rekam medis tidak ditemukan.'
                ], 404);
            }

            $recordData = MedicalRecord::with(['patient.user', 'doctor.user', 'admin', 'prescriptions.prescriptionItems'])
                ->find($id);

            $json = json_encode($recordData->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $hash = hash('sha256', $json);

            $payload = [
                'idmedicalrecord' => $id,
                'hash' => $hash
            ];

            $response = Http::timeout(10)->post('http://172.25.117.62:3000/api/medical-records/verify', $payload);

            $body = $response->json();

            $auditTrail = AuditTrail::where('medicalrecord_id', $id)
                ->where('patient_id', $patient->idpatient)
                ->orderBy('timestamp', 'desc')
                ->first();

            if ($auditTrail && $body) {
                if (isset($body['success']) && $body['success'] === false && 
                    isset($body['message']) && strpos($body['message'], 'tidak ada di jaringan') !== false) {
                    $auditTrail->update([
                        'blockchain_hash' => 'NOT_FOUND_IN_BLOCKCHAIN_' . date('YmdHis'),
                        'timestamp' => now()
                    ]);
                }
                elseif (isset($body['message']) && strpos($body['message'], '✅') !== false) {
                    $auditTrail->update([
                        'blockchain_hash' => $body['data']['storedHash'] ?? $hash,
                        'timestamp' => now()
                    ]);
                }
                elseif (isset($body['message']) && strpos($body['message'], '⚠️') !== false) {
                    $auditTrail->update([
                        'blockchain_hash' => 'INVALID_DATA_MODIFIED_' . date('YmdHis'),
                        'timestamp' => now()
                    ]);
                }
            }

            if ($response->successful()) {
                return response()->json($body, 200);
            }
            if ($body) {
                return response()->json($body, 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server blockchain.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 200); 
        }
    }
}
