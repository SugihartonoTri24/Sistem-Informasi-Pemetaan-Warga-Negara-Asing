<?php

namespace App\Imports;

 use App\Models\Foreigner;
 use App\Imports\ForeignersImport;

class ForeignersImport
{
   

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        ForeignersImport::readAndSave($request->file('file')->getRealPath());

        return redirect()->back()->with('success', 'Data CSV berhasil disimpan ke MySQL!');
    }


    public static function readAndSave($filePath)
    {
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            $isHeader = true;

            while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                if (empty($row[0])) continue;

                $lat = null;
                $lng = null;
                if (!empty($row[8])) {
                    $coords = explode(',', $row[8]);
                    if (count($coords) == 2) {
                        $lat = trim($coords[0]);
                        $lng = trim($coords[1]);
                    }
                }

                Foreigner::create([
                    'nama'                      => $row[0] ?? '-',
                    'jenis_kelamin'             => strtoupper($row[1] ?? 'L'),
                    'nomor_paspor'              => $row[2] ?? '-',
                    'warganegara'               => $row[3] ?? '-',
                    'jenis_izin_tinggal'        => $row[4] ?? '-',
                    'masa_berlaku_izin_tinggal' => date('Y-m-d'),
                    'penjamin'                  => $row[6] ?? '-',
                    'jenis_penjamin'            => 'PERUSAHAAN',
                    'alamat'                    => $row[7] ?? '-',
                    'latitude'                  => $lat,
                    'longitude'                 => $lng,
                ]);
            }
            fclose($handle);
        }
    }
}