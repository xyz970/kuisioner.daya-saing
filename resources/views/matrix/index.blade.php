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
        <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">Kuisoner Faktor Kunci</h1>
                <p class="text-sm text-gray-500 mt-1">Keterangan: </p>
                <p class="text-sm text-gray-500 mt-1">0 Artinya Tidak Ada Hubungan (Non-Existent) </p>
                <p class="text-sm text-gray-500 mt-1">1 Artinya Hubungan Lemah </p>
                <p class="text-sm text-gray-500 mt-1">2 Artinya Hubungan Sama-sama Kuat</p>
                <p class="text-sm text-gray-500 mt-1">3 Artinya Hubungan Kuat</p>
                <p class="text-sm text-gray-500 mt-1">P Artinya Potential Influence (Tidak Bisa Ditentukan Dengan Kesepakatan)</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @if(isset($latestSubmission))
                    {{-- <a href="{{ route('matrix.export', $latestSubmission->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors duration-200 inline-flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Excel
                    </a> --}}
                @endif

                @if(session('success'))
                    <script>
                        localStorage.removeItem('draft_matrix');
                        localStorage.removeItem('draft_aktorMatrix');
                    </script>
                    <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200 hidden md:inline-block">
                        {{ session('success') }}
                    </span>
                @endif

                <span id="save-status" class="text-xs font-medium text-gray-400 italic hidden md:inline-block"></span>

                <button type="button" onclick="openSaveModal()" class="mb-10 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors duration-200 inline-flex items-center gap-2 text-sm">
                    Simpan Hasil
                </button>
            </div>
        </div>

        <div class="table-wrapper overflow-auto border border-gray-200 rounded-xl shadow-sm bg-white max-h-[60vh] mb-10">
            <div id="matrix-container" class="grid gap-[2px] p-[2px] bg-gray-100 w-max"></div>
        </div>
        
        <div class="mb-10">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Kuisoner Aktor Kunci  <span id="lock-status" class="text-sm text-red-500 font-semibold">Isi Kuisioner Faktor Kunci terlebih dahulu</span></h2>
            <p class="text-sm text-gray-500 mt-1">Keterangan: </p>
            <p class="text-sm text-gray-500 mt-1">0 Artinya Tidak Ada Pengaruh </p>
            <p class="text-sm text-gray-500 mt-1">1 Artinya Memiliki Pengaruh Kecil </p>
            <p class="text-sm text-gray-500 mt-1">2 Artinya Memiliki Pengaruh Sedang</p>
            <p class="text-sm text-gray-500 mt-1">3 Artinya Memiliki Pengaruh Besar</p>
            <p class="text-sm text-gray-500 mt-1">4 Artinya Mempengaruhi Eksistensi Kawasan Industri</p>
            
            <div id="aktor-section" class="table-wrapper overflow-auto border border-gray-200 rounded-xl shadow-sm bg-white max-h-[60vh] opacity-40 pointer-events-none transition-all duration-500 grayscale w-fit max-w-full">
                <div id="aktor-matrix-container" class="grid gap-[2px] p-[2px] bg-gray-100 w-max"></div>
            </div>
        </div>

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

    <dialog id="save-modal" class="p-0 rounded-xl shadow-2xl border border-gray-100 max-w-[90vw] md:max-w-md w-full">
        <form action="{{ route('matrix.store') }}" method="POST" id="matrixForm" class="bg-white rounded-xl overflow-hidden">
            @csrf
            <input type="hidden" name="key_factor" id="key_factor">
            <input type="hidden" name="key_actor" id="key_actor">
            
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Kirim Assessmen</h3>
                <p class="text-sm text-gray-500">Mohon isi data berikut dengan sebenar-benarnya</p>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Masukkan nama lengkap Anda">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="job" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="cth. Direktur">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Perusahaan / Organisasi</label>
                    <input type="text" name="company" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" placeholder="Masukkan nama perusahaan / organisasi Anda">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kawasan Industrial</label>
                    <select name="industrial_park" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                        <option value="" disabled selected>Pilih Kawasan Industri...</option>
                        <option value="Surabaya Industrial Estate Rungkut (SIER)">Surabaya Industrial Estate Rungkut (SIER)</option>
                        <option value="Pasuruan Industrial Estate Rembang (PIER)">Pasuruan Industrial Estate Rembang (PIER)</option>
                        <option value="Maspion Industrial Estate">Maspion Industrial Estate</option>
                        <option value="Sidoarjo Rangkah Industrial Estate (SiRIE)">Sidoarjo Rangkah Industrial Estate (SiRIE)</option>
                        <option value="Kawasan Industrial Tuban">Kawasan Industrial Tuban</option>
                        <option value="I-SENTRA Smart Eco Industrial Park (SEIPs) Lamongan">I-SENTRA Smart Eco Industrial Park (SEIPs) Lamongan</option>
                    </select>
                </div>
            </div>

            <div class="p-6 bg-gray-50 flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('save-modal').close()" class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" id="final-submit-btn" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg transition-colors pointer-events-none opacity-50">
                    Konfirmasi & Simpan
                </button>
            </div>
        </form>
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

        // Independent States for Sidebar Toggles
        let showFullLabels = window.innerWidth >= 768; 
        let showAktorFullLabels = window.innerWidth >= 768; 

        // State Arrays
        let matrix = Array(size).fill(null).map(() => Array(size).fill(null));
        let aktorMatrix = Array(aktorSize).fill(null).map(() => Array(aktorSize).fill(null));

        // --- NEW: AUTOSAVE LOAD LOGIC ---
        // Safely check and load data from local storage if it matches our table dimensions
        const draftMatrix = JSON.parse(localStorage.getItem('draft_matrix'));
        const draftAktor = JSON.parse(localStorage.getItem('draft_aktorMatrix'));

        if (draftMatrix && draftMatrix.length === size) {
            matrix = draftMatrix;
        }
        if (draftAktor && draftAktor.length === aktorSize) {
            aktorMatrix = draftAktor;
        }
        
        let currentRow = -1; let currentCol = -1;
        let currentAktorRow = -1; let currentAktorCol = -1;

        const container = document.getElementById('matrix-container');
        const modal = document.getElementById('input-modal');
        const aktorModal = document.getElementById('aktor-input-modal');
        const saveModal = document.getElementById('save-modal');

        // --- NEW: AUTOSAVE WRITE LOGIC ---
        function saveDraft() {
            localStorage.setItem('draft_matrix', JSON.stringify(matrix));
            localStorage.setItem('draft_aktorMatrix', JSON.stringify(aktorMatrix));
            
            const statusEl = document.getElementById('save-status');
            statusEl.innerText = "Draft tersimpan otomatis...";
            setTimeout(() => statusEl.innerText = "", 2000); // Clear message after 2s
        }

        function toggleSidebar() {
            showFullLabels = !showFullLabels;
            renderGrid();
        }

        function toggleAktorSidebar() {
            showAktorFullLabels = !showAktorFullLabels;
            renderAktorGrid();
        }

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

        // --- 2. MATRIX 1 LOGIC ---
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

        // --- 3. MATRIX 2 LOGIC ---
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

        // --- 4. MODAL INTERACTIONS ---
        function openModal(row, col) {
            currentRow = row; currentCol = col;
            document.getElementById('modal-title').innerText = `${variables[row].code} vs ${variables[col].code}`;
            document.getElementById('modal-desc').innerHTML = ``;
            modal.showModal();
        }

        function setComparisonValue(val) {
            matrix[currentRow][currentCol] = val;
            saveDraft(); // Automatically save
            modal.close();
            renderGrid();
            checkCompletion(); 
        }

        function openAktorModal(row, col) {
            currentAktorRow = row; currentAktorCol = col;
            document.getElementById('aktor-modal-title').innerText = `${aktorVariables[row].code} vs ${aktorVariables[col].code}`;
            document.getElementById('aktor-modal-desc').innerHTML = ``;
            aktorModal.showModal();
        }

        function setAktorValue(val) {
            aktorMatrix[currentAktorRow][currentAktorCol] = val;
            saveDraft(); // Automatically save
            aktorModal.close();
            renderAktorGrid();
            checkCompletion();
        }

        // --- 5. VALIDATION LOGIC ---
        function checkCompletion() {
            let m1Complete = true;
            for(let i=0; i<size; i++) {
                for(let j=0; j<size; j++) { if(i < j && matrix[i][j] === null) m1Complete = false; }
            }
            
            const section = document.getElementById('aktor-section');
            const status = document.getElementById('lock-status');
            
            if(m1Complete) {
                section.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                status.innerText = "";
                status.className = "text-sm text-emerald-600 font-semibold";
            } else {
                section.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                status.innerText = "Isi Kuisioner Faktor Kunci terlebih dahulu";
                status.className = "text-sm text-red-500 font-semibold";
            }

            let m2Complete = true;
            for(let i=0; i<aktorSize; i++) {
                for(let j=0; j<aktorSize; j++) { if(i < j && aktorMatrix[i][j] === null) m2Complete = false; }
            }

            const btn = document.getElementById('final-submit-btn');
            if (m1Complete && m2Complete) {
                btn.classList.remove('pointer-events-none', 'opacity-50');
                btn.classList.add('hover:bg-indigo-700');
            } else {
                btn.classList.add('pointer-events-none', 'opacity-50');
                btn.classList.remove('hover:bg-indigo-700');
            }
        }

        // --- 6. INITIALIZATION ---
        const btnContainer = document.getElementById('button-container');
        const likertScale = [0, 1, 2, 3, 'P'];
        likertScale.forEach(val => {
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

        function openSaveModal() {
            document.getElementById('key_factor').value = JSON.stringify(matrix);
            document.getElementById('key_actor').value = JSON.stringify(aktorMatrix);
            document.getElementById('save-modal').showModal();
        }

        window.addEventListener('resize', () => {
            let shouldShowFull = window.innerWidth >= 768;
            if (shouldShowFull !== showFullLabels || shouldShowFull !== showAktorFullLabels) {
                showFullLabels = shouldShowFull;
                showAktorFullLabels = shouldShowFull;
                renderGrid();
                renderAktorGrid();
            }
        });

        window.addEventListener('click', function(event) {
            if (event.target.tagName === 'DIALOG') {
                const rect = event.target.getBoundingClientRect();
                const clickedOutside = (
                    event.clientX < rect.left ||
                    event.clientX > rect.right ||
                    event.clientY < rect.top ||
                    event.clientY > rect.bottom
                );
                if (clickedOutside) {
                    event.target.close();
                }
            }
        });

        renderGrid();
        renderAktorGrid();
        checkCompletion(); 
    </script>
</body>
</html>