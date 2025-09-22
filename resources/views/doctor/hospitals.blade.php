@extends('layouts.app')

@section('title', 'Rumah Sakit Saya - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Rumah Sakit Saya</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Daftar rumah sakit tempat {{ $doctor->user->name }} terdaftar sebagai dokter.
        </p>
    </div>

    <!-- Doctor Info Card -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Dokter</h3>
            <dl class="mt-5 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $doctor->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nomor Lisensi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $doctor->license_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Spesialisasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $doctor->specialization ?? 'Belum ditentukan' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Rumah Sakit</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $doctor->hospitals->count() }} Rumah Sakit</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Hospitals List -->
    @if($doctor->hospitals->count() > 0)
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Rumah Sakit</h3>
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($doctor->hospitals as $hospital)
                    <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">{{ $hospital->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $hospital->type }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $hospital->address }}</span>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Terdaftar sejak {{ $hospital->pivot->created_at ? $hospital->pivot->created_at->format('d M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Statistik</h3>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="bg-gray-50 px-4 py-5 sm:rounded-lg sm:px-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Rumah Sakit</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $doctor->hospitals->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-5 sm:rounded-lg sm:px-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tipe Berbeda</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $doctor->hospitals->unique('type')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-5 sm:rounded-lg sm:px-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Rekam Medis</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $doctor->medicalRecords()->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center bg-white shadow sm:rounded-lg py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum terdaftar di rumah sakit</h3>
            <p class="mt-1 text-sm text-gray-500">
                Anda belum terdaftar di rumah sakit manapun. Hubungi administrator rumah sakit untuk mendaftarkan akun Anda.
            </p>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="flex justify-center">
        <a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection