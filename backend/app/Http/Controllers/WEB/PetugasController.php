<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // =========================================================
    // PROFILE PETUGAS
    // =========================================================

    // Menampilkan halaman profil petugas
    public function profile()
    {
        $user = auth()->user();

        return view('petugas.profile', compact('user'));
    }


    // Mengubah foto profil petugas
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('foto_profile')) {

            // Hapus foto lama jika ada
            if (
                $user->foto_profile &&
                file_exists(public_path($user->foto_profile))
            ) {
                unlink(public_path($user->foto_profile));
            }

            // Folder penyimpanan foto
            $folder = public_path('storage/profil');

            // Buat folder jika belum ada
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Ambil file foto
            $file = $request->file('foto_profile');

            // Buat nama file unik
            $filename = time() . '_' . $file->getClientOriginalName();

            // Pindahkan foto
            $file->move(
                $folder,
                $filename
            );

            // Simpan lokasi foto ke database
            $user->foto_profile = 'storage/profil/' . $filename;

            $user->save();
        }

        return redirect()
            ->route('petugas.profile')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }


    // Mengubah data profil petugas
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'no_hp' => 'nullable|string|max:20',

            'alamat' => 'nullable|string',
        ]);

        // Update data profil
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()
            ->route('petugas.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }


    // =========================================================
    // PEMINJAMAN
    // =========================================================

    // Menampilkan daftar pengajuan peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
        ->where('status', 'diajukan')
        ->when($search, function ($query, $search) {

            return $query->whereHas('user', function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );

            });

        })
        ->latest()
        ->get();

        return view(
            'petugas.peminjaman.index',
            compact(
                'peminjamans',
                'search'
            )
        );
    }


    // =========================================================
    // SETUJUI PEMINJAMAN
    // =========================================================

    // Menyetujui peminjaman
    // Status menjadi dipinjam
    // Stok alat dikurangi
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();

        try {

            $peminjaman = Peminjaman::with('detailPinjams')
                ->findOrFail($id);


            // Pastikan status masih diajukan
            if ($peminjaman->status !== 'diajukan') {

                DB::rollBack();

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Status peminjaman sudah berubah.'
                    );
            }


            // Cek stok terlebih dahulu
            foreach ($peminjaman->detailPinjams as $detail) {

                $alat = Alat::findOrFail($detail->alat_id);

                if ($alat->stok < $detail->jumlah) {

                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'Stok alat "' .
                            $alat->nama_alat .
                            '" tidak mencukupi.'
                        );
                }
            }


            // Ubah status menjadi dipinjam
            $peminjaman->update([
                'status' => 'dipinjam'
            ]);


            // Kurangi stok alat
            foreach ($peminjaman->detailPinjams as $detail) {

                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok -= $detail->jumlah;

                $alat->save();
            }


            DB::commit();

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Peminjaman disetujui dan stok alat dikurangi.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' . $e->getMessage()
                );
        }
    }


    // =========================================================
    // TOLAK PEMINJAMAN
    // =========================================================

    // Menolak peminjaman
    // Pengajuan dihapus agar peminjam dapat mengajukan ulang
    public function tolakPeminjaman($id)
    {
        try {

            $peminjaman = Peminjaman::findOrFail($id);


            // Pastikan status masih diajukan
            if ($peminjaman->status === 'diajukan') {

                $peminjaman->delete();

                return redirect()
                    ->back()
                    ->with(
                        'success',
                        'Pengajuan peminjaman berhasil ditolak.'
                    );
            }


            return redirect()
                ->back()
                ->with(
                    'error',
                    'Status peminjaman sudah berubah.'
                );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' .
                    $e->getMessage()
                );
        }
    }


    // =========================================================
    // PENGEMBALIAN
    // =========================================================

    // Menampilkan pemantauan pengembalian
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian'
        ])
        ->whereIn('status', [
            'dipinjam',
            'telat',
            'dikembalikan'
        ])
        ->when($search, function ($query, $search) {

            return $query->whereHas('user', function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );

            });

        })
        ->latest()
        ->get();

        return view(
            'petugas.pengembalian.index',
            compact(
                'peminjamans',
                'search'
            )
        );
    }


    // Memproses pengembalian
    public function prosesPengembalian(
        Request $request,
        $peminjamanId
    ) {

        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);


        DB::beginTransaction();

        try {

            $peminjaman = Peminjaman::with([
                'detailPinjams',
                'pengembalian'
            ])
            ->findOrFail($peminjamanId);


            // Pastikan peminjaman masih dipinjam atau telat
            if (!in_array(
                $peminjaman->status,
                ['dipinjam', 'telat']
            )) {

                DB::rollBack();

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Peminjaman ini sudah dikembalikan atau statusnya tidak dapat diproses.'
                    );
            }


            // Pastikan belum ada data pengembalian
            if ($peminjaman->pengembalian) {

                DB::rollBack();

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Pengembalian untuk peminjaman ini sudah diproses.'
                    );
            }


            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->denda ?? 0,
                'petugas_id'      => auth()->id(),
            ]);


            // Ubah status menjadi dikembalikan
            $peminjaman->update([
                'status' => 'dikembalikan'
            ]);


            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjams as $detail) {

                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok += $detail->jumlah;

                $alat->save();
            }


            DB::commit();

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Pengembalian berhasil diproses.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' .
                    $e->getMessage()
                );
        }
    }


    // =========================================================
    // TOLAK PENGEMBALIAN
    // =========================================================

    // Menolak proses pengembalian
    public function tolakPengembalian($id)
    {
        try {

            $peminjaman = Peminjaman::findOrFail($id);


            // Pengembalian hanya dapat ditolak
            // jika status masih dipinjam atau telat
            if (in_array(
                $peminjaman->status,
                ['dipinjam', 'telat']
            )) {

                return redirect()
                    ->back()
                    ->with(
                        'success',
                        'Proses pengembalian dibatalkan.'
                    );
            }


            return redirect()
                ->back()
                ->with(
                    'error',
                    'Pengembalian tidak dapat ditolak karena status sudah berubah.'
                );

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' .
                    $e->getMessage()
                );
        }
    }


    // =========================================================
    // LAPORAN
    // =========================================================

    // Menampilkan laporan
    public function laporan(Request $request)
    {
        $status = $request->input('status');

        $dari_tanggal = $request->input('dari_tanggal');

        $sampai_tanggal = $request->input('sampai_tanggal');


        $laporans = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian'
        ])
        ->when(
            $status,
            function ($query, $status) {

                return $query->where(
                    'status',
                    $status
                );

            }
        )
        ->when(
            $dari_tanggal && $sampai_tanggal,
            function ($query) use (
                $dari_tanggal,
                $sampai_tanggal
            ) {

                return $query->whereBetween(
                    'tgl_pinjam',
                    [
                        $dari_tanggal,
                        $sampai_tanggal
                    ]
                );

            }
        )
        ->latest()
        ->get();


        return view(
            'petugas.laporan.index',
            compact(
                'laporans',
                'status',
                'dari_tanggal',
                'sampai_tanggal'
            )
        );
    }


    // Menampilkan halaman khusus cetak laporan
    public function cetakLaporan(Request $request)
    {
        $status = $request->input('status');

        $dari_tanggal = $request->input('dari_tanggal');

        $sampai_tanggal = $request->input('sampai_tanggal');


        $laporans = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian'
        ])
        ->when(
            $status,
            function ($query, $status) {

                return $query->where(
                    'status',
                    $status
                );

            }
        )
        ->when(
            $dari_tanggal && $sampai_tanggal,
            function ($query) use (
                $dari_tanggal,
                $sampai_tanggal
            ) {

                return $query->whereBetween(
                    'tgl_pinjam',
                    [
                        $dari_tanggal,
                        $sampai_tanggal
                    ]
                );

            }
        )
        ->latest()
        ->get();


        return view(
            'petugas.laporan.cetak',
            compact(
                'laporans',
                'status',
                'dari_tanggal',
                'sampai_tanggal'
            )
        );
    }
}