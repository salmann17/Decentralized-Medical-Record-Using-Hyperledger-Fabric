@extends('layouts.app')

@section('title', 'Register Dokter - Decentralized Medical Record')

@section('content')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Daftar sebagai Dokter
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Masuk di sini
            </a>
        </p>
        <p class="mt-1 text-center text-sm text-gray-600">
            Atau daftar sebagai
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Pasien
            </a>
            atau
            <a href="{{ route('register.admin') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Admin
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="{{ route('register.doctor.submit') }}">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Lengkap
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required 
                               value="{{ old('name') }}"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               value="{{ old('email') }}"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700">
                        Nomor Lisensi Dokter
                    </label>
                    <div class="mt-1">
                        <input id="license_number" name="license_number" type="text" required 
                               value="{{ old('license_number') }}"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                               placeholder="Contoh: 12345678">
                    </div>
                    @error('license_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="spesialization" class="block text-sm font-medium text-gray-700">
                        Spesialisasi
                    </label>
                    <div class="mt-1">
                        <select id="spesialization" name="spesialization" required 
                                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                            <option value="">Pilih Spesialisasi</option>
                            <option value="Umum" {{ old('spesialization') == 'Umum' ? 'selected' : '' }}>Umum</option>
                            <option value="Anak" {{ old('spesialization') == 'Anak' ? 'selected' : '' }}>Anak (Pediatri)</option>
                            <option value="Penyakit Dalam" {{ old('spesialization') == 'Penyakit Dalam' ? 'selected' : '' }}>Penyakit Dalam</option>
                            <option value="Bedah" {{ old('spesialization') == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                            <option value="Kandungan" {{ old('spesialization') == 'Kandungan' ? 'selected' : '' }}>Kandungan (Obgyn)</option>
                            <option value="Jantung" {{ old('spesialization') == 'Jantung' ? 'selected' : '' }}>Jantung (Kardiologi)</option>
                            <option value="Paru" {{ old('spesialization') == 'Paru' ? 'selected' : '' }}>Paru (Pulmonologi)</option>
                            <option value="Saraf" {{ old('spesialization') == 'Saraf' ? 'selected' : '' }}>Saraf (Neurologi)</option>
                            <option value="Mata" {{ old('spesialization') == 'Mata' ? 'selected' : '' }}>Mata (Oftalmologi)</option>
                            <option value="THT" {{ old('spesialization') == 'THT' ? 'selected' : '' }}>THT</option>
                            <option value="Kulit" {{ old('spesialization') == 'Kulit' ? 'selected' : '' }}>Kulit (Dermatologi)</option>
                            <option value="Gigi" {{ old('spesialization') == 'Gigi' ? 'selected' : '' }}>Gigi</option>
                            <option value="Orthopedi" {{ old('spesialization') == 'Orthopedi' ? 'selected' : '' }}>Orthopedi</option>
                            <option value="Psikiatri" {{ old('spesialization') == 'Psikiatri' ? 'selected' : '' }}>Psikiatri</option>
                            <option value="Radiologi" {{ old('spesialization') == 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
                            <option value="Anestesi" {{ old('spesialization') == 'Anestesi' ? 'selected' : '' }}>Anestesi</option>
                        </select>
                    </div>
                    @error('spesialization')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Konfirmasi Password
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                    </div>
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" 
                            class="flex w-full justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Daftar sebagai Dokter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
