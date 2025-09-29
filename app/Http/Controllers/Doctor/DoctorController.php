<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\AccessRequest;
use App\Models\Hospital;
use App\Models\AuditTrail;
use App\Models\Prescription;
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
    public function accessRequests(Request $request)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $query = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->with(['patient.user']);

            // Filter berdasarkan status jika ada
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $requests = $query->orderBy('requested_at', 'desc')->get();

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
     * API untuk search pasien (untuk AJAX)
     */
    public function searchPatients(Request $request)
    {
        try {
            $doctor = Doctor::where('doctor_id', Auth::id())->first();
            
            if (!$doctor) {
                return response()->json(['error' => 'Data dokter tidak ditemukan'], 404);
            }

            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Ambil daftar pasien yang belum pernah diminta akses
            $existingRequests = AccessRequest::where('doctor_id', $doctor->doctor_id)
                ->pluck('patient_id')
                ->toArray();

            $patients = Patient::with('user')
                ->whereNotIn('patient_id', $existingRequests)
                ->whereHas('user', function($userQuery) use ($query) {
                    $userQuery->where('name', 'like', '%' . $query . '%')
                             ->orWhere('email', 'like', '%' . $query . '%');
                })
                ->get()
                ->map(function($patient) {
                    return [
                        'patient_id' => $patient->patient_id,
                        'name' => $patient->user->name,
                        'email' => $patient->user->email,
                        'gender' => $patient->gender ?? 'unknown',
                        'blood' => $patient->blood_type ?? 'Unknown'
                    ];
                });

            return response()->json($patients);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan permintaan akses baru
     */
    public function storeAccessRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,patient_id'
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

            // Simpan permintaan akses - TIDAK perlu audit trail
            $accessRequest = AccessRequest::create([
                'doctor_id' => $doctor->doctor_id,
                'patient_id' => $request->patient_id,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // TIDAK ada log audit trail disini karena:
            // - Access request hanya permintaan, bukan aktivitas medis
            // - Audit trail untuk aktivitas VIEW/CREATE medical records
            // - Pasien belum approve, jadi belum ada akses yang terjadi

            return redirect()->route('doctor.access-requests')
                ->with('success', 'Permintaan akses berhasil dikirim');
                
        } catch (\Exception $e) {
            Log::error('Error creating access request: ' . $e->getMessage());
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
                ->with(['patient.user', 'doctor.user', 'hospital', 'prescription'])
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
                ->with(['doctor.user', 'hospital', 'prescription'])
                ->orderBy('visit_date', 'asc') // Ubah ke ASC agar rekam medis terlama tampil pertama
                ->get();

            // TIDAK perlu insert audit trail disini karena sudah di-insert
            // saat pasien approve access request dengan medicalrecord_id = NULL

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
            // Vital signs (optional)
            'blood_pressure' => 'nullable|string|max:45',
            'heart_rate' => 'nullable|integer|min:30|max:250',
            'temperature' => 'nullable|numeric|between:30.0,45.0',
            'respiratory_rate' => 'nullable|integer|min:5|max:60',
            // Clinical narrative (optional)
            'chief_complaint' => 'nullable|string',
            'history_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            // Assessment (required)
            'diagnosis_code' => 'required|string|max:45',
            'diagnosis_desc' => 'required|string|max:135',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,final,immutable',
            // Prescription fields (multiple items)
            'prescriptions' => 'required|array|min:1',
            'prescriptions.*.item' => 'required|string|max:135',
            'prescriptions.*.dosage' => 'required|string|max:45',
            'prescriptions.*.frequency' => 'required|string|max:45',
            'prescriptions.*.duration' => 'required|string|max:45'
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

            // Start database transaction
            DB::beginTransaction();

            try {
                // 1. Buat prescription dengan multiple items
                // Untuk sementara gunakan item pertama, nanti bisa diperluas ke multiple prescriptions
                $firstPrescription = $request->prescriptions[0];
                $prescription = Prescription::create([
                    'item' => $firstPrescription['item'],
                    'dosage' => $firstPrescription['dosage'],
                    'frequency' => $firstPrescription['frequency'],
                    'duration' => $firstPrescription['duration']
                ]);

                // 2. Buat medical record dengan prescription_id
                $medicalRecord = MedicalRecord::create([
                    'patient_id' => $patientId,
                    'hospital_id' => $request->hospital_id,
                    'doctor_id' => $doctor->doctor_id,
                    'visit_date' => $request->visit_date,
                    // Vital signs
                    'blood_pressure' => $request->blood_pressure,
                    'heart_rate' => $request->heart_rate,
                    'temperature' => $request->temperature,
                    'respiratory_rate' => $request->respiratory_rate,
                    // Clinical narrative
                    'chief_complaint' => $request->chief_complaint,
                    'history_present_illness' => $request->history_present_illness,
                    'physical_examination' => $request->physical_examination,
                    // Assessment
                    'diagnosis_code' => $request->diagnosis_code,
                    'diagnosis_desc' => $request->diagnosis_desc,
                    'treatment' => $request->treatment,
                    'notes' => $request->notes ?? '',
                    'status' => $request->status,
                    'prescription_id' => $prescription->prescription_id
                ]);

                // 3. Log audit trail untuk CREATE medical record
                AuditTrail::create([
                    'users_id' => Auth::id(),
                    'patient_id' => $patientId,
                    'medicalrecord_id' => $medicalRecord->medicalrecord_id,
                    'action' => 'create',
                    'timestamp' => now(),
                    'blockchain_hash' => 'record_created_' . uniqid()
                ]);

                // Commit transaction
                DB::commit();

                // TODO: Blockchain integration - record medical record on blockchain

                return redirect()->route('doctor.patient-records', $patientId)
                    ->with('success', 'Rekam medis dan resep berhasil disimpan dengan status: ' . ucfirst($request->status));
                    
            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollback();
                throw $e;
            }
                
        } catch (\Exception $e) {
            Log::error('Error creating medical record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
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

            $record = MedicalRecord::with(['patient.user', 'doctor.user', 'hospital', 'prescription'])
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

            // Update audit trail yang existing dengan medicalrecord_id
            // Cari audit trail yang sudah ada (saat pasien approve) dan update dengan record ID
            $existingAudit = AuditTrail::where('users_id', Auth::id())
                ->where('patient_id', $record->patient_id)
                ->whereNull('medicalrecord_id') // Audit trail saat approve (medicalrecord_id = NULL)
                ->where('action', 'view')
                ->first();

            if ($existingAudit) {
                // Update audit trail existing dengan medicalrecord_id
                $existingAudit->update([
                    'medicalrecord_id' => $recordId,
                    'timestamp' => now(), // Update timestamp ke waktu view
                    'blockchain_hash' => 'record_viewed_' . uniqid()
                ]);
            } else {
                // Jika tidak ada audit trail existing, buat baru
                AuditTrail::create([
                    'users_id' => Auth::id(),
                    'patient_id' => $record->patient_id,
                    'medicalrecord_id' => $recordId,
                    'action' => 'view',
                    'timestamp' => now(),
                    'blockchain_hash' => 'record_viewed_' . uniqid()
                ]);
            }

            return view('doctor.records.show', compact('record', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status rekam medis
     */
    public function updateRecordStatus(Request $request, $recordId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,final,immutable',
            'reason' => 'nullable|string|max:255'
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

            $record = MedicalRecord::with(['patient.user', 'doctor.user', 'hospital', 'prescription'])
                ->find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek apakah dokter adalah pemilik record
            if ($record->doctor_id !== $doctor->doctor_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
            }

            // Cek apakah record bisa diubah (tidak immutable)
            if ($record->status === 'immutable') {
                return redirect()->back()->with('error', 'Rekam medis immutable tidak dapat diubah');
            }

            $oldStatus = $record->status;
            $newStatus = $request->status;

            // Validasi transisi status
            $validTransitions = [
                'draft' => ['final', 'immutable'],
                'final' => ['immutable']
            ];

            if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
                return redirect()->back()->with('error', 'Transisi status dari ' . ucfirst($oldStatus) . ' ke ' . ucfirst($newStatus) . ' tidak diizinkan');
            }

            // Update status
            $record->update([
                'status' => $newStatus
            ]);

            // TIDAK perlu log audit trail untuk perubahan status
            // karena aktivitas "create" sudah dicatat saat record pertama kali dibuat
            // Perubahan status hanya internal workflow, bukan aktivitas medis baru

            // Success message berdasarkan status
            $message = match($newStatus) {
                'final' => 'Rekam medis berhasil difinalisasi',
                'immutable' => 'Rekam medis berhasil dibuat immutable (tidak dapat diubah)',
                default => 'Status rekam medis berhasil diupdate'
            };

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error updating record status: ' . $e->getMessage());
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
            $doctor = Doctor::where('doctor_id', Auth::id())
                ->with(['user', 'hospitals'])
                ->first();
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Debug: Log current specialization value
            Log::info('Doctor specialization: ' . ($doctor->specialization ?? 'NULL'));

            return view('doctor.settings.index', compact('doctor'));
            
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
            'specialization' => 'required|string|in:Umum,Kardiologi,Neurologi,Orthopedi,Pediatri,Kandungan,Bedah,Mata,THT,Kulit,Jiwa,Radiologi,Anestesi,Patologi,Rehabilitasi',
            'license_number' => 'required|numeric'
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

            // Update data dokter
            $doctor->update([
                'specialization' => $request->specialization,
                'license_number' => $request->license_number
            ]);

            // Log audit trail
            $this->logAuditTrail(Auth::id(), null, null, 'UPDATE_PROFILE');

            // TODO: Blockchain integration - record profile update on blockchain

            return redirect()->back()->with('success', 'Profil dokter berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update password dokter
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::find(Auth::id());
            
            if (!$user) {
                return redirect()->back()->with('error', 'User tidak ditemukan');
            }

            // Cek current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Password saat ini tidak sesuai');
            }

            // Update password
            $user->update(['password' => bcrypt($request->password)]);

            return redirect()->back()->with('success', 'Password berhasil diubah');
            
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
