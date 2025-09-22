<!-- filepath: resources\views\doctor\dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Dokter - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dashboard Dokter</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Selamat datang, {{ $doctor->user->name }}. Kelola akses pasien dan rekam medis Anda.
        </p>
    </div>

    <!-- Doctor Info Card -->
    @if($doctor)
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Dokter</h3>
            <dl class="mt-5 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
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
                    <dt class="text-sm font-medium text-gray-500">Rumah Sakit</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($doctor->hospitals->count() > 0)
                            @foreach($doctor->hospitals as $hospital)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">
                                    {{ $hospital->name }}
                                </span>
                            @endforeach
                            <a href="{{ route('doctor.hospitals') }}" class="text-blue-600 hover:text-blue-500 text-sm">
                                Lihat semua →
                            </a>
                        @else
                            <span class="text-gray-500">Belum terdaftar di rumah sakit</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
    @endif

    <!-- Access Requests -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Permintaan Akses Terbaru</h3>
            @if($accessRequests->count() > 0)
                <div class="mt-6 flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @foreach($accessRequests as $request)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr($request->patient->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $request->patient->user->name }}</p>
                                    <p class="text-sm text-gray-500">Diminta pada {{ $request->requested_at }}</p>
                                </div>
                                <div>
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="mt-4 text-sm text-gray-500">Belum ada permintaan akses.</p>
            @endif
        </div>
    </div>

    <!-- My Patients -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Pasien Saya</h3>
            @if($myPatients->count() > 0)
                <div class="mt-6 flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @foreach($myPatients as $patient)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-700">{{ substr($patient->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $patient->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $patient->gender }}, {{ $patient->blood }}</p>
                                </div>
                                <div>
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Tambah Rekam Medis
                                    </button>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="mt-4 text-sm text-gray-500">Belum ada pasien yang memberikan akses.</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Permintaan Akses</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $accessRequests->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121l-.224-.224a4 4 0 01-1.066-6.364L16.5 8.5a4 4 0 00-5.657-5.657l-.707.707a1 1 0 01-1.414 0L8.5 3.293a.997.997 0 00-1.414 0L5.793 4.586a1 1 0 000 1.414l.293.293a4 4 0 00-.364 1.414L5 9.007v9.986z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rumah Sakit</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $doctor->hospitals->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection