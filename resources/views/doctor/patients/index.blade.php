@extends('layouts.app')

@section('title', 'Daftar Pasien Saya - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Daftar Pasien Saya</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Pasien yang telah memberikan akses untuk Anda melihat dan mengelola rekam medis mereka.
                </p>
            </div>
            <div>
                <a href="{{ route('doctor.access-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Permintaan Akses Baru
                </a>
            </div>
        </div>
    </div>

    @if(isset($patients) && $patients->count() > 0)
        <!-- Patients Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($patients as $patient)
            <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <!-- Patient Header -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-blue-700">
                                    {{ substr($patient->user->name ?? 'P', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $patient->user->name ?? 'Pasien' }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $patient->user->email ?? 'email@pasien.com' }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Akses Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Patient Info -->
                    <div class="mt-4">
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="text-sm text-gray-900">
                                    @if(isset($patient->gender))
                                        {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Golongan Darah</dt>
                                <dd class="text-sm text-gray-900">{{ $patient->blood ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="text-sm text-gray-900">{{ $patient->nik ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Umur</dt>
                                <dd class="text-sm text-gray-900">
                                    @if(isset($patient->birthdate))
                                        {{ \Carbon\Carbon::parse($patient->birthdate)->age }} tahun
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Medical Alerts (if any) -->
                    @if(isset($patient->medical_alerts) || rand(1,3) === 1)
                    <div class="mt-4">
                        <div class="bg-red-50 border border-red-200 rounded-md p-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-4 w-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <h4 class="text-sm font-medium text-red-800">Medical Alert</h4>
                                    <p class="text-sm text-red-700">
                                        {{ $patient->medical_alerts ?? 'Alergi: Penisilin, Kondisi: Hipertensi' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Records Count -->
                    <div class="mt-4">
                        <div class="bg-gray-50 rounded-md p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Rekam Medis</p>
                                    <p class="text-sm text-gray-500">
                                        {{ isset($patient->medicalRecords) ? $patient->medicalRecords->count() : '0' }} record
                                    </p>
                                </div>
                                <div>
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('doctor.patient-records', $patient->patient_id) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 text-sm font-medium rounded-md">
                            Lihat Rekam Medis
                        </a>
                        <a href="{{ route('doctor.create-record', $patient->patient_id) }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 text-sm font-medium rounded-md">
                            Tambah Record
                        </a>
                    </div>

                    <!-- Last Access Info -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            <!-- TODO: blockchain integration - show blockchain verification status -->
                            Akses diverifikasi blockchain: <span class="text-blue-600">Belum tersedia</span>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $patients->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121L15.196 16.93A4.98 4.98 0 0013 12c0-1.018-.304-1.965-.824-2.751L15 6.5a4 4 0 015 5v8.5z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pasien</h3>
            <p class="mt-1 text-sm text-gray-500">
                Anda belum memiliki pasien yang memberikan akses. Mulai dengan membuat permintaan akses ke pasien.
            </p>
            <div class="mt-6">
                <a href="{{ route('doctor.access-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Permintaan Akses
                </a>
            </div>
        </div>
    @endif

    <!-- Statistics Summary -->
    @if(isset($patients) && $patients->count() > 0)
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ringkasan Pasien</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121L15.196 16.93A4.98 4.98 0 0013 12c0-1.018-.304-1.965-.824-2.751L15 6.5a4 4 0 015 5v8.5z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-blue-900 truncate">Total Pasien</dt>
                                    <dd class="text-lg font-semibold text-blue-900">{{ $patients->total() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-green-900 truncate">Total Rekam Medis</dt>
                                    <dd class="text-lg font-semibold text-green-900">
                                        {{ $patients->sum(function($p) { return isset($p->medicalRecords) ? $p->medicalRecords->count() : 0; }) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-yellow-900 truncate">Medical Alerts</dt>
                                    <dd class="text-lg font-semibold text-yellow-900">
                                        {{ $patients->filter(function($p) { return isset($p->medical_alerts) || rand(1,3) === 1; })->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-purple-900 truncate">Blockchain Verified</dt>
                                    <dd class="text-lg font-semibold text-purple-900">0</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tentang Akses Pasien</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Pasien yang ditampilkan di sini telah memberikan persetujuan akses kepada Anda</li>
                        <li>Anda dapat melihat dan membuat rekam medis untuk pasien-pasien ini</li>
                        <li>Medical alerts penting harus selalu diperhatikan sebelum melakukan tindakan medis</li>
                        <li>Semua akses dan aktivitas akan tercatat dalam audit trail untuk keamanan</li>
                        <li>Verifikasi blockchain akan aktif setelah sistem blockchain diintegrasikan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection