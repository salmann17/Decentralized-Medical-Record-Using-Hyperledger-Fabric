@extends('layouts.app')

@section('title', 'Audit Trail - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Audit Trail</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Catatan lengkap semua aktivitas yang terkait dengan rekam medis Anda.
                </p>
            </div>
            <div>
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
                            <dd class="text-lg font-semibold text-gray-900">{{ $totalAudits }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Semua aktivitas tercatat</div>
                </div>
            </div>
        </div>

        <!-- Unique Doctors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Dokter Unik</dt>
                            <dd class="text-lg font-semibold text-blue-900">{{ $uniqueDoctors }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Dokter yang mengakses</div>
                </div>
            </div>
        </div>

        <!-- Records Accessed -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Akses Data</dt>
                            <dd class="text-lg font-semibold text-green-900">{{ $recordsAccessed }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Rekam medis dilihat</div>
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
                            <dd class="text-lg font-semibold text-purple-900">{{ $blockchainVerified }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-sm text-gray-500">Terverifikasi blockchain</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Filter Aktivitas</h3>
            <form method="GET" action="{{ route('patient.audit-trail') }}" id="filterForm">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Date Range Filter -->
                    <div>
                        <label for="dateFrom" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                        <input type="date" id="dateFrom" name="date_from" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ request('date_from') }}">
                    </div>

                    <div>
                        <label for="dateTo" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                        <input type="date" id="dateTo" name="date_to" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ request('date_to') }}">
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
                        </select>
                    </div>

                    <!-- Apply Filter Button -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('patient.audit-trail') }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Riwayat Aktivitas</h3>
                
                <!-- Per Page Selector -->
                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm text-gray-700">Tampilkan:</label>
                    <select id="perPage" name="per_page" onchange="changePerPage(this.value)"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-700">per halaman</span>
                </div>
            </div>
            
            <!-- Pagination Info -->
            @if($auditTrails->count() > 0)
            <div class="text-sm text-gray-700 mb-4">
                Menampilkan {{ $auditTrails->firstItem() }} sampai {{ $auditTrails->lastItem() }} dari {{ $auditTrails->total() }} aktivitas
            </div>
            @endif
            
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
                                    @elseif($audit->action === 'update')
                                        <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </span>
                                    @elseif($audit->action === 'delete')
                                        <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium text-gray-900">
                                                @if($audit->doctor && $audit->doctor->user)
                                                    {{ $audit->doctor->user->name }}
                                                @else
                                                    Sistem
                                                @endif
                                            </span>
                                            @if($audit->action === 'view')
                                                melihat rekam medis
                                            @elseif($audit->action === 'create')
                                                membuat rekam medis baru
                                            @elseif($audit->action === 'update')
                                                mengubah rekam medis
                                            @elseif($audit->action === 'delete')
                                                menghapus rekam medis
                                            @else
                                                melakukan aktivitas
                                            @endif
                                            
                                            @if($audit->medicalRecord)
                                                <span class="text-gray-500">untuk pasien {{ $patient->user->name }}</span>
                                            @endif
                                        </p>
                                        
                                        @if($audit->medicalRecord)
                                        <p class="mt-1 text-xs text-gray-500">
                                            <svg class="inline h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Record: {{ $audit->medicalRecord->diagnosis_desc ? Str::limit($audit->medicalRecord->diagnosis_desc, 50) : 'Medical Record #' . $audit->medicalrecord_id }}
                                        </p>
                                        @endif
                                        
                                        @if($audit->blockchain_hash && $audit->blockchain_hash !== '' && !str_starts_with($audit->blockchain_hash, 'dummy_'))
                                        <p class="mt-1 text-xs text-gray-500 flex items-center">
                                            <svg class="h-3 w-3 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-mono text-purple-600">Blockchain: {{ Str::limit($audit->blockchain_hash, 12) }}</span>
                                        </p>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                        <time datetime="{{ $audit->timestamp }}">
                                            {{ \Carbon\Carbon::parse($audit->timestamp)->format('d M Y') }}
                                        </time>
                                        <p class="text-xs mt-0.5">
                                            {{ \Carbon\Carbon::parse($audit->timestamp)->format('H:i') }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($audit->timestamp)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['date_from', 'date_to', 'action']))
                                Tidak ada aktivitas yang sesuai dengan filter yang dipilih.
                            @else
                                Belum ada aktivitas yang tercatat dalam sistem.
                            @endif
                        </p>
                    </li>
                    @endforelse
                </ul>
            </div>

            <!-- Pagination -->
            @if($auditTrails->hasPages())
            <div class="mt-6">
                {{ $auditTrails->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Information Card -->
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tentang Audit Trail</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Semua aktivitas yang terkait dengan rekam medis Anda tercatat secara otomatis</li>
                        <li>Informasi mencakup siapa, kapan, dan aktivitas apa yang dilakukan</li>
                        <li>Data audit tidak dapat diubah atau dihapus untuk menjaga integritas</li>
                        <li>Aktivitas yang terverifikasi blockchain ditandai dengan ikon gembok</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
@endsection