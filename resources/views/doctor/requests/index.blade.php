@extends('layouts.app')

@section('title', 'Permintaan Akses Pasien - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Permintaan Akses Pasien</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Kelola permintaan akses data pasien. Anda hanya dapat mengakses data pasien yang telah menyetujui permintaan akses.
                </p>
            </div>
            <div>
                <a href="{{ route('doctor.access-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Permintaan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="border-b border-gray-200">
                @php
                    // Get all requests for counting (tidak difilter)
                    $allRequests = App\Models\AccessRequest::where('admin_id', $admin->idadmin)->get();
                    $currentStatus = request('status');
                @endphp
                
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('doctor.access-requests') }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ !$currentStatus ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                        <span class="ml-2 {{ !$currentStatus ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $allRequests->count() }}
                        </span>
                    </a>
                    
                    <a href="{{ route('doctor.access-requests', ['status' => 'pending']) }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $currentStatus === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pending
                        <span class="ml-2 {{ $currentStatus === 'pending' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $allRequests->where('status', 'pending')->count() }}
                        </span>
                    </a>
                    
                    <a href="{{ route('doctor.access-requests', ['status' => 'approved']) }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $currentStatus === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Disetujui
                        <span class="ml-2 {{ $currentStatus === 'approved' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $allRequests->where('status', 'approved')->count() }}
                        </span>
                    </a>
                    
                    <a href="{{ route('doctor.access-requests', ['status' => 'rejected']) }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $currentStatus === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Ditolak
                        <span class="ml-2 {{ $currentStatus === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $allRequests->where('status', 'rejected')->count() }}
                        </span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Access Requests Table -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if(isset($requests) && $requests->count() > 0)
                <div class="flow-root">
                    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Pasien
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Status
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Tanggal Permintaan
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Tanggal Respons
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Blockchain Hash
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($requests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ substr($request->patient->user->name ?? 'P', 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $request->patient->user->name ?? 'Pasien' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $request->patient->user->email ?? 'email@pasien.com' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($request->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Pending
                                                </span>
                                            @elseif($request->status === 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Disetujui
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $request->requested_at ? date('d/m/Y H:i', strtotime($request->requested_at)) : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $request->responded_at ? date('d/m/Y H:i', strtotime($request->responded_at)) : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <!-- TODO: blockchain integration - show actual blockchain hash -->
                                            <span class="text-gray-400">Akan tersedia</span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            @if($request->status === 'approved')
                                                <a href="{{ route('doctor.patient-records', $request->patient->idpatient) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Lihat Rekam Medis
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <!-- Pagination will be implemented when we convert to paginated results -->
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada permintaan akses</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat permintaan akses ke pasien.</p>
                    <div class="mt-6">
                        <a href="{{ route('doctor.access-requests.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Permintaan Akses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tentang Permintaan Akses</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Anda hanya dapat mengakses data pasien yang telah menyetujui permintaan</li>
                        <li>Status <strong>pending</strong> berarti menunggu persetujuan pasien</li>
                        <li>Status <strong>approved</strong> berarti Anda dapat mengakses data pasien</li>
                        <li>Status <strong>rejected</strong> berarti permintaan ditolak oleh pasien</li>
                        <li>Blockchain hash akan dibuat otomatis setelah integrasi blockchain aktif</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection