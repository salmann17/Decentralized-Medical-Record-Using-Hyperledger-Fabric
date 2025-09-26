@extends('layouts.app')

@section('title', 'Dokter dengan Akses - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dokter dengan Akses Aktif</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Daftar dokter yang memiliki akses ke rekam medis Anda. Anda dapat mencabut akses kapan saja.
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Dokter dengan Akses</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $activeDoctors->count() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Rumah Sakit Terkait</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $hospitalCount }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Akses Bulan Ini</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-purple-600">{{ $monthlyAccess }}</dd>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:p-6">
        <form method="GET" action="{{ route('patient.active-doctors') }}" class="sm:flex sm:items-center sm:justify-between">
            <div class="w-full">
                <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Cari dokter</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                               placeholder="Cari berdasarkan nama dokter atau rumah sakit...">
                    </div>
                    <div>
                        <select name="specialization" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Spesialisasi</option>
                            @foreach($specializations as $spec)
                            <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'specialization']))
                        <a href="{{ route('patient.active-doctors') }}" 
                           class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Doctors List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($activeDoctors->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($activeDoctors as $doctor)
            <li class="px-4 py-6 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                <span class="text-lg font-semibold text-white">
                                    {{ substr($doctor->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-6 min-w-0 flex-1">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="text-lg font-medium text-gray-900 truncate">
                                        {{ $doctor->user->name }}
                                    </div>
                                    @if($doctor->specialization)
                                    <div class="text-sm text-blue-600 font-medium mt-1">
                                        {{ $doctor->specialization }}
                                    </div>
                                    @endif
                                    
                                    <div class="flex items-center text-sm text-gray-500 mt-2">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0V9a2 2 0 012-2h2a2 2 0 012 2v12M13 7a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        @if($doctor->hospital)
                                        {{ $doctor->hospital->name }}
                                        @else
                                        Rumah Sakit tidak tersedia
                                        @endif
                                    </div>
                                    
                                    @if($doctor->license_number)
                                    <div class="text-xs text-gray-400 mt-1">
                                        SIP: {{ $doctor->license_number }}
                                    </div>
                                    @endif
                                    
                                    @if($doctor->pivot && $doctor->pivot->access_granted_at)
                                    <div class="text-xs text-gray-400 mt-2 flex items-center">
                                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Akses diberikan {{ \Carbon\Carbon::parse($doctor->pivot->access_granted_at)->diffForHumans() }}
                                        @if($doctor->pivot->access_until)
                                        • Berlaku hingga {{ \Carbon\Carbon::parse($doctor->pivot->access_until)->format('d M Y') }}
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Access Statistics -->
                            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ $doctor->access_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Kali Akses</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ $doctor->records_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Rekam Medis</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($doctor->pivot && $doctor->pivot->last_access_at)
                                        {{ \Carbon\Carbon::parse($doctor->pivot->last_access_at)->diffForHumans() }}
                                        @else
                                        Belum pernah
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">Akses Terakhir</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 ml-6">
                        <div class="flex flex-col items-end space-y-2">
                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-2.5 py-0.5 text-xs font-medium">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Akses Aktif
                            </span>
                            
                            @if($doctor->pivot && $doctor->pivot->access_until)
                            @php
                                $daysLeft = \Carbon\Carbon::parse($doctor->pivot->access_until)->diffInDays(now(), false);
                            @endphp
                            @if($daysLeft <= 7 && $daysLeft >= 0)
                            <span class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 px-2.5 py-0.5 text-xs font-medium">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Segera Berakhir
                            </span>
                            @endif
                            @endif
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('patient.audit-trail', ['doctor_id' => $doctor->doctor_id]) }}" 
                               class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125M8.25 8.25V6.108" />
                                </svg>
                                Lihat Aktivitas
                            </a>
                            <form method="POST" action="{{ route('patient.active-doctors.revoke', $doctor->doctor_id) }}" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses dokter {{ $doctor->user->name }}? Dokter tidak akan dapat mengakses rekam medis Anda setelah akses dicabut.')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors w-full">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                    </svg>
                                    Cabut Akses
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada dokter dengan akses</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'specialization']))
                    Tidak ada dokter yang sesuai dengan kriteria pencarian Anda.
                @else
                    Dokter akan muncul di sini setelah Anda menyetujui permintaan akses mereka.
                @endif
            </p>
            @if(request()->hasAny(['search', 'specialization']))
            <div class="mt-6">
                <a href="{{ route('patient.active-doctors') }}" 
                   class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Lihat Semua Dokter
                </a>
            </div>
            @else
            <div class="mt-6">
                <a href="{{ route('patient.access-requests') }}" 
                   class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Lihat Permintaan Akses
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Access Control Info -->
        <div class="rounded-md bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Kontrol Akses</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Anda dapat mencabut akses dokter kapan saja. Pencabutan akses akan:</p>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Menghentikan akses dokter ke rekam medis Anda</li>
                            <li>Tercatat dalam audit trail untuk transparansi</li>
                            <li>Tidak menghapus riwayat akses sebelumnya</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Trail Info -->
        <div class="rounded-md bg-purple-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125M8.25 8.25V6.108" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-purple-800">Jejak Audit</h3>
                    <div class="mt-2 text-sm text-purple-700">
                        <p>Klik "Lihat Aktivitas" untuk melihat:</p>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Kapan dokter mengakses data Anda</li>
                            <li>Data apa yang diakses</li>
                            <li>Berapa lama akses berlangsung</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection