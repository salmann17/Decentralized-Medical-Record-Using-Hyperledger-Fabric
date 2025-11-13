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

    <!-- Patient Filter -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-4">
                <label for="patient-filter" class="text-sm font-medium text-gray-700">Filter Pasien:</label>
                <select id="patient-filter" 
                        onchange="filterByPatient(this.value)"
                        class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="all" {{ request('patient_id', 'all') === 'all' ? 'selected' : '' }}>Semua Pasien</option>
                    @foreach($patients as $patient)
                    <option value="{{ $patient->idpatient }}" {{ request('patient_id') == $patient->idpatient ? 'selected' : '' }}>
                        {{ $patient->user->name }}
                    </option>
                    @endforeach
                </select>
                @if(request('patient_id') && request('patient_id') !== 'all')
                <a href="{{ route('doctor.records') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Reset Filter
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('doctor.records', array_filter(['patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status', 'all') === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                        <span class="ml-2 {{ request('status', 'all') === 'all' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalAll ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.records', array_filter(['status' => 'draft', 'patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'draft' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Draft
                        <span class="ml-2 {{ request('status') === 'draft' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalDraft ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.records', array_filter(['status' => 'final', 'patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'final' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Final
                        <span class="ml-2 {{ request('status') === 'final' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalFinal ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.records', array_filter(['status' => 'verified', 'patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'verified' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                         Terverifikasi
                        <span class="ml-2 {{ request('status') === 'verified' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalVerified ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.records', array_filter(['status' => 'invalid', 'patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'invalid' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Invalid
                        <span class="ml-2 {{ request('status') === 'invalid' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalInvalid ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.records', array_filter(['status' => 'not_found', 'patient_id' => request('patient_id')])) }}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'not_found' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                         Not Found
                        <span class="ml-2 {{ request('status') === 'not_found' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $totalNotFound ?? 0 }}
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
                        @elseif($record->status === 'final')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Final
                        </span>
                        @endif
                        @if($record->version > 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            v{{ $record->version }}
                        </span>
                        @endif
                    </div>
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
                        @php
                        $blockchainHash = $record->auditTrails && $record->auditTrails->count() > 0 ? $record->auditTrails->first()->blockchain_hash : null;
                        $isValid = $blockchainHash && !str_starts_with($blockchainHash, 'INVALID_') && !str_starts_with($blockchainHash, 'NOT_FOUND_');
                        $isInvalid = $blockchainHash && str_starts_with($blockchainHash, 'INVALID_');
                        $isNotFound = $blockchainHash && str_starts_with($blockchainHash, 'NOT_FOUND_');
                        @endphp
                        
                        @if($isValid)
                            <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs text-green-700">
                                Blockchain: <span class="font-semibold text-green-600">✓ Terverifikasi</span>
                                <span class="text-gray-400 ml-1" title="{{ $blockchainHash }}">
                                    ({{ Str::limit($blockchainHash, 8, '...') }})
                                </span>
                            </span>
                        @elseif($isInvalid)
                            <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-xs text-red-700">
                                Blockchain: <span class="font-semibold text-red-600">⚠ Data Dimodifikasi</span>
                            </span>
                        @elseif($isNotFound)
                            <svg class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs text-yellow-700">
                                Blockchain: <span class="font-semibold text-yellow-600">❌ Tidak Ditemukan</span>
                            </span>
                        @else
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span class="text-xs text-gray-500">
                                Blockchain: <span class="text-gray-500">Belum Diverifikasi</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-2">
                    <a href="{{ route('doctor.show-record', $record->idmedicalrecord) }}"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-3 py-2 text-xs font-medium rounded-md">
                        Lihat Detail
                    </a>
                    <button type="button"
                        onclick="verifyBlockchain({{ $record->idmedicalrecord }})"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center px-3 py-2 text-xs font-medium rounded-md">
                        Verifikasi Blockchain
                    </button>
                    @if($record->status === 'draft')
                    <form method="POST" action="{{ route('doctor.finalize-record', $record->idmedicalrecord) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 text-xs font-medium rounded-md"
                            onclick="return confirm('Finalisasi rekam medis ini?')">
                            Finalisasi
                        </button>
                    </form>
                    <a href="{{ route('doctor.edit-draft', $record->idmedicalrecord) }}"
                        class="flex-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-center px-3 py-2 text-xs font-medium rounded-md">
                        Edit Draft
                    </a>
                    @elseif($record->status === 'final')
                    <a href="{{ route('doctor.edit-record', $record->idmedicalrecord) }}"
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
        {{ $records->appends(request()->query())->links() }}
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
                        <li><strong>Draft</strong>: Rekam medis masih dapat diedit dan diubah</li>
                        <li><strong>Final</strong>: Rekam medis sudah lengkap</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Filter by patient function
    function filterByPatient(patientId) {
        const currentStatus = '{{ request("status", "all") }}';
        const url = new URL(window.location.href);
        
        // Remove old parameters
        url.search = '';
        
        // Add patient filter
        if (patientId !== 'all') {
            url.searchParams.set('patient_id', patientId);
        }
        
        // Add status filter if not 'all'
        if (currentStatus !== 'all') {
            url.searchParams.set('status', currentStatus);
        }
        
        window.location.href = url.toString();
    }

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

        fetch(`/doctor/records/${recordId}/verify-blockchain`, {
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