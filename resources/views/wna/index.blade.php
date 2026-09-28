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

    <!-- CONTENT WRAPPER -->
    <div id="content">
        <div class="container-fluid px-4 py-3">
            <!-- Header Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Data Warga Negara Asing (WNA)</h4>
                    <small class="text-muted">Kelola dan pantau seluruh data WNA yang terdaftar</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalImport">
                        <i class="bi bi-file-earmark-excel"></i> Import Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah WNA
                    </button>
                </div>
            </div>

            <!-- Card Tabel -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <!-- Filter & Search Bar -->
                    <div class="p-3 border-bottom bg-light">
                        <form action="{{ route('wna.index') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, paspor, warganegara, penjamin...">
                                    <button class="btn btn-secondary" type="submit">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small text-secondary">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. Paspor</th>
                                    <th>Warganegara</th>
                                    <th>Izin Tinggal</th>
                                    <th>Penjamin</th>
                                    <th>Jenis Penjamin</th>
                                    <th>Alamat</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($foreigners as $index => $item)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $foreigners->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-gender-{{ $item->jenis_kelamin == 'L' ? 'male text-primary' : 'female text-danger' }}"></i>
                                            {{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $item->nomor_paspor }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $item->warganegara }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            {{ $item->jenis_izin_tinggal }}
                                        </span>
                                        @if($item->masa_berlaku_izin_tinggal)
                                            <div class="small text-muted mt-1">s/d {{ \Carbon\Carbon::parse($item->masa_berlaku_izin_tinggal)->format('d-m-Y') }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $item->penjamin }}</td>
                                    <td>
                                        @if($item->jenis_penjamin == 'PERUSAHAAN')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">PERUSAHAAN</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">PERORANGAN</span>
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width: 180px;" title="{{ $item->alamat }}">
                                        {{ $item->alamat }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('foreigners.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Belum ada data WNA yang terdaftar.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="p-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Menampilkan {{ $foreigners->firstItem() ?? 0 }} - {{ $foreigners->lastItem() ?? 0 }} dari {{ $foreigners->total() ?? 0 }} data
                        </small>
                        <div>
                            {{ $foreigners->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT WNA (Dipindah keluar tabel agar HTML valid) -->
@foreach($foreigners as $item)
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data WNA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('foreigners.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ $item->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $item->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Paspor</label>
                        <input type="text" name="nomor_paspor" class="form-control" value="{{ $item->nomor_paspor }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Warganegara</label>
                        <input type="text" name="warganegara" class="form-control" value="{{ $item->warganegara }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Izin Tinggal</label>
                        <input type="text" name="jenis_izin_tinggal" class="form-control" value="{{ $item->jenis_izin_tinggal }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Masa Berlaku Izin Tinggal</label>
                        <input type="date" name="masa_berlaku_izin_tinggal" class="form-control" value="{{ $item->masa_berlaku_izin_tinggal }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Penjamin</label>
                        <input type="text" name="penjamin" class="form-control" value="{{ $item->penjamin }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Penjamin</label>
                        <select name="jenis_penjamin" class="form-select" required>
                            <option value="PERUSAHAAN" {{ $item->jenis_penjamin == 'PERUSAHAAN' ? 'selected' : '' }}>PERUSAHAAN</option>
                            <option value="PERORANGAN" {{ $item->jenis_penjamin == 'PERORANGAN' ? 'selected' : '' }}>PERORANGAN</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required>{{ $item->alamat }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="{{ $item->latitude }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="{{ $item->longitude }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- MODAL TAMBAH WNA -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Data WNA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('foreigners.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Paspor</label>
                        <input type="text" name="nomor_paspor" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Warganegara</label>
                        <input type="text" name="warganegara" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Izin Tinggal</label>
                        <input type="text" name="jenis_izin_tinggal" class="form-control" placeholder="Contoh: ITAS KERJA" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Masa Berlaku Izin Tinggal</label>
                        <input type="date" name="masa_berlaku_izin_tinggal" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Penjamin</label>
                        <input type="text" name="penjamin" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Penjamin</label>
                        <select name="jenis_penjamin" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="PERUSAHAAN">PERUSAHAAN</option>
                            <option value="PERORANGAN">PERORANGAN</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" placeholder="-0.123456">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" placeholder="109.123456">
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

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Data dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('foreigners.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel / CSV</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        <small class="text-muted d-block mt-1">Format yang didukung: .xlsx, .xls, .csv (Maks. 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle (Wajib untuk komponen Modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>