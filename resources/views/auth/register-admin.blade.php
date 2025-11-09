@extends('layouts.app')

@section('title', 'Register Admin - Decentralized Medical Record')

@section('content')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Daftar sebagai Admin Rumah Sakit
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
            <a href="{{ route('register.doctor') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Dokter
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="{{ route('register.admin.submit') }}">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Penanggung Jawab
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required 
                               value="{{ old('name') }}"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                               placeholder="Nama pribadi Anda">
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
                    <label for="admin_name" class="block text-sm font-medium text-gray-700">
                        Nama Rumah Sakit/Klinik
                    </label>
                    <div class="mt-1">
                        <input id="admin_name" name="admin_name" type="text" required 
                               value="{{ old('admin_name') }}"
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                               placeholder="Contoh: RS Jakarta Medical Center">
                    </div>
                    @error('admin_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                        Jenis Fasilitas Kesehatan
                    </label>
                    <div class="mt-1">
                        <select id="type" name="type" required 
                                class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                            <option value="">Pilih Jenis</option>
                            <option value="Rumah Sakit" {{ old('type') == 'Rumah Sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                            <option value="Klinik" {{ old('type') == 'Klinik' ? 'selected' : '' }}>Klinik</option>
                            <option value="Puskesmas" {{ old('type') == 'Puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                        </select>
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Alamat Lengkap
                    </label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="3" required 
                                  class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                                  placeholder="Alamat lengkap rumah sakit/klinik">{{ old('address') }}</textarea>
                    </div>
                    @error('address')
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
                        Daftar sebagai Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
