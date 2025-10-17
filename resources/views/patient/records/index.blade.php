@extends('layouts.app')

@section('title', 'Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-semibold leading-6 text-gray-900">Rekam Medis Saya</h3>
            <p class="mt-2 max-w-4xl text-sm text-gray-500">
                Riwayat lengkap kunjungan dan perawatan medis Anda. Kontrol penuh di tangan Anda.
            </p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:p-6">
        <form method="GET" action="{{ route('patient.records') }}" class="sm:flex sm:items-center sm:justify-between">
            <div class="w-full">
                <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Cari rekam medis</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                               placeholder="Cari berdasarkan rumah sakit, dokter, atau diagnosis...">
                    </div>
                    <div>
                        <select name="status" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Final</option>
                            <option value="immutable" {{ request('status') === 'immutable' ? 'selected' : '' }}>Immutable</option>
                        </select>
                    </div>
                    <div>
                        <select name="period" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Periode</option>
                            <option value="7days" {{ request('period') === '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="30days" {{ request('period') === '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="6months" {{ request('period') === '6months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                            <option value="1year" {{ request('period') === '1year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'status', 'period']))
                        <a href="{{ route('patient.records') }}" 
                           class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Rekam Medis</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $records->total() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Kunjungan Bulan Ini</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $records->where('visit_date', '>=', now()->startOfMonth())->count() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Rumah Sakit Terkait</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $records->unique('hospital_id')->count() }}</dd>
        </div>
    </div>

    <!-- Medical Records List -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        @if($records->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($records as $record)
            <li class="px-4 py-6 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $record->admin->hospital_name ?? 'Hospital' }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="font-medium">{{ $record->doctor->user->name }}</span>
                                        @if($record->doctor->specialization)
                                        • {{ $record->doctor->specialization }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M3 5a2 2 0 012-2h1a2 2 0 012 2v1H3V5z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($record->visit_date)->format('d M Y • H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex flex-col items-end">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $record->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $record->status === 'final' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $record->status === 'immutable' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                            @if($record->blockchain_hash)
                            <span class="text-xs text-gray-400 mt-1 flex items-center">
                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Blockchain
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('patient.records.detail', $record->idmedicalrecord) }}" 
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        @if($records->hasPages())
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            {{ $records->links() }}
        </div>
        @endif

        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'status', 'period']))
                    Tidak ada rekam medis yang sesuai dengan kriteria pencarian Anda.
                @else
                    Rekam medis akan muncul setelah Anda berkunjung ke rumah sakit yang terdaftar dalam sistem.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'period']))
            <div class="mt-6">
                <a href="{{ route('patient.records') }}" 
                   class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Lihat Semua Rekam Medis
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Information Card -->
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tentang Status Rekam Medis</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Draft:</strong> Rekam medis sedang dalam proses penulisan oleh dokter</li>
                        <li><strong>Final:</strong> Rekam medis telah selesai dan dikonfirmasi oleh dokter</li>
                        <li><strong>Immutable:</strong> Rekam medis telah disimpan ke blockchain dan tidak dapat diubah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection