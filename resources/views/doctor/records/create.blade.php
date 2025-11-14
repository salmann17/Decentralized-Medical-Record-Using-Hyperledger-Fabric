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
                <a href="{{ isset($patient) ? route('doctor.patient-records', $patient->idpatient) : route('doctor.my-patients') }}" 
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
                                {{ substr($patient->user->name ?? 'P', 0, 2) }}
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
        <form action="{{ route('doctor.store-record', $patient->idpatient) }}" method="POST" id="medical-record-form">
            @csrf
            
            <div class="space-y-6">
                <!-- Visit Information -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Kunjungan</h3>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="visit_date" class="block text-sm font-medium text-gray-700">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                                <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md @error('visit_date') border-red-300 @enderror">
                                @error('visit_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="admin_id" class="block text-sm font-medium text-gray-700">Rumah Sakit/Admin <span class="text-red-500">*</span></label>
                                <select id="admin_id" name="admin_id" required
                                        class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md @error('admin_id') border-red-300 @enderror">
                                    <option value="">Pilih Rumah Sakit</option>
                                    @foreach($hospitals as $hospital)
                                        <option value="{{ $hospital->idadmin }}" {{ old('admin_id') == $hospital->idadmin ? 'selected' : '' }}>
                                            {{ $hospital->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tanda-tanda Vital</h3>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-4">
                            <div>
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700">Tekanan Darah</label>
                                <input type="text" id="blood_pressure" name="blood_pressure" value="{{ old('blood_pressure') }}" placeholder="120/80"
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700">Denyut Nadi (bpm)</label>
                                <input type="number" id="heart_rate" name="heart_rate" value="{{ old('heart_rate') }}" placeholder="72" min="30" max="250"
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700">Suhu (°C)</label>
                                <input type="number" step="0.1" id="temperature" name="temperature" value="{{ old('temperature') }}" placeholder="36.5" min="30" max="45"
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Laju Napas (x/min)</label>
                                <input type="number" id="respiratory_rate" name="respiratory_rate" value="{{ old('respiratory_rate') }}" placeholder="18" min="5" max="60"
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clinical Assessment -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Asesmen Klinis</h3>
                        
                        <div>
                            <label for="chief_complaint" class="block text-sm font-medium text-gray-700">Keluhan Utama</label>
                            <textarea id="chief_complaint" name="chief_complaint" rows="2"
                                      class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('chief_complaint') }}</textarea>
                        </div>

                        <div>
                            <label for="history_present_illness" class="block text-sm font-medium text-gray-700">Riwayat Penyakit Sekarang</label>
                            <textarea id="history_present_illness" name="history_present_illness" rows="3"
                                      class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('history_present_illness') }}</textarea>
                        </div>

                        <div>
                            <label for="physical_examination" class="block text-sm font-medium text-gray-700">Pemeriksaan Fisik</label>
                            <textarea id="physical_examination" name="physical_examination" rows="3"
                                      class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('physical_examination') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="diagnosis_code" class="block text-sm font-medium text-gray-700">Kode Diagnosis (ICD-10) <span class="text-red-500">*</span></label>
                                <input type="text" id="diagnosis_code" name="diagnosis_code" value="{{ old('diagnosis_code') }}" required
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="diagnosis_desc" class="block text-sm font-medium text-gray-700">Deskripsi Diagnosis <span class="text-red-500">*</span></label>
                                <input type="text" id="diagnosis_desc" name="diagnosis_desc" value="{{ old('diagnosis_desc') }}" required
                                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="treatment" class="block text-sm font-medium text-gray-700">Rencana Pengobatan <span class="text-red-500">*</span></label>
                            <textarea id="treatment" name="treatment" rows="3" required
                                      class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('treatment') }}</textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Prescriptions -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Resep Obat</h3>
                            <button type="button" onclick="addPrescription()" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Resep
                            </button>
                        </div>
                        
                        <div id="prescriptions-container">
                            <!-- Prescriptions will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status Rekam Medis <span class="text-red-500">*</span></label>
                            <select id="status" name="status" required
                                    class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft </option>
                                <option value="final" {{ old('status') == 'final' ? 'selected' : '' }}>Final </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('doctor.patient-records', $patient->idpatient) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Rekam Medis
                    </button>
                </div>
            </div>
        </form>
    @else
        <!-- No Patient Selected -->
        <div class="text-center py-12 bg-white shadow sm:rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121L15.196 16.93A4.98 4.98 0 0013 12c0-1.018-.304-1.965-.824-2.751L15 6.5a4 4 0 015 5v8.5z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Pilih Pasien Terlebih Dahulu</h3>
            <p class="mt-1 text-sm text-gray-500">Anda harus memilih pasien terlebih dahulu untuk membuat rekam medis.</p>
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
let prescriptionIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    addPrescription();
});

function addPrescription() {
    const container = document.getElementById('prescriptions-container');
    const prescriptionHtml = `
        <div class="prescription-card border-2 border-gray-200 rounded-lg p-6 space-y-4 mb-4" data-prescription-index="${prescriptionIndex}">
            <div class="flex items-center justify-between">
                <h4 class="text-base font-medium text-gray-900">Resep #${prescriptionIndex + 1}</h4>
                <button type="button" onclick="removePrescription(this)" 
                        class="remove-prescription-btn inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                    <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Resep
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tipe Resep <span class="text-red-500">*</span></label>
                <select name="prescriptions[${prescriptionIndex}][type]" onchange="togglePrescriptionType(this)" required
                        class="prescription-type mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                    <option value="single">Single (Obat Tunggal)</option>
                    <option value="compound">Compound (Racikan)</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Single untuk obat tunggal, Compound untuk obat racikan</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Obat <span class="text-red-500">*</span></label>
                <input type="text" name="prescriptions[${prescriptionIndex}][name]" required placeholder="Contoh: Paracetamol atau Racikan Batuk"
                       class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dosis <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][dosage]" required placeholder="500mg"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Frekuensi <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][frequency]" required placeholder="3x sehari"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Durasi <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][duration]" required placeholder="3 hari"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div class="description-field hidden">
                <label class="block text-sm font-medium text-gray-700">Komposisi Racikan <span class="text-red-500">*</span></label>
                <textarea name="prescriptions[${prescriptionIndex}][description]" rows="3" placeholder="Contoh:\n- Paracetamol 250mg\n- Amoxicillin 125mg\n- CTM 2mg"
                          class="description-textarea mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                <p class="mt-1 text-xs text-gray-500">Wajib diisi untuk obat racikan. Sebutkan detail komposisi dan dosisnya.</p>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', prescriptionHtml);
    prescriptionIndex++;
    updateRemoveButtons();
}

function togglePrescriptionType(select) {
    const prescription = select.closest('.prescription-card');
    const type = select.value;
    const descriptionField = prescription.querySelector('.description-field');
    const descriptionTextarea = prescription.querySelector('.description-textarea');
    
    if (type === 'compound') {
        descriptionField.classList.remove('hidden');
        descriptionTextarea.required = true;
    } else {
        descriptionField.classList.add('hidden');
        descriptionTextarea.required = false;
        descriptionTextarea.value = '';
    }
}

function removePrescription(button) {
    button.closest('.prescription-card').remove();
    updateRemoveButtons();
    renumberPrescriptions();
}

function updateRemoveButtons() {
    const prescriptions = document.querySelectorAll('.prescription-card');
    prescriptions.forEach((prescription, index) => {
        const removeBtn = prescription.querySelector('.remove-prescription-btn');
        if (prescriptions.length <= 1) {
            removeBtn.classList.add('hidden');
        } else {
            removeBtn.classList.remove('hidden');
        }
    });
}

function renumberPrescriptions() {
    document.querySelectorAll('.prescription-card').forEach((prescription, index) => {
        prescription.querySelector('h4').textContent = `Resep #${index + 1}`;
    });
}

document.getElementById('medical-record-form').addEventListener('submit', function(e) {
    const prescriptions = document.querySelectorAll('.prescription-card');
    if (prescriptions.length === 0) {
        e.preventDefault();
        alert('Minimal harus ada satu resep!');
        return false;
    }
    
    let hasError = false;
    let errorMessage = '';
    
    prescriptions.forEach((prescription, index) => {
        const type = prescription.querySelector('.prescription-type').value;
        const description = prescription.querySelector('.description-textarea').value.trim();
        
        if (type === 'compound' && description === '') {
            hasError = true;
            errorMessage = `Resep #${index + 1}: Komposisi racikan wajib diisi untuk obat racikan!`;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert(errorMessage);
        return false;
    }
});
</script>
@endsection
