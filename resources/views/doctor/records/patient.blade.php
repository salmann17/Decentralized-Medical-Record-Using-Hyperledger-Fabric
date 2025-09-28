@extends('layouts.app')

@section('title', 'Rekam Medis Pasien - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">
                    Rekam Medis - {{ $patient->user->name ?? 'Pasien' }}
                </h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Semua rekam medis untuk pasien ini yang telah Anda buat.
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('doctor.create-record', $patient->patient_id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Rekam Medis
                </a>
                <a href="{{ route('doctor.my-patients') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Pasien
                </a>
            </div>
        </div>
    </div>

    <!-- Patient Summary Card -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-medium text-blue-700">
                            {{ substr($patient->user->name ?? 'P', 0, 1) }}
                        </span>
                    </div>
                </div>
                <div class="ml-6 flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $patient->user->name ?? 'Pasien' }}</h3>
                            <p class="text-sm text-gray-500">{{ $patient->user->email ?? 'email@pasien.com' }}</p>
                            <div class="mt-2 flex space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ $patient->blood ?? 'Golongan darah tidak diketahui' }}
                                </span>
                                @if(isset($patient->birthdate))
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($patient->birthdate)->age }} tahun
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Akses Aktif
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Total: {{ isset($records) ? $records->count() : 0 }} rekam medis
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($records) && $records->count() > 0)
        <!-- Medical Records Timeline -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Riwayat Rekam Medis</h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($records as $index => $record)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                
                                <div class="relative flex space-x-3">
                                    <!-- Timeline Icon -->
                                    <div>
                                        @if($record->status === 'draft')
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </span>
                                        @elseif($record->status === 'final')
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="min-w-0 flex-1 pt-1.5">
                                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                            <!-- Header -->
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $record->visit_date ? date('d F Y', strtotime($record->visit_date)) : date('d F Y') }}
                                                    </p>
                                                    <h4 class="text-lg font-medium text-gray-900 mt-1">
                                                        Kunjungan #{{ $records->count() - $index }}
                                                    </h4>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @if($record->status === 'draft')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Draft
                                                        </span>
                                                    @elseif($record->status === 'final')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Final
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            Immutable
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Medical Information -->
                                            <div class="mt-4 space-y-3">
                                                <!-- Diagnosis -->
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-900">Diagnosis</h5>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ Str::limit($record->diagnosis ?? 'Diagnosis tidak tersedia', 150) }}
                                                    </p>
                                                </div>

                                                <!-- Treatment -->
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-900">Treatment</h5>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ Str::limit($record->treatment ?? 'Treatment tidak tersedia', 150) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Actions and Blockchain Status -->
                                            <div class="mt-4 flex items-center justify-between">
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                    Blockchain: 
                                                    <span class="ml-1 text-blue-600">
                                                        {{ isset($record->blockchain_hash) && $record->blockchain_hash ? 'Verified' : 'Pending Integration' }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('doctor.show-record', $record->medicalrecord_id ?? $record->id ?? 1) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                        Lihat Detail
                                                    </a>
                                                    @if($record->status !== 'immutable')
                                                        <a href="#" 
                                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                            Edit
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Pagination -->
                @if(method_exists($records, 'links'))
                    <div class="mt-8">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">
                Belum ada rekam medis untuk pasien {{ $patient->user->name ?? 'ini' }}. Mulai dengan membuat rekam medis pertama.
            </p>
            <div class="mt-6">
                <a href="{{ route('doctor.create-record', $patient->patient_id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Rekam Medis Pertama
                </a>
            </div>
        </div>
    @endif

    <!-- Patient Medical Summary -->
    @if(isset($records) && $records->count() > 0)
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ringkasan Medis</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-blue-900 truncate">Total Kunjungan</dt>
                                    <dd class="text-lg font-semibold text-blue-900">{{ $records->count() }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-yellow-900 truncate">Draft</dt>
                                    <dd class="text-lg font-semibold text-yellow-900">{{ $records->where('status', 'draft')->count() }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-green-900 truncate">Final</dt>
                                    <dd class="text-lg font-semibold text-green-900">{{ $records->where('status', 'final')->count() }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-purple-900 truncate">Immutable</dt>
                                    <dd class="text-lg font-semibold text-purple-900">{{ $records->where('status', 'immutable')->count() }}</dd>
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
                <h3 class="text-sm font-medium text-blue-800">Tentang Rekam Medis Pasien</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Timeline menampilkan rekam medis dari yang terbaru ke terlama</li>
                        <li>Status "Draft" memungkinkan edit, "Final" sudah lengkap, "Immutable" tidak dapat diubah</li>
                        <li>Semua perubahan dan akses akan tercatat dalam audit trail</li>
                        <li>Blockchain verification akan aktif setelah sistem blockchain terintegrasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection