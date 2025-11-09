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
            
            @php
            $latestAudit = $record->auditTrails->first();
            $blockchainHash = $latestAudit ? $latestAudit->blockchain_hash : null;
            $isValid = $blockchainHash && !str_starts_with($blockchainHash, 'INVALID_') && !str_starts_with($blockchainHash, 'NOT_FOUND_');
            $isInvalid = $blockchainHash && str_starts_with($blockchainHash, 'INVALID_');
            $isNotFound = $blockchainHash && str_starts_with($blockchainHash, 'NOT_FOUND_');
            @endphp
            
            @if($isValid)
            <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-medium">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                ✓ Terverifikasi Blockchain
            </span>
            @elseif($isInvalid)
            <span class="inline-flex items-center rounded-full bg-red-100 text-red-800 px-3 py-1 text-sm font-medium">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                ⚠ Data Dimodifikasi
            </span>
            @elseif($isNotFound)
            <span class="inline-flex items-center rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-sm font-medium">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                ❌ Tidak Ditemukan
            </span>
            @elseif($blockchainHash)
            <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-3 py-1 text-sm font-medium">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                Blockchain Hash Tersedia
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
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $record->admin->name ?? 'N/A' }}</dd>
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
            @if($record->prescriptions && $record->prescriptions->count() > 0)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Resep Obat</h3>
                    <p class="mt-1 text-sm text-gray-500">Daftar resep obat untuk Anda</p>
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
            $latestAudit = $record->auditTrails->first();
            $blockchainHash = $latestAudit ? $latestAudit->blockchain_hash : null;
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
                    <p>Rekam medis ini telah terverifikasi dan hash sesuai dengan yang tersimpan di blockchain.</p>
                    <div class="mt-2 font-mono text-xs bg-white p-2 rounded border break-all">
                        <strong>Hash:</strong><br>{{ Str::limit($blockchainHash, 64, '...') }}
                    </div>
                </div>
            </div>
            @elseif($isInvalid)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-red-800">⚠ Data Dimodifikasi</h3>
                </div>
                <div class="mt-2 text-sm text-red-700">
                    <p><strong>Peringatan:</strong> Rekam medis ini telah dimodifikasi dan tidak sesuai dengan hash yang tersimpan di blockchain.</p>
                    <div class="mt-2 p-2 bg-red-100 rounded border border-red-300">
                        <p class="text-xs font-semibold">⚠ Hash tidak valid - data mungkin telah diubah oleh pihak yang tidak berwenang.</p>
                    </div>
                </div>
            </div>
            @elseif($isNotFound)
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-orange-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-orange-800">❌ Tidak Ditemukan di Blockchain</h3>
                </div>
                <div class="mt-2 text-sm text-orange-700">
                    <p>Rekam medis ini tidak ditemukan di jaringan blockchain. Mungkin belum pernah disimpan atau telah dihapus.</p>
                    <div class="mt-2 p-2 bg-orange-100 rounded border border-orange-300">
                        <p class="text-xs">❌ Data tidak ada di blockchain - verifikasi gagal.</p>
                    </div>
                </div>
            </div>
            @elseif($blockchainHash)
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-medium text-gray-800">Blockchain Hash Tersedia</h3>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    <p>Hash blockchain tersedia, klik tombol verifikasi untuk memvalidasi data.</p>
                    <div class="mt-2 font-mono text-xs bg-white p-2 rounded border break-all">
                        <strong>Hash:</strong><br>{{ Str::limit($blockchainHash, 64, '...') }}
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
                    <div class="text-sm font-medium text-gray-900">{{ $record->admin->name ?? 'N/A' }}</div>
                    @if(isset($record->admin->address))
                    <div class="text-sm text-gray-500 mt-1">{{ $record->admin->address }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
        <button type="button" 
                onclick="verifyBlockchain({{ $record->idmedicalrecord }})"
                class="inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500">
            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Verifikasi Blockchain
        </button>
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

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function verifyBlockchain(recordId) {
        Swal.fire({
            title: 'Verifikasi Blockchain',
            text: 'Sedang memverifikasi data dengan blockchain...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/patient/records/${recordId}/verify-blockchain`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.message.includes('✅')) {
                        Swal.fire({
                            title: 'Terverifikasi!',
                            html: `
                        <div class="text-left">
                            <p class="mb-2"><strong>Rekam medis terverifikasi dan hash sesuai.</strong></p>
                            <hr class="my-3">
                            <p class="text-sm text-gray-600"><strong>ID Rekam Medis:</strong> ${data.data.idmedicalrecord}</p>
                            <p class="text-sm text-gray-600"><strong>Version:</strong> ${data.data.version}</p>
                            <p class="text-sm text-gray-600"><strong>Hash:</strong> <span class="font-mono text-xs">${data.data.storedHash.substring(0, 32)}...</span></p>
                            <p class="text-sm text-gray-600"><strong>Timestamp:</strong> ${new Date(data.data.timestamp).toLocaleString('id-ID')}</p>
                        </div>
                    `,
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else if (data.message.includes('⚠️')) {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: 'Rekam medis tidak terverifikasi karena telah dimodifikasi oleh pihak yang tidak bertanggung jawab.',
                            icon: 'warning',
                            confirmButtonColor: '#f59e0b'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Tidak Ditemukan',
                            text: 'Rekam medis tidak ditemukan di jaringan blockchain.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        }).then(() => {
                            location.reload();
                        });
                    }
                } else if (data.message.includes('tidak ada di jaringan')) {
                    const missingId = (data.data && data.data.idmedicalrecord) ? data.data.idmedicalrecord : recordId;
                    Swal.fire({
                        title: 'Tidak Ditemukan',
                        text: `Rekam medis dengan ID ${missingId} tidak ditemukan di jaringan blockchain.`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Terjadi kesalahan saat verifikasi.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal menghubungi server. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                }).then(() => {
                    location.reload();
                });
            });
    }
</script>

@endsection