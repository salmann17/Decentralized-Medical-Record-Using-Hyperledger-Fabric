@extends('layouts.app')

@section('title', 'Login - Decentralized Medical Record')

@section('content')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Masuk ke akun Anda
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Belum punya akun? Daftar sebagai:
        </p>
        <p class="mt-1 text-center text-sm text-gray-600">
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Pasien
            </a>
            •
            <a href="{{ route('register.doctor') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Dokter
            </a>
            •
            <a href="{{ route('register.admin') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Admin
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="{{ route('login.submit') }}">
                @csrf
                
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
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" 
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Ingat saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="flex w-full justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Masuk
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">Demo Akun</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Admin</p>
                        <p class="text-xs font-medium">admin1@hospital.com</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Dokter</p>
                        <p class="text-xs font-medium">doctor1@mail.com</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Pasien</p>
                        <p class="text-xs font-medium">patient1@mail.com</p>
                    </div>
                </div>
                <p class="mt-2 text-center text-xs text-gray-500">Password: password123</p>
            </div>
        </div>
    </div>
</div>
@endsection