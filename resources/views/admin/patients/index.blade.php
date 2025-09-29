@extends('layouts.app')

@section('title', 'Manajemen Pasien - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Manajemen Pasien</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Daftar pasien yang memiliki rekam medis di {{ $hospital ? $hospital->name : 'rumah sakit ini' }}. 
            <span class="text-orange-600 font-medium">Data ditampilkan sesuai aturan privasi - hanya informasi identitas dasar.</span>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Pasien</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalPatients ?? 0 }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Pasien Aktif Bulan Ini</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $activePatientsThisMonth ?? 0 }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Kunjungan Terakhir</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ $lastVisitDate ? \Carbon\Carbon::parse($lastVisitDate)->format('d/m/Y') : '-' }}
            </dd>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Daftar Pasien</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Informasi dasar pasien yang pernah berobat di rumah sakit ini.
            </p>
        </div>

        @if($patients && $patients->count() > 0)
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Pasien
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIK
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gender
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Lahir
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Golongan Darah
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kota
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($patient->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $patient->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $patient->nik }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $patient->gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($patient->blood)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $patient->blood }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ Str::limit($patient->address, 30) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $patients->links() }}
            </div>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.025" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pasien</h3>
            <p class="mt-1 text-sm text-gray-500">
                Belum ada pasien yang memiliki rekam medis di rumah sakit ini.
            </p>
        </div>
        @endif
    </div>

    <!-- Privacy Notice -->
    <div class="rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Ketentuan Privasi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>
                        Sesuai dengan konsep desentralisasi dan aturan privasi, admin rumah sakit hanya dapat melihat data identitas dasar pasien. 
                        Detail isi rekam medis tetap menjadi hak akses penuh pasien dan dokter yang merawat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection