@extends('layouts.app')

@section('title', 'Pengaturan Akun - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
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

    <!-- Error Messages -->
    @if($errors->any())
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
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
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Pengaturan Akun</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Kelola informasi profil, keamanan akun, dan preferensi sistem Anda.
                </p>
            </div>
            <div>
                <button type="button" onclick="saveAllSettings()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Semua
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Navigation Tabs -->
    <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" name="tabs" class="block w-full focus:ring-blue-500 focus:border-blue-500 border-gray-300 rounded-md">
            <option selected>Profil Dokter</option>
            <option>Keamanan</option>
            <option>Export Data</option>
        </select>
    </div>
    
    <div class="hidden sm:block">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="#profile" class="tab-link active border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Profil Dokter
            </a>
            <a href="#security" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Keamanan
            </a>
            <a href="#export" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Export Data
            </a>
        </nav>
    </div>

    <!-- Profile Tab -->
    <div id="profile-tab" class="tab-content">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Profil Dokter</h3>
                
                <form id="profileForm" action="{{ route('doctor.settings.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Full Name -->
                        <div>
                            <label for="fullName" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                            <input type="text" id="fullName" name="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ isset($doctor) && $doctor->user ? $doctor->user->name : '' }}">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ isset($doctor) && $doctor->user ? $doctor->user->email : '' }}">
                        </div>

                        <!-- Medical License -->
                        <div>
                            <label for="license" class="block text-sm font-medium text-gray-700">Nomor STR (Surat Tanda Registrasi) *</label>
                            <input type="number" id="license" name="license_number" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ isset($doctor) ? $doctor->license_number : '' }}">
                        </div>

                        <!-- Specialization -->
                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700">Spesialisasi *</label>
                            <select id="specialization" name="specialization" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @if(!isset($doctor) || !$doctor->specialization)
                                    <option value="" selected>Pilih Spesialisasi</option>
                                @else
                                    <option value="">Pilih Spesialisasi</option>
                                @endif
                                <option value="Umum" {{ isset($doctor) && $doctor->specialization === 'Umum' ? 'selected' : '' }}>Dokter Umum</option>
                                <option value="Kardiologi" {{ isset($doctor) && $doctor->specialization === 'Kardiologi' ? 'selected' : '' }}>Kardiologi</option>
                                <option value="Neurologi" {{ isset($doctor) && $doctor->specialization === 'Neurologi' ? 'selected' : '' }}>Neurologi</option>
                                <option value="Orthopedi" {{ isset($doctor) && $doctor->specialization === 'Orthopedi' ? 'selected' : '' }}>Orthopedi</option>
                                <option value="Pediatri" {{ isset($doctor) && $doctor->specialization === 'Pediatri' ? 'selected' : '' }}>Pediatri</option>
                                <option value="Kandungan" {{ isset($doctor) && $doctor->specialization === 'Kandungan' ? 'selected' : '' }}>Kandungan</option>
                                <option value="Bedah" {{ isset($doctor) && $doctor->specialization === 'Bedah' ? 'selected' : '' }}>Bedah</option>
                                <option value="Mata" {{ isset($doctor) && $doctor->specialization === 'Mata' ? 'selected' : '' }}>Mata</option>
                                <option value="THT" {{ isset($doctor) && $doctor->specialization === 'THT' ? 'selected' : '' }}>THT</option>
                                <option value="Kulit" {{ isset($doctor) && $doctor->specialization === 'Kulit' ? 'selected' : '' }}>Kulit dan Kelamin</option>
                                <option value="Jiwa" {{ isset($doctor) && $doctor->specialization === 'Jiwa' ? 'selected' : '' }}>Kesehatan Jiwa</option>
                                <option value="Radiologi" {{ isset($doctor) && $doctor->specialization === 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
                                <option value="Anestesi" {{ isset($doctor) && $doctor->specialization === 'Anestesi' ? 'selected' : '' }}>Anestesi</option>
                                <option value="Patologi" {{ isset($doctor) && $doctor->specialization === 'Patologi' ? 'selected' : '' }}>Patologi</option>
                                <option value="Rehabilitasi" {{ isset($doctor) && $doctor->specialization === 'Rehabilitasi' ? 'selected' : '' }}>Rehabilitasi Medik</option>
                            </select>
                        </div>

                        <!-- Hospitals (Many-to-Many Relationship) -->
                        <div class="sm:col-span-2">
                            <label for="hospitals" class="block text-sm font-medium text-gray-700">Rumah Sakit/Klinik Terkait</label>
                            <div class="mt-2 space-y-2">
                                @if(isset($doctor) && $doctor->hospitals && $doctor->hospitals->count() > 0)
                                    @foreach($doctor->hospitals as $hospital)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $hospital->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $hospital->address }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 text-gray-500">
                                        <p>Belum terdaftar di rumah sakit manapun</p>
                                        <p class="text-xs">Hubungi administrator untuk mendaftarkan rumah sakit</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Security Tab -->
    <div id="security-tab" class="tab-content hidden">
        <!-- Change Password -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ubah Password</h3>
                
                <form id="passwordForm" action="{{ route('doctor.settings.password.update') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="currentPassword" class="block text-sm font-medium text-gray-700">Password Saat Ini *</label>
                            <input type="password" id="currentPassword" name="current_password" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-700">Password Baru *</label>
                            <input type="password" id="newPassword" name="password" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <div class="mt-1">
                                <div class="text-xs text-gray-500">
                                    Password harus memiliki minimal 8 karakter
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru *</label>
                            <input type="password" id="confirmPassword" name="password_confirmation" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Data Tab -->
    <div id="export-tab" class="tab-content hidden">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Export Data</h3>
                
                <div class="space-y-6">
                    <!-- Data Export Options -->
                    <div>
                        <h4 class="text-base font-medium text-gray-900">Export Data Dokter</h4>
                        <p class="mt-1 text-sm text-gray-500">Download data Anda dalam berbagai format.</p>
                        
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">Profil dan Pengaturan</h5>
                                    <p class="text-sm text-gray-500">Data profil dokter, pengaturan akun, dan preferensi</p>
                                </div>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export JSON
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">Rekam Medis</h5>
                                    <p class="text-sm text-gray-500">Semua rekam medis yang telah Anda buat</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        Export PDF
                                    </button>
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        Export Excel
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">Audit Trail</h5>
                                    <p class="text-sm text-gray-500">Riwayat aktivitas dan log sistem</p>
                                </div>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    Export CSV
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900">Data Lengkap</h5>
                                    <p class="text-sm text-gray-500">Semua data dalam satu file archive</p>
                                </div>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                    </svg>
                                    Export ZIP
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Account Deletion -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Hapus Akun</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Tindakan ini akan menghapus akun Anda secara permanen dan tidak dapat dibatalkan. Semua data akan dihapus dari sistem.</p>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                                            Hapus Akun Saya
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            tabLinks.forEach(tab => {
                tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected tab
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            const targetTab = this.getAttribute('href').substring(1) + '-tab';
            document.getElementById(targetTab).classList.remove('hidden');
        });
    });

    // 2FA Toggle - Removed as no longer needed
});

// Photo preview - Removed as no longer needed

// Form submissions
function saveAllSettings() {
    // Submit profile form instead of showing alert
    document.getElementById('profileForm').submit();
}

// Profile form
document.getElementById('profileForm').addEventListener('submit', function(e) {
    // Allow form submission to go through to controller
    const name = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const license = document.getElementById('license').value.trim();
    const specialization = document.getElementById('specialization').value;
    
    if (!name || !email || !license || !specialization) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi (*)');
        return false;
    }
});

// Password form
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak sama');
        return false;
    }
    
    if (newPassword.length < 8) {
        e.preventDefault();
        alert('Password harus minimal 8 karakter');
        return false;
    }
    
    // Allow form to submit to controller
});
</script>
@endsection