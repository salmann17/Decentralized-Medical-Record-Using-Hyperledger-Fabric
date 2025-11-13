<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        if (!$admin) {
            return redirect()->route('home')->with('error', 'Admin profile not found.');
        }
        
        $doctorsCount = $admin->doctors()->count();
        $patientsCount = Patient::count(); 
        $medicalRecordsCount = $admin->medicalRecords()->count();
        
        $recentDoctors = $admin->doctors()->with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'admin',
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
        $admin = $user->admin;
        
        if (!$admin) {
            return redirect()->route('home')->with('error', 'Admin profile not found.');
        }
        
        $adminDoctors = $admin->doctors()->with('user')->get();
        
        $deletedDoctors = $admin->doctorsOnlyTrashed()->with('user')->get();
        
        $availableDoctors = Doctor::with('user')
            ->whereDoesntHave('adminsWithTrashed', function($query) use ($admin) {
                $query->where('admins.idadmin', $admin->idadmin);
            })
            ->get()
            ->merge($deletedDoctors) 
            ->sortBy('user.name'); 

        return view('admin.doctors.index', compact('admin', 'adminDoctors', 'deletedDoctors', 'availableDoctors'));
    }

    /**
     * Assign doctor to admin
     */
    public function assignDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,iddoctor'
        ]);

        $user = Auth::user();
        $admin = $user->admin;
        $doctor = Doctor::find($request->doctor_id);

        $existingPivot = DB::table('doctors_admins')
            ->where('admin_id', $admin->idadmin)
            ->where('doctor_id', $doctor->iddoctor)
            ->first();

        if ($existingPivot) {
            if ($existingPivot->deleted_at) {
                DB::table('doctors_admins')
                    ->where('admin_id', $admin->idadmin)
                    ->where('doctor_id', $doctor->iddoctor)
                    ->update(['deleted_at' => null, 'updated_at' => now()]);
                
                return redirect()->back()->with('success', 'Dokter berhasil dikembalikan ke admin.');
            } else {
                return redirect()->back()->with('error', 'Dokter sudah terdaftar di admin ini.');
            }
        } else {
            $admin->doctors()->attach($doctor->iddoctor);
            return redirect()->back()->with('success', 'Dokter berhasil ditambahkan ke admin.');
        }
    }

    /**
     * Remove doctor from admin (Soft Delete)
     */
    public function removeDoctor(Request $request, $doctorId)
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        $admin->doctors()->updateExistingPivot($doctorId, [
            'deleted_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Dokter berhasil dinonaktifkan dari admin.');
    }

    /**
     * Restore doctor to admin
     */
    public function restoreDoctor(Request $request, $doctorId)
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        DB::table('doctors_admins')
            ->where('admin_id', $admin->idadmin)
            ->where('doctor_id', $doctorId)
            ->update(['deleted_at' => null]);
        
        return redirect()->back()->with('success', 'Dokter berhasil dikembalikan ke admin.');
    }

    /**
     * Show patients management page
     */
    public function patients()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        if (!$admin) {
            return redirect()->route('home')->with('error', 'Admin profile not found.');
        }

        $patients = Patient::with('user')
            ->whereHas('medicalRecords', function($query) use ($admin) {
                $query->where('admin_id', $admin->idadmin);
            })
            ->paginate(10);

        $totalPatients = Patient::whereHas('medicalRecords', function($query) use ($admin) {
                $query->where('admin_id', $admin->idadmin);
            })->count();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $activePatientsThisMonth = Patient::whereHas('medicalRecords', function($query) use ($admin, $currentMonth, $currentYear) {
                $query->where('admin_id', $admin->idadmin)
                      ->whereMonth('visit_date', $currentMonth)
                      ->whereYear('visit_date', $currentYear);
            })->distinct()->count();

        $lastVisitRecord = MedicalRecord::where('admin_id', $admin->idadmin)
            ->orderBy('visit_date', 'desc')
            ->first();
            
        $lastVisitDate = $lastVisitRecord ? $lastVisitRecord->visit_date : null;

        return view('admin.patients.index', compact(
            'patients', 
            'admin', 
            'totalPatients',
            'activePatientsThisMonth',
            'lastVisitDate'
        ));
    }

    public function records()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        $records = MedicalRecord::with(['patient.user', 'doctor.user', 'auditTrails' => function($q) {
                $q->whereNotNull('blockchain_hash')
                  ->where('blockchain_hash', '!=', '')
                  ->orderBy('timestamp', 'desc')
                  ->limit(1);
            }])
            ->where('admin_id', $admin->idadmin)
            ->orderBy('visit_date', 'desc')
            ->paginate(15);

        $totalRecords = MedicalRecord::where('admin_id', $admin->idadmin)->count();
        $draftRecords = MedicalRecord::where('admin_id', $admin->idadmin)->where('status', 'draft')->count();
        $finalRecords = MedicalRecord::where('admin_id', $admin->idadmin)->where('status', 'final')->count();
        
        $immutableRecords = MedicalRecord::where('admin_id', $admin->idadmin)
            ->whereHas('auditTrails', function($q) {
                $q->whereNotNull('blockchain_hash')
                  ->where('blockchain_hash', '!=', '')
                  ->where('blockchain_hash', 'NOT LIKE', 'INVALID_%')
                  ->where('blockchain_hash', 'NOT LIKE', 'NOT_FOUND_%');
            })
            ->count();

        return view('admin.records.index', compact('records', 'admin', 'totalRecords', 'draftRecords', 'finalRecords', 'immutableRecords'));
    }

    public function audit()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        if (!$admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Admin profile not found.');
        }
        
        $query = \App\Models\AuditTrail::with(['patient.user', 'doctor.user', 'medicalRecord.patient.user'])
            ->whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            });

        if (request('action')) {
            $query->where('action', request('action'));
        }

        if (request('date_from')) {
            $query->whereDate('timestamp', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('timestamp', '<=', request('date_to'));
        }

        $auditLogs = $query->orderBy('timestamp', 'desc')->paginate(20);

        $totalAccess = \App\Models\AuditTrail::whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            })->count();

        $todayAccess = \App\Models\AuditTrail::whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            })
            ->whereDate('timestamp', now()->toDateString())
            ->count();

        $viewAccess = \App\Models\AuditTrail::whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            })
            ->where('action', 'view')
            ->count();

        $createAccess = \App\Models\AuditTrail::whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            })
            ->where('action', 'create')
            ->count();

        return view('admin.audit.index', compact(
            'auditLogs', 
            'admin',
            'totalAccess',
            'todayAccess',
            'viewAccess',
            'createAccess'
        ));
    }

    /**
     * Show admin settings page
     */
    public function settings()
    {
        $user = Auth::user();
        $admin = $user->admin;

        return view('admin.settings.index', compact('admin'));
    }

    /**
     * Update admin settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'address' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $admin = $user->admin;

        $admin->update([
            'name' => $request->name,
            'type' => $request->type,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Pengaturan admin berhasil diperbarui.');
    }
}