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
        
        // Dokter aktif (deleted_at = null di pivot)
        $adminDoctors = $admin->doctors()->with('user')->get();
        
        // Dokter yang di-soft delete (deleted_at != null di pivot)
        $deletedDoctors = $admin->doctorsOnlyTrashed()->with('user')->get();
        
        // Dokter yang tersedia untuk dropdown (belum terdaftar ATAU sudah di-soft delete)
        // Gabungkan dokter baru + dokter yang sudah di-remove
        $availableDoctors = Doctor::with('user')
            ->whereDoesntHave('adminsWithTrashed', function($query) use ($admin) {
                $query->where('admins.idadmin', $admin->idadmin);
            })
            ->get()
            ->merge($deletedDoctors) // Tambahkan dokter yang di-soft delete
            ->sortBy('user.name'); // Sort berdasarkan nama

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

        // Cek apakah dokter pernah terdaftar (termasuk yang soft deleted)
        $existingPivot = DB::table('doctors_admins')
            ->where('admin_id', $admin->idadmin)
            ->where('doctor_id', $doctor->iddoctor)
            ->first();

        if ($existingPivot) {
            // Jika sudah ada tapi deleted, restore
            if ($existingPivot->deleted_at) {
                DB::table('doctors_admins')
                    ->where('admin_id', $admin->idadmin)
                    ->where('doctor_id', $doctor->iddoctor)
                    ->update(['deleted_at' => null, 'updated_at' => now()]);
                
                return redirect()->back()->with('success', 'Dokter berhasil dikembalikan ke admin.');
            } else {
                // Sudah aktif
                return redirect()->back()->with('error', 'Dokter sudah terdaftar di admin ini.');
            }
        } else {
            // Belum pernah terdaftar, attach baru
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
        
        // Update pivot table dengan deleted_at
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
        
        // Set deleted_at ke null untuk restore
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

        // Get patients who have medical records in this admin
        $patients = Patient::with('user')
            ->whereHas('medicalRecords', function($query) use ($admin) {
                $query->where('admin_id', $admin->idadmin);
            })
            ->paginate(10);

        // Calculate total patients count
        $totalPatients = Patient::whereHas('medicalRecords', function($query) use ($admin) {
                $query->where('admin_id', $admin->idadmin);
            })->count();

        // Calculate active patients this month (patients with visits this month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $activePatientsThisMonth = Patient::whereHas('medicalRecords', function($query) use ($admin, $currentMonth, $currentYear) {
                $query->where('admin_id', $admin->idadmin)
                      ->whereMonth('visit_date', $currentMonth)
                      ->whereYear('visit_date', $currentYear);
            })->distinct()->count();

        // Get last visit date from medical records
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

    /**
     * Show medical records management page
     */
    public function records()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        // Get medical records created in this admin (metadata only)
        $records = MedicalRecord::with(['patient.user', 'doctor.user'])
            ->where('admin_id', $admin->idadmin)
            ->orderBy('visit_date', 'desc')
            ->paginate(15);

        return view('admin.records.index', compact('records', 'admin'));
    }

    /**
     * Show audit trail page
     */
    public function audit()
    {
        $user = Auth::user();
        $admin = $user->admin;
        
        if (!$admin) {
            return view('admin.audit.index', compact('admin'))
                ->with('error', 'Admin not found.');
        }
        
        // Base query for audit logs
        $query = \App\Models\AuditTrail::with(['patient', 'doctor.user', 'medicalRecord.patient.user'])
            ->whereHas('medicalRecord', function($subQuery) use ($admin) {
                $subQuery->where('admin_id', $admin->idadmin);
            });

        // Apply filters if provided
        if (request('action')) {
            $query->where('action', request('action'));
        }

        if (request('date_from')) {
            $query->whereDate('timestamp', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('timestamp', '<=', request('date_to'));
        }

        // Get paginated results
        $auditLogs = $query->orderBy('timestamp', 'desc')->paginate(20);

        // Calculate statistics
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