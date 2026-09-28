<?php

namespace App\Http\Controllers;

use App\Models\Foreigner;
use App\Imports\ForeignersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForeignerController extends Controller
{
    // 1. Menampilkan halaman Dashboard & Peta beserta data statistik
    public function index()
    {
        // Hitung total data langsung via database query (lebih hemat memori dibanding count dari Collection)
        $totalForeigners = Foreigner::count();
        $totalPerusahaan = Foreigner::where('jenis_penjamin', 'PERUSAHAAN')->count();
        $totalPerorangan = Foreigner::where('jenis_penjamin', 'PERORANGAN')->count();

        // Data Grafik: Jumlah berdasarkan Jenis Penjamin
        $chartPenjamin = Foreigner::select('jenis_penjamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_penjamin')
            ->pluck('total', 'jenis_penjamin');

        // Data Grafik: Top 5 Jenis Izin Tinggal
        $chartIzinTinggal = Foreigner::select('jenis_izin_tinggal', DB::raw('count(*) as total'))
            ->groupBy('jenis_izin_tinggal')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'jenis_izin_tinggal');

        // Data Grafik: Top 5 Warganegara
        $chartWarganegara = Foreigner::select('warganegara', DB::raw('count(*) as total'))
            ->groupBy('warganegara')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'warganegara');

        return view('map', compact(
            'totalForeigners', 
            'totalPerusahaan', 
            'totalPerorangan',
            'chartPenjamin',
            'chartIzinTinggal',
            'chartWarganegara'
        ));
    }

    // 2. Endpoint JSON untuk memuat data via AJAX/API Leaflet Map
    public function getLocations()
    {
        $foreigners = Foreigner::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return response()->json($foreigners);
    }

    // 3. Menyimpan data WNA baru secara manual
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                      => 'required|string|max:255',
            'jenis_kelamin'             => 'required|in:L,P',
            'nomor_paspor'              => 'required|string|unique:foreigners,nomor_paspor',
            'warganegara'               => 'required|string|max:100',
            'jenis_izin_tinggal'        => 'required|string|max:100',
            'masa_berlaku_izin_tinggal' => 'required|date',
            'penjamin'                  => 'required|string|max:255',
            'jenis_penjamin'            => 'required|in:PERUSAHAAN,PERORANGAN',
            'alamat'                    => 'required|string',
            'latitude'                  => 'nullable|numeric',
            'longitude'                 => 'nullable|numeric',
        ], [
            'nomor_paspor.required' => 'Nomor paspor wajib diisi!',
            'nomor_paspor.unique'   => 'Nomor paspor ini sudah terdaftar di sistem!',
        ]);

        Foreigner::create($validated);

        return redirect()->back()->with('success', 'Data WNA berhasil ditambahkan!');
    }

    // 4. Import data dari file Excel / CSV
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Maksimal 10MB
        ], [
            'file.required' => 'Silakan pilih file terlebih dahulu!',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv!',
            'file.max'      => 'Ukuran file maksimal adalah 10 MB!'
        ]);

        Excel::import(new ForeignersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Excel berhasil di-import ke database!');
    }

    // 5. Mengubah/Update data WNA
    public function update(Request $request, $id)
    {
        $wna = Foreigner::findOrFail($id);

        $validated = $request->validate([
            'nama'                      => 'required|string|max:255',
            'jenis_kelamin'             => 'required|in:L,P',
            'nomor_paspor'              => 'required|string|unique:foreigners,nomor_paspor,' . $id,
            'warganegara'               => 'required|string|max:100',
            'jenis_izin_tinggal'        => 'required|string|max:100',
            'masa_berlaku_izin_tinggal' => 'required|date',
            'penjamin'                  => 'required|string|max:255',
            'jenis_penjamin'            => 'required|in:PERUSAHAAN,PERORANGAN',
            'alamat'                    => 'required|string',
            'latitude'                  => 'nullable|numeric',
            'longitude'                 => 'nullable|numeric',
        ], [
            'nomor_paspor.unique' => 'Nomor paspor ini sudah digunakan oleh data WNA lain!',
        ]);

        $wna->update($validated);

        return redirect()->back()->with('success', 'Data WNA berhasil diperbarui!');
    }

    // 6. Menghapus data WNA
    public function destroy($id)
    {
        $wna = Foreigner::findOrFail($id);
        $wna->delete();

        return redirect()->back()->with('success', 'Data WNA berhasil dihapus!');
    }
    // Tambahkan method ini di ForeignerController.php untuk menampilkan halaman tabel Data WNA
public function listData(Request $request)
{
    $query = Foreigner::query();

    // Fitur Pencarian Data
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nomor_paspor', 'like', "%{$search}%")
              ->orWhere('warganegara', 'like', "%{$search}%")
              ->orWhere('penjamin', 'like', "%{$search}%");
        });
    }

    $foreigners = $query->latest()->paginate(10);

    return view('wna.index', compact('foreigners'));
}
}

