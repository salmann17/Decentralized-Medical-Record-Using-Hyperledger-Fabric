<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\AccessRequest;
use App\Models\Hospital;
use App\Models\AuditTrail;
use App\Models\User;

class DoctorController extends Controller
{
    /**
     * Dashboar    public function settings()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())
                ->with(['user', 'hospitals'])
                ->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            return view('doctor.settings.index', compact('doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }k untuk dokter
     */
    public function dashboard()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil statistik
            $totalPatients = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('status', 'approved')
                ->count();
                
            $pendingRequests = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('status', 'pending')
                ->count();
                
            $totalRecords = MedicalRecord::where('doctor_id', $doctor->doctor_id)->count();
            
            $recentRecords = MedicalRecord::where('doctor_id', $doctor->doctor_id)
                ->with(['patient.user', 'hospital'])
                ->orderBy('visit_date', 'desc')
                ->limit(5)
                ->get();

            // TODO: Blockchain integration - get blockchain statistics
            $blockchainStats = [
                'total_blocks' => 0,
                'last_hash' => null
            ];

            $data = [
                'doctor' => $doctor,
                'stats' => [
                    'total_patients' => $totalPatients,
                    'pending_requests' => $pendingRequests,
                    'total_records' => $totalRecords,
                    'blockchain_records' => $blockchainStats['total_blocks']
                ],
                'recent_records' => $recentRecords
            ];

            return view('doctor.dashboard', $data);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar rumah sakit tempat dokter bekerja
     */
    public function hospitals()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $hospitals = $doctor->hospitals()->get();

            return view('doctor.hospitals', compact('hospitals', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar permintaan akses pasien
     */
    public function accessRequests()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $requests = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->with(['patient.user'])
                ->orderBy('requested_at', 'desc')
                ->get();

            return view('doctor.requests.index', compact('requests', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk membuat permintaan akses baru
     */
    public function createAccessRequest()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil daftar pasien yang belum pernah diminta akses
            $existingRequests = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->pluck('patient_id')
                ->toArray();

            $patients = Patient::with('user')
                ->whereNotIn('patient_id', $existingRequests)
                ->get();

            return view('doctor.requests.create', compact('patients', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Simpan permintaan akses baru
     */
    public function storeAccessRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,patient_id',
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek apakah permintaan sudah ada
            $existingRequest = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $request->patient_id)
                ->first();

            if ($existingRequest) {
                return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
            }

            // Simpan permintaan akses
            AccessRequest::create([
                'doctor_id' => $doctor->doctor_id,
                'patient_id' => $request->patient_id,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // Log audit trail
            $this->logAuditTrail(Auth::id(), $request->patient_id, null, 'REQUEST_ACCESS');

            // TODO: Blockchain integration - record access request on blockchain

            return redirect()->route('doctor.access-requests')
                ->with('success', 'Permintaan akses berhasil dikirim');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Request akses untuk pasien tertentu
     */
    public function requestAccess($patientId)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek apakah pasien ada
            $patient = Patient::find($patientId);
            if (!$patient) {
                return redirect()->back()->with('error', 'Pasien tidak ditemukan');
            }

            // Cek apakah permintaan sudah ada
            $existingRequest = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $patientId)
                ->first();

            if ($existingRequest) {
                return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
            }

            // Simpan permintaan akses
            AccessRequest::create([
                'doctor_id' => $doctor->doctor_id,
                'patient_id' => $patientId,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // Log audit trail
            $this->logAuditTrail(Auth::id(), $patientId, null, 'REQUEST_ACCESS');

            // TODO: Blockchain integration - record access request on blockchain

            return redirect()->back()->with('success', 'Permintaan akses berhasil dikirim');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar pasien yang memberikan akses
     */
    public function myPatients()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $patients = Patient::whereHas('accessRequests', function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->doctor_id)
                    ->where('status', 'approved');
            })->with(['user', 'accessRequests' => function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->doctor_id)
                    ->where('status', 'approved');
            }])->paginate(12);

            return view('doctor.patients.index', compact('patients', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan semua rekam medis yang dapat diakses dokter
     */
    public function records()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil ID pasien yang memberikan akses
            $approvedPatientIds = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('status', 'approved')
                ->pluck('patient_id')
                ->toArray();

            // Query builder for pagination
            $query = MedicalRecord::whereIn('patient_id', $approvedPatientIds)
                ->with(['patient.user', 'doctor.user', 'hospital'])
                ->orderBy('visit_date', 'desc');

            // Apply status filter if requested
            if (request('status') && request('status') !== 'all') {
                $query->where('status', request('status'));
            }

            // Paginate results
            $records = $query->paginate(10);

            return view('doctor.records.index', compact('records', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan rekam medis untuk pasien tertentu
     */
    public function patientRecords($patientId)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            $patient = Patient::with('user')->find($patientId);
            
            $records = MedicalRecord::where('patient_id', $patientId)
                ->with(['doctor.user', 'hospital'])
                ->orderBy('visit_date', 'desc')
                ->get();

            // Log audit trail untuk akses rekam medis
            $this->logAuditTrail(Auth::id(), $patientId, null, 'VIEW_PATIENT_RECORDS');

            return view('doctor.records.patient', compact('records', 'patient', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk membuat rekam medis baru
     */
    public function createRecord($patientId)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            $patient = Patient::with('user')->find($patientId);
            $hospitals = $doctor->hospitals;

            return view('doctor.records.create', compact('patient', 'doctor', 'hospitals'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Simpan rekam medis baru
     */
    public function storeRecord(Request $request, $patientId)
    {
        $validator = Validator::make($request->all(), [
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'visit_date' => 'required|date',
            'diagnosis_code' => 'required|string|max:10',
            'diagnosis_desc' => 'required|string|max:500',
            'treatment' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            // Simpan rekam medis
            $medicalRecord = MedicalRecord::create([
                'patient_id' => $patientId,
                'hospital_id' => $request->hospital_id,
                'doctor_id' => $doctor->doctor_id,
                'visit_date' => $request->visit_date,
                'diagnosis_code' => $request->diagnosis_code,
                'diagnosis_desc' => $request->diagnosis_desc,
                'treatment' => $request->treatment,
                'notes' => $request->notes,
                'status' => 'active'
            ]);

            // Log audit trail
            $this->logAuditTrail(Auth::id(), $patientId, $medicalRecord->medicalrecord_id, 'CREATE_RECORD');

            // TODO: Blockchain integration - record medical record on blockchain

            return redirect()->route('doctor.patient-records', $patientId)
                ->with('success', 'Rekam medis berhasil disimpan');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail rekam medis
     */
    public function showRecord($recordId)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::with(['patient.user', 'doctor.user', 'hospital'])
                ->find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->where('patient_id', $record->patient_id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rekam medis ini');
            }

            // Log audit trail
            $this->logAuditTrail(Auth::id(), $record->patient_id, $recordId, 'VIEW_RECORD');

            return view('doctor.records.show', compact('record', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan audit trail untuk dokter
     */
    public function auditTrail()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $auditTrails = AuditTrail::where('users_id', Auth::id())
                ->with(['patient.user', 'medicalRecord'])
                ->orderBy('timestamp', 'desc')
                ->get();

            return view('doctor.audit.index', compact('auditTrails', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan pengaturan akun dokter
     */
    public function settings()
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->with('user')->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $hospitals = Hospital::all();

            return view('doctor.settings.index', compact('doctor', 'hospitals'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update pengaturan akun dokter
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:135',
            'email' => 'required|email|max:135|unique:users,email,' . Auth::id() . ',idusers',
            'specialization' => 'required|string|max:45',
            'license_number' => 'required|numeric',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Update data user
            $user = User::find(Auth::id());
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }

            // Update data dokter
            $doctor->update([
                'specialization' => $request->specialization,
                'license_number' => $request->license_number
            ]);

            // Log audit trail
            $this->logAuditTrail(Auth::id(), null, null, 'UPDATE_PROFILE');

            // TODO: Blockchain integration - record profile update on blockchain

            return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk logging audit trail
     */
    private function logAuditTrail($userId, $patientId, $medicalRecordId, $action)
    {
        try {
            AuditTrail::create([
                'users_id' => $userId,
                'patient_id' => $patientId,
                'medicalrecord_id' => $medicalRecordId,
                'action' => $action,
                'timestamp' => now(),
                'blockchain_hash' => null // TODO: Generate blockchain hash
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan stop execution
            Log::error('Failed to log audit trail: ' . $e->getMessage());
        }
    }
}
