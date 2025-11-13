@extends('layouts.app')

@section('title', 'Rekam Medis - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Rekam Medis</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Metadata rekam medis yang dibuat di {{ $admin->name }}. 
            <span class="text-orange-600 font-medium">Isi detail rekam medis tidak ditampilkan sesuai aturan privasi.</span>
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Rekam Medis</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalRecords }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Status Draft</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-orange-600">{{ $draftRecords }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Status Final</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $finalRecords }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Terverifikasi Blockchain</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $immutableRecords }}</dd>
        </div>
    </div>

    <!-- Records Table -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Daftar Rekam Medis (Metadata)</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Informasi umum rekam medis tanpa detail diagnosis dan treatment.
            </p>
        </div>

        @if($records->count() > 0)
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Rekam Medis
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pasien
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dokter
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Kunjungan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Blockchain Hash
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $record->idmedicalrecord }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($record->patient->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->patient->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->patient->nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $record->doctor->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $record->doctor->spesialization }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($record->visit_date)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->status === 'draft')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Draft
                                    </span>
                                @elseif($record->status === 'final')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Final
                                    </span>
                                @elseif($record->status === 'immutable')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Immutable
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                @php
                                    $latestAudit = $record->auditTrails->first();
                                    $blockchainHash = $latestAudit ? $latestAudit->blockchain_hash : null;
                                @endphp
                                @if($blockchainHash)
                                    <span class="truncate block w-32" title="{{ $blockchainHash }}">
                                        {{ Str::limit($blockchainHash, 20) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $records->links() }}
            </div>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">
                Belum ada rekam medis yang dibuat di rumah sakit ini.
            </p>
        </div>
        @endif
    </div>

    <!-- Privacy Notice -->
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Ketentuan Akses Rekam Medis</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Admin hanya dapat melihat metadata rekam medis (ID, tanggal, dokter, status)</li>
                        <li>Detail diagnosis, treatment, dan catatan medis tidak ditampilkan</li>
                        <li>Akses penuh tetap dipegang oleh pasien dan dokter yang menangani</li>
                        <li>Blockchain hash menunjukkan integritas data di sistem desentralisasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection