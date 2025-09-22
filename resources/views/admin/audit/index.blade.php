@extends('layouts.app')

@section('title', 'Audit Trail - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Audit Trail</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Log akses rekam medis di {{ $hospital->name }}. 
            <span class="text-orange-600 font-medium">Menampilkan metadata akses tanpa detail isi rekam medis.</span>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Akses</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $auditLogs->total() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Akses Hari Ini</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">
                {{ $auditLogs->where('access_time', '>=', now()->startOfDay())->count() }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Akses View</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">
                {{ $auditLogs->where('action', 'view')->count() }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Akses Create</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-purple-600">
                {{ $auditLogs->where('action', 'create')->count() }}
            </dd>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filter Audit Log</h3>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700">Jenis Aksi</label>
                    <select name="action" id="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Aksi</option>
                        <option value="view" {{ request('action') === 'view' ? 'selected' : '' }}>View</option>
                        <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Tanggal Dari</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.audit') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Log Aktivitas</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Riwayat akses rekam medis oleh dokter dan staff medis.
            </p>
        </div>

        @if($auditLogs->count() > 0)
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu Akses
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rekam Medis
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pasien
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($auditLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($log->access_time)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ ucfirst($log->user->role) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->action === 'view')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        View
                                    </span>
                                @elseif($log->action === 'create')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Create
                                    </span>
                                @elseif($log->action === 'update')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Update
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $log->medical_record->medical_record_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $log->medicalRecord->patient->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $log->medicalRecord->patient->nik }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $auditLogs->appends(request()->query())->links() }}
            </div>
        </div>
        @else
        <div class="border-t border-gray-200 px-4 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada log aktivitas</h3>
            <p class="mt-1 text-sm text-gray-500">
                Belum ada aktivitas akses rekam medis yang tercatat.
            </p>
        </div>
        @endif
    </div>

    <!-- Security Notice -->
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Audit Trail & Keamanan</h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Setiap akses rekam medis dicatat secara otomatis dalam sistem audit</li>
                        <li>Log audit hanya menampilkan metadata, bukan isi detail rekam medis</li>
                        <li>Informasi termasuk: waktu akses, user, jenis aksi, dan IP address</li>
                        <li>Data audit mendukung compliance dan transparansi sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection