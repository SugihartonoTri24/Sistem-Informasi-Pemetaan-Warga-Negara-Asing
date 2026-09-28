<?php

namespace App\Http\Controllers;

use App\Models\Wna; // Pastikan Model Wna sudah ada
use Illuminate\Http\Request;

class WnaController extends Controller
{
    public function index(Request $request)
    {
        $query = Wna::query();

        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_paspor', 'like', '%' . $request->search . '%')
                  ->orWhere('kewarganegaraan', 'like', '%' . $request->search . '%');
        }

        $wnaList = $query->latest()->paginate(10);
        return view('wna.index', compact('wnaList'));
    }

    public function create()
    {
        return view('wna.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'       => 'required|string|max:255',
            'nomor_paspor'       => 'required|string|max:50|unique:wnas,nomor_paspor',
            'kewarganegaraan'   => 'required|string|max:100',
            'jenis_izin_tinggal' => 'required|string|max:100',
            'penjamin'           => 'required|string|max:255',
            'jenis_penjamin'     => 'required|in:PERUSAHAAN,PERORANGAN',
            'lokasi_kegiatan'    => 'required|string|max:255',
        ]);

        Wna::create($validated);

        return redirect()->route('wna.index')->with('success', 'Data WNA berhasil ditambahkan.');
    }

    public function edit(Wna $wna)
    {
        return view('wna.edit', compact('wna'));
    }

    public function update(Request $request, Wna $wna)
    {
        $validated = $request->validate([
            'nama_lengkap'       => 'required|string|max:255',
            'nomor_paspor'       => 'required|string|max:50|unique:wnas,nomor_paspor,' . $wna->id,
            'kewarganegaraan'   => 'required|string|max:100',
            'jenis_izin_tinggal' => 'required|string|max:100',
            'penjamin'           => 'required|string|max:255',
            'jenis_penjamin'     => 'required|in:PERUSAHAAN,PERORANGAN',
            'lokasi_kegiatan'    => 'required|string|max:255',
        ]);

        $wna->update($validated);

        return redirect()->route('wna.index')->with('success', 'Data WNA berhasil diperbarui.');
    }

    public function destroy(Wna $wna)
    {
        $wna->delete();
        return redirect()->route('wna.index')->with('success', 'Data WNA berhasil dihapus.');
    }
}