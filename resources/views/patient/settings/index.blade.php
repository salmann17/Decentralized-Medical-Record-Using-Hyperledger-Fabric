@extends('layouts.app')

@section('title', 'Pengaturan - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <h3 class="text-2xl font-semibold leading-6 text-gray-900">Pengaturan Akun</h3>
        <p class="mt-2 max-w-4xl text-sm text-gray-500">
            Kelola profil dan pengaturan privasi akun Anda. Pastikan informasi selalu terbaru untuk kemudahan akses layanan medis.
        </p>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Profile Information -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Profil</h3>
            <form method="POST" action="{{ route('patient.settings.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" 
                               name="name" 
                               id="name"
                               value="{{ old('name', isset($patient) && isset($patient->user) ? $patient->user->name : '') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               value="{{ old('email', isset($patient) && isset($patient->user) ? $patient->user->email : '') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" 
                               name="date_of_birth" 
                               id="date_of_birth"
                               value="{{ old('date_of_birth', isset($patient) ? $patient->date_of_birth : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('date_of_birth') border-red-300 @enderror">
                        @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" 
                                id="gender"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('gender') border-red-300 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', isset($patient) ? $patient->gender : '') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', isset($patient) ? $patient->gender : '') === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="tel" 
                               name="phone" 
                               id="phone"
                               value="{{ old('phone', isset($patient) ? $patient->phone : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-300 @enderror">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Kontak Darurat</label>
                        <input type="tel" 
                               name="emergency_contact" 
                               id="emergency_contact"
                               value="{{ old('emergency_contact', isset($patient) ? $patient->emergency_contact : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('emergency_contact') border-red-300 @enderror">
                        @error('emergency_contact')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="address" 
                              id="address"
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-300 @enderror">{{ old('address', isset($patient) ? $patient->address : '') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medical Information -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-gray-900">Informasi Medis</h4>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Blood Type -->
                        <div>
                            <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                            <select name="blood_type" 
                                    id="blood_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('blood_type') border-red-300 @enderror">
                                <option value="">Pilih Golongan Darah</option>
                                <option value="A+" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type', isset($patient) ? $patient->blood_type : '') === 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Insurance Number -->
                        <div>
                            <label for="insurance_number" class="block text-sm font-medium text-gray-700">Nomor Asuransi/BPJS</label>
                            <input type="text" 
                                   name="insurance_number" 
                                   id="insurance_number"
                                   value="{{ old('insurance_number', isset($patient) ? $patient->insurance_number : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('insurance_number') border-red-300 @enderror">
                            @error('insurance_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700">Alergi (jika ada)</label>
                        <textarea name="allergies" 
                                  id="allergies"
                                  rows="2"
                                  placeholder="Sebutkan alergi yang Anda miliki..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('allergies') border-red-300 @enderror">{{ old('allergies', isset($patient) ? $patient->allergies : '') }}</textarea>
                        @error('allergies')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Chronic Conditions -->
                    <div>
                        <label for="chronic_conditions" class="block text-sm font-medium text-gray-700">Penyakit Kronis (jika ada)</label>
                        <textarea name="chronic_conditions" 
                                  id="chronic_conditions"
                                  rows="2"
                                  placeholder="Sebutkan penyakit kronis yang Anda miliki..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('chronic_conditions') border-red-300 @enderror">{{ old('chronic_conditions', isset($patient) ? $patient->chronic_conditions : '') }}</textarea>
                        @error('chronic_conditions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ubah Kata Sandi</h3>
            <form method="POST" action="{{ route('patient.settings.change-password') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Kata Sandi Saat Ini</label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('current_password') border-red-300 @enderror">
                        @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                        <input type="password" 
                               name="new_password" 
                               id="new_password"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('new_password') border-red-300 @enderror">
                        @error('new_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" 
                               name="new_password_confirmation" 
                               id="new_password_confirmation"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Save Password Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159-.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1.02.43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Privacy Settings -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Pengaturan Privasi</h3>
            <form method="POST" action="{{ route('patient.settings.privacy') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <!-- Auto-approve access -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label for="auto_approve_access" class="text-sm font-medium text-gray-700">
                                Persetujuan Otomatis
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Secara otomatis menyetujui permintaan akses dari dokter yang pernah merawat Anda sebelumnya
                            </p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" 
                                   name="auto_approve_access" 
                                   id="auto_approve_access"
                                   value="1"
                                   {{ old('auto_approve_access', isset($patient) ? $patient->auto_approve_access : false) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Notification preferences -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label for="email_notifications" class="text-sm font-medium text-gray-700">
                                Notifikasi Email
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Terima notifikasi email untuk aktivitas penting terkait rekam medis
                            </p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" 
                                   name="email_notifications" 
                                   id="email_notifications"
                                   value="1"
                                   {{ old('email_notifications', isset($patient) ? $patient->email_notifications : true) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Data sharing -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label for="allow_research_data" class="text-sm font-medium text-gray-700">
                                Izinkan Penggunaan Data untuk Penelitian
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Data Anda dapat digunakan secara anonim untuk penelitian medis (identitas tidak akan diungkap)
                            </p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" 
                                   name="allow_research_data" 
                                   id="allow_research_data"
                                   value="1"
                                   {{ old('allow_research_data', isset($patient) ? $patient->allow_research_data : false) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Save Privacy Settings -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Pengaturan Privasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-gray-50 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Akun</h3>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Patient ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ isset($patient) ? ($patient->patient_id ?? 'N/A') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Registrasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($patient) && isset($patient->user) && $patient->user->created_at ? \Carbon\Carbon::parse($patient->user->created_at)->format('d M Y, H:i') : 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Login</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($patient) && isset($patient->user) && $patient->user->last_login_at ? \Carbon\Carbon::parse($patient->user->last_login_at)->diffForHumans() : 'Belum pernah login' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Akun</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection