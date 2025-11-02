<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
        $doctor = Auth::user()->doctor;
        
        $totalPatients = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('status', 'approved')
            ->count();
            
        $pendingRequests = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('status', 'pending')
            ->count();
            
        $totalRecords = MedicalRecord::where('doctor_id', $doctor->iddoctor)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            })
            ->count();
        
        $blockchainRecords = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->whereNotNull('blockchain_hash')
            ->where('blockchain_hash', '!=', '')
            ->distinct('medicalrecord_id')
            ->count('medicalrecord_id');
        
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

        $data = [
            'doctor' => $doctor,
            'stats' => [
                'total_patients' => $totalPatients,
                'pending_requests' => $pendingRequests,
                'total_records' => $totalRecords,
                'blockchain_records' => $blockchainRecords
            ],
            'recent_records' => $recentRecords
        ];

        return view('doctor.dashboard', $data);
    }

    /**
     * Tampilkan daftar rumah sakit tempat dokter bekerja
     */
    public function hospitals()
    {
        $doctor = Auth::user()->doctor;
        $hospitals = $doctor->admins()->get();

        return view('doctor.hospitals', compact('hospitals', 'doctor'));
    }

    /**
     * Tampilkan daftar permintaan akses pasien
     */
    public function accessRequests(Request $request)
    {
        $doctor = Auth::user()->doctor;
        
        $query = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->with(['patient.user']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('requested_at', 'desc')->get();

        return view('doctor.requests.index', compact('requests', 'doctor'));
    }

    /**
     * Tampilkan form untuk membuat permintaan akses baru
     */
    public function createAccessRequest()
    {
        $doctor = Auth::user()->doctor;
        
        $existingRequests = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->pluck('patient_id')
            ->toArray();

        $patients = Patient::with('user')
            ->whereNotIn('idpatient', $existingRequests)
            ->get();

        return view('doctor.requests.create', compact('patients', 'doctor'));
    }

    /**
     * API untuk search pasien (untuk AJAX)
     */
    public function searchPatients(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

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
    }

    /**
     * Simpan permintaan akses baru
     */
    public function storeAccessRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,idpatient'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $doctor = Auth::user()->doctor;

        $existingRequest = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('patient_id', $request->patient_id)
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
        }

        AccessRequest::create([
            'doctor_id' => $doctor->iddoctor,
            'patient_id' => $request->patient_id,
            'status' => 'pending',
            'requested_at' => now()
        ]);

        return redirect()->route('doctor.access-requests')
            ->with('success', 'Permintaan akses berhasil dikirim');
    }

    /**
     * Request akses untuk pasien tertentu
     */
    public function requestAccess($patientId)
    {
        $doctor = Auth::user()->doctor;

        $existingRequest = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('patient_id', $patientId)
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Permintaan akses untuk pasien ini sudah ada');
        }

        AccessRequest::create([
            'doctor_id' => $doctor->iddoctor,
            'patient_id' => $patientId,
            'status' => 'pending',
            'requested_at' => now()
        ]);

        return redirect()->back()->with('success', 'Permintaan akses berhasil dikirim');
    }

    /**
     * Tampilkan daftar pasien yang memberikan akses
     */
    public function myPatients()
    {
        $doctor = Auth::user()->doctor;
        
        $patients = Patient::whereHas('accessRequests', function($query) use ($doctor) {
            $query->where('doctor_id', $doctor->iddoctor)
                ->where('status', 'approved');
        })->with(['user', 'accessRequests' => function($query) use ($doctor) {
            $query->where('doctor_id', $doctor->iddoctor)
                ->where('status', 'approved');
        }, 'medicalRecords' => function($query) {
            $query->whereNotExists(function($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            });
        }])->paginate(12);

        // Hitung total rekam medis yang tercatat di blockchain
        $blockchainVerified = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->whereNotNull('blockchain_hash')
            ->where('blockchain_hash', '!=', '')
            ->distinct('medicalrecord_id')
            ->count('medicalrecord_id');

        return view('doctor.patients.index', compact('patients', 'doctor', 'blockchainVerified'));
    }

    /**
     * Tampilkan semua rekam medis yang dapat diakses dokter
     */
    public function records()
    {
        $doctor = Auth::user()->doctor;
        
        $approvedPatientIds = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('status', 'approved')
            ->pluck('patient_id')
            ->toArray();

        $baseQuery = MedicalRecord::where('doctor_id', $doctor->iddoctor)
            ->whereIn('patient_id', $approvedPatientIds)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('medical_records as mr2')
                    ->whereColumn('mr2.previous_id', 'medical_records.idmedicalrecord');
            });

        $totalAll = (clone $baseQuery)->count();
        $totalDraft = (clone $baseQuery)->where('status', 'draft')->count();
        $totalFinal = (clone $baseQuery)->where('status', 'final')->count();

        $query = (clone $baseQuery)
            ->with(['patient.user', 'doctor.user', 'admin', 'prescriptions', 'auditTrails' => function($q) {
                $q->whereNotNull('blockchain_hash')
                  ->where('blockchain_hash', '!=', '')
                  ->orderBy('timestamp', 'desc')
                  ->limit(1);
            }])
            ->orderBy('visit_date', 'desc');

        if (request('status') && request('status') !== 'all') {
            $query->where('medical_records.status', request('status'));
        }

        $records = $query->paginate(10);

        return view('doctor.records.index', compact('records', 'doctor', 'totalAll', 'totalDraft', 'totalFinal'));
    }

    /**
     * Tampilkan rekam medis untuk pasien tertentu
     */
    public function patientRecords($patientId)
    {
        $doctor = Auth::user()->doctor;
        
        $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('patient_id', $patientId)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
        }

        $patient = Patient::with('user')->find($patientId);
        
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
    }

    /**
     * Tampilkan form untuk membuat rekam medis baru
     */
    public function createRecord($patientId)
    {
        $doctor = Auth::user()->doctor;
        
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
    }

    /**
     * Simpan rekam medis baru
     */
    public function storeRecord(Request $request, $patientId)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,idadmin',
            'visit_date' => 'required|date',
            'blood_pressure' => 'nullable|string|max:45',
            'heart_rate' => 'nullable|integer|min:30|max:250',
            'temperature' => 'nullable|numeric|between:30.0,45.0',
            'respiratory_rate' => 'nullable|integer|min:5|max:60',
            'chief_complaint' => 'nullable|string',
            'history_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'diagnosis_code' => 'required|string|max:45',
            'diagnosis_desc' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,final,immutable',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doctor = Auth::user()->doctor;

        $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('patient_id', $patientId)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pasien ini');
        }

        DB::beginTransaction();

        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patientId,
            'admin_id' => $request->admin_id,
            'doctor_id' => $doctor->iddoctor,
            'visit_date' => $request->visit_date,
            'blood_pressure' => $request->blood_pressure ?? '',
            'heart_rate' => $request->heart_rate ?? 0,
            'temperature' => $request->temperature ?? 0,
            'respiratory_rate' => $request->respiratory_rate ?? 0,
            'chief_complaint' => $request->chief_complaint,
            'history_present_illness' => $request->history_present_illness,
            'physical_examination' => $request->physical_examination,
            'diagnosis_code' => $request->diagnosis_code,
            'diagnosis_desc' => $request->diagnosis_desc,
            'treatment' => $request->treatment,
            'notes' => $request->notes ?? '',
            'status' => $request->status,
            'version' => 1
        ]);

        foreach ($request->prescriptions as $prescriptionData) {
            $prescription = Prescription::create([
                'medicalrecord_id' => $medicalRecord->idmedicalrecord,
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

        $blockchainResult = null;
        if ($request->status === 'final') {
            
            $blockchainResult = $this->sendToBlockchain($medicalRecord);
        }

        // Simpan audit trail dengan blockchain hash
        $auditData = [
            'doctor_id' => $doctor->iddoctor,
            'patient_id' => $patientId,
            'medicalrecord_id' => $medicalRecord->idmedicalrecord,
            'action' => 'create',
            'timestamp' => now(),
            'blockchain_hash' => $blockchainResult['hash'] ?? null
        ];
        
        AuditTrail::create($auditData);

        DB::commit();

        if ($request->status === 'final') {
            if ($blockchainResult && $blockchainResult['success']) {
                return redirect()->route('doctor.patient-records', $patientId)
                    ->with('success', 'Rekam medis berhasil disimpan dengan status Final dan tercatat di blockchain.');
            } else {
                return redirect()->route('doctor.patient-records', $patientId)
                    ->with('warning', 'Rekam medis berhasil disimpan dengan status Final, namun gagal mengirim ke blockchain. Silakan coba finalisasi ulang.');
            }
        }

        return redirect()->route('doctor.patient-records', $patientId)
            ->with('success', 'Rekam medis dan resep berhasil disimpan dengan status: ' . ucfirst($request->status));
    }

    /**
     * Tampilkan detail rekam medis
     */
    public function showRecord($recordId)
    {
        $doctor = Auth::user()->doctor;
        
        $record = MedicalRecord::with([
            'patient.user', 
            'doctor.user', 
            'admin',
            'prescriptions.prescriptionItems'
        ])->find($recordId);

        $hasAccess = AccessRequest::where('doctor_id', $doctor->iddoctor)
            ->where('patient_id', $record->patient_id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rekam medis ini');
        }

        $existingViewAudit = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->where('medicalrecord_id', $recordId)
            ->where('action', 'view')
            ->first();

        if ($existingViewAudit) {
            $existingViewAudit->update(['timestamp' => now()]);
        } else {
            AuditTrail::create([
                'doctor_id' => $doctor->iddoctor,
                'patient_id' => $record->patient_id,
                'medicalrecord_id' => $recordId,
                'action' => 'view',
                'timestamp' => now(),
            ]);
        }

        return view('doctor.records.show', compact('record', 'doctor'));
    }

    /**
     * Tampilkan form edit rekam medis (tanpa versioning)
     */
    public function showEditForm($recordId)
    {
        $doctor = Auth::user()->doctor;
        
        $record = MedicalRecord::with(['patient.user', 'admin', 'prescriptions.prescriptionItems'])
            ->find($recordId);

        if ($record->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        $hospitals = $doctor->admins;
        
        if ($record->status === 'final') {
            return view('doctor.records.edit', compact('record', 'doctor', 'hospitals'))
                ->with('info', 'Anda akan membuat versi baru (v' . ($record->version + 1) . ') saat menyimpan. Versi lama tetap tersimpan.');
        }

        return view('doctor.records.edit', compact('record', 'doctor', 'hospitals'));
    }

    /**
     * Update rekam medis (handle versioning untuk record FINAL)
     */
    public function updateRecord(Request $request, $recordId)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,idadmin',
            'visit_date' => 'required|date',
            'blood_pressure' => 'nullable|string|max:45',
            'heart_rate' => 'nullable|integer|min:30|max:250',
            'temperature' => 'nullable|numeric|between:30.0,45.0',
            'respiratory_rate' => 'nullable|integer|min:5|max:60',
            'chief_complaint' => 'nullable|string',
            'history_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'diagnosis_code' => 'required|string|max:45',
            'diagnosis_desc' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'save_action' => 'required|in:draft,final',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doctor = Auth::user()->doctor;
        $originalRecord = MedicalRecord::find($recordId);

        if ($originalRecord->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        DB::beginTransaction();

        if ($originalRecord->status === 'final') {
            $record = new MedicalRecord();
            $record->patient_id = $originalRecord->patient_id;
            $record->doctor_id = $originalRecord->doctor_id;
            $record->admin_id = $request->admin_id;
            $record->visit_date = $request->visit_date;
            $record->blood_pressure = $request->blood_pressure ?? '';
            $record->heart_rate = $request->heart_rate ?? 0;
            $record->temperature = $request->temperature ?? 0;
            $record->respiratory_rate = $request->respiratory_rate ?? 0;
            $record->chief_complaint = $request->chief_complaint;
            $record->history_present_illness = $request->history_present_illness;
            $record->physical_examination = $request->physical_examination;
            $record->diagnosis_code = $request->diagnosis_code;
            $record->diagnosis_desc = $request->diagnosis_desc;
            $record->treatment = $request->treatment;
            $record->notes = $request->notes ?? '';
            $record->status = $request->save_action;
            $record->version = $originalRecord->version + 1;
            $record->previous_id = $originalRecord->idmedicalrecord;
            $record->save();
        } else {
            $record = $originalRecord;
            $record->update([
                'admin_id' => $request->admin_id,
                'visit_date' => $request->visit_date,
                'blood_pressure' => $request->blood_pressure ?? '',
                'heart_rate' => $request->heart_rate ?? 0,
                'temperature' => $request->temperature ?? 0,
                'respiratory_rate' => $request->respiratory_rate ?? 0,
                'chief_complaint' => $request->chief_complaint,
                'history_present_illness' => $request->history_present_illness,
                'physical_examination' => $request->physical_examination,
                'diagnosis_code' => $request->diagnosis_code,
                'diagnosis_desc' => $request->diagnosis_desc,
                'treatment' => $request->treatment,
                'notes' => $request->notes ?? '',
                'status' => $request->save_action,
                'updated_at' => now()
            ]);
            
            foreach ($record->prescriptions as $oldPrescription) {
                $oldPrescription->prescriptionItems()->delete();
                $oldPrescription->delete();
            }
        }

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

        $blockchainResult = null;
        if ($request->save_action === 'final') {
            if ($originalRecord->status === 'final' && $record->previous_id) {
                $blockchainResult = $this->sendVersionToBlockchain($record);
            } else {
                $blockchainResult = $this->sendToBlockchain($record);
            }
        }

        AuditTrail::create([
            'doctor_id' => $doctor->iddoctor,
            'patient_id' => $record->patient_id,
            'medicalrecord_id' => $record->idmedicalrecord,
            'action' => 'update',
            'timestamp' => now(),
            'blockchain_hash' => $blockchainResult['hash'] ?? null
        ]);

        DB::commit();

        if ($request->save_action === 'final') {
            if ($blockchainResult && $blockchainResult['success']) {
                $message = ($originalRecord->status === 'final') 
                    ? 'Rekam medis berhasil diperbarui dan versi baru tercatat di blockchain.'
                    : 'Rekam medis berhasil difinalisasi dan tercatat di blockchain.';
                return redirect()->route('doctor.show-record', $record->idmedicalrecord)
                    ->with('success', $message);
            } else {
                $message = ($originalRecord->status === 'final')
                    ? 'Rekam medis berhasil disimpan, namun gagal mencatat versi baru di blockchain.'
                    : 'Rekam medis berhasil difinalisasi, namun gagal mengirim ke blockchain.';
                return redirect()->route('doctor.show-record', $record->idmedicalrecord)
                    ->with('warning', $message);
            }
        }

        return redirect()->route('doctor.show-record', $record->idmedicalrecord)
            ->with('success', 'Perubahan berhasil disimpan sebagai draft');
    }

    /**
     * Finalisasi rekam medis (ubah status dari draft ke final)
     */
    public function finalizeRecord($recordId)
    {
        $doctor = Auth::user()->doctor;
        $record = MedicalRecord::find($recordId);

        if ($record->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        if ($record->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya rekam medis dengan status draft yang dapat difinalisasi');
        }

        $record->update([
            'status' => 'final',
            'updated_at' => now()
        ]);

        // Kirim ke blockchain
        $blockchainResult = $this->sendToBlockchain($record);
        
        // Simpan audit trail dengan blockchain hash
        AuditTrail::create([
            'doctor_id' => $doctor->iddoctor,
            'patient_id' => $record->patient_id,
            'medicalrecord_id' => $record->idmedicalrecord,
            'action' => 'update',
            'timestamp' => now(),
            'blockchain_hash' => $blockchainResult['hash'] ?? null
        ]);

        if ($blockchainResult['success']) {
            return redirect()->back()->with('success', 'Rekam medis berhasil difinalisasi dan tercatat di blockchain.');
        } else {
            return redirect()->back()->with('error', 'Rekam medis berhasil difinalisasi, namun gagal mengirim ke blockchain. Silakan coba lagi.');
        }
    }

    /**
     * Edit draft rekam medis (khusus untuk status draft)
     */
    public function editDraft($recordId)
    {
        $doctor = Auth::user()->doctor;

        $record = MedicalRecord::with(['patient.user', 'admin', 'prescriptions.prescriptionItems'])
            ->find($recordId);

        if ($record->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        if ($record->status !== 'draft') {
            return redirect()->route('doctor.edit-record', $recordId)
                ->with('info', 'Rekam medis final akan membuat versi baru. Gunakan halaman edit biasa.');
        }

        $hospitals = $doctor->admins()->get();

        return view('doctor.records.edit-draft', compact('record', 'doctor', 'hospitals'));
    }

    /**
     * Update draft rekam medis (khusus untuk status draft)
     */
    public function updateDraft(Request $request, $recordId)
    {
        $validator = Validator::make($request->all(), [
            'visit_date' => 'required|date',
            'admin_id' => 'required|exists:admins,idadmin',
            'chief_complaint' => 'nullable|string',
            'history_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'diagnosis_code' => 'nullable|string|max:50',
            'diagnosis_desc' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doctor = Auth::user()->doctor;
        $record = MedicalRecord::find($recordId);

        if ($record->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        if ($record->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya rekam medis draft yang dapat diedit dengan cara ini');
        }

        DB::beginTransaction();

        $record->update([
            'visit_date' => $request->visit_date,
            'admin_id' => $request->admin_id,
            'chief_complaint' => $request->chief_complaint,
            'history_present_illness' => $request->history_present_illness,
            'physical_examination' => $request->physical_examination,
            'diagnosis_code' => $request->diagnosis_code,
            'diagnosis_desc' => $request->diagnosis_desc,
            'treatment' => $request->treatment,
            'notes' => $request->notes,
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'updated_at' => now(),
        ]);

        foreach ($record->prescriptions as $oldPrescription) {
            $oldPrescription->prescriptionItems()->delete();
            $oldPrescription->delete();
        }

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

        DB::commit();

        return redirect()->route('doctor.records')->with('success', 'Draft rekam medis berhasil diperbarui');
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doctor = Auth::user()->doctor;
        $record = MedicalRecord::with(['patient.user', 'doctor.user', 'admin', 'prescriptions'])->find($recordId);

        if ($record->doctor_id !== $doctor->iddoctor) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah rekam medis ini');
        }

        if ($record->status === 'immutable') {
            return redirect()->back()->with('error', 'Rekam medis immutable tidak dapat diubah');
        }

        $oldStatus = $record->status;
        $newStatus = $request->status;

        $validTransitions = [
            'draft' => ['final', 'immutable'],
            'final' => ['immutable']
        ];

        if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
            return redirect()->back()->with('error', 'Transisi status dari ' . ucfirst($oldStatus) . ' ke ' . ucfirst($newStatus) . ' tidak diizinkan');
        }

        $record->update(['status' => $newStatus]);

        $message = match($newStatus) {
            'final' => 'Rekam medis berhasil difinalisasi',
            'immutable' => 'Rekam medis berhasil dibuat immutable (tidak dapat diubah)',
            default => 'Status rekam medis berhasil diupdate'
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Tampilkan audit trail untuk dokter
     */
    public function auditTrail(Request $request)
    {
        $doctor = Auth::user()->doctor;
        
        $query = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->with(['patient.user', 'medicalRecord']);

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

        $totalAudits = AuditTrail::where('doctor_id', $doctor->iddoctor)->count();
        $uniquePatients = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->whereNotNull('patient_id')
            ->distinct('patient_id')
            ->count('patient_id');
        $recordsCreated = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->where('action', 'create')
            ->count();
        $blockchainVerified = AuditTrail::where('doctor_id', $doctor->iddoctor)
            ->whereNotNull('blockchain_hash')
            ->where('blockchain_hash', '!=', '')
            ->where('blockchain_hash', 'not like', 'dummy_%')
            ->count();

        return view('doctor.audit.index', compact(
            'auditTrails', 
            'doctor', 
            'totalAudits', 
            'uniquePatients', 
            'recordsCreated', 
            'blockchainVerified'
        ));
    }

    /**
     * Tampilkan pengaturan akun dokter
     */
    public function settings()
    {
        $doctor = Auth::user()->doctor;
        $doctor->load(['user', 'admins']);

        return view('doctor.settings.index', compact('doctor'));
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doctor = Auth::user()->doctor;
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai');
        }

        $user->update(['password' => bcrypt($request->password)]);

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }

    /**
     * Helper method untuk logging audit trail
     */
    private function logAuditTrail($doctorId, $patientId, $medicalRecordId, $action)
    {
        AuditTrail::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'medicalrecord_id' => $medicalRecordId,
            'action' => $action,
            'timestamp' => now(),
            'blockchain_hash' => null
        ]);
    }

    /**
     * Kirim hash rekam medis ke blockchain via REST API Node.js
     * 
     * @param MedicalRecord $record
     * @return array ['success' => bool, 'hash' => string|null]
     */
    private function sendToBlockchain($record)
    {
        try {
            $recordData = MedicalRecord::with(['patient.user', 'doctor.user', 'admin', 'prescriptions.prescriptionItems'])
                ->find($record->idmedicalrecord);

            $json = json_encode($recordData->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // Hitung hash SHA-256 dari data JSON
            $hash = hash('sha256', $json);

            $payload = [
                'idmedicalrecord' => $record->idmedicalrecord,
                'patient_id' => $record->patient_id,
                'doctor_id' => $record->doctor_id,
                'status' => $record->status,
                'hash' => $hash
            ];

            // Kirim POST request ke Node.js blockchain API
            $response = Http::timeout(10)->post('http://172.25.117.62:3000/api/medical-records', $payload);

            if ($response->successful()) {
                Log::info('✅ Rekam medis dikirim ke blockchain: ' . $record->idmedicalrecord, [
                    'hash' => $hash,
                    'patient_id' => $record->patient_id,
                    'doctor_id' => $record->doctor_id
                ]);

                return [
                    'success' => true,
                    'hash' => $hash,
                    'response' => $response->json()
                ];
            } else {
                Log::error('❌ Gagal kirim ke blockchain: ' . $response->body(), [
                    'status' => $response->status(),
                    'record_id' => $record->idmedicalrecord
                ]);

                return [
                    'success' => false,
                    'hash' => null,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('🚨 Error blockchain: ' . $e->getMessage(), [
                'record_id' => $record->idmedicalrecord,
                'exception' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'hash' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim versi baru rekam medis ke blockchain via REST API Node.js
     * 
     * @param MedicalRecord $record
     * @return array ['success' => bool, 'hash' => string|null]
     */
    private function sendVersionToBlockchain($record)
    {
        try {
            $recordData = MedicalRecord::with(['patient.user', 'doctor.user', 'admin', 'prescriptions.prescriptionItems'])
                ->find($record->idmedicalrecord);

            $json = json_encode($recordData->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $hash = hash('sha256', $json);

            $payload = [
                'new_id' => $record->idmedicalrecord,
                'previous_id' => $record->previous_id,
                'patient_id' => $record->patient_id,
                'doctor_id' => $record->doctor_id,
                'status' => $record->status,
                'hash' => $hash,
                'version' => $record->version
            ];

            $response = Http::timeout(10)->post('http://localhost:3000/api/medical-records/version', $payload);

            if ($response->successful() && $response->json('success') === true) {
                return [
                    'success' => true,
                    'hash' => $hash,
                    'response' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'hash' => null,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'hash' => null,
                'error' => $e->getMessage()
            ];
        }
    }
}







