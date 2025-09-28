@extends('layouts.app')

@section('title', 'Buat Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Buat Rekam Medis Baru</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    @if(isset($patient))
                        Membuat rekam medis baru untuk {{ $patient->user->name ?? 'Pasien' }}
                    @else
                        Pilih pasien terlebih dahulu untuk membuat rekam medis
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ isset($patient) ? route('doctor.patient-records', $patient->patient_id) : route('doctor.my-patients') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    @if(isset($patient))
        <!-- Patient Info -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-lg font-medium text-blue-700">
                                {{ substr($patient->user->name ?? 'P', 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $patient->user->name ?? 'Pasien' }}</h3>
                        <p class="text-sm text-gray-500">{{ $patient->user->email ?? 'email@pasien.com' }}</p>
                        <div class="mt-2 flex space-x-4 text-sm text-gray-500">
                            <span>{{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                            <span>{{ $patient->blood ?? 'Golongan darah tidak diketahui' }}</span>
                            @if(isset($patient->birthdate))
                                <span>{{ \Carbon\Carbon::parse($patient->birthdate)->age }} tahun</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Record Form -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('doctor.store-record', $patient->patient_id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Visit Information -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Kunjungan</h3>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="visit_date" class="block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                                <div class="mt-1">
                                    <input type="date" 
                                           id="visit_date" 
                                           name="visit_date" 
                                           value="{{ date('Y-m-d') }}"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vital Signs -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tanda-tanda Vital</h3>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-4">
                            <div>
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700">Tekanan Darah</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           id="blood_pressure" 
                                           name="blood_pressure" 
                                           placeholder="120/80 mmHg"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700">Denyut Nadi</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="heart_rate" 
                                           name="heart_rate" 
                                           placeholder="80"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">bpm</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700">Suhu Tubuh</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="temperature" 
                                           name="temperature" 
                                           step="0.1"
                                           placeholder="36.5"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">°C</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Pernapasan</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="respiratory_rate" 
                                           name="respiratory_rate" 
                                           placeholder="20"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">/min</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chief Complaint -->
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700">Keluhan Utama</label>
                        <div class="mt-1">
                            <textarea id="chief_complaint" 
                                      name="chief_complaint" 
                                      rows="3" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                      placeholder="Jelaskan keluhan utama pasien..."
                                      required></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Keluhan utama yang disampaikan oleh pasien.</p>
                    </div>

                    <!-- History of Present Illness -->
                    <div>
                        <label for="history_present_illness" class="block text-sm font-medium text-gray-700">Riwayat Penyakit Sekarang</label>
                        <div class="mt-1">
                            <textarea id="history_present_illness" 
                                      name="history_present_illness" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                      placeholder="Riwayat perkembangan penyakit saat ini..."
                                      required></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Detail perkembangan dan kronologi keluhan pasien.</p>
                    </div>

                    <!-- Physical Examination -->
                    <div>
                        <label for="physical_examination" class="block text-sm font-medium text-gray-700">Pemeriksaan Fisik</label>
                        <div class="mt-1">
                            <textarea id="physical_examination" 
                                      name="physical_examination" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                      placeholder="Hasil pemeriksaan fisik pasien..."
                                      required></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Hasil pemeriksaan fisik yang dilakukan pada pasien.</p>
                    </div>

                    <!-- Assessment & Plan -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Assessment & Plan</h3>
                        
                        <!-- Diagnosis -->
                        <div>
                            <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                            <div class="mt-1">
                                <textarea id="diagnosis" 
                                          name="diagnosis" 
                                          rows="3" 
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                          placeholder="Diagnosis medis berdasarkan pemeriksaan..."
                                          required></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Diagnosis primer dan sekunder berdasarkan hasil pemeriksaan.</p>
                        </div>

                        <!-- Treatment Plan -->
                        <div>
                            <label for="treatment_plan" class="block text-sm font-medium text-gray-700">Rencana Pengobatan</label>
                            <div class="mt-1">
                                <textarea id="treatment_plan" 
                                          name="treatment_plan" 
                                          rows="4" 
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                          placeholder="Rencana pengobatan dan tindakan medis..."
                                          required></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Rencana pengobatan, obat-obatan, dan tindakan medis yang akan dilakukan.</p>
                        </div>

                        <!-- Follow-up Instructions -->
                        <div>
                            <label for="follow_up_instructions" class="block text-sm font-medium text-gray-700">Instruksi Follow-up</label>
                            <div class="mt-1">
                                <textarea id="follow_up_instructions" 
                                          name="follow_up_instructions" 
                                          rows="3" 
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" 
                                          placeholder="Instruksi untuk kunjungan berikutnya..."></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Instruksi untuk pasien dan rencana kunjungan selanjutnya.</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Rekam Medis</label>
                        <div class="mt-1">
                            <select id="status" 
                                    name="status" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    required>
                                <option value="draft">Draft - Masih dapat diedit</option>
                                <option value="final" selected>Final - Rekam medis final</option>
                            </select>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Status "Draft" memungkinkan Anda mengedit rekam medis nanti. Status "Final" menandakan rekam medis sudah lengkap.
                        </p>
                    </div>

                    <!-- Blockchain Preparation (Placeholder) -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">Blockchain Integration</h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>Rekam medis ini akan otomatis di-hash dan dicatat di blockchain untuk memastikan integritas data. Hash blockchain akan dibuat setelah sistem blockchain aktif.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="window.history.back()" 
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Batal
                        </button>
                        <button type="submit" 
                                name="action" 
                                value="draft"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0L5.586 13.414A2 2 0 015 12V7a2 2 0 012-2z" />
                            </svg>
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" 
                                name="action" 
                                value="final"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Simpan Final
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- No Patient Selected -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121L15.196 16.93A4.98 4.98 0 0013 12c0-1.018-.304-1.965-.824-2.751L15 6.5a4 4 0 015 5v8.5z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Pilih Pasien Terlebih Dahulu</h3>
            <p class="mt-1 text-sm text-gray-500">
                Anda harus memilih pasien terlebih dahulu untuk membuat rekam medis.
            </p>
            <div class="mt-6">
                <a href="{{ route('doctor.my-patients') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Pilih Pasien
                </a>
            </div>
        </div>
    @endif
</div>

<script>
// Auto-save draft functionality (can be enhanced later)
let autoSaveTimer;

function autoSave() {
    // TODO: Implement auto-save functionality
    console.log('Auto-save functionality will be implemented');
}

// Set up auto-save on form fields
document.addEventListener('DOMContentLoaded', function() {
    const formFields = document.querySelectorAll('textarea, input[type="text"], input[type="number"], select');
    
    formFields.forEach(field => {
        field.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSave, 30000); // Auto-save after 30 seconds of inactivity
        });
    });
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = document.querySelectorAll('[required]');
    let hasError = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-300');
            hasError = true;
        } else {
            field.classList.remove('border-red-300');
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi.');
    }
});
</script>
@endsection