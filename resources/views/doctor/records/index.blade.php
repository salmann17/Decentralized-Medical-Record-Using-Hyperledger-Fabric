@extends('layouts.app')

@section('title', 'Rekam Medis - Decentralized Medical Record')

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
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Rekam Medis</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Semua rekam medis yang telah Anda buat untuk pasien-pasien Anda.
                </p>
            </div>
            <div>
                <a href="{{ route('doctor.my-patients') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Rekam Medis Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('doctor.records') }}" 
                       class="border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status', 'all') === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                        <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ isset($records) ? (method_exists($records, 'total') ? $records->total() : $records->count()) : 0 }}
                        </span>
                    </a>
                    
                    <a href="{{ route('doctor.records', ['status' => 'draft']) }}" 
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'draft' ? 'border-yellow-500 text-yellow-600' : '' }}">
                        Draft
                        <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ isset($records) ? $records->where('status', 'draft')->count() : 0 }}
                        </span>
                    </a>
                    
                    <a href="{{ route('doctor.records', ['status' => 'final']) }}" 
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'final' ? 'border-green-500 text-green-600' : '' }}">
                        Final
                        <span class="ml-2 bg-green-100 text-green-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ isset($records) ? $records->where('status', 'final')->count() : 0 }}
                        </span>
                    </a>
                    
                </nav>
            </div>
        </div>
    </div>

    <!-- Records Grid -->
    @if(isset($records) && $records->count() > 0)
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @foreach($records as $record)
            <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <!-- Record Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-700">
                                        {{ substr($record->patient->user->name ?? 'P', 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $record->patient->user->name ?? 'Pasien' }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $record->visit_date ? date('d/m/Y', strtotime($record->visit_date)) : date('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($record->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Draft
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Final
                                </span>
                        </div>
                            @endif
                    </div>

                    <!-- Record Content -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900">Diagnosis</h4>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ Str::limit($record->diagnosis_desc ?? 'Diagnosis tidak tersedia', 100) }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900">Treatment</h4>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ Str::limit($record->treatment ?? 'Treatment tidak tersedia', 100) }}
                        </p>
                    </div>

                    <!-- Blockchain Status -->
                    <div class="mt-4">
                        <div class="flex items-center space-x-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span class="text-xs text-gray-500">
                                Blockchain: 
                                <span class="text-blue-600">{{ isset($record->blockchain_hash) && $record->blockchain_hash ? 'Verified' : 'Pending Integration' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-2">
                        <a href="{{ route('doctor.show-record', $record->medicalrecord_id ?? $record->id ?? 1) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-3 py-2 text-xs font-medium rounded-md">
                            Lihat Detail
                        </a>
                        @if($record->status === 'draft')
                            <form method="POST" action="{{ route('doctor.update-record-status', $record->medicalrecord_id ?? $record->id ?? 1) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="final">
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 text-xs font-medium rounded-md"
                                        onclick="return confirm('Finalisasi rekam medis ini?')">
                                    Finalisasi
                                </button>
                            </form>
                        @elseif($record->status === 'final')
                            <form method="POST" action="{{ route('doctor.update-record-status', $record->medicalrecord_id ?? $record->id ?? 1) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="immutable">
                                <button type="submit" 
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 text-xs font-medium rounded-md"
                                        onclick="return confirm('PERINGATAN: Rekam medis akan menjadi immutable dan tidak dapat diubah lagi!')">
                                    Immutable
                                </button>
                            </form>
                        @endif
                        @if($record->status !== 'immutable')
                            <a href="#" 
                               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center px-3 py-2 text-xs font-medium rounded-md">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($records) && method_exists($records, 'links'))
        <div class="mt-8">
            {{ $records->links() }}
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">
                Anda belum membuat rekam medis. Mulai dengan memilih pasien dan membuat rekam medis baru.
            </p>
            <div class="mt-6">
                <a href="{{ route('doctor.my-patients') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Pilih Pasien
                </a>
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
                <h3 class="text-sm font-medium text-blue-800">Tentang Status Rekam Medis</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Draft</strong>: Rekam medis masih dapat diedit</li>
                        <li><strong>Final</strong>: Rekam medis sudah final</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection