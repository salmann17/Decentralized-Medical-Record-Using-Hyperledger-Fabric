<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Hospital;
use App\Models\User;
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
        
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $doctorsCount = $hospital ? $hospital->doctors()->count() : 0;
        $patientsCount = Patient::count(); 
        $medicalRecordsCount = $hospital ? $hospital->medicalRecords()->count() : 0;
        
        $recentDoctors = $hospital ? $hospital->doctors()->with('user')->latest('doctor_hospital.created_at')->take(5)->get() : collect();

        return view('admin.dashboard', compact(
            'hospital',
            'doctorsCount',
            'patientsCount', 
            'medicalRecordsCount',
            'recentDoctors'
        ));
    }

    /**
     * Show doctors management page
     */
    public function doctors()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $hospitalDoctors = $hospital ? $hospital->doctors()->with('user')->get() : collect();
        
        $availableDoctors = Doctor::with('user')
            ->whereDoesntHave('hospitals', function($query) use ($hospital) {
                $query->where('hospitals.hospital_id', $hospital->hospital_id);
            })
            ->get();

        return view('admin.doctors.index', compact('hospital', 'hospitalDoctors', 'availableDoctors'));
    }

    /**
     * Assign doctor to hospital
     */
    public function assignDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,doctor_id'
        ]);

        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        $doctor = Doctor::find($request->doctor_id);

        // Check if already assigned
        if (!$hospital->doctors()->where('doctors.doctor_id', $doctor->doctor_id)->exists()) {
            $hospital->doctors()->attach($doctor->doctor_id);
            return redirect()->back()->with('success', 'Dokter berhasil ditambahkan ke rumah sakit.');
        }

        return redirect()->back()->with('error', 'Dokter sudah terdaftar di rumah sakit ini.');
    }

    /**
     * Remove doctor from hospital
     */
    public function removeDoctor(Request $request, $doctorId)
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        $hospital->doctors()->detach($doctorId);
        
        return redirect()->back()->with('success', 'Dokter berhasil dihapus dari rumah sakit.');
    }

    /**
     * Show patients management page
     */
    public function patients()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        if (!$hospital) {
            return view('admin.patients.index', compact('hospital'))
                ->with('error', 'Hospital not found.');
        }

        // Get patients who have medical records in this hospital
        $patients = Patient::with('user')
            ->whereHas('medicalRecords', function($query) use ($hospital) {
                $query->where('hospital_id', $hospital->hospital_id);
            })
            ->paginate(10);

        // Calculate total patients count
        $totalPatients = Patient::whereHas('medicalRecords', function($query) use ($hospital) {
                $query->where('hospital_id', $hospital->hospital_id);
            })->count();

        // Calculate active patients this month (patients with visits this month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $activePatientsThisMonth = Patient::whereHas('medicalRecords', function($query) use ($hospital, $currentMonth, $currentYear) {
                $query->where('hospital_id', $hospital->hospital_id)
                      ->whereMonth('visit_date', $currentMonth)
                      ->whereYear('visit_date', $currentYear);
            })->distinct()->count();

        // Get last visit date from medical records
        $lastVisitRecord = MedicalRecord::where('hospital_id', $hospital->hospital_id)
            ->orderBy('visit_date', 'desc')
            ->first();
            
        $lastVisitDate = $lastVisitRecord ? $lastVisitRecord->visit_date : null;

        return view('admin.patients.index', compact(
            'patients', 
            'hospital', 
            'totalPatients',
            'activePatientsThisMonth',
            'lastVisitDate'
        ));
    }

    /**
     * Show medical records management page
     */
    public function records()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        // Get medical records created in this hospital (metadata only)
        $records = MedicalRecord::with(['patient.user', 'doctor.user'])
            ->where('hospital_id', $hospital->hospital_id)
            ->orderBy('visit_date', 'desc')
            ->paginate(15);

        return view('admin.records.index', compact('records', 'hospital'));
    }

    /**
     * Show audit trail page
     */
    public function audit()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();
        
        // Get audit trail for this hospital
        $auditLogs = \App\Models\AuditTrail::with(['user', 'medicalRecord.patient.user'])
            ->whereHas('medicalRecord', function($query) use ($hospital) {
                $query->where('hospital_id', $hospital->hospital_id);
            })
            ->orderBy('access_time', 'desc')
            ->paginate(20);

        return view('admin.audit.index', compact('auditLogs', 'hospital'));
    }

    /**
     * Show hospital settings page
     */
    public function settings()
    {
        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();

        return view('admin.settings.index', compact('hospital'));
    }

    /**
     * Update hospital settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'address' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $hospital = Hospital::where('hospital_id', $user->idusers)->first();

        $hospital->update([
            'name' => $request->name,
            'type' => $request->type,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Pengaturan rumah sakit berhasil diperbarui.');
    }
}