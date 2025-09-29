@extends('layouts.app')

@section('title', 'Permintaan Akses - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Permintaan Akses Rekam Medis</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Kelola permintaan akses dari dokter ke rekam medis Anda. Anda memiliki kontrol penuh untuk menyetujui atau menolak setiap permintaan.
        </p>
    </div>

    <!-- Statistics Cards -->
    @php
        // Get all requests for counting (tidak difilter)
        $allRequests = App\Models\AccessRequest::where('patient_id', $patient->patient_id)->get();
        $pendingCount = $allRequests->where('status', 'pending')->count();
        $approvedCount = $allRequests->where('status', 'approved')->count();
        $rejectedCount = $allRequests->where('status', 'rejected')->count();
        $currentStatus = request('status');
    @endphp
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Permintaan Menunggu</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-orange-600">{{ $pendingCount }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Permintaan Disetujui</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $approvedCount }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Permintaan Ditolak</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">{{ $rejectedCount }}</dd>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex" aria-label="Tabs">
                <a href="{{ route('patient.access-requests', ['status' => 'pending']) }}" 
                   class="whitespace-nowrap border-b-2 py-4 px-6 text-sm font-medium {{ $currentStatus === 'pending' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Menunggu ({{ $pendingCount }})
                </a>
                <a href="{{ route('patient.access-requests', ['status' => 'approved']) }}" 
                   class="whitespace-nowrap border-b-2 py-4 px-6 text-sm font-medium {{ $currentStatus === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('patient.access-requests', ['status' => 'rejected']) }}" 
                   class="whitespace-nowrap border-b-2 py-4 px-6 text-sm font-medium {{ $currentStatus === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
                <a href="{{ route('patient.access-requests') }}" 
                   class="whitespace-nowrap border-b-2 py-4 px-6 text-sm font-medium {{ !$currentStatus ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Semua ({{ $pendingCount + $approvedCount + $rejectedCount }})
                </a>
            </nav>
        </div>
    </div>

    <!-- Access Requests List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if(isset($requests) && $requests->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($requests ?? [] as $request)
            <li class="px-4 py-6 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">
                                    {{ substr($request->doctor->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $request->doctor->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @if($request->doctor->specialization)
                                        <span class="font-medium">{{ $request->doctor->specialization }}</span> •
                                        @endif
                                        @if($request->doctor->hospitals && $request->doctor->hospitals->count() > 0)
                                        {{ $request->doctor->hospitals->first()->name }}
                                        @endif
                                    </div>
                                    @if($request->doctor->license_number)
                                    <div class="text-xs text-gray-400 mt-1">
                                        SIP: {{ $request->doctor->license_number }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2">
                                @if($request->reason)
                                <div class="text-sm text-gray-600 bg-gray-50 rounded-md p-3">
                                    <span class="font-medium">Alasan permintaan:</span><br>
                                    {{ $request->reason }}
                                </div>
                                @endif
                                <div class="text-xs text-gray-400 mt-2 flex items-center">
                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Diminta {{ \Carbon\Carbon::parse($request->requested_at)->diffForHumans() }}
                                    @if($request->responded_at)
                                    • Ditanggapi {{ \Carbon\Carbon::parse($request->responded_at)->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex flex-col items-end">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                @if($request->status === 'pending')
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Menunggu
                                @elseif($request->status === 'approved')
                                    <svg class="-ml-0.5 mr-1.5 h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Disetujui
                                @else
                                    <svg class="-ml-0.5 mr-1.5 h-3 w-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Ditolak
                                @endif
                            </span>
                        </div>
                        
                        @if($request->status === 'pending')
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('patient.access-requests.approve', $request->request_id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui permintaan akses ini?')"
                                        class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('patient.access-requests.reject', $request->request_id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin menolak permintaan akses ini?')"
                                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        @if(isset($requests) && $requests->hasPages())
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            {{ $requests->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @switch(request('status', 'pending'))
                    @case('approved')
                        Belum ada permintaan yang disetujui
                        @break
                    @case('rejected')
                        Belum ada permintaan yang ditolak
                        @break
                    @case('pending')
                        Tidak ada permintaan akses baru
                        @break
                    @default
                        Belum ada permintaan akses
                @endswitch
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @switch(request('status', 'pending'))
                    @case('approved')
                        Permintaan akses yang Anda setujui akan muncul di sini.
                        @break
                    @case('rejected')
                        Permintaan akses yang Anda tolak akan muncul di sini.
                        @break
                    @case('pending')
                        Permintaan akses baru dari dokter akan muncul di sini untuk Anda tinjau.
                        @break
                    @default
                        Semua permintaan akses dari dokter akan muncul di sini.
                @endswitch
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
                <h3 class="text-sm font-medium text-blue-800">Tentang Kontrol Akses</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Anda memiliki kontrol penuh untuk menyetujui atau menolak setiap permintaan akses</li>
                        <li>Dokter hanya dapat mengakses rekam medis yang telah Anda setujui</li>
                        <li>Semua aktivitas akses tercatat dalam audit trail untuk transparansi</li>
                        <li>Anda dapat mencabut akses kapan saja melalui menu "Dokter dengan Akses"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection