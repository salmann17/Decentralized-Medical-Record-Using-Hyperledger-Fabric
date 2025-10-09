@extends('layouts.app')

@section('title', 'Buat Permintaan Akses - Decentralized Medical Record')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Buat Permintaan Akses Pasien</h3>
                <p class="mt-2 max-w-4xl text-sm text-gray-500">
                    Ajukan permintaan akses ke pasien untuk dapat melihat dan membuat rekam medis mereka.
                </p>
            </div>
            <div>
                <a href="{{ route('doctor.access-requests') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Search Patient -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Cari Pasien</h3>
            <div class="max-w-xl">
                <label for="patient-search" class="block text-sm font-medium text-gray-700">Nama atau Email Pasien</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input type="text" 
                           id="patient-search" 
                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Ketik nama atau email pasien...">
                    <button type="button" 
                            onclick="searchPatient()" 
                            class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 sm:text-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-500">Cari berdasarkan nama lengkap atau alamat email pasien.</p>
            </div>

            <!-- Search Results -->
            <div id="search-results" class="mt-6 hidden">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Hasil Pencarian</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div id="patient-list">
                        <!-- Search results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Form -->
    <div class="bg-white shadow sm:rounded-lg" id="request-form" style="display: none;">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Form Permintaan Akses</h3>
            
            <form action="{{ route('doctor.access-requests.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <input type="hidden" id="selected-patient-id" name="patient_id" value="">
                
                <!-- Selected Patient Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-300 flex items-center justify-center">
                                <span id="patient-initial" class="text-sm font-medium text-blue-700">P</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-blue-900">Pasien Terpilih</h4>
                            <p id="selected-patient-info" class="text-sm text-blue-700">Belum ada pasien yang dipilih</p>
                        </div>
                    </div>
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
                                <p>Permintaan akses ini akan dicatat di blockchain untuk memastikan transparansi dan keamanan. Hash blockchain akan dibuat secara otomatis setelah integrasi aktif.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="resetForm()" 
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Petunjuk Penggunaan</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <ol class="list-decimal list-inside space-y-1">
                    <li>Cari pasien berdasarkan nama atau email</li>
                    <li>Pilih pasien dari hasil pencarian</li>
                    <li>Isi form permintaan akses dengan lengkap</li>
                    <li>Kirim permintaan dan tunggu persetujuan pasien</li>
                    <li>Setelah disetujui, Anda dapat mengakses data pasien</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
function searchPatient() {
    const query = document.getElementById('patient-search').value.toLowerCase().trim();
    
    if (query.length < 2) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }
    
    // Fetch real data from API
    fetch(`{{ route('doctor.search-patients') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(patients => {
            displaySearchResults(patients);
        })
        .catch(error => {
            console.error('Error searching patients:', error);
            displaySearchResults([]);
        });
}

function displaySearchResults(patients) {
    const resultsDiv = document.getElementById('search-results');
    const patientList = document.getElementById('patient-list');
    
    if (patients.length === 0) {
        patientList.innerHTML = '<p class="text-sm text-gray-500">Tidak ada pasien ditemukan.</p>';
    } else {
        patientList.innerHTML = patients.map(patient => `
            <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">${patient.name.charAt(0)}</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${patient.name}</p>
                        <p class="text-sm text-gray-500">${patient.email} • ${patient.gender === 'male' ? 'Laki-laki' : patient.gender === 'female' ? 'Perempuan' : 'Unknown'} • ${patient.blood}</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="selectPatient(${patient.idpatient}, '${patient.name}', '${patient.email}')" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                    Pilih
                </button>
            </div>
        `).join('');
    }
    
    resultsDiv.classList.remove('hidden');
}

function selectPatient(patientId, name, email) {
    // Set the patient ID in the hidden input
    document.getElementById('selected-patient-id').value = patientId;
    
    // Update the selected patient display
    document.getElementById('patient-initial').textContent = name.charAt(0);
    document.getElementById('selected-patient-info').textContent = `${name} (${email})`;
    
    // Show the request form
    document.getElementById('request-form').style.display = 'block';
    
    // Hide search results
    document.getElementById('search-results').classList.add('hidden');
    
    // Clear search input
    document.getElementById('patient-search').value = '';
    
    // Scroll to form
    document.getElementById('request-form').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('request-form').style.display = 'none';
    document.getElementById('selected-patient-id').value = '';
    document.getElementById('patient-search').value = '';
    document.getElementById('search-results').classList.add('hidden');
    document.querySelector('form').reset();
}

// Real-time search with debounce
let searchTimeout;
document.getElementById('patient-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(searchPatient, 300);
});

// Enter key search
document.getElementById('patient-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchPatient();
    }
});
</script>
@endsection