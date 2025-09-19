@extends('layouts.app')

@section('title', 'Home - Decentralized Medical Record')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="relative isolate px-6 pt-14 lg:px-8">
        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                    Decentralized Medical Record
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Sistem rekam medis terdesentralisasi yang memberikan kontrol penuh kepada pasien atas data medis mereka. 
                    Keamanan terjamin dengan teknologi blockchain dan akses yang transparan.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ route('register') }}" 
                       class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" 
                       class="text-sm font-semibold leading-6 text-gray-900">
                        Login <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-blue-600">Keunggulan</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Revolusi Sistem Rekam Medis
                </p>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Teknologi blockchain dan sistem terdesentralisasi memberikan keamanan, transparansi, dan kontrol yang belum pernah ada sebelumnya dalam pengelolaan data medis.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-3 lg:gap-y-16">
                    <!-- Feature 1: Kontrol Akses -->
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            Kontrol Akses Pasien
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600">
                            Pasien memiliki kendali penuh atas siapa yang dapat mengakses data medis mereka. 
                            Setiap permintaan akses dari dokter harus mendapat persetujuan eksplisit dari pasien.
                        </dd>
                    </div>

                    <!-- Feature 2: Jejak Audit -->
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-12c0 1.232-.046 2.453-.138 3.662a4.006 4.006 0 01-3.7 3.7 48.678 48.678 0 01-7.324 0 4.006 4.006 0 01-3.7-3.7c-.017-.22-.032-.441-.046-.662M12 21a9 9 0 00-9-9m9 9a9 9 0 019-9M15 9H9m12 0A9 9 0 1121 9z" />
                                </svg>
                            </div>
                            Jejak Audit Transparan
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600">
                            Setiap akses dan modifikasi data medis tercatat dalam audit trail yang dapat dilihat pasien. 
                            Transparansi penuh siapa, kapan, dan mengapa data diakses.
                        </dd>
                    </div>

                    <!-- Feature 3: Integritas Data -->
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            Integritas Data Blockchain
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600">
                            Hash setiap rekam medis disimpan di blockchain, memastikan data tidak dapat dimanipulasi. 
                            Autentisitas dan integritas data terjamin selamanya.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="bg-gray-50 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-blue-600">Cara Kerja</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Proses Sederhana, Keamanan Maksimal
                </p>
            </div>
            
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <div class="grid grid-cols-1 gap-y-16 lg:grid-cols-4 lg:gap-x-8">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-white text-xl font-bold">
                            1
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-gray-900">Dokter Meminta Akses</h3>
                        <p class="mt-2 text-gray-600">
                            Dokter mengajukan permintaan untuk mengakses rekam medis pasien tertentu melalui sistem.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-white text-xl font-bold">
                            2
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-gray-900">Pasien Menyetujui</h3>
                        <p class="mt-2 text-gray-600">
                            Pasien menerima notifikasi dan memutuskan untuk menyetujui atau menolak permintaan akses.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-white text-xl font-bold">
                            3
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-gray-900">Dokter Menambah Data</h3>
                        <p class="mt-2 text-gray-600">
                            Setelah akses disetujui, dokter dapat menambah atau melihat rekam medis pasien.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-white text-xl font-bold">
                            4
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-gray-900">Hash ke Blockchain</h3>
                        <p class="mt-2 text-gray-600">
                            Hash rekam medis disimpan ke blockchain untuk menjamin integritas dan keaslian data.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600">
        <div class="px-6 py-24 sm:px-6 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                    Mulai Kontrol Data Medis Anda
                </h2>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-blue-200">
                    Bergabunglah dengan revolusi sistem rekam medis yang memberikan kendali penuh kepada pasien.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ route('register') }}" 
                       class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        Daftar Gratis
                    </a>
                    <a href="#" class="text-sm font-semibold leading-6 text-white">
                        Pelajari lebih lanjut <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center space-x-6 md:order-2">
                <p class="text-xs leading-5 text-gray-500">
                    Demo aplikasi Decentralized Medical Record
                </p>
            </div>
            <div class="mt-8 md:order-1 md:mt-0">
                <p class="text-center text-xs leading-5 text-gray-500">
                    &copy; 2025 Decentralized Medical Record. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </footer>
</div>
@endsection