<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard & Peta Sebaran Lokasi WNA</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Marker Cluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        
        /* Layout Sidebar */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1d20;
            border-bottom: 1px solid #2c3034;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 0.95rem;
            display: block;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.2s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li.active > a {
            color: #fff;
            background: #0d6efd;
        }

        #sidebar ul li a i {
            margin-right: 10px;
        }

        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Map & Popup styling */
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
        }
        .legend-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .popup-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .popup-table th, .popup-table td {
            border: 1px solid #dee2e6;
            padding: 4px 6px;
            white-space: nowrap;
        }
        .popup-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .leaflet-popup-content {
            width: 480px !important;
        }
    </style>
</head>
<body>

<div id="wrapper">

    <!-- SIDEBAR NAVIGASI -->
    <nav id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="28">
                SIP_WNA
            </h5>
        </div>

        <ul class="list-unstyled components">
            <li class="{{ request()->routeIs('map.*') ? 'active' : '' }}">
                <a href="{{ route('map.index') }}"><i class="bi bi-geo-alt-fill"></i> Peta & Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('wna.*') ? 'active' : '' }}">
                <a href="{{ route('wna.index') }}"><i class="bi bi-people-fill"></i> Data WNA</a>
            </li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- NAVBAR ATAS -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3 shadow-sm">
            <div class="container-fluid p-0">
                 <img src="{{ asset('images/logo.png') }}" alt="Logo" height="28">Sistem Informasi Pemetaan Warga Negara Asing
                
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small"><i class="bi bi-person-circle"></i> Administrator</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">

            <!-- DASHBOARD RINGKASAN DATA -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase fw-bold">Total WNA Terdaftar</h6>
                            <h2 class="display-6 fw-bold mb-0">{{ $totalForeigners }} <span class="fs-6 fw-normal">Orang</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-danger text-white shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase fw-bold">Penjamin Perusahaan</h6>
                            <h2 class="display-6 fw-bold mb-0">{{ $totalPerusahaan }} <span class="fs-6 fw-normal">Orang</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase fw-bold">Penjamin Perorangan</h6>
                            <h2 class="display-6 fw-bold mb-0">{{ $totalPerorangan }} <span class="fs-6 fw-normal">Orang</span></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DASHBOARD GRAFIK KATEGORI -->
            <div class="row mb-4">
                <!-- Grafik 1: Jenis Penjamin -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Grafik Jenis Penjamin</div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="chartPenjaminCanvas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik 2: Top Jenis Izin Tinggal -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Top 5 Jenis Izin Tinggal</div>
                        <div class="card-body">
                            <canvas id="chartIzinTinggalCanvas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik 3: Top Warganegara -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Top 5 Warganegara</div>
                        <div class="card-body">
                            <canvas id="chartWarganegaraCanvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HEADER & ACTION BUTTONS -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold m-0">Peta Sebaran Lokasi WNA</h3>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                        <i class="bi bi-file-earmark-excel"></i> Upload Excel / CSV
                    </button>
                    <button class="btn btn-primary" onclick="resetAndOpenAddModal()">
                        <i class="bi bi-person-plus-fill"></i> Tambah Data WNA
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- MAP CONTAINER -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-2">
                        <small class="fw-bold">Keterangan Marker Penjamin/Sponsor:</small>
                        <span class="ms-3"><span class="legend-box bg-danger"></span> Perusahaan</span>
                        <span class="ms-3"><span class="legend-box bg-primary"></span> Perorangan</span>
                    </div>
                    <div id="map"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Upload Excel/CSV -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportExcelLabel">Upload File Excel / CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('foreigners.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <small class="text-muted">
                        * Format kolom Excel disesuaikan dengan file DATABASE LOKASI WNA (NAMA, JENIS KELAMIN, NOMOR PASPOR, WARGANEGARA, JENIS IZIN TINGGAL, MASA BERLAKU IZIN TINGGAL, PENJAMIN/SPONSOR, ALAMAT, TITIK KOORDINAT).
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Form Tambah Data WNA Manual -->
<div class="modal fade" id="modalAddWna" tabindex="-1" aria-labelledby="modalAddWnaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddWnaLabel">Form Input Data WNA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddWna" action="{{ route('foreigners.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" id="add_nama" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select id="add_jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Paspor</label>
                            <input type="text" id="add_nomor_paspor" name="nomor_paspor" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warganegara</label>
                            <input type="text" id="add_warganegara" name="warganegara" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Izin Tinggal</label>
                            <input type="text" id="add_jenis_izin_tinggal" name="jenis_izin_tinggal" class="form-control" placeholder="Contoh: ITAS INVESTOR" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Berlaku Izin Tinggal</label>
                            <input type="date" id="add_masa_berlaku_izin_tinggal" name="masa_berlaku_izin_tinggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Penjamin / Sponsor</label>
                            <input type="text" id="add_penjamin" name="penjamin" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Penjamin</label>
                            <select id="add_jenis_penjamin" name="jenis_penjamin" class="form-select" required>
                                <option value="PERUSAHAAN">PERUSAHAAN</option>
                                <option value="PERORANGAN">PERORANGAN</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea id="add_alamat" name="alamat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Contoh: -2.1539" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Contoh: 106.1308" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data WNA -->
<div class="modal fade" id="modalEditWna" tabindex="-1" aria-labelledby="modalEditWnaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditWnaLabel">Edit Data WNA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditWna" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" id="edit_nama" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select id="edit_jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Paspor</label>
                            <input type="text" id="edit_nomor_paspor" name="nomor_paspor" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warganegara</label>
                            <input type="text" id="edit_warganegara" name="warganegara" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Izin Tinggal</label>
                            <input type="text" id="edit_jenis_izin_tinggal" name="jenis_izin_tinggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Berlaku Izin Tinggal</label>
                            <input type="date" id="edit_masa_berlaku_izin_tinggal" name="masa_berlaku_izin_tinggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Penjamin / Sponsor</label>
                            <input type="text" id="edit_penjamin" name="penjamin" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Penjamin</label>
                            <select id="edit_jenis_penjamin" name="jenis_penjamin" class="form-select" required>
                                <option value="PERUSAHAAN">PERUSAHAAN</option>
                                <option value="PERORANGAN">PERORANGAN</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea id="edit_alamat" name="alamat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="edit_latitude" name="latitude" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="edit_longitude" name="longitude" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formDeleteWna" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const map = L.map('map').setView([-2.1539, 106.1308], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const createIcon = (color) => {
        return new L.Icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    };

    const redIcon = createIcon('red');   
    const blueIcon = createIcon('blue'); 

    const markersGroup = L.markerClusterGroup();

    fetch('/api/foreigners')
        .then(response => response.json())
        .then(data => {
            const groupedLocations = {};

            data.forEach(wna => {
                const key = `${wna.latitude},${wna.longitude}`;
                if (!groupedLocations[key]) {
                    groupedLocations[key] = [];
                }
                groupedLocations[key].push(wna);
            });

            Object.keys(groupedLocations).forEach(key => {
                const items = groupedLocations[key];
                const firstItem = items[0];

                const lat = parseFloat(firstItem.latitude);
                const lng = parseFloat(firstItem.longitude);

                if (isNaN(lat) || isNaN(lng)) return;

                const isCompany = firstItem.jenis_penjamin === 'PERUSAHAAN';
                const markerIcon = isCompany ? redIcon : blueIcon;
                const badgeColor = isCompany ? 'bg-danger' : 'bg-primary';

                const firstItemJson = JSON.stringify(firstItem).replace(/'/g, "&apos;").replace(/"/g, "&quot;");

                let popupContent = `
                    <div style="font-size: 12px; width: 100%;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <h6 class="m-0 fw-bold text-uppercase">${firstItem.penjamin ?? 'Tanpa Penjamin'}</h6>
                                <span class="badge ${badgeColor}">${firstItem.jenis_penjamin}</span>
                            </div>
                            ${isCompany ? `<button class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 10px;" onclick="addWnaToLocation(${firstItemJson})">+ Tambah WNA di Titik Ini</button>` : ''}
                        </div>
                        
                        <p class="mb-2" style="font-size: 11px;">
                            <b>Alamat:</b> ${firstItem.alamat ?? '-'}<br>
                            <b>Total WNA di titik ini:</b> <span class="badge bg-dark">${items.length} Orang</span>
                        </p>
                        
                        <div style="max-height: 200px; overflow-y: auto; overflow-x: auto;">
                            <table class="popup-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Paspor</th>
                                        <th>Warganegara</th>
                                        <th>Izin Tinggal</th>
                                        <th>Masa Berlaku</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                items.forEach((wna, index) => {
                    const wnaJson = JSON.stringify(wna).replace(/'/g, "&apos;").replace(/"/g, "&quot;");

                    popupContent += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td><b>${wna.nama}</b> (${wna.jenis_kelamin})</td>
                            <td>${wna.nomor_paspor ?? '-'}</td>
                            <td>${wna.warganegara ?? '-'}</td>
                            <td>${wna.jenis_izin_tinggal ?? '-'}</td>
                            <td>${wna.masa_berlaku_izin_tinggal ?? '-'}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning py-0 px-1 text-white" title="Edit" onclick="editWna(${wnaJson})">✏️</button>
                                <button class="btn btn-sm btn-danger py-0 px-1" title="Hapus" onclick="deleteWna(${wna.id})">🗑️</button>
                            </td>
                        </tr>
                    `;
                });

                popupContent += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                const marker = L.marker([lat, lng], { icon: markerIcon }).bindPopup(popupContent, { maxWidth: 500 });
                markersGroup.addLayer(marker);
            });

            map.addLayer(markersGroup);
        })
        .catch(err => console.error("Gagal memuat data koordinat:", err));

    function addWnaToLocation(data) {
        document.getElementById('add_penjamin').value = data.penjamin || '';
        document.getElementById('add_jenis_penjamin').value = 'PERUSAHAAN';
        document.getElementById('add_alamat').value = data.alamat || '';
        document.getElementById('latitude').value = data.latitude || '';
        document.getElementById('longitude').value = data.longitude || '';

        const modalAdd = new bootstrap.Modal(document.getElementById('modalAddWna'));
        modalAdd.show();
    }

    function resetAndOpenAddModal() {
        document.getElementById('formAddWna').reset();

        const modalAdd = new bootstrap.Modal(document.getElementById('modalAddWna'));
        modalAdd.show();
    }

    function editWna(data) {
        document.getElementById('formEditWna').action = `/foreigners/${data.id}`;

        document.getElementById('edit_nama').value = data.nama || '';
        document.getElementById('edit_jenis_kelamin').value = data.jenis_kelamin || 'L';
        document.getElementById('edit_nomor_paspor').value = data.nomor_paspor || '';
        document.getElementById('edit_warganegara').value = data.warganegara || '';
        document.getElementById('edit_jenis_izin_tinggal').value = data.jenis_izin_tinggal || '';
        document.getElementById('edit_masa_berlaku_izin_tinggal').value = data.masa_berlaku_izin_tinggal || '';
        document.getElementById('edit_penjamin').value = data.penjamin || '';
        document.getElementById('edit_jenis_penjamin').value = data.jenis_penjamin || 'PERUSAHAAN';
        document.getElementById('edit_alamat').value = data.alamat || '';
        document.getElementById('edit_latitude').value = data.latitude || '';
        document.getElementById('edit_longitude').value = data.longitude || '';

        const modalEdit = new bootstrap.Modal(document.getElementById('modalEditWna'));
        modalEdit.show();
    }

    function deleteWna(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data WNA ini?')) {
            const formDelete = document.getElementById('formDeleteWna');
            formDelete.action = `/foreigners/${id}`;
            formDelete.submit();
        }
    }

    // Render Chart 1: Donut Chart Jenis Penjamin
    const ctxPenjamin = document.getElementById('chartPenjaminCanvas').getContext('2d');
    new Chart(ctxPenjamin, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartPenjamin->keys()) !!},
            datasets: [{
                data: {!! json_encode($chartPenjamin->values()) !!},
                backgroundColor: ['#dc3545', '#0d6efd']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Render Chart 2: Bar Chart Jenis Izin Tinggal
    const ctxIzin = document.getElementById('chartIzinTinggalCanvas').getContext('2d');
    new Chart(ctxIzin, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartIzinTinggal->keys()) !!},
            datasets: [{
                label: 'Jumlah WNA',
                data: {!! json_encode($chartIzinTinggal->values()) !!},
                backgroundColor: '#198754'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // Render Chart 3: Horizontal Bar Chart Warganegara
    const ctxWn = document.getElementById('chartWarganegaraCanvas').getContext('2d');
    new Chart(ctxWn, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartWarganegara->keys()) !!},
            datasets: [{
                label: 'Jumlah WNA',
                data: {!! json_encode($chartWarganegara->values()) !!},
                backgroundColor: '#ffc107'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>

</body>
</html>