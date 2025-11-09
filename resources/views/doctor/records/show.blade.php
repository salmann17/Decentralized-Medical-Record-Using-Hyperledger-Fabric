@extends('layouts.app')

@section('title', 'Detail Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="border-b border-gray-200 pb-5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('doctor.records') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <svg class="-ml-1 mr-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Rekam Medis
            </a>
            @if($record->version > 1)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    <svg class="-ml-0.5 mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
                    </svg>
                    Versi {{ $record->version }}
                </span>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                {{ $record->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $record->status === 'final' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $record->status === 'immutable' ? 'bg-green-100 text-green-800' : '' }}">
                {{ ucfirst($record->status) }}
            </span>
            @if($record->blockchain_hash)
            <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-medium">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Blockchain Verified
            </span>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Medical Record Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Kunjungan</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pasien</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $record->patient->user->name }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Rumah Sakit</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $record->admin->name }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tanggal Kunjungan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ \Carbon\Carbon::parse($record->visit_date)->format('l, d F Y') }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Keluhan Utama</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ $record->chief_complaint ?: 'Tidak ada keluhan yang dicatat' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Vital Signs -->
            @if($record->blood_pressure || $record->heart_rate || $record->temperature || $record->respiratory_rate)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Tanda Vital</h3>
                </div>
                <div class="border-t border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4">
                        @if($record->blood_pressure)
                        <div class="flex items-center p-3 bg-red-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-red-800">Tekanan Darah</div>
                                <div class="text-lg font-bold text-red-900">{{ $record->blood_pressure }}</div>
                            </div>
                        </div>
                        @endif

                        @if($record->heart_rate)
                        <div class="flex items-center p-3 bg-pink-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-pink-800">Detak Jantung</div>
                                <div class="text-lg font-bold text-pink-900">{{ $record->heart_rate }} bpm</div>
                            </div>
                        </div>
                        @endif

                        @if($record->temperature)
                        <div class="flex items-center p-3 bg-orange-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-orange-800">Suhu Tubuh</div>
                                <div class="text-lg font-bold text-orange-900">{{ $record->temperature }}°C</div>
                            </div>
                        </div>
                        @endif

                        @if($record->respiratory_rate)
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-blue-800">Frekuensi Napas</div>
                                <div class="text-lg font-bold text-blue-900">{{ $record->respiratory_rate }}/min</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Clinical History -->
            @if($record->chief_complaint)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Keluhan Utama</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($record->chief_complaint)) !!}
                    </div>
                </div>
            </div>
            @endif

            @if($record->history_present_illness)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Riwayat Penyakit Sekarang</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($record->history_present_illness)) !!}
                    </div>
                </div>
            </div>
            @endif

            @if($record->physical_examination)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Pemeriksaan Fisik</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($record->physical_examination)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Medical Examination -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Pemeriksaan Medis</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Kode Diagnosis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                <span class="inline-flex items-center rounded-md bg-blue-100 text-blue-800 px-2 py-1 text-xs font-medium">
                                    {{ $record->diagnosis_code }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                @if($record->diagnosis_desc)
                                    <div class="prose prose-sm max-w-none">
                                        {!! nl2br(e($record->diagnosis_desc)) !!}
                                    </div>
                                @else
                                    <em class="text-gray-500">Diagnosis belum diisi</em>
                                @endif
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Pemeriksaan Fisik</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                @if($record->physical_examination)
                                    <div class="prose prose-sm max-w-none">
                                        {!! nl2br(e($record->physical_examination)) !!}
                                    </div>
                                @else
                                    <em class="text-gray-500">Belum ada catatan pemeriksaan fisik</em>
                                @endif
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tindakan Medis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                @if($record->treatment)
                                    <div class="prose prose-sm max-w-none">
                                        {!! nl2br(e($record->treatment)) !!}
                                    </div>
                                @else
                                    <em class="text-gray-500">Tidak ada tindakan medis khusus</em>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Prescriptions -->
            @if($record->prescriptions && $record->prescriptions->count() > 0)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Resep Obat</h3>
                    <p class="mt-1 text-sm text-gray-500">Daftar resep obat untuk pasien ini</p>
                </div>
                <div class="border-t border-gray-200">
                    @foreach($record->prescriptions as $prescription)
                    <div class="px-4 py-4 sm:px-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $prescription->type === 'single' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $prescription->type === 'single' ? 'Resep Tunggal' : 'Resep Racikan' }}
                            </span>
                        </div>
                        
                        @if($prescription->instructions)
                        <div class="mb-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800"><strong>Instruksi:</strong> {{ $prescription->instructions }}</p>
                        </div>
                        @endif

                        <div class="space-y-3">
                            @foreach($prescription->prescriptionItems as $item)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-base font-semibold text-gray-900 mb-2">{{ $item->name }}</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dosis</div>
                                            <div class="text-sm font-semibold text-gray-900 mt-1">{{ $item->dosage }}</div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Frekuensi</div>
                                            <div class="text-sm font-semibold text-gray-900 mt-1">{{ $item->frequency }}</div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Durasi</div>
                                            <div class="text-sm font-semibold text-gray-900 mt-1">{{ $item->duration }}</div>
                                        </div>
                                    </div>
                                    @if($item->notes)
                                    <div class="mt-2 text-sm text-gray-600 italic">
                                        Catatan: {{ $item->notes }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Additional Notes -->
            @if($record->notes)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Catatan Tambahan</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($record->notes)) !!}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Blockchain Information -->
            @php
                $blockchainHash = $record->auditTrails && $record->auditTrails->count() > 0 ? $record->auditTrails->first()->blockchain_hash : null;
                $isValid = $blockchainHash && !str_starts_with($blockchainHash, 'INVALID_') && !str_starts_with($blockchainHash, 'NOT_FOUND_');
                $isInvalid = $blockchainHash && str_starts_with($blockchainHash, 'INVALID_');
                $isNotFound = $blockchainHash && str_starts_with($blockchainHash, 'NOT_FOUND_');
            @endphp
            
            @if($isValid)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-green-800">✓ Terverifikasi Blockchain</h3>
                </div>
                <div class="mt-2 text-sm text-green-700">
                    <p>Rekam medis ini telah terverifikasi dan hash sesuai dengan blockchain.</p>
                    <div class="mt-2 font-mono text-xs bg-white p-2 rounded border break-all">
                        {{ $blockchainHash }}
                    </div>
                </div>
            </div>
            @elseif($isInvalid)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-red-800">⚠ Data Telah Dimodifikasi</h3>
                </div>
                <div class="mt-2 text-sm text-red-700">
                    <p>Peringatan! Rekam medis ini terdeteksi telah dimodifikasi dan tidak sesuai dengan hash di blockchain.</p>
                    @if($record->auditTrails && $record->auditTrails->first())
                    <p class="mt-1 text-xs">Terakhir diverifikasi: {{ $record->auditTrails->first()->timestamp->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
            @elseif($isNotFound)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-yellow-800">❌ Tidak Ditemukan di Blockchain</h3>
                </div>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Rekam medis ini tidak ditemukan di jaringan blockchain. Silakan verifikasi ulang atau hubungi administrator.</p>
                    @if($record->auditTrails && $record->auditTrails->first())
                    <p class="mt-1 text-xs">Terakhir dicek: {{ $record->auditTrails->first()->timestamp->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-gray-800">Belum Diverifikasi</h3>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                    <p>Rekam medis ini belum diverifikasi dengan blockchain. Klik tombol "Verifikasi Blockchain" untuk memverifikasi.</p>
                </div>
            </div>
            @endif

            <!-- Record Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium leading-6 text-gray-900">Informasi Rekam Medis</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ isset($record->created_at) ? \Carbon\Carbon::parse($record->created_at)->format('d M Y, H:i') : 'N/A' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ isset($record->updated_at) ? \Carbon\Carbon::parse($record->updated_at)->format('d M Y, H:i') : 'N/A' }}
                            </dd>
                        </div>
                        @if(isset($record->finalized_at) && $record->finalized_at)
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Diselesaikan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ \Carbon\Carbon::parse($record->finalized_at)->format('d M Y, H:i') }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium leading-6 text-gray-900">Informasi Pasien</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ substr($record->patient->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $record->patient->user->name }}</div>
                            @if(isset($record->patient->gender))
                            <div class="text-sm text-gray-500">{{ $record->patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                            @endif
                            @if(isset($record->patient->birthdate))
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($record->patient->birthdate)->format('d M Y') }}
                                ({{ \Carbon\Carbon::parse($record->patient->birthdate)->age }} tahun)
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hospital Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium leading-6 text-gray-900">Informasi Rumah Sakit</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="text-sm font-medium text-gray-900">{{ $record->admin->name }}</div>
                    @if($record->admin->address)
                    <div class="text-sm text-gray-500 mt-1">{{ $record->admin->address }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between">
        <div class="flex space-x-3">

            <!-- Finalisasi Button - Only for draft -->
            @if($record->status === 'draft')
            <form method="POST" action="{{ route('doctor.finalize-record', $record->idmedicalrecord) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                        onclick="return confirm('Apakah Anda yakin ingin menfinalisasi rekam medis ini? Status akan berubah menjadi final.')">
                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Finalisasi
                </button>
            </form>
            @endif

        </div>
        <div class="flex space-x-3">
            <a href="{{ route('doctor.records') }}" 
               class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@endsection