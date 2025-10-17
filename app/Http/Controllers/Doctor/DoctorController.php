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
use App\Models\AuditTrail;
use App\Models\Prescription;
use App\Models\User;

class DoctorController extends Controller
{
    /**
     * Dashboard untuk dokter
     */
    public function dashboard()
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil statistik
            $totalPatients = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('status', 'approved')
                ->count();
                
            $pendingRequests = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('status', 'pending')
                ->count();
                
            // PENTING: Hitung hanya rekam medis versi terbaru (bukan semua versi)
            // Gunakan whereNotExists untuk memastikan tidak ada versi lebih baru
            $totalRecords = MedicalRecord::where('doctor_id', $doctor->iddoctor)
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('medical_records as mr2')
                        ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
                })
                ->count();
            
            // PENTING: Ambil hanya rekam medis versi terbaru untuk recent records
            // Tidak menampilkan versi lama (v1, v2, dst.)
            $recentRecords = MedicalRecord::where('doctor_id', $doctor->iddoctor)
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('medical_records as mr2')
                        ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
                })
                ->with(['patient.user', 'admin'])
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $hospitals = $doctor->admins()->get();

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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $query = AccessRequest::where('doctor_id', $doctor->iddoctor)
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil daftar pasien yang belum pernah diminta akses
            $existingRequests = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->pluck('patient_id')
                ->toArray();

            $patients = Patient::with('user')
                ->whereNotIn('idpatient', $existingRequests)
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return response()->json(['error' => 'Data dokter tidak ditemukan'], 404);
            }

            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Ambil daftar pasien yang belum pernah diminta akses
            $existingRequests = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->pluck('patient_id')
                ->toArray();

            $patients = Patient::with('user')
                ->whereNotIn('idpatient', $existingRequests)
                ->whereHas('user', function($userQuery) use ($query) {
                    $userQuery->where('name', 'like', '%' . $query . '%')
                             ->orWhere('email', 'like', '%' . $query . '%');
                })
                ->get()
                ->map(function($patient) {
                    return [
                        'patient_id' => $patient->idpatient,
                        'name' => $patient->user->name,
                        'email' => $patient->user->email,
                        'gender' => $patient->gender ?? 'unknown',
                        'blood' => $patient->blood ?? 'Unknown'
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
        // Debug: Log all request data
        Log::info('Access Request Data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,idpatient'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            Log::info('Doctor data:', ['doctor_id' => $doctor->iddoctor, 'patient_id' => $request->patient_id]);

            // Cek apakah permintaan sudah ada
            $existingRequest = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $request->patient_id)
                ->first();

            if ($existingRequest) {
                Log::info('Existing request found:', $existingRequest->toArray());
                return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
            }

            // Simpan permintaan akses - TIDAK perlu audit trail
            $accessRequest = AccessRequest::create([
                'doctor_id' => $doctor->iddoctor,
                'patient_id' => $request->patient_id,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            Log::info('Access request created:', $accessRequest->toArray());

            return redirect()->route('doctor.access-requests')
                ->with('success', 'Permintaan akses berhasil dikirim');
                
        } catch (\Exception $e) {
            Log::error('Error creating access request: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Request akses untuk pasien tertentu
     */
    public function requestAccess($patientId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek apakah pasien ada
            $patient = Patient::find($patientId);
            if (!$patient) {
                return redirect()->back()->with('error', 'Pasien tidak ditemukan');
            }

            // Cek apakah permintaan sudah ada
            $existingRequest = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $patientId)
                ->first();

            if ($existingRequest) {
                return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
            }

            // Simpan permintaan akses
            AccessRequest::create([
                'doctor_id' => $doctor->iddoctor,
                'patient_id' => $patientId,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // TIDAK perlu log audit trail untuk request access
            // Audit trail HANYA untuk aktivitas medis (create/view medical record)

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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $patients = Patient::whereHas('accessRequests', function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->iddoctor)
                    ->where('status', 'approved');
            })->with(['user', 'accessRequests' => function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->iddoctor)
                    ->where('status', 'approved');
            }])->paginate(12);

            return view('doctor.patients.index', compact('patients', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan semua rekam medis yang dapat diakses dokter
     * 
     * LOGIKA FILTER VERSI TERBARU:
     * - Ambil semua rekam medis yang TIDAK memiliki versi lebih baru
     * - Check: Tidak ada record lain dengan previous_id = idmedicalrecord record ini
     * - Ini memastikan setiap "chain" rekam medis hanya menampilkan versi terbaru
     * - Draft tanpa previous_id (record baru) juga ditampilkan
     */
    public function records()
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Ambil ID pasien yang memberikan akses
            $approvedPatientIds = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('status', 'approved')
                ->pluck('patient_id')
                ->toArray();

            // PENTING: Filter hanya versi terbaru menggunakan whereNotExists
            // Logic: Tampilkan record JIKA tidak ada record lain yang previous_id-nya menunjuk ke record ini
            // Artinya: Jika ada v2 dari v1, maka v1 tidak ditampilkan (karena ada v2 dengan previous_id=v1.id)
            $baseQuery = MedicalRecord::where('doctor_id', $doctor->iddoctor)
                ->whereIn('patient_id', $approvedPatientIds)
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('medical_records as mr2')
                        ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
                });

            // Hitung total untuk setiap status (untuk badge count di tabs)
            $totalAll = (clone $baseQuery)->count();
            $totalDraft = (clone $baseQuery)->where('status', 'draft')->count();
            $totalFinal = (clone $baseQuery)->where('status', 'final')->count();

            // Query untuk pagination dengan filter status
            $query = (clone $baseQuery)
                ->with(['patient.user', 'doctor.user', 'admin', 'prescriptions'])
                ->orderBy('visit_date', 'desc');

            // Apply status filter if requested
            if (request('status') && request('status') !== 'all') {
                $query->where('medical_records.status', request('status'));
            }

            // Paginate results
            $records = $query->paginate(10);

            return view('doctor.records.index', compact('records', 'doctor', 'totalAll', 'totalDraft', 'totalFinal'));
            
        } catch (\Exception $e) {
            Log::error('Error in records: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan rekam medis untuk pasien tertentu
     */
    public function patientRecords($patientId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            $patient = Patient::with('user')->find($patientId);
            
            // Hanya ambil versi terbaru untuk setiap group previous_id
            // atau record yang tidak memiliki versi lebih baru
            $records = MedicalRecord::where('patient_id', $patientId)
                ->whereNotExists(function ($query) use ($patientId) {
                    $query->select(DB::raw(1))
                        ->from('medical_records as mr2')
                        ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord')
                        ->where('mr2.patient_id', $patientId);
                })
                ->with(['doctor.user', 'admin', 'prescriptions'])
                ->orderBy('visit_date', 'desc')
                ->get();

            return view('doctor.records.patient', compact('records', 'patient', 'doctor'));
            
        } catch (\Exception $e) {
            Log::error('Error in patientRecords: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk membuat rekam medis baru
     */
    public function createRecord($patientId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            $patient = Patient::with('user')->find($patientId);
            $hospitals = $doctor->admins;

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
            'admin_id' => 'required|exists:admins,idadmin',
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
            'diagnosis_desc' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,final,immutable',
            // Prescription fields (multiple prescriptions support)
            'prescriptions' => 'required|array|min:1',
            'prescriptions.*.type' => 'required|in:single,compound',
            'prescriptions.*.instructions' => 'nullable|string',
            'prescriptions.*.items' => 'required|array|min:1',
            'prescriptions.*.items.*.name' => 'required|string|max:135',
            'prescriptions.*.items.*.dosage' => 'required|string|max:45',
            'prescriptions.*.items.*.frequency' => 'required|string|max:45',
            'prescriptions.*.items.*.duration' => 'required|string|max:45',
            'prescriptions.*.items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $patientId)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // 1. Buat medical record terlebih dahulu
                $medicalRecord = MedicalRecord::create([
                    'patient_id' => $patientId,
                    'admin_id' => $request->admin_id,
                    'doctor_id' => $doctor->iddoctor,
                    'visit_date' => $request->visit_date,
                    // Vital signs
                    'blood_pressure' => $request->blood_pressure ?? '',
                    'heart_rate' => $request->heart_rate ?? 0,
                    'temperature' => $request->temperature ?? 0,
                    'respiratory_rate' => $request->respiratory_rate ?? 0,
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
                    'version' => 1
                ]);

                // 2. Buat prescriptions dengan items
                foreach ($request->prescriptions as $prescriptionData) {
                    // Buat prescription header
                    $prescription = Prescription::create([
                        'medicalrecord_id' => $medicalRecord->idmedicalrecord,
                        'type' => $prescriptionData['type'],
                        'instructions' => $prescriptionData['instructions'] ?? null
                    ]);

                    // Buat prescription items
                    foreach ($prescriptionData['items'] as $itemData) {
                        $prescription->prescriptionItems()->create([
                            'name' => $itemData['name'],
                            'dosage' => $itemData['dosage'],
                            'frequency' => $itemData['frequency'],
                            'duration' => $itemData['duration'],
                            'notes' => $itemData['notes'] ?? null
                        ]);
                    }
                }

                // 3. Log audit trail untuk CREATE medical record
                // Audit trail HANYA untuk aktivitas dokter, bukan pasien
                AuditTrail::create([
                    'doctor_id' => $doctor->iddoctor,  // Menggunakan doctor_id, bukan users_id
                    'patient_id' => $patientId,
                    'medicalrecord_id' => $medicalRecord->idmedicalrecord,
                    'action' => 'create',
                    'timestamp' => now(),
                    'blockchain_hash' => 'sementara null karena belum nyambung blockchain'
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
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail rekam medis
     */
    public function showRecord($recordId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::with([
                'patient.user', 
                'doctor.user', 
                'admin',  // Ganti dari 'hospital' ke 'admin'
                'prescriptions.prescriptionItems'  // Load prescriptions dengan items
            ])->find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek akses ke pasien
            $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
                ->where('patient_id', $record->patient_id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rekam medis ini');
            }

            // Audit Trail untuk VIEW - hanya mencatat aktivitas DOKTER
            // Cek apakah dokter sudah pernah VIEW record ini sebelumnya
            $existingViewAudit = AuditTrail::where('doctor_id', $doctor->iddoctor)
                ->where('medicalrecord_id', $recordId)
                ->where('action', 'view')
                ->first();

            if ($existingViewAudit) {
                // Jika sudah pernah view, hanya update timestamp (tidak buat record baru)
                $existingViewAudit->update([
                    'timestamp' => now()
                ]);
                Log::info('Updated existing view audit trail', [
                    'doctor_id' => $doctor->iddoctor,
                    'medicalrecord_id' => $recordId,
                    'audit_id' => $existingViewAudit->idaudit
                ]);
            } else {
                // Jika belum pernah view, buat audit trail baru
                AuditTrail::create([
                    'doctor_id' => $doctor->iddoctor,
                    'patient_id' => $record->patient_id,
                    'medicalrecord_id' => $recordId,
                    'action' => 'view',
                    'timestamp' => now(),
                ]);
                Log::info('Created new view audit trail', [
                    'doctor_id' => $doctor->iddoctor,
                    'medicalrecord_id' => $recordId
                ]);
            }

            return view('doctor.records.show', compact('record', 'doctor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit rekam medis
     * 
     * LOGIKA VERSIONING:
     * - Jika status = draft: Edit langsung (in-place update)
     * - Jika status = final: Buat versi baru (duplikasi data, version+1, status=draft)
     * 
     * AUDIT TRAIL:
     * - TIDAK dicatat saat klik tombol "Edit" (hanya duplikasi data)
     * - Audit trail HANYA dicatat saat dokter menekan "Simpan" (di updateRecord)
     */
    public function editRecord($recordId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::with(['patient.user', 'admin', 'prescriptions.prescriptionItems'])
                ->find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek apakah dokter adalah pemilik record
            if ($record->doctor_id !== $doctor->iddoctor) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
            }

            // Jika status = draft, edit langsung
            if ($record->status === 'draft') {
                $hospitals = $doctor->admins;
                return view('doctor.records.edit', compact('record', 'doctor', 'hospitals'));
            }

            // Jika status = final, buat versi baru
            if ($record->status === 'final') {
                // Duplikasi record untuk versioning (TANPA audit trail)
                DB::beginTransaction();
                
                try {
                    // Buat record baru dengan version + 1
                    $newRecord = $record->replicate();
                    $newRecord->version = $record->version + 1;
                    $newRecord->previous_id = $record->idmedicalrecord;
                    $newRecord->status = 'draft';
                    $newRecord->created_at = now();
                    $newRecord->updated_at = now();
                    $newRecord->save();

                    // Duplikasi prescriptions
                    foreach ($record->prescriptions as $prescription) {
                        $newPrescription = $prescription->replicate();
                        $newPrescription->medicalrecord_id = $newRecord->idmedicalrecord;
                        $newPrescription->save();

                        // Duplikasi prescription items
                        foreach ($prescription->prescriptionItems as $item) {
                            $newItem = $item->replicate();
                            $newItem->prescription_id = $newPrescription->idprescription;
                            $newItem->save();
                        }
                    }

                    DB::commit();

                    // Load relasi untuk view
                    $newRecord->load(['patient.user', 'admin', 'prescriptions.prescriptionItems']);
                    
                    // Gunakan $record (bukan $newRecord) untuk konsistensi dengan view
                    $hospitals = $doctor->admins;
                    $record = $newRecord; // Re-assign ke $record
                    
                    return view('doctor.records.edit', compact('record', 'doctor', 'hospitals'))
                        ->with('info', 'Anda sedang mengedit versi baru (v' . $record->version . ') dari rekam medis ini. Versi lama tetap tersimpan.');

                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error creating new version: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal membuat versi baru: ' . $e->getMessage());
                }
            }

            return redirect()->back()->with('error', 'Rekam medis dengan status immutable tidak dapat diedit');
            
        } catch (\Exception $e) {
            Log::error('Error in editRecord: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update rekam medis
     * 
     * LOGIKA PENYIMPANAN:
     * - Hanya update record yang sudah ada (TIDAK insert baru)
     * - Status ditentukan dari save_action (draft atau final)
     * 
     * AUDIT TRAIL:
     * - SELALU dicatat setiap kali menyimpan (draft atau final)
     * - Timestamp audit trail = waktu klik tombol "Simpan", BUKAN waktu klik "Edit"
     * 
     * PROTEKSI DUPLIKASI:
     * - Hanya ada 1 transaksi DB per request
     * - Form memiliki proteksi double-click
     */
    public function updateRecord(Request $request, $recordId)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,idadmin',
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
            'diagnosis_desc' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'save_action' => 'required|in:draft,final',
            // Prescription fields
            'prescriptions' => 'required|array|min:1',
            'prescriptions.*.type' => 'required|in:single,compound',
            'prescriptions.*.instructions' => 'nullable|string',
            'prescriptions.*.items' => 'required|array|min:1',
            'prescriptions.*.items.*.name' => 'required|string|max:135',
            'prescriptions.*.items.*.dosage' => 'required|string|max:45',
            'prescriptions.*.items.*.frequency' => 'required|string|max:45',
            'prescriptions.*.items.*.duration' => 'required|string|max:45',
            'prescriptions.*.items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek apakah dokter adalah pemilik record
            if ($record->doctor_id !== $doctor->iddoctor) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
            }

            // Hanya bisa edit jika status draft
            if ($record->status !== 'draft') {
                return redirect()->back()->with('error', 'Hanya rekam medis dengan status draft yang dapat diedit langsung');
            }

            DB::beginTransaction();

            try {
                // Update medical record
                $record->update([
                    'admin_id' => $request->admin_id,
                    'visit_date' => $request->visit_date,
                    // Vital signs
                    'blood_pressure' => $request->blood_pressure ?? '',
                    'heart_rate' => $request->heart_rate ?? 0,
                    'temperature' => $request->temperature ?? 0,
                    'respiratory_rate' => $request->respiratory_rate ?? 0,
                    // Clinical narrative
                    'chief_complaint' => $request->chief_complaint,
                    'history_present_illness' => $request->history_present_illness,
                    'physical_examination' => $request->physical_examination,
                    // Assessment
                    'diagnosis_code' => $request->diagnosis_code,
                    'diagnosis_desc' => $request->diagnosis_desc,
                    'treatment' => $request->treatment,
                    'notes' => $request->notes ?? '',
                    'status' => $request->save_action, // draft atau final
                    'updated_at' => now()
                ]);

                // Hapus prescriptions lama
                foreach ($record->prescriptions as $oldPrescription) {
                    $oldPrescription->prescriptionItems()->delete();
                    $oldPrescription->delete();
                }

                // Buat prescriptions baru
                foreach ($request->prescriptions as $prescriptionData) {
                    $prescription = Prescription::create([
                        'medicalrecord_id' => $record->idmedicalrecord,
                        'type' => $prescriptionData['type'],
                        'instructions' => $prescriptionData['instructions'] ?? null
                    ]);

                    foreach ($prescriptionData['items'] as $itemData) {
                        $prescription->prescriptionItems()->create([
                            'name' => $itemData['name'],
                            'dosage' => $itemData['dosage'],
                            'frequency' => $itemData['frequency'],
                            'duration' => $itemData['duration'],
                            'notes' => $itemData['notes'] ?? null
                        ]);
                    }
                }

                // PENTING: Audit trail dicatat SETIAP KALI menyimpan (draft atau final)
                // Bukan saat klik Edit, tetapi saat klik Simpan
                AuditTrail::create([
                    'doctor_id' => $doctor->iddoctor,
                    'patient_id' => $record->patient_id,
                    'medicalrecord_id' => $record->idmedicalrecord,
                    'action' => 'update',
                    'timestamp' => now(),
                    'blockchain_hash' => null // Akan diisi setelah blockchain integration
                ]);

                DB::commit();

                $message = $request->save_action === 'draft' 
                    ? 'Perubahan berhasil disimpan sebagai draft' 
                    : 'Rekam medis berhasil difinalisasi';

                return redirect()->route('doctor.show-record', $record->idmedicalrecord)
                    ->with('success', $message);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error updating medical record: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Finalisasi rekam medis (ubah status dari draft ke final)
     * Digunakan dari tombol "Finalisasi" di halaman show/index
     */
    public function finalizeRecord($recordId)
    {
        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek apakah dokter adalah pemilik record
            if ($record->doctor_id !== $doctor->iddoctor) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
            }

            // Hanya bisa finalisasi jika status draft
            if ($record->status !== 'draft') {
                return redirect()->back()->with('error', 'Hanya rekam medis dengan status draft yang dapat difinalisasi');
            }

            // Update status ke final
            $record->update([
                'status' => 'final',
                'updated_at' => now()
            ]);

            // PENTING: Audit trail dicatat saat finalisasi (bukan saat klik Edit)
            AuditTrail::create([
                'doctor_id' => $doctor->iddoctor,
                'patient_id' => $record->patient_id,
                'medicalrecord_id' => $record->idmedicalrecord,
                'action' => 'update',
                'timestamp' => now(),
                'blockchain_hash' => null // Akan diisi setelah blockchain integration
            ]);

            return redirect()->back()->with('success', 'Rekam medis berhasil difinalisasi');

        } catch (\Exception $e) {
            Log::error('Error finalizing record: ' . $e->getMessage());
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $record = MedicalRecord::with(['patient.user', 'doctor.user', 'admin', 'prescriptions'])
                ->find($recordId);

            if (!$record) {
                return redirect()->back()->with('error', 'Rekam medis tidak ditemukan');
            }

            // Cek apakah dokter adalah pemilik record
            if ($record->doctor_id !== $doctor->iddoctor) {
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $auditTrails = AuditTrail::where('doctor_id', $doctor->iddoctor)
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
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $doctor->load(['user', 'admins']);

            Log::info('Doctor spesialization: ' . ($doctor->spesialization ?? 'NULL'));

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
            'spesialization' => 'required|string|in:Umum,Kardiologi,Neurologi,Orthopedi,Pediatri,Kandungan,Bedah,Mata,THT,Kulit,Jiwa,Radiologi,Anestesi,Patologi,Rehabilitasi',
            'license_number' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $doctor = Auth::user()->doctor;
            
            if (!$doctor) {
                return redirect()->back()->with('error', 'Data dokter tidak ditemukan');
            }

            $user = User::find(Auth::id());
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

            $doctor->update([
                'spesialization' => $request->spesialization,
                'license_number' => $request->license_number
            ]);

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
    private function logAuditTrail($doctorId, $patientId, $medicalRecordId, $action)
    {
        try {
            AuditTrail::create([
                'doctor_id' => $doctorId,
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







