@extends('layouts.app')

@section('title', 'Dokter dengan Akses - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dokter dengan Akses</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Daftar dokter yang memiliki akses ke rekam medis Anda. Anda dapat mencabut akses kapan saja untuk menjaga privasi data medis.
        </p>
    </div>

    <!-- Statistics Card -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Dokter dengan Akses</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ isset($doctors) ? $doctors->count() : 0 }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Rekam Medis Aktif</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $totalRecords ?? 0 }}</dd>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex-1 min-w-0">
                    <form method="GET" action="{{ route('patient.active-doctors') }}" class="flex items-center space-x-4">
                        <div class="flex-1 min-w-0">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari dokter atau rumah sakit..."
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Cari
                            </button>
                            @if(request('search'))
                            <a href="{{ route('patient.active-doctors') }}" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Doctors List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if(isset($doctors) && $doctors->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($doctors ?? [] as $doctor)
            <li class="px-4 py-6 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-lg font-semibold text-white">
                                    {{ substr($doctor->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ $doctor->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1 space-y-1">
                                        @if($doctor->specialization)
                                        <div class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-medium">{{ $doctor->specialization }}</span>
                                        </div>
                                        @endif
                                        
                                        @if($doctor->hospitals && $doctor->hospitals->count() > 0)
                                        <div class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $doctor->hospitals->first()->name }}
                                        </div>
                                        @endif

                                        @if($doctor->phone)
                                        <div class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21L6.16 10.86c-.46.235-.46.97 0 1.205l4.064 2.069a1 1 0 00.894 0l4.064-2.069c.46-.235.46-.97 0-1.205l-4.064-2.069a1 1 0 00-.894 0L6.16 10.86a1 1 0 01-.502-1.21L7.156 5.184A1 1 0 018.104 4.5H11.4a2 2 0 012 2v.09" />
                                            </svg>
                                            {{ $doctor->phone }}
                                        </div>
                                        @endif

                                        @if($doctor->license_number)
                                        <div class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs">SIP: {{ $doctor->license_number }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Access Information -->
                            <div class="mt-3">
                                @php
                                    $accessRequest = $doctor->accessRequests->where('patient_id', auth()->id())->first();
                                @endphp
                                @if($accessRequest)
                                <div class="flex items-center text-xs text-gray-500 space-x-4">
                                    <div class="flex items-center">
                                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Akses diberikan {{ \Carbon\Carbon::parse($accessRequest->response_date)->diffForHumans() }}
                                    </div>
                                    @if($accessRequest->expires_at)
                                    <div class="flex items-center">
                                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Berlaku hingga {{ \Carbon\Carbon::parse($accessRequest->expires_at)->format('d M Y') }}
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-3">
                        <!-- Access Status Badge -->
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-800">
                            <svg class="-ml-0.5 mr-1.5 h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Akses Aktif
                        </span>
                        
                        <!-- Revoke Access Button -->
                        <form method="POST" action="{{ route('patient.active-doctors.revoke', $doctor->doctor_id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Apakah Anda yakin ingin mencabut akses dokter {{ $doctor->user->name }}? Dokter tidak akan dapat mengakses rekam medis Anda setelah akses dicabut.')"
                                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                Cabut Akses
                            </button>
                        </form>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        @if(isset($doctors) && $doctors->hasPages())
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            {{ $doctors->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request('search'))
                    Dokter tidak ditemukan
                @else
                    Belum ada dokter dengan akses
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request('search'))
                    Tidak ada dokter yang sesuai dengan pencarian "{{ request('search') }}".
                @else
                    Dokter yang telah Anda berikan akses akan muncul di sini.
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
                <h3 class="text-sm font-medium text-blue-800">Pengelolaan Akses Dokter</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Anda dapat mencabut akses dokter kapan saja untuk menjaga privasi</li>
                        <li>Pencabutan akses akan menghentikan kemampuan dokter mengakses rekam medis Anda</li>
                        <li>Semua aktivitas akses tercatat dalam audit trail untuk transparansi</li>
                        <li>Dokter dapat mengajukan ulang permintaan akses jika diperlukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection