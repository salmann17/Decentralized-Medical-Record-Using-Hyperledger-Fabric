@extends('layouts.app')

@section('title', 'Edit Draft Rekam Medis - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
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

    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc space-y-1 pl-5">
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
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Edit Draft Rekam Medis</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Edit rekam medis draft untuk {{ $record->patient->user->name ?? 'Pasien' }}
                    @if($record->version > 1)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Versi {{ $record->version }}
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('doctor.records') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Info Notice -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Mode Edit Draft</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Anda sedang mengedit rekam medis dengan status <strong>Draft</strong>. Perubahan akan langsung tersimpan tanpa membuat versi baru.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Record Form -->
    <form action="{{ route('doctor.update-draft', $record->idmedicalrecord) }}" method="POST" id="draft-form">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Patient Information (Read-only) -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Informasi Pasien</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-blue-700">
                                    {{ substr($record->patient->user->name ?? 'P', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-base font-medium text-gray-900">{{ $record->patient->user->name ?? 'Pasien' }}</h4>
                            <p class="text-sm text-gray-500">{{ $record->patient->user->email ?? 'email@pasien.com' }}</p>
                            <div class="mt-1 flex space-x-4 text-sm text-gray-500">
                                <span>{{ $record->patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                                <span>{{ $record->patient->blood ?? 'Golongan darah tidak diketahui' }}</span>
                                @if(isset($record->patient->birthdate))
                                    <span>{{ \Carbon\Carbon::parse($record->patient->birthdate)->age }} tahun</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Kunjungan</h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="visit_date" class="block text-sm font-medium text-gray-700">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                            <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', $record->visit_date) }}" required
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
                                    <option value="{{ $hospital->idadmin }}" {{ old('admin_id', $record->admin_id) == $hospital->idadmin ? 'selected' : '' }}>
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
                            <input type="text" id="blood_pressure" name="blood_pressure" value="{{ old('blood_pressure', $record->blood_pressure) }}" placeholder="120/80"
                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="heart_rate" class="block text-sm font-medium text-gray-700">Denyut Nadi (bpm)</label>
                            <input type="number" id="heart_rate" name="heart_rate" value="{{ old('heart_rate', $record->heart_rate) }}" placeholder="72" min="30" max="250"
                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700">Suhu (°C)</label>
                            <input type="number" step="0.1" id="temperature" name="temperature" value="{{ old('temperature', $record->temperature) }}" placeholder="36.5" min="30" max="45"
                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Laju Napas (x/min)</label>
                            <input type="number" id="respiratory_rate" name="respiratory_rate" value="{{ old('respiratory_rate', $record->respiratory_rate) }}" placeholder="18" min="5" max="60"
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
                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('chief_complaint', $record->chief_complaint) }}</textarea>
                    </div>

                    <div>
                        <label for="history_present_illness" class="block text-sm font-medium text-gray-700">Riwayat Penyakit Sekarang</label>
                        <textarea id="history_present_illness" name="history_present_illness" rows="3"
                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('history_present_illness', $record->history_present_illness) }}</textarea>
                    </div>

                    <div>
                        <label for="physical_examination" class="block text-sm font-medium text-gray-700">Pemeriksaan Fisik</label>
                        <textarea id="physical_examination" name="physical_examination" rows="3"
                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('physical_examination', $record->physical_examination) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="diagnosis_code" class="block text-sm font-medium text-gray-700">Kode Diagnosis (ICD-10)</label>
                            <input type="text" id="diagnosis_code" name="diagnosis_code" value="{{ old('diagnosis_code', $record->diagnosis_code) }}"
                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="diagnosis_desc" class="block text-sm font-medium text-gray-700">Deskripsi Diagnosis <span class="text-red-500">*</span></label>
                            <input type="text" id="diagnosis_desc" name="diagnosis_desc" value="{{ old('diagnosis_desc', $record->diagnosis_desc) }}" required
                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label for="treatment" class="block text-sm font-medium text-gray-700">Rencana Pengobatan <span class="text-red-500">*</span></label>
                        <textarea id="treatment" name="treatment" rows="3" required
                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('treatment', $record->treatment) }}</textarea>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes', $record->notes) }}</textarea>
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
                        @if($record->prescriptions && count($record->prescriptions) > 0)
                            @foreach($record->prescriptions as $index => $prescription)
                                <div class="prescription-card border-2 border-gray-200 rounded-lg p-6 space-y-4 mb-4" data-prescription-index="{{ $index }}">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-base font-medium text-gray-900">Resep #{{ $index + 1 }}</h4>
                                        <button type="button" onclick="removePrescription(this)" 
                                                class="remove-prescription-btn inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 {{ count($record->prescriptions) <= 1 ? 'hidden' : '' }}">
                                            <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Resep
                                        </button>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tipe Resep <span class="text-red-500">*</span></label>
                                        <select name="prescriptions[{{ $index }}][type]" class="prescription-type" onchange="togglePrescriptionType(this)" required
                                                class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                            <option value="single" {{ old('prescriptions.'.$index.'.type', $prescription->type) === 'single' ? 'selected' : '' }}>Single (Obat Tunggal)</option>
                                            <option value="compound" {{ old('prescriptions.'.$index.'.type', $prescription->type) === 'compound' ? 'selected' : '' }}>Compound (Racikan)</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Single untuk 1 obat, Compound untuk racikan dengan beberapa obat</p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Nama Obat <span class="text-red-500">*</span></label>
                                            <input type="text" name="prescriptions[{{ $index }}][name]" value="{{ old('prescriptions.'.$index.'.name', $prescription->name) }}" required
                                                   placeholder="Contoh: Paracetamol atau Racikan Batuk"
                                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Dosis <span class="text-red-500">*</span></label>
                                            <input type="text" name="prescriptions[{{ $index }}][dosage]" value="{{ old('prescriptions.'.$index.'.dosage', $prescription->dosage) }}" required
                                                   placeholder="Contoh: 500mg"
                                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Frekuensi <span class="text-red-500">*</span></label>
                                            <input type="text" name="prescriptions[{{ $index }}][frequency]" value="{{ old('prescriptions.'.$index.'.frequency', $prescription->frequency) }}" required
                                                   placeholder="Contoh: 3x sehari"
                                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Durasi <span class="text-red-500">*</span></label>
                                            <input type="text" name="prescriptions[{{ $index }}][duration]" value="{{ old('prescriptions.'.$index.'.duration', $prescription->duration) }}" required
                                                   placeholder="Contoh: 5 hari"
                                                   class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>

                                    <div class="description-field {{ $prescription->type === 'compound' ? '' : 'hidden' }}">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Komposisi Racikan <span class="text-red-500 description-required">*</span>
                                        </label>
                                        <textarea name="prescriptions[{{ $index }}][description]" rows="3" class="description-textarea"
                                                  placeholder="Jelaskan komposisi racikan ini (wajib untuk tipe compound)"
                                                  {{ $prescription->type === 'compound' ? 'required' : '' }}
                                                  class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('prescriptions.'.$index.'.description', $prescription->description) }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Wajib diisi untuk resep racikan/compound</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 z-10 shadow-lg">
                <a href="{{ route('doctor.records') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" id="save-btn"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span class="btn-text">Simpan Perubahan</span>
                    <span class="btn-loading hidden">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let prescriptionIndex = {{ $record->prescriptions ? count($record->prescriptions) : 0 }};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading state
    const form = document.getElementById('draft-form');
    const saveBtn = document.getElementById('save-btn');
    
    if (form && saveBtn) {
        form.addEventListener('submit', function() {
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('hidden');
            saveBtn.querySelector('.btn-loading').classList.remove('hidden');
        });
    }
});

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
                <select name="prescriptions[${prescriptionIndex}][type]" class="prescription-type" onchange="togglePrescriptionType(this)" required
                        class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                    <option value="single">Single (Obat Tunggal)</option>
                    <option value="compound">Compound (Racikan)</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Single untuk 1 obat, Compound untuk racikan dengan beberapa obat</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nama Obat <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][name]" required
                           placeholder="Contoh: Paracetamol atau Racikan Batuk"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dosis <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][dosage]" required
                           placeholder="Contoh: 500mg"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Frekuensi <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][frequency]" required
                           placeholder="Contoh: 3x sehari"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Durasi <span class="text-red-500">*</span></label>
                    <input type="text" name="prescriptions[${prescriptionIndex}][duration]" required
                           placeholder="Contoh: 5 hari"
                           class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div class="description-field hidden">
                <label class="block text-sm font-medium text-gray-700">
                    Komposisi Racikan <span class="text-red-500 description-required">*</span>
                </label>
                <textarea name="prescriptions[${prescriptionIndex}][description]" rows="3" class="description-textarea"
                          placeholder="Jelaskan komposisi racikan ini (wajib untuk tipe compound)"
                          class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                <p class="mt-1 text-xs text-gray-500">Wajib diisi untuk resep racikan/compound</p>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', prescriptionHtml);
    prescriptionIndex++;
    updateRemoveButtons();
}

function removePrescription(button) {
    const prescriptions = document.querySelectorAll('.prescription-card');
    if (prescriptions.length <= 1) {
        alert('Minimal harus ada satu resep!');
        return;
    }
    if (confirm('Apakah Anda yakin ingin menghapus resep ini?')) {
        button.closest('.prescription-card').remove();
        updateRemoveButtons();
        renumberPrescriptions();
    }
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
</script>

<!-- Print Styles -->
<style>
@media print {
    .no-print, .no-print * {
        display: none !important;
    }
    body {
        background: white;
    }
    .shadow, .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endsection
