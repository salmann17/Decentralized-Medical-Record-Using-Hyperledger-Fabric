@extends('layouts.app')

@section('title', 'Audit Trail - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Audit Trail</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Pantau semua aktivitas yang terkait dengan rekam medis Anda. Setiap akses, perubahan, dan tindakan tercatat untuk transparansi dan keamanan.
        </p>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalAudits = $auditTrails->count();
        $todayAudits = $auditTrails->whereDate('action_timestamp', now()->toDateString())->count();
        $weekAudits = $auditTrails->where('action_timestamp', '>=', now()->subWeek())->count();
    @endphp
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Aktivitas</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $totalAudits }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Aktivitas Hari Ini</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $todayAudits }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Aktivitas 7 Hari Terakhir</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-orange-600">{{ $weekAudits }}</dd>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('patient.audit-trail') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div class="sm:col-span-1">
                        <label for="search" class="block text-sm font-medium text-gray-700">Pencarian</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Cari aktivitas..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <!-- Action Filter -->
                    <div class="sm:col-span-1">
                        <label for="action" class="block text-sm font-medium text-gray-700">Jenis Aktivitas</label>
                        <select name="action" id="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Aktivitas</option>
                            <option value="view" {{ request('action') === 'view' ? 'selected' : '' }}>Melihat Rekam Medis</option>
                            <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Membuat Data</option>
                            <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Mengubah Data</option>
                            <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Menghapus Data</option>
                            <option value="access_granted" {{ request('action') === 'access_granted' ? 'selected' : '' }}>Akses Diberikan</option>
                            <option value="access_revoked" {{ request('action') === 'access_revoked' ? 'selected' : '' }}>Akses Dicabut</option>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="sm:col-span-1">
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                        <input type="date" 
                               name="date_from" 
                               id="date_from"
                               value="{{ request('date_from') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div class="sm:col-span-1">
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                        <input type="date" 
                               name="date_to" 
                               id="date_to"
                               value="{{ request('date_to') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'action', 'date_from', 'date_to']))
                        <a href="{{ route('patient.audit-trail') }}" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset
                        </a>
                        @endif
                    </div>
                    
                    <!-- Export Button -->
                    <button type="button" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Trail List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($auditTrails->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($auditTrails as $audit)
            <li class="px-4 py-6 sm:px-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 min-w-0 flex-1">
                        <!-- Action Icon -->
                        <div class="flex-shrink-0">
                            @php
                                $iconClass = match($audit->action) {
                                    'view' => 'bg-blue-100 text-blue-600',
                                    'create' => 'bg-green-100 text-green-600',
                                    'update' => 'bg-yellow-100 text-yellow-600',
                                    'delete' => 'bg-red-100 text-red-600',
                                    'access_granted' => 'bg-emerald-100 text-emerald-600',
                                    'access_revoked' => 'bg-orange-100 text-orange-600',
                                    default => 'bg-gray-100 text-gray-600'
                                };
                            @endphp
                            <div class="h-10 w-10 rounded-full {{ $iconClass }} flex items-center justify-center">
                                @switch($audit->action)
                                    @case('view')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        @break
                                    @case('create')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        @break
                                    @case('update')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        @break
                                    @case('delete')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        @break
                                    @case('access_granted')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @break
                                    @case('access_revoked')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                @endswitch
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        @switch($audit->action)
                                            @case('view')
                                                {{ $audit->user->name ?? 'Unknown User' }} melihat rekam medis
                                                @break
                                            @case('create')
                                                {{ $audit->user->name ?? 'Unknown User' }} membuat data baru
                                                @break
                                            @case('update')
                                                {{ $audit->user->name ?? 'Unknown User' }} mengubah data
                                                @break
                                            @case('delete')
                                                {{ $audit->user->name ?? 'Unknown User' }} menghapus data
                                                @break
                                            @case('access_granted')
                                                Akses diberikan kepada {{ $audit->user->name ?? 'Unknown User' }}
                                                @break
                                            @case('access_revoked')
                                                Akses dicabut dari {{ $audit->user->name ?? 'Unknown User' }}
                                                @break
                                            @default
                                                {{ $audit->user->name ?? 'Unknown User' }} melakukan aktivitas
                                        @endswitch
                                    </p>
                                    
                                    @if($audit->details)
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $audit->details }}
                                    </p>
                                    @endif
                                    
                                    <div class="mt-2 flex items-center text-xs text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($audit->action_timestamp)->format('d M Y, H:i') }}
                                        </div>
                                        
                                        @if($audit->ip_address)
                                        <div class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                            </svg>
                                            {{ $audit->ip_address }}
                                        </div>
                                        @endif
                                        
                                        @if($audit->user_agent)
                                        <div class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ Str::limit($audit->user_agent, 30) }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Details -->
                            @if($audit->record_id)
                            <div class="mt-3 bg-gray-50 rounded-md p-3">
                                <div class="flex items-center text-xs text-gray-600">
                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-medium">Record ID:</span>&nbsp;{{ $audit->record_id }}
                                    @if($audit->medicalRecord)
                                    &nbsp;({{ $audit->medicalRecord->visit_date ? \Carbon\Carbon::parse($audit->medicalRecord->visit_date)->format('d M Y') : 'Unknown Date' }})
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        @if($auditTrails->hasPages())
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            {{ $auditTrails->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request()->hasAny(['search', 'action', 'date_from', 'date_to']))
                    Tidak ada aktivitas yang sesuai filter
                @else
                    Belum ada aktivitas tercatat
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'action', 'date_from', 'date_to']))
                    Coba ubah filter pencarian untuk melihat hasil yang berbeda.
                @else
                    Semua aktivitas terkait rekam medis Anda akan tercatat di sini.
                @endif
            </p>
        </div>
        @endif
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
                        <li>Anda dapat mengekspor audit trail untuk keperluan dokumentasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection