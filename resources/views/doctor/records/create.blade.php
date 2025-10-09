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
                <form action="{{ route('doctor.store-record', $patient->idpatient) }}" method="POST" class="space-y-6">
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
                                           value="{{ old('visit_date', date('Y-m-d')) }}"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('visit_date') border-red-300 @enderror"
                                           required>
                                </div>
                                @error('visit_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="admin_id" class="block text-sm font-medium text-gray-700">Rumah Sakit</label>
                                <div class="mt-1">
                                    <select id="admin_id" 
                                            name="admin_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('admin_id') border-red-300 @enderror"
                                            required>
                                        <option value="">Pilih Rumah Sakit</option>
                                        @if(isset($admins) && $admins->count() > 0)
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->idadmin }}" {{ old('admin_id') == $admin->idadmin ? 'selected' : '' }}>
                                                    {{ $admin->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('admin_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                           value="{{ old('blood_pressure') }}"
                                           placeholder="120/80 mmHg"
                                           maxlength="45"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('blood_pressure') border-red-300 @enderror">
                                </div>
                                @error('blood_pressure')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700">Denyut Nadi</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="heart_rate" 
                                           name="heart_rate" 
                                           value="{{ old('heart_rate') }}"
                                           placeholder="80"
                                           min="30" max="250"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('heart_rate') border-red-300 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">bpm</span>
                                    </div>
                                </div>
                                @error('heart_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700">Suhu Tubuh</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="temperature" 
                                           name="temperature" 
                                           value="{{ old('temperature') }}"
                                           step="0.1"
                                           placeholder="36.5"
                                           min="30" max="45"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('temperature') border-red-300 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">°C</span>
                                    </div>
                                </div>
                                @error('temperature')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Pernapasan</label>
                                <div class="mt-1 relative">
                                    <input type="number" 
                                           id="respiratory_rate" 
                                           name="respiratory_rate" 
                                           value="{{ old('respiratory_rate') }}"
                                           placeholder="20"
                                           min="5" max="60"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('respiratory_rate') border-red-300 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">/min</span>
                                    </div>
                                </div>
                                @error('respiratory_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('chief_complaint') border-red-300 @enderror" 
                                      placeholder="Jelaskan keluhan utama pasien...">{{ old('chief_complaint') }}</textarea>
                        </div>
                        @error('chief_complaint')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Keluhan utama yang disampaikan oleh pasien.</p>
                    </div>

                    <!-- History of Present Illness -->
                    <div>
                        <label for="history_present_illness" class="block text-sm font-medium text-gray-700">Riwayat Penyakit Sekarang</label>
                        <div class="mt-1">
                            <textarea id="history_present_illness" 
                                      name="history_present_illness" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('history_present_illness') border-red-300 @enderror" 
                                      placeholder="Riwayat perkembangan penyakit saat ini...">{{ old('history_present_illness') }}</textarea>
                        </div>
                        @error('history_present_illness')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Detail perkembangan dan kronologi keluhan pasien.</p>
                    </div>

                    <!-- Physical Examination -->
                    <div>
                        <label for="physical_examination" class="block text-sm font-medium text-gray-700">Pemeriksaan Fisik</label>
                        <div class="mt-1">
                            <textarea id="physical_examination" 
                                      name="physical_examination" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('physical_examination') border-red-300 @enderror" 
                                      placeholder="Hasil pemeriksaan fisik pasien...">{{ old('physical_examination') }}</textarea>
                        </div>
                        @error('physical_examination')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Hasil pemeriksaan fisik yang dilakukan pada pasien.</p>
                    </div>

                    <!-- Assessment & Plan -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Assessment & Plan</h3>
                        
                        <!-- Diagnosis -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="diagnosis_code" class="block text-sm font-medium text-gray-700">Kode Diagnosis (ICD-10)</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           id="diagnosis_code" 
                                           name="diagnosis_code" 
                                           value="{{ old('diagnosis_code') }}"
                                           placeholder="Contoh: A09.9, K59.1"
                                           maxlength="45"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('diagnosis_code') border-red-300 @enderror"
                                           required>
                                </div>
                                @error('diagnosis_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="diagnosis_desc" class="block text-sm font-medium text-gray-700">Deskripsi Diagnosis</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           id="diagnosis_desc" 
                                           name="diagnosis_desc" 
                                           value="{{ old('diagnosis_desc') }}"
                                           placeholder="Contoh: Gastroenteritis akut"
                                           maxlength="135"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('diagnosis_desc') border-red-300 @enderror"
                                           required>
                                </div>
                                @error('diagnosis_desc')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Treatment Plan -->
                        <div>
                            <label for="treatment" class="block text-sm font-medium text-gray-700">Rencana Pengobatan</label>
                            <div class="mt-1">
                                <textarea id="treatment" 
                                          name="treatment" 
                                          rows="4" 
                                          maxlength="135"
                                          value="{{ old('treatment') }}"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('treatment') border-red-300 @enderror" 
                                          placeholder="Rencana pengobatan dan tindakan medis..."
                                          required>{{ old('treatment') }}</textarea>
                            </div>
                            @error('treatment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Rencana pengobatan, tindakan medis yang akan dilakukan.</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                            <div class="mt-1">
                                <textarea id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          maxlength="135"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('notes') border-red-300 @enderror" 
                                          placeholder="Catatan tambahan atau instruksi khusus...">{{ old('notes') }}</textarea>
                            </div>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Catatan tambahan untuk pasien atau dokter lainnya.</p>
                        </div>
                    </div>

                    <!-- Prescription Information -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Resep</h3>
                            <button type="button" 
                                    id="add-prescription"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Item Resep
                            </button>
                        </div>
                        
                        <div id="prescriptions-container">
                            <!-- Prescription Item Template -->
                            <div class="prescription-item border border-gray-200 rounded-lg p-4 space-y-4" data-index="0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">Item Resep #1</h4>
                                    <button type="button" 
                                            class="remove-prescription hidden inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                                
                                <!-- Item Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Obat/Item <span class="text-red-500">*</span></label>
                                    <div class="mt-1">
                                        <input type="text" 
                                               name="prescriptions[0][item]" 
                                               value="{{ old('prescriptions.0.item') }}"
                                               placeholder="Contoh: Paracetamol 500mg, Amoxicillin 250mg"
                                               maxlength="135"
                                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prescriptions.0.item') border-red-300 @enderror"
                                               required>
                                    </div>
                                    @error('prescriptions.0.item')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Prescription Details Grid -->
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Dosis <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <input type="text" 
                                                   name="prescriptions[0][dosage]" 
                                                   value="{{ old('prescriptions.0.dosage') }}"
                                                   placeholder="Contoh: 500mg, 1 tablet"
                                                   maxlength="45"
                                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prescriptions.0.dosage') border-red-300 @enderror"
                                                   required>
                                        </div>
                                        @error('prescriptions.0.dosage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Frekuensi <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <input type="text" 
                                                   name="prescriptions[0][frequency]" 
                                                   value="{{ old('prescriptions.0.frequency') }}"
                                                   placeholder="Contoh: 3x sehari, 2x sehari"
                                                   maxlength="45"
                                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prescriptions.0.frequency') border-red-300 @enderror"
                                                   required>
                                        </div>
                                        @error('prescriptions.0.frequency')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Durasi <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <input type="text" 
                                                   name="prescriptions[0][duration]" 
                                                   value="{{ old('prescriptions.0.duration') }}"
                                                   placeholder="Contoh: 7 hari, 2 minggu"
                                                   maxlength="45"
                                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prescriptions.0.duration') border-red-300 @enderror"
                                                   required>
                                        </div>
                                        @error('prescriptions.0.duration')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            <strong>Catatan:</strong> Semua field resep wajib diisi. Informasi resep akan otomatis tersimpan dan terhubung dengan rekam medis ini.
                        </p>
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
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Simpan Rekam Medis
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
let prescriptionIndex = 0;

function autoSave() {
    // TODO: Implement auto-save functionality
    console.log('Auto-save functionality will be implemented');
}

// Prescription management
function addPrescription() {
    prescriptionIndex++;
    const container = document.getElementById('prescriptions-container');
    const template = container.querySelector('.prescription-item').cloneNode(true);
    
    // Update index and names
    template.setAttribute('data-index', prescriptionIndex);
    template.querySelector('h4').textContent = `Item Resep #${prescriptionIndex + 1}`;
    
    // Update form field names
    const inputs = template.querySelectorAll('input');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${prescriptionIndex}]`));
            input.value = '';
        }
    });
    
    // Update error containers
    const errorContainers = template.querySelectorAll('.text-red-600');
    errorContainers.forEach(container => container.remove());
    
    // Show remove button
    const removeBtn = template.querySelector('.remove-prescription');
    removeBtn.classList.remove('hidden');
    removeBtn.classList.add('inline-flex');
    
    container.appendChild(template);
    updateRemoveButtonsVisibility();
}

function removePrescription(element) {
    const prescriptionItem = element.closest('.prescription-item');
    prescriptionItem.remove();
    updatePrescriptionNumbers();
    updateRemoveButtonsVisibility();
}

function updatePrescriptionNumbers() {
    const items = document.querySelectorAll('.prescription-item');
    items.forEach((item, index) => {
        item.setAttribute('data-index', index);
        item.querySelector('h4').textContent = `Item Resep #${index + 1}`;
        
        // Update form field names
        const inputs = item.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
            }
        });
    });
    
    prescriptionIndex = items.length - 1;
}

function updateRemoveButtonsVisibility() {
    const items = document.querySelectorAll('.prescription-item');
    const removeButtons = document.querySelectorAll('.remove-prescription');
    
    if (items.length <= 1) {
        removeButtons.forEach(btn => {
            btn.classList.add('hidden');
            btn.classList.remove('inline-flex');
        });
    } else {
        removeButtons.forEach(btn => {
            btn.classList.remove('hidden');
            btn.classList.add('inline-flex');
        });
    }
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
    
    // Add prescription button
    document.getElementById('add-prescription').addEventListener('click', addPrescription);
    
    // Remove prescription buttons (event delegation)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-prescription')) {
            removePrescription(e.target);
        }
    });
    
    // Initial state
    updateRemoveButtonsVisibility();
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
    
    // Check if at least one prescription exists
    const prescriptions = document.querySelectorAll('.prescription-item');
    if (prescriptions.length === 0) {
        alert('Minimal harus ada satu item resep.');
        hasError = true;
    }
    
    if (hasError) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi.');
    }
});
</script>
@endsection