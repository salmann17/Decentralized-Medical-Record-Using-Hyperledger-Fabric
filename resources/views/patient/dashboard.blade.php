@extends('layouts.app')

@section('title', 'Dashboard Pasien - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dashboard Pasien</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            @if($patient)
                Selamat datang {{ $patient->user->name }}. Anda memiliki kontrol penuh atas rekam medis Anda.
            @else
                Selamat datang di dashboard pasien.
            @endif
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Rekam Medis</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalRecords }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Permintaan Akses Baru</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-orange-600">{{ $pendingRequests }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Dokter dengan Akses Aktif</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $activeDoctors }}</dd>
        </div>
    </div>

    <!-- Patient Info Card -->
    @if($patient)
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Informasi Pasien</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Data pribadi dan medis Anda.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $patient->user->name }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">NIK</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $patient->nik }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ \Carbon\Carbon::parse($patient->birthdate)->format('d F Y') }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Golongan Darah</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $patient->blood }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $patient->address }}</dd>
                </div>
            </dl>
        </div>
    </div>
    @endif

    <!-- Pending Access Requests -->
    @if($recentRequests->count() > 0)
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Permintaan Akses Baru</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Dokter yang meminta akses ke rekam medis Anda.</p>
            </div>
            <a href="{{ route('patient.access-requests') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Lihat semua →
            </a>
        </div>
        
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($recentRequests as $request)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ substr($request->doctor->user->name, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $request->doctor->user->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->doctor->spesialization ?? 'Dokter Umum' }} • 
                                    {{ \Carbon\Carbon::parse($request->requested_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('patient.access-requests.approve', $request->idrequest) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-500">
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('patient.access-requests.reject', $request->idrequest) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-500">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Recent Medical Records -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Rekam Medis Terbaru</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Riwayat kunjungan dan perawatan medis Anda.</p>
            </div>
            <a href="{{ route('patient.records') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Lihat semua →
            </a>
        </div>
        
        @if($recentRecords->count() > 0)
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($recentRecords as $record)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record->admin->name ?? 'Admin' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $record->doctor->user->name }} • {{ \Carbon\Carbon::parse($record->visit_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $record->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $record->status === 'final' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $record->status === 'immutable' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                            <a href="{{ route('patient.records.detail', $record->medicalrecord_id) }}" 
                               class="ml-3 text-sm font-medium text-blue-600 hover:text-blue-500">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekam medis</h3>
            <p class="mt-1 text-sm text-gray-500">Rekam medis akan muncul setelah Anda berkunjung ke rumah sakit.</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('patient.access-requests') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-blue-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">Kelola Permintaan Akses</h3>
                <p class="mt-2 text-sm text-gray-500">Setujui atau tolak permintaan akses dari dokter.</p>
            </div>
        </a>

        <a href="{{ route('patient.records') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-green-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">Rekam Medis</h3>
                <p class="mt-2 text-sm text-gray-500">Akses lengkap ke seluruh riwayat medis Anda.</p>
            </div>
        </a>

        <a href="{{ route('patient.audit-trail') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:border-gray-400">
            <div>
                <span class="inline-flex rounded-lg p-3 bg-purple-50 ring-4 ring-white">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125M8.25 8.25V6.108" />
                    </svg>
                </span>
            </div>
            <div class="mt-8">
                <h3 class="text-base font-medium text-gray-900">Audit Trail</h3>
                <p class="mt-2 text-sm text-gray-500">Lihat semua aktivitas akses ke data medis Anda.</p>
            </div>
        </a>
    </div>

    <!-- Privacy & Security Notice -->
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Kontrol Penuh Data Anda</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>
                        Sistem desentralisasi ini memberikan Anda kontrol penuh atas rekam medis. Hanya Anda yang dapat memberikan akses kepada dokter, 
                        dan semua aktivitas tercatat dalam blockchain untuk transparansi dan keamanan maksimal.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection