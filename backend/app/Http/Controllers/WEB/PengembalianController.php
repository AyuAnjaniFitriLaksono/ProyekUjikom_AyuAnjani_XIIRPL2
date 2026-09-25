<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar pengembalian
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $pengembalians = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjams.alat',
            'petugas'
        ])
        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('kondisi_kembali', 'like', "%{$search}%")
                  ->orWhere('denda', 'like', "%{$search}%")
                  ->orWhereHas('peminjaman.user', function ($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('petugas', function ($petugas) use ($search) {
                      $petugas->where('name', 'like', "%{$search}%");
                  });

            });

        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.pengembalian.index', compact(
            'pengembalians',
            'search'
        ));
    }


    /**
     * Form tambah pengembalian
     */
    public function create($peminjaman_id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailpinjam.alat'
        ])->findOrFail($peminjaman_id);

        // Pengembalian hanya boleh dilakukan jika status dipinjam
        if ($peminjaman->status !== 'dipinjam') {

            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'error',
                    'Peminjaman ini tidak dapat dikembalikan karena statusnya bukan dipinjam.'
                );
        }

        return view(
            'admin.pengembalian.create',
            compact('peminjaman')
        );
    }


    /**
     * Simpan pengembalian
     */
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_kembali' => 'required|string|max:255',
            'denda' => 'nullable|integer|min:0',
        ]);

        try {

            DB::transaction(function () use ($request) {

                $peminjaman = Peminjaman::with('detailpinjam')
                    ->lockForUpdate()
                    ->findOrFail($request->peminjaman_id);


                // Pastikan peminjaman masih berstatus dipinjam
                if ($peminjaman->status !== 'dipinjam') {

                    throw new Exception(
                        "Peminjaman tidak dapat diproses karena statusnya '{$peminjaman->status}'."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Tentukan status akhir
                |--------------------------------------------------------------------------
                */

                $tglKembaliPlan = \Carbon\Carbon::parse(
                    $peminjaman->tgl_kembali_plan
                )->startOfDay();

                $hariIni = \Carbon\Carbon::now()->startOfDay();

                $statusBaru = $hariIni->greaterThan($tglKembaliPlan)
                    ? 'telat'
                    : 'selesai';


                /*
                |--------------------------------------------------------------------------
                | Simpan pengembalian
                |--------------------------------------------------------------------------
                */

                Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => now()->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $request->denda ?? 0,
                    'petugas_id' => auth()->id(),
                ]);


                /*
                |--------------------------------------------------------------------------
                | Kembalikan stok alat
                |--------------------------------------------------------------------------
                */

                foreach ($peminjaman->detailpinjam as $detail) {

                    $alat = Alat::lockForUpdate()
                        ->findOrFail($detail->alat_id);

                    $alat->increment(
                        'stok',
                        $detail->jumlah
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Update status peminjaman
                |--------------------------------------------------------------------------
                */

                $peminjaman->update([
                    'status' => $statusBaru
                ]);
            });


            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Pengembalian alat berhasil disimpan.'
                );

        } catch (Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /**
     * Form edit pengembalian
     */
    public function edit($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'petugas'
        ])->findOrFail($id);

        $peminjamans = Peminjaman::with('user')->get();

        $petugas = User::where('role', 'petugas')->get();

        return view(
            'admin.pengembalian.edit',
            compact('pengembalian', 'peminjamans', 'petugas')
        );
    }


    /**
     * Update pengembalian
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string|max:255',
            'denda' => 'nullable|integer|min:0',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);

        $pengembalian->update([
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $request->denda ?? 0,
        ]);

        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil diperbarui.'
            );
    }


    /**
     * Hapus pengembalian
     */
    public function destroy($id)
    {
        try {

            DB::transaction(function () use ($id) {

                $pengembalian = Pengembalian::findOrFail($id);

                $peminjaman = Peminjaman::with('detailpinjams')
                    ->lockForUpdate()
                    ->findOrFail(
                        $pengembalian->peminjaman_id
                    );


                /*
                |--------------------------------------------------------------------------
                | Kurangi kembali stok alat
                |--------------------------------------------------------------------------
                */

                foreach ($peminjaman->detailpinjams as $detail) {

                    $alat = Alat::lockForUpdate()
                        ->findOrFail($detail->alat_id);


                    if ($alat->stok < $detail->jumlah) {

                        throw new Exception(
                            "Stok alat '{$alat->nama_alat}' tidak mencukupi untuk membatalkan pengembalian."
                        );
                    }


                    $alat->decrement(
                        'stok',
                        $detail->jumlah
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Kembalikan status peminjaman
                |--------------------------------------------------------------------------
                */

                $peminjaman->update([
                    'status' => 'dipinjam'
                ]);


                /*
                |--------------------------------------------------------------------------
                | Hapus pengembalian
                |--------------------------------------------------------------------------
                */

                $pengembalian->delete();
            });


            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Data pengembalian berhasil dihapus.'
                );

        } catch (Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}