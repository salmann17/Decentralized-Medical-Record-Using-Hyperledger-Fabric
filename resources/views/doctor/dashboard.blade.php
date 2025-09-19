@extends('layouts.app')

@section('title', 'Dashboard Dokter - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dashboard Dokter</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            @if($doctor)
                Selamat datang {{ $doctor->user->name }}, {{ $doctor->specialization }} di {{ $doctor->hospital->name }}.
            @else
                Selamat datang di dashboard dokter.
            @endif
        </p>
    </div>

    <!-- Doctor Info Card -->
    @if($doctor)
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Informasi Dokter</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Detail profil dokter Anda.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $doctor->user->name }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Spesialisasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $doctor->specialization }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nomor Lisensi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $doctor->license_number }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Rumah Sakit</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $doctor->hospital->name }}</dd>
                </div>
            </dl>
        </div>
    </div>
    @endif

    <!-- Access Requests -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Permintaan Akses Saya</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Status permintaan akses ke rekam medis pasien.</p>
            </div>
            <button type="button" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Request Akses Baru
            </button>
        </div>
        
        @if($accessRequests->count() > 0)
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($accessRequests as $request)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($request->patient->user->name, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $request->patient->user->name }}</div>
                                <div class="text-sm text-gray-500">NIK: {{ $request->patient->nik }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request->status === 'approved') bg-green-100 text-green-800
                                @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($request->requested_at)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada permintaan akses</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat permintaan akses ke pasien.</p>
        </div>
        @endif
    </div>

    <!-- My Patients -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Pasien Saya</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Pasien yang memberikan akses ke rekam medis mereka.</p>
            </div>
        </div>
        
        @if($myPatients->count() > 0)
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($myPatients as $patient)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ substr($patient->user->name, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $patient->user->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} • 
                                    Golongan Darah: {{ $patient->blood }}
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Lihat Rekam Medis
                            </button>
                            <button type="button" class="inline-flex items-center rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                Tambah Rekam Medis
                            </button>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.025" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pasien</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada pasien yang memberikan akses kepada Anda.</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-blue-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-12c0 1.232-.046 2.453-.138 3.662a4.006 4.006 0 01-3.7 3.7 48.678 48.678 0 01-7.324 0 4.006 4.006 0 01-3.7-3.7c-.017-.22-.032-.441-.046-.662M12 21a9 9 0 00-9-9m9 9a9 9 0 019-9M15 9H9m12 0A9 9 0 1121 9z" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">
                    <a href="#" class="focus:outline-none">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        Request Akses Pasien
                    </a>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Minta akses ke rekam medis pasien tertentu.
                </p>
            </div>
        </div>

        <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-green-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">
                    <a href="#" class="focus:outline-none">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        Tambah Rekam Medis
                    </a>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Buat rekam medis baru untuk pasien Anda.
                </p>
            </div>
        </div>

        <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-purple-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">
                    <a href="#" class="focus:outline-none">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        Daftar Pasien
                    </a>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Lihat semua pasien yang dapat Anda akses.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection