<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner Pengisian Faktor & Aktor Kunci</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .table-wrapper::-webkit-scrollbar { height: 10px; width: 10px; }
        .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
        .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .cell-hover:hover { transform: scale(1.15); z-index: 10; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        dialog::backdrop { 
            background: rgba(0, 0, 0, 0.4); 
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-6 font-sans text-gray-800">

    <div class="max-w-[95vw] mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Assessmen Faktor & Aktor Kunci</h1>
                <p class="text-sm text-gray-500 mt-1">Silakan ikuti instruksi secara berurutan.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @if(session('success'))
                    <script>
                        localStorage.removeItem('draft_matrix');
                        localStorage.removeItem('draft_aktorMatrix');
                        localStorage.removeItem('draft_identity');
                    </script>
                    <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200 hidden md:inline-block">
                        {{ session('success') }}
                    </span>
                @endif
                <span id="save-status" class="text-xs font-medium text-gray-400 italic hidden md:inline-block"></span>
            </div>
        </div>

        <form action="{{ route('matrix.store') }}" method="POST" id="matrixForm">
            @csrf
            <input type="hidden" name="key_factor" id="key_factor">
            <input type="hidden" name="key_actor" id="key_actor">

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">1. Data Identitas</h2>
                    <span id="identity-status" class="text-sm text-red-500 font-semibold bg-red-50 px-3 py-1 rounded-full border border-red-200">Wajib Diisi</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="user_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Masukkan nama lengkap Anda" oninput="validateIdentity()">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                        <input type="text" name="job" id="user_job" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="cth. Direktur" oninput="validateIdentity()">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Perusahaan / Organisasi</label>
                        <input type="text" name="company" id="user_company" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Masukkan nama perusahaan / organisasi Anda" oninput="validateIdentity()">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kawasan Industrial</label>
                        <select name="industrial_park" id="user_park" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white" onchange="validateIdentity()">
                            <option value="" disabled selected>Pilih Kawasan Industri...</option>
                            <option value="Surabaya Industrial Estate Rungkut (SIER)">Surabaya Industrial Estate Rungkut (SIER)</option>
                            {{-- <option value="Pasuruan Industrial Estate Rembang (PIER)">Pasuruan Industrial Estate Rembang (PIER)</option> --}}
                            <option value="Maspion Industrial Estate">Maspion Industrial Estate</option>
                            <option value="Sidoarjo Rangkah Industrial Estate (SiRIE)">Sidoarjo Rangkah Industrial Estate (SiRIE)</option>
                            <option value="Kawasan Industrial Tuban">Kawasan Industrial Tuban</option>
                            <option value="I-SENTRA Smart Eco Industrial Park (SEIPs) Lamongan">I-SENTRA Smart Eco Industrial Park (SEIPs) Lamongan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="matrix1-section" class="mb-10 opacity-40 pointer-events-none grayscale transition-all duration-500">
                <div class="mb-3 flex flex-col md:flex-row md:items-center gap-2">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">2. Kuisioner Faktor Kunci</h2>
                    <span id="m1-lock-status" class="text-sm text-red-500 font-semibold">Lengkapi Data Identitas terlebih dahulu</span>
                </div>
                
                <p class="text-sm text-gray-500 mb-3 leading-relaxed">
                    <strong>Keterangan:</strong><br>
                    0 = Tidak Ada Hubungan (Non-Existent) | 1 = Hubungan Lemah | 2 = Hubungan Sama-sama Kuat | 3 = Hubungan Kuat | P = Potential Influence (Tidak Bisa Ditentukan)
                </p>

                <div class="table-wrapper overflow-auto border border-gray-200 rounded-xl shadow-sm bg-white w-fit max-w-full max-h-[60vh]">
                    <div id="matrix-container" class="grid gap-[2px] p-[2px] bg-gray-100 w-max"></div>
                </div>
            </div>
            
            <div id="aktor-section" class="mb-10 opacity-40 pointer-events-none grayscale transition-all duration-500">
                <div class="mb-3 flex flex-col md:flex-row md:items-center gap-2">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">3. Kuisioner Aktor Kunci</h2>
                    <span id="lock-status" class="text-sm text-red-500 font-semibold">Isi Kuisioner Faktor Kunci terlebih dahulu</span>
                </div>

                <p class="text-sm text-gray-500 mb-3 leading-relaxed">
                    <strong>Keterangan:</strong><br>
                    0 = Tidak Ada Pengaruh | 1 = Pengaruh Kecil | 2 = Pengaruh Sedang | 3 = Pengaruh Besar | 4 = Mempengaruhi Eksistensi Kawasan
                </p>
                
                <div class="table-wrapper overflow-auto border border-gray-200 rounded-xl shadow-sm bg-white max-h-[60vh] w-fit max-w-full">
                    <div id="aktor-matrix-container" class="grid gap-[2px] p-[2px] bg-gray-100 w-max"></div>
                </div>
            </div>

            <div class="mt-8 mb-20 p-6 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Kirim Assessmen</h3>
                    <p id="final-status" class="text-sm text-red-500 font-semibold mt-1">Selesaikan seluruh langkah (1, 2, dan 3) untuk mengaktifkan tombol ini.</p>
                </div>
                <button type="submit" id="final-submit-btn" class="w-full md:w-auto px-8 py-3 text-sm md:text-base font-semibold text-white bg-indigo-600 rounded-xl transition-all duration-300 pointer-events-none opacity-50 flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Hasil
                </button>
            </div>

        </form>
    </div>

    <dialog id="input-modal" class="p-0 rounded-xl shadow-2xl border border-gray-100 max-w-[90vw] md:max-w-sm w-full">
        <div class="p-6 bg-white rounded-xl">
            <h3 id="modal-title" class="text-lg font-bold text-gray-900 mb-1"></h3>
            <p id="modal-desc" class="text-sm text-gray-500 mb-6"></p>
            <div id="button-container" class="flex flex-wrap gap-2 justify-center mb-6"></div>
            <button onclick="document.getElementById('input-modal').close()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                Cancel
            </button>
        </div>
    </dialog>

    <dialog id="aktor-input-modal" class="p-0 rounded-xl shadow-2xl border border-gray-100 max-w-[90vw] md:max-w-sm w-full">
        <div class="p-6 bg-white rounded-xl">
            <h3 id="aktor-modal-title" class="text-lg font-bold text-gray-900 mb-1"></h3>
            <p id="aktor-modal-desc" class="text-sm text-gray-500 mb-6"></p>
            <div id="aktor-button-container" class="flex flex-wrap gap-2 justify-center mb-6"></div>
            <button onclick="document.getElementById('aktor-input-modal').close()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                Cancel
            </button>
        </div>
    </dialog>

    <script>
        // --- 1. DATA DEFINITIONS ---
        const variables = [
            { code: 'E1', desc: 'Ketersediaan Tenant Anchor/Utama' }, { code: 'E2', desc: 'Harga Jual/ Sewa Lahan Industri' },
            { code: 'E3', desc: 'Nilai Tukar (Exchange Rate)' }, { code: 'E4', desc: 'Ketersediaan Bahan Baku' },
            { code: 'E5', desc: 'Upah Tenaga Kerja' }, { code: 'K1', desc: 'Kepastian Status Lokasi Lahan' },
            { code: 'K2', desc: 'Support Universitas Terhadap Industri' }, { code: 'K3', desc: 'Penegakan dan Kepastian Hukum' },
            { code: 'K4', desc: 'Profesionalisme Pengelola Kawasan' }, { code: 'K5', desc: 'Kapasitas & Integritas Pemerintah' },
            { code: 'S1', desc: 'Kontribusi ke Lokal Ekonomi' }, { code: 'S2', desc: 'Tingkat Kandungan Dalam Negeri (TKDN)' },
            { code: 'S3', desc: 'Konflik Perusahaan Dengan Serikat Pekerja' }, { code: 'S4', desc: 'Konflik Perusahaan Dengan Warga' },
            { code: 'S5', desc: 'Kualitas Tenaga Kerja Lokal' }, { code: 'L1', desc: 'Keamanan Lokasi' },
            { code: 'L2', desc: 'Reuse, Remanufacturing, Recycling' }, { code: 'L3', desc: 'Eco Material' },
            { code: 'L4', desc: 'Waste Management' }, { code: 'L5', desc: 'Keamanan dari Kebencanaan' },
            { code: 'I1', desc: 'Efisiensi Logistik dan Konektivitas Maritim' }, { code: 'I2', desc: 'Ketersediaan Kualitas Infrastruktur di Luar Kawasan' },
            { code: 'I3', desc: 'Ketersediaan Pasokan Energi dan Air' }, { code: 'I4', desc: 'Infrastruktur Digital dan Telekomunikasi' },
            { code: 'I5', desc: 'Ketersediaan Infrastruktur Internal' }
        ];

        const aktorVariables = [
            { code: 'A1', desc: 'Kementrian Perindustrian' }, { code: 'A2', desc: 'Kementrian Investasi/BKPM' },
            { code: 'A3', desc: 'Kemenko Perekonomian' }, { code: 'A4', desc: 'Pemerintah Provinsi' },
            { code: 'A5', desc: 'Pemerintah Kabupaten/Kota' }, { code: 'A6', desc: 'Pengelola Kawasan Industri' },
            { code: 'A7', desc: 'Anchor Tenant' }, { code: 'A8', desc: 'Asosiasi Kawasan Industri' },
            { code: 'A9', desc: 'Serikat Pekerja' }, { code: 'A10', desc: 'Masyarakat Lokal (LSM/NGO)' },
            { code: 'A11', desc: 'Pemasok Lokal (Supplier)' }, { code: 'A12', desc: 'Aparat Penegak Hukum (APH)' },
            { code: 'A13', desc: 'Penyedia Jasa Logistik' }, { code: 'A14', desc: 'Lembaga Pendidikan Vokasi & Universitas' }
        ];

        const size = variables.length; 
        const aktorSize = aktorVariables.length;

        let showFullLabels = window.innerWidth >= 768; 
        let showAktorFullLabels = window.innerWidth >= 768; 

        let matrix = Array(size).fill(null).map(() => Array(size).fill(null));
        let aktorMatrix = Array(aktorSize).fill(null).map(() => Array(aktorSize).fill(null));

        // --- 2. LOAD AUTOSAVES ---
        const draftMatrix = JSON.parse(localStorage.getItem('draft_matrix'));
        const draftAktor = JSON.parse(localStorage.getItem('draft_aktorMatrix'));
        const draftIdentityRaw = localStorage.getItem('draft_identity');

        if (draftMatrix && draftMatrix.length === size) matrix = draftMatrix;
        if (draftAktor && draftAktor.length === aktorSize) aktorMatrix = draftAktor;
        
        if (draftIdentityRaw) {
            const draftId = JSON.parse(draftIdentityRaw);
            document.getElementById('user_name').value = draftId.name || '';
            document.getElementById('user_job').value = draftId.job || '';
            document.getElementById('user_company').value = draftId.company || '';
            document.getElementById('user_park').value = draftId.park || '';
        }

        let currentRow = -1; let currentCol = -1;
        let currentAktorRow = -1; let currentAktorCol = -1;

        const container = document.getElementById('matrix-container');
        const modal = document.getElementById('input-modal');
        const aktorModal = document.getElementById('aktor-input-modal');

        // --- 3. AUTOSAVE TRIGGER ---
        function saveDraft() {
            localStorage.setItem('draft_matrix', JSON.stringify(matrix));
            localStorage.setItem('draft_aktorMatrix', JSON.stringify(aktorMatrix));
            
            const statusEl = document.getElementById('save-status');
            statusEl.innerText = "Draft tersimpan otomatis...";
            statusEl.classList.remove('hidden');
            setTimeout(() => statusEl.classList.add('hidden'), 2000);
        }

        // --- 4. VALIDATION & CASCADE LOGIC ---
        function validateIdentity() {
            checkCompletion();
            const name = document.getElementById('user_name').value.trim();
            const job = document.getElementById('user_job').value.trim();
            const company = document.getElementById('user_company').value.trim();
            const park = document.getElementById('user_park').value.trim();
            const identityComplete = name && job && company && park;
            
            const idStatus = document.getElementById('identity-status');
            const m1Section = document.getElementById('matrix1-section');
            const m1LockStatus = document.getElementById('m1-lock-status');

            if (identityComplete) {
                // Save identity draft
                localStorage.setItem('draft_identity', JSON.stringify({name, job, company, park}));
                
                // Visual Updates for Matrix 1
                idStatus.innerText = "Selesai";
                idStatus.className = "text-sm text-emerald-600 font-semibold bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200";
                
                m1Section.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                m1LockStatus.innerText = "";
            } else {
                idStatus.innerText = "Wajib Diisi";
                idStatus.className = "text-sm text-red-500 font-semibold bg-red-50 px-3 py-1 rounded-full border border-red-200";
                
                m1Section.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                m1LockStatus.innerText = "Lengkapi Data Identitas terlebih dahulu";
            }
        }

        function checkCompletion() {
            // Check Identity
            const name = document.getElementById('user_name').value.trim();
            const job = document.getElementById('user_job').value.trim();
            const company = document.getElementById('user_company').value.trim();
            const park = document.getElementById('user_park').value.trim();
            const identityComplete = name && job && company && park;

            // Check Matrix 1
            let m1Complete = true;
            for(let i=0; i<size; i++) {
                for(let j=0; j<size; j++) { if(i < j && matrix[i][j] === null) m1Complete = false; }
            }
            
            // Lock/Unlock Matrix 2 Logic
            const aktorSection = document.getElementById('aktor-section');
            const lockStatus = document.getElementById('lock-status');
            
            if(identityComplete && m1Complete) {
                aktorSection.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                lockStatus.innerText = "";
            } else {
                aktorSection.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                lockStatus.innerText = !identityComplete ? "Lengkapi Identitas & Faktor Kunci terlebih dahulu" : "Selesaikan Kuisioner Faktor Kunci terlebih dahulu";
            }

            // Check Matrix 2
            let m2Complete = true;
            for(let i=0; i<aktorSize; i++) {
                for(let j=0; j<aktorSize; j++) { if(i < j && aktorMatrix[i][j] === null) m2Complete = false; }
            }

            // Lock/Unlock Bottom Submit Button
            const btn = document.getElementById('final-submit-btn');
            const finalStatus = document.getElementById('final-status');

            if (identityComplete && m1Complete && m2Complete) {
                btn.classList.remove('pointer-events-none', 'opacity-50');
                btn.classList.add('hover:bg-indigo-700', 'hover:shadow-lg', 'transform', 'hover:-translate-y-0.5');
                finalStatus.innerText = "Semua data telah diisi. Formulir siap dikirim.";
                finalStatus.className = "text-sm text-emerald-600 font-semibold mt-1";
            } else {
                btn.classList.add('pointer-events-none', 'opacity-50');
                btn.classList.remove('hover:bg-indigo-700', 'hover:shadow-lg', 'transform', 'hover:-translate-y-0.5');
                finalStatus.innerText = "Selesaikan seluruh langkah (1, 2, dan 3) untuk mengaktifkan tombol ini.";
                finalStatus.className = "text-sm text-red-500 font-semibold mt-1";
            }
        }

        // Handle Submission formatting right before post
        document.getElementById('matrixForm').addEventListener('submit', function(e) {
            document.getElementById('key_factor').value = JSON.stringify(matrix);
            document.getElementById('key_actor').value = JSON.stringify(aktorMatrix);
        });

        function toggleSidebar() { showFullLabels = !showFullLabels; renderGrid(); }
        function toggleAktorSidebar() { showAktorFullLabels = !showAktorFullLabels; renderAktorGrid(); }

        function getCellClasses(value, isAktor = false) {
            let textSize = isAktor ? "text-sm" : "text-xs";
            let classes = `flex items-center justify-center font-semibold ${textSize} rounded-sm transition-all duration-150 `;
            
            if (value === null) return classes + "bg-white text-gray-800 cell-hover cursor-pointer";
            if (value == 0) return classes + "bg-gray-100 text-gray-800 border border-gray-200 cell-hover cursor-pointer";
            if (value == 1) return classes + "bg-sky-200 text-sky-900 cell-hover cursor-pointer";
            if (value == 2) return classes + "bg-sky-400 text-white cell-hover cursor-pointer";
            if (value == 3) return classes + "bg-sky-600 text-white cell-hover cursor-pointer";
            if (value == 4) return classes + "bg-indigo-600 text-white cell-hover cursor-pointer";
            if (value === 'P' || value === 'p') return classes + "bg-purple-500 text-white cell-hover cursor-pointer";
            return classes + "bg-white";
        }

        // --- 5. RENDER GRIDS ---
        function renderGrid() {
            container.innerHTML = ''; 
            const firstColWidth = showFullLabels ? '360px' : '50px';
            container.style.gridTemplateColumns = `${firstColWidth} repeat(${size}, 40px)`;
            container.style.gridTemplateRows = `40px repeat(${size}, 40px)`;

            const corner = document.createElement('div');
            corner.className = 'sticky top-0 left-0 z-20 bg-gray-900 text-white flex items-center px-2 font-bold text-xs rounded-sm shadow-md cursor-pointer hover:bg-gray-800 transition-colors select-none';
            if (showFullLabels) {
                corner.classList.add('justify-between');
                corner.innerHTML = `<span>Variabel</span> <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>`;
            } else {
                corner.classList.add('justify-center');
                corner.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`;
            }
            corner.onclick = toggleSidebar;
            container.appendChild(corner);

            variables.forEach(v => {
                const header = document.createElement('div');
                header.className = 'sticky top-0 z-10 bg-gray-800 text-white flex items-center justify-center font-bold text-xs rounded-sm cursor-help';
                header.innerText = v.code; header.title = v.desc; 
                container.appendChild(header);
            });

            for (let i = 0; i < size; i++) {
                const rowHeader = document.createElement('div');
                rowHeader.className = 'sticky left-0 z-10 bg-gray-800 text-white flex items-center px-2 font-semibold whitespace-nowrap rounded-sm shadow-sm cursor-help';
                if (showFullLabels) {
                    rowHeader.classList.add('justify-start', 'text-[11px]');
                    rowHeader.innerText = `${variables[i].code} - ${variables[i].desc}`;
                } else {
                    rowHeader.classList.add('justify-center', 'text-xs');
                    rowHeader.innerText = variables[i].code;
                }
                rowHeader.title = variables[i].desc; 
                container.appendChild(rowHeader);

                for (let j = 0; j < size; j++) {
                    const cell = document.createElement('div');
                    if (i === j) {
                        cell.className = 'flex items-center justify-center font-bold text-gray-400 bg-gray-200 rounded-sm cursor-not-allowed';
                        cell.innerText = '-';
                    } else if (i > j) {
                        cell.className = 'flex items-center justify-center bg-gray-200/60 rounded-sm cursor-not-allowed';
                    } else {
                        const val = matrix[i][j];
                        cell.className = getCellClasses(val, false);
                        cell.innerText = val !== null ? val : '';
                        cell.onclick = () => openModal(i, j);
                    }
                    container.appendChild(cell);
                }
            }
        }

        function renderAktorGrid() {
            const aktorContainer = document.getElementById('aktor-matrix-container');
            aktorContainer.innerHTML = ''; 
            
            const firstColWidth = showAktorFullLabels ? '360px' : '50px'; 
            aktorContainer.style.gridTemplateColumns = `${firstColWidth} repeat(${aktorSize}, 55px)`;
            aktorContainer.style.gridTemplateRows = `55px repeat(${aktorSize}, 55px)`;

            const corner = document.createElement('div');
            corner.className = 'sticky top-0 left-0 z-20 bg-slate-900 text-white flex items-center px-2 font-bold text-sm rounded-sm shadow-md cursor-pointer hover:bg-slate-800 transition-colors select-none';
            if (showAktorFullLabels) {
                corner.classList.add('justify-between');
                corner.innerHTML = `<span>Aktor</span> <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>`;
            } else {
                corner.classList.add('justify-center');
                corner.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`;
            }
            corner.onclick = toggleAktorSidebar;
            aktorContainer.appendChild(corner);

            aktorVariables.forEach(v => {
                const header = document.createElement('div');
                header.className = 'sticky top-0 z-10 bg-slate-800 text-white flex items-center justify-center font-bold text-sm rounded-sm cursor-help';
                header.innerText = v.code; header.title = v.desc; 
                aktorContainer.appendChild(header);
            });

            for (let i = 0; i < aktorSize; i++) {
                const rowHeader = document.createElement('div');
                rowHeader.className = 'sticky left-0 z-10 bg-slate-800 text-white flex items-center px-2 font-semibold whitespace-nowrap rounded-sm shadow-sm cursor-help';
                if (showAktorFullLabels) {
                    rowHeader.classList.add('justify-start', 'text-[13px]');
                    rowHeader.innerText = `${aktorVariables[i].code} - ${aktorVariables[i].desc}`;
                } else {
                    rowHeader.classList.add('justify-center', 'text-sm');
                    rowHeader.innerText = aktorVariables[i].code;
                }
                rowHeader.title = aktorVariables[i].desc;
                aktorContainer.appendChild(rowHeader);

                for (let j = 0; j < aktorSize; j++) {
                    const cell = document.createElement('div');
                    if (i === j) {
                        cell.className = 'flex items-center justify-center font-bold text-gray-400 bg-gray-200 rounded-sm cursor-not-allowed text-sm';
                        cell.innerText = '0';
                    } else if (i > j) {
                        cell.className = 'flex items-center justify-center bg-gray-200/60 rounded-sm cursor-not-allowed';
                    } else {
                        const val = aktorMatrix[i][j];
                        cell.className = getCellClasses(val, true); 
                        cell.innerText = val !== null ? val : '';
                        cell.onclick = () => openAktorModal(i, j);
                    }
                    aktorContainer.appendChild(cell);
                }
            }
        }

        // --- 6. MODALS ---
        function openModal(row, col) {
            currentRow = row; currentCol = col;
            document.getElementById('modal-title').innerText = `${variables[row].code} vs ${variables[col].code}`;
            modal.showModal();
        }

        function setComparisonValue(val) {
            matrix[currentRow][currentCol] = val;
            saveDraft();
            modal.close();
            renderGrid();
            checkCompletion(); 
        }

        function openAktorModal(row, col) {
            currentAktorRow = row; currentAktorCol = col;
            document.getElementById('aktor-modal-title').innerText = `${aktorVariables[row].code} vs ${aktorVariables[col].code}`;
            aktorModal.showModal();
        }

        function setAktorValue(val) {
            aktorMatrix[currentAktorRow][currentAktorCol] = val;
            saveDraft();
            aktorModal.close();
            renderAktorGrid();
            checkCompletion();
        }

        // --- 7. INITIALIZATION ---
        const btnContainer = document.getElementById('button-container');
        [0, 1, 2, 3, 'P'].forEach(val => {
            const btn = document.createElement('button');
            btn.innerText = val;
            let btnClass = "flex-1 min-w-[50px] py-3 text-lg font-bold rounded-lg border transition-all ";
            if(val == 0) btnClass += "bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-300";
            else if(val == 1) btnClass += "bg-sky-100 text-sky-800 hover:bg-sky-200 border-sky-300";
            else if(val == 2) btnClass += "bg-sky-400 text-white hover:bg-sky-500 border-sky-500";
            else if(val == 3) btnClass += "bg-sky-600 text-white hover:bg-sky-700 border-sky-700";
            else if(val === 'P') btnClass += "bg-purple-500 text-white hover:bg-purple-600 border-purple-600";
            btn.className = btnClass;
            btn.onclick = () => setComparisonValue(val);
            btnContainer.appendChild(btn);
        });

        const aktorBtnContainer = document.getElementById('aktor-button-container');
        [0, 1, 2, 3, 4].forEach(val => {
            const btn = document.createElement('button');
            btn.innerText = val;
            let btnClass = "flex-1 min-w-[50px] py-3 text-lg font-bold rounded-lg border transition-all ";
            if(val == 0) btnClass += "bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-300";
            else if(val == 1) btnClass += "bg-sky-100 text-sky-800 hover:bg-sky-200 border-sky-300";
            else if(val == 2) btnClass += "bg-sky-400 text-white hover:bg-sky-500 border-sky-500";
            else if(val == 3) btnClass += "bg-sky-600 text-white hover:bg-sky-700 border-sky-700";
            else if(val == 4) btnClass += "bg-indigo-600 text-white hover:bg-indigo-700 border-indigo-700";
            btn.className = btnClass;
            btn.onclick = () => setAktorValue(val);
            aktorBtnContainer.appendChild(btn);
        });

        window.addEventListener('resize', () => {
            let shouldShowFull = window.innerWidth >= 768;
            if (shouldShowFull !== showFullLabels || shouldShowFull !== showAktorFullLabels) {
                showFullLabels = shouldShowFull;
                showAktorFullLabels = shouldShowFull;
                renderGrid();
                renderAktorGrid();
            }
        });

        // Close dialog when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.tagName === 'DIALOG') {
                const rect = event.target.getBoundingClientRect();
                const clickedOutside = (
                    event.clientX < rect.left ||
                    event.clientX > rect.right ||
                    event.clientY < rect.top ||
                    event.clientY > rect.bottom
                );
                if (clickedOutside) event.target.close();
            }
        });

        renderGrid();
        renderAktorGrid();
        validateIdentity(); // Initial check to lock/unlock states based on cached data
    </script>
</body>
</html>