<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Sebaran Lokasi WNA</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-4">
    <h2 class="mb-3 text-center">Sistem Pemetaan Lokasi WNA</h2>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Inisialisasi Peta (Pusat lokasi di sekitar Bangka Belitung)
    const map = L.map('map').setView([-2.1539, 106.1308], 10);

    // 2. Tambahkan Layer Peta dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Icon Custom Warna Marker
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

    const redIcon = createIcon('red');   // Untuk Sponsor Perusahaan
    const blueIcon = createIcon('blue'); // Untuk Sponsor Perorangan

    // 3. Fetch Data WNA dari Backend Laravel
    fetch('/api/foreigners')
        .then(response => response.json())
        .then(data => {
            data.forEach(wna => {
                // Tentukan warna marker berdasarkan jenis penjamin
                const markerIcon = wna.jenis_penjamin === 'PERUSAHAAN' ? redIcon : blueIcon;

                // Tambahkan Marker ke Peta
                const marker = L.marker([wna.latitude, wna.longitude], { icon: markerIcon }).addTo(map);

                // Konten Popup saat Marker diklik
                const popupContent = `
                    <div style="font-size: 13px;">
                        <h6 class="m-0 fw-bold">${wna.nama}</h6>
                        <small class="text-muted">${wna.warganegara} (${wna.jenis_kelamin})</small>
                        <hr class="my-1">
                        <b>No. Paspor:</b> ${wna.nomor_paspor}<br>
                        <b>Izin Tinggal:</b> ${wna.jenis_izin_tinggal}<br>
                        <b>Masa Berlaku:</b> ${wna.masa_berlaku_izin_tinggal}<br>
                        <b>Penjamin:</b> ${wna.penjamin} (<span class="badge ${wna.jenis_penjamin === 'PERUSAHAAN' ? 'bg-danger' : 'bg-primary'}">${wna.jenis_penjamin}</span>)<br>
                        <b>Alamat:</b> ${wna.alamat}
                    </div>
                `;
                marker.bindPopup(popupContent);
            });
        })
        .catch(err => console.error("Gagal memuat data koordinat:", err));
</script>
</body>
</html>