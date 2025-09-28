@extends('layouts.app')

@section('title', 'Detail Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('patient.records') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <svg class="-ml-1 mr-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Rekam Medis
            </a>
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
                            <dt class="text-sm font-medium text-gray-500">Rumah Sakit</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $record->hospital->name }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Dokter</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ $record->doctor->user->name }}
                                @if($record->doctor->specialization)
                                <span class="text-gray-500">({{ $record->doctor->specialization }})</span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tanggal Kunjungan</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ \Carbon\Carbon::parse($record->visit_date)->format('l, d F Y') }}
                                <!-- <span class="text-gray-500">pukul {{ \Carbon\Carbon::parse($record->visit_date)->format('H:i') }}</span> -->
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
            @if($record->prescription)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Resep Obat</h3>
                </div>
                <div class="border-t border-gray-200">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-base font-semibold text-gray-900 mb-2">{{ $record->prescription->item }}</div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dosis</div>
                                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $record->prescription->dosage }}</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Frekuensi</div>
                                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $record->prescription->frequency }}</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Durasi</div>
                                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $record->prescription->duration }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            @if($record->blockchain_hash)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-green-800">Blockchain Verified</h3>
                </div>
                <div class="mt-2 text-sm text-green-700">
                    <p>Rekam medis ini telah disimpan ke blockchain dan tidak dapat diubah.</p>
                    <div class="mt-2 font-mono text-xs bg-white p-2 rounded border">
                        Hash: {{ $record->blockchain_hash }}
                    </div>
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
                            <dt class="text-sm font-medium text-gray-500">ID Rekam Medis</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-mono">
                                {{ $record->medicalrecord_id }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, H:i') }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ \Carbon\Carbon::parse($record->updated_at)->format('d M Y, H:i') }}
                            </dd>
                        </div>
                        @if($record->finalized_at)
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

            <!-- Doctor Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-base font-medium leading-6 text-gray-900">Informasi Dokter</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ substr($record->doctor->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $record->doctor->user->name }}</div>
                            @if($record->doctor->specialization)
                            <div class="text-sm text-gray-500">{{ $record->doctor->specialization }}</div>
                            @endif
                            @if($record->doctor->license_number)
                            <div class="text-xs text-gray-400">SIP: {{ $record->doctor->license_number }}</div>
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
                    <div class="text-sm font-medium text-gray-900">{{ $record->hospital->name }}</div>
                    @if($record->hospital->address)
                    <div class="text-sm text-gray-500 mt-1">{{ $record->hospital->address }}</div>
                    @endif
                    @if($record->hospital->phone)
                    <div class="text-xs text-gray-400 mt-1">Tel: {{ $record->hospital->phone }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
        <button type="button" onclick="window.print()" 
                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak
        </button>
        <a href="{{ route('patient.records') }}" 
           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection