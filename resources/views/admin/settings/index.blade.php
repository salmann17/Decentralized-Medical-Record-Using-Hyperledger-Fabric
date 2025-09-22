@extends('layouts.app')

@section('title', 'Pengaturan Rumah Sakit - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Pengaturan Rumah Sakit</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Kelola informasi profil dan pengaturan {{ $hospital->name }}.
        </p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Hospital Profile Form -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Profil Rumah Sakit</h3>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Hospital Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Rumah Sakit</label>
                    <input type="text" name="name" id="name" 
                           value="{{ old('name', $hospital->name) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hospital Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe Rumah Sakit</label>
                    <select name="type" id="type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tipe Rumah Sakit</option>
                        <option value="Rumah Sakit Umum" {{ old('type', $hospital->type) === 'Rumah Sakit Umum' ? 'selected' : '' }}>
                            Rumah Sakit Umum
                        </option>
                        <option value="Rumah Sakit Khusus" {{ old('type', $hospital->type) === 'Rumah Sakit Khusus' ? 'selected' : '' }}>
                            Rumah Sakit Khusus
                        </option>
                        <option value="Rumah Sakit Pendidikan" {{ old('type', $hospital->type) === 'Rumah Sakit Pendidikan' ? 'selected' : '' }}>
                            Rumah Sakit Pendidikan
                        </option>
                        <option value="Rumah Sakit Pemerintah" {{ old('type', $hospital->type) === 'Rumah Sakit Pemerintah' ? 'selected' : '' }}>
                            Rumah Sakit Pemerintah
                        </option>
                        <option value="Rumah Sakit Swasta" {{ old('type', $hospital->type) === 'Rumah Sakit Swasta' ? 'selected' : '' }}>
                            Rumah Sakit Swasta
                        </option>
                        <option value="Klinik" {{ old('type', $hospital->type) === 'Klinik' ? 'selected' : '' }}>
                            Klinik
                        </option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hospital Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="4" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                              required>{{ old('address', $hospital->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hospital Statistics -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Statistik Rumah Sakit</h3>
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-gray-50 px-4 py-5 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Dokter</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $hospital->doctors()->count() }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pasien</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $hospital->medicalRecords()->distinct('patient_id')->count() }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Rekam Medis</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ $hospital->medicalRecords()->count() }}
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Hospital Information -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informasi Sistem</h3>
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID Rumah Sakit</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $hospital->hospital_id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Registrasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($hospital->created_at)->format('d/m/Y H:i') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($hospital->updated_at)->format('d/m/Y H:i') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Blockchain</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Terhubung
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Keamanan</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Audit Trail</h4>
                        <p class="text-sm text-gray-500">Catat semua aktivitas akses rekam medis</p>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Enkripsi Data</h4>
                        <p class="text-sm text-gray-500">Enkripsi otomatis untuk semua data sensitif</p>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Blockchain Integration</h4>
                        <p class="text-sm text-gray-500">Integrasi dengan Hyperledger Fabric</p>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
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
                <h3 class="text-sm font-medium text-blue-800">Catatan Penting</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Perubahan data rumah sakit akan tercatat dalam audit trail</li>
                        <li>Pastikan informasi yang dimasukkan akurat dan sesuai dengan dokumen resmi</li>
                        <li>Kontak administrator sistem jika membutuhkan bantuan teknis</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection