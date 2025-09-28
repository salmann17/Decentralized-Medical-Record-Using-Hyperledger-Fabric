@extends('layouts.app')

@section('title', 'Audit Trail Dokter - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Audit Trail</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Catatan lengkap semua aktivitas dan perubahan yang Anda lakukan dalam sistem.
                </p>
            </div>
            <div class="flex space-x-3">
                <button type="button" id="exportBtn" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </button>
                <button type="button" onclick="location.reload()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Filter Aktivitas</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <!-- Date Range Filter -->
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" id="dateFrom" name="date_from" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                </div>

                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" id="dateTo" name="date_to" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>

                <!-- Action Filter -->
                <div>
                    <label for="actionFilter" class="block text-sm font-medium text-gray-700">Tipe Aktivitas</label>
                    <select id="actionFilter" name="action" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Aktivitas</option>
                        <option value="view" {{ request('action') === 'view' ? 'selected' : '' }}>Melihat Data</option>
                        <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Membuat Data</option>
                        <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Mengubah Data</option>
                        <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Menghapus Data</option>
                        <option value="access_request" {{ request('action') === 'access_request' ? 'selected' : '' }}>Permintaan Akses</option>
                        <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    </select>
                </div>

                <!-- Apply Filter Button -->
                <div class="flex items-end">
                    <button type="button" onclick="applyFilters()" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Activities -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Aktivitas</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $auditTrails->count() }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Semua aktivitas Anda</div>
                </div>
            </div>
        </div>

        <!-- Patient Access -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Akses Pasien</dt>
                            <dd class="text-lg font-semibold text-blue-900">{{ $auditTrails->whereNotNull('patient_id')->groupBy('patient_id')->count() }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Pasien yang diakses</div>
                </div>
            </div>
        </div>

        <!-- Records Created -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rekam Medis</dt>
                            <dd class="text-lg font-semibold text-green-900">{{ $auditTrails->where('action', 'create')->count() }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Rekam medis dibuat</div>
                </div>
            </div>
        </div>

        <!-- Blockchain Verified -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Blockchain</dt>
                            <dd class="text-lg font-semibold text-purple-900">{{ $auditTrails->whereNotNull('blockchain_hash')->where('blockchain_hash', '!=', '')->where('blockchain_hash', 'not like', 'dummy_%')->count() }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Terverifikasi blockchain</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Riwayat Aktivitas</h3>
            
            <!-- Activity Timeline -->
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse($auditTrails as $audit)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            
                            <div class="relative flex space-x-3">
                                <!-- Activity Icon -->
                                <div>
                                    @if($audit->action === 'view')
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </span>
                                    @elseif($audit->action === 'create')
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Activity Content -->
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    @if($audit->action === 'view')
                                                        Melihat Rekam Medis
                                                    @elseif($audit->action === 'create')
                                                        Membuat Rekam Medis
                                                    @else
                                                        {{ ucfirst($audit->action) }}
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    @if($audit->action === 'view' && $audit->medicalRecord)
                                                        Melihat rekam medis ID #{{ $audit->medicalrecord_id }}
                                                    @elseif($audit->action === 'create' && $audit->medicalRecord)
                                                        Membuat rekam medis baru untuk kunjungan #{{ $audit->medicalrecord_id }}
                                                    @else
                                                        Aktivitas {{ $audit->action }} pada sistem
                                                    @endif
                                                </p>
                                                @if($audit->patient && $audit->patient->user)
                                                    <p class="text-sm text-blue-600 mt-1">
                                                        <span class="font-medium">Pasien:</span> {{ $audit->patient->user->name }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($audit->timestamp)->diffForHumans() }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($audit->timestamp)->format('H:i:s') }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Additional Details -->
                                        <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center space-x-4">
                                                <span class="flex items-center">
                                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Berhasil
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($audit->timestamp)->format('d M Y') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Blockchain Status -->
                                            <div class="flex items-center">
                                                <svg class="h-3 w-3 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                <span class="text-blue-600">
                                                    @if($audit->blockchain_hash && $audit->blockchain_hash !== 'dummy_hash_' && !str_contains($audit->blockchain_hash, 'dummy'))
                                                        Hash: {{ substr($audit->blockchain_hash, 0, 8) }}...
                                                    @else
                                                        Pending Integration
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Aktivitas</h3>
                            <p class="mt-1 text-sm text-gray-500">Audit trail akan muncul ketika Anda melakukan aktivitas dalam sistem.</p>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>

            <!-- Load More Button -->
            <div class="mt-8 text-center">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    Muat Lebih Banyak
                </button>
            </div>
        </div>
    </div>

    <!-- Blockchain Integration Info -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-purple-800">Tentang Audit Trail Blockchain</h3>
            <div class="mt-2 text-sm text-purple-700">
                <ul class="list-disc list-inside space-y-1">
                    <li>Semua aktivitas akan dicatat secara immutable setelah blockchain terintegrasi</li>
                    <li>Hash blockchain akan memastikan integritas data audit trail</li>
                    <li>Akses audit trail dapat diverifikasi secara independen</li>
                    <li>Sistem saat ini mencatat semua aktivitas untuk persiapan blockchain</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const action = document.getElementById('actionFilter').value;
    
    // Build query string
    const params = new URLSearchParams();
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    if (action) params.append('action', action);
    
    // Redirect with filters
    window.location.href = window.location.pathname + '?' + params.toString();
}

// Export functionality
document.getElementById('exportBtn').addEventListener('click', function() {
    // In real implementation, this would call backend export endpoint
    alert('Export PDF akan tersedia setelah backend dikonfigurasi');
    
    // Placeholder for future export functionality
});

// Auto-refresh every 30 seconds
setInterval(function() {
    // Optional: Auto-refresh for real-time updates
    // location.reload();
}, 30000);
</script>
@endsection