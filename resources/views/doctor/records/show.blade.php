@extends('layouts.app')

@section('title', 'Detail Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">
                    Detail Rekam Medis #{{ $record->medicalrecord_id ?? $record->id ?? 'N/A' }}
                </h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Informasi lengkap rekam medis untuk {{ $record->patient->user->name ?? 'Pasien' }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if($record->status !== 'immutable')
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('doctor.records') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H9.414a1 1 0 01-.707-.293l-2-2A1 1 0 005.414 6H4a2 2 0 00-2 2v6a2 2 0 002 2h2m3 4h6m-3-3v6" />
                    </svg>
                    Print / PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Record Status -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($record->status === 'draft')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Draft
                        </span>
                    @elseif($record->status === 'final')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Final
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Immutable
                        </span>
                    @endif
                    
                    <div class="text-sm text-gray-500">
                        Dibuat pada {{ isset($record->created_at) ? $record->created_at->format('d F Y, H:i') : date('d F Y, H:i') }}
                    </div>
                </div>
                
                <!-- Blockchain Status -->
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span>Blockchain: Pending Integration</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Pasien</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Pasien</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->patient->user->name ?? 'Pasien' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Kunjungan</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($record->visit_date) ? date('d F Y', strtotime($record->visit_date)) : date('d F Y') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Medis</h3>
            
            <!-- Chief Complaint -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-2">Keluhan Utama</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700">{{ $record->chief_complaint ?? $record->notes ?? 'Tidak ada catatan keluhan utama' }}</p>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-2">Tanda Vital</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tekanan Darah</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ isset($record->vital_signs->blood_pressure) ? $record->vital_signs->blood_pressure : 'N/A' }} 
                                <span class="text-xs text-gray-500">mmHg</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nadi</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ isset($record->vital_signs->heart_rate) ? $record->vital_signs->heart_rate : 'N/A' }}
                                <span class="text-xs text-gray-500">bpm</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Suhu</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ isset($record->vital_signs->temperature) ? $record->vital_signs->temperature : 'N/A' }}°
                                <span class="text-xs text-gray-500">C</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Respirasi</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ isset($record->vital_signs->respiratory_rate) ? $record->vital_signs->respiratory_rate : 'N/A' }}
                                <span class="text-xs text-gray-500">/min</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">SpO2</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ isset($record->vital_signs->oxygen_saturation) ? $record->vital_signs->oxygen_saturation : 'N/A' }}
                                <span class="text-xs text-gray-500">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-2">Diagnosis</h4>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-900 font-medium">{{ $record->diagnosis_desc ?? $record->diagnosis ?? 'Diagnosis belum tersedia' }}</p>
                </div>
            </div>

            <!-- Treatment Plan -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-2">Rencana Pengobatan</h4>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-900">{{ $record->treatment ?? 'Rencana pengobatan belum tersedia' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Blockchain Information -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-purple-800">Tentang Verifikasi Blockchain</h3>
                <div class="mt-2 text-sm text-purple-700">
                    <p>
                        Setelah sistem blockchain terintegrasi, rekam medis ini akan diverifikasi dan dicatat secara permanen di blockchain. 
                        Hal ini akan memastikan integritas data dan mencegah perubahan yang tidak sah.
                    </p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Menunggu Integrasi Blockchain
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        @if($record->status !== 'immutable')
            <button type="button" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        @endif
        <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H9.414a1 1 0 01-.707-.293l-2-2A1 1 0 005.414 6H4a2 2 0 00-2 2v6a2 2 0 002 2h2m3 4h6m-3-3v6" />
            </svg>
            Print
        </button>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .no-print, .no-print * {
        display: none !important;
    }
    body {
        background: white;
    }
    .shadow, .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endsection