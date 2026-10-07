<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /**
     * Menampilkan data pengembalian
     * dan peminjaman yang masih aktif.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        /*
        |--------------------------------------------------------------------------
        | DATA PENGEMBALIAN
        |--------------------------------------------------------------------------
        */

        $pengembalians = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjams.alat',
            'petugas',
            'detailPengembalian.alat'
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    $q->where(
                        'kondisi_kembali',
                        'like',
                        "%{$search}%"
                    )

                        ->orWhere(
                            'denda',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhereHas(
                            'peminjaman.user',
                            function ($user) use ($search) {
                                $user->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        )

                        ->orWhereHas(
                            'petugas',
                            function ($petugas) use ($search) {
                                $petugas->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN AKTIF
        |--------------------------------------------------------------------------
        */

        $peminjamanAktif = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get();

        return view(
            'admin.pengembalian.index',
            compact(
                'pengembalians',
                'peminjamanAktif',
                'search'
            )
        );
    }


    /**
     * Form tambah pengembalian
     */
    public function create($peminjaman_id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])->findOrFail($peminjaman_id);

        /*
        |--------------------------------------------------------------------------
        | Hanya peminjaman aktif yang boleh dikembalikan
        |--------------------------------------------------------------------------
        */

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'error',
                    'Peminjaman ini tidak dapat diproses karena sudah tidak aktif.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA PETUGAS
        |--------------------------------------------------------------------------
        */

        $petugas = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | HITUNG INFORMASI KETERLAMBATAN
        |--------------------------------------------------------------------------
        */

        $hariIni = Carbon::now()->startOfDay();

        $tglKembaliPlan = Carbon::parse(
            $peminjaman->tgl_kembali_plan
        )->startOfDay();

        $hariTerlambat = 0;

        if ($hariIni->greaterThan($tglKembaliPlan)) {
            $hariTerlambat = $tglKembaliPlan->diffInDays($hariIni);
        }

        /*
        |--------------------------------------------------------------------------
        | DENDA KETERLAMBATAN
        | Rp1.000 per hari
        |--------------------------------------------------------------------------
        */

        $dendaKeterlambatan = $hariTerlambat * 1000;

        return view(
            'admin.pengembalian.create',
            compact(
                'peminjaman',
                'petugas',
                'hariTerlambat',
                'dendaKeterlambatan'
            )
        );
    }


    /**
     * Simpan pengembalian
     */
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',

            'tgl_kembali' => 'required|date',

            'kondisi_kembali' => 'required|in:baik,rusak ringan,rusak berat',

            'denda_kerusakan' => 'required|numeric|min:0',

            'detail' => 'required|array',

            'detail.*.alat_id' => 'required|exists:alat,id',

            'detail.*.jumlah' => 'required|integer|min:1',

            'detail.*.kondisi' => 'required|in:baik,rusak ringan,rusak berat',
        ]);

        try {

            DB::transaction(function () use ($request) {

                /*
                |--------------------------------------------------------------------------
                | AMBIL PEMINJAMAN
                |--------------------------------------------------------------------------
                */

                $peminjaman = Peminjaman::with([
                    'user',
                    'detailPinjams'
                ])
                    ->lockForUpdate()
                    ->findOrFail($request->peminjaman_id);

                /*
                |--------------------------------------------------------------------------
                | PASTIKAN MASIH AKTIF
                |--------------------------------------------------------------------------
                */

                if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                    throw new Exception(
                        'Peminjaman ini sudah tidak dapat diproses.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | HITUNG KETERLAMBATAN
                |--------------------------------------------------------------------------
                */

                $tglKembali = Carbon::parse(
                    $request->tgl_kembali
                )->startOfDay();

                $tglKembaliPlan = Carbon::parse(
                    $peminjaman->tgl_kembali_plan
                )->startOfDay();

                $hariTerlambat = 0;

                if ($tglKembali->greaterThan($tglKembaliPlan)) {
                    $hariTerlambat = $tglKembaliPlan->diffInDays(
                        $tglKembali
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | DENDA KETERLAMBATAN
                | Rp1.000 per hari
                |--------------------------------------------------------------------------
                */

                $dendaKeterlambatan = $hariTerlambat * 1000;

                /*
                |--------------------------------------------------------------------------
                | DENDA KERUSAKAN
                |--------------------------------------------------------------------------
                */

                $dendaKerusakan = (int) $request->denda_kerusakan;

                /*
                |--------------------------------------------------------------------------
                | TOTAL DENDA
                |--------------------------------------------------------------------------
                */

                $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

                /*
                |--------------------------------------------------------------------------
                | SIMPAN DATA PENGEMBALIAN
                |--------------------------------------------------------------------------
                */

                $pengembalian = Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => $request->tgl_kembali,
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $totalDenda,
                    'petugas_id' => auth()->id(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | SIMPAN DETAIL PENGEMBALIAN
                |--------------------------------------------------------------------------
                */

                foreach ($request->detail as $detail) {

                    $jumlah = (int) $detail['jumlah'];

                    $kondisi = $detail['kondisi'];

                    DetailPengembalian::create([
                        'pengembalian_id' => $pengembalian->id,
                        'alat_id' => $detail['alat_id'],
                        'jumlah' => $jumlah,
                        'kondisi' => $kondisi,
                        'denda' => 0,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK ALAT
                |--------------------------------------------------------------------------
                */

                foreach ($peminjaman->detailPinjams as $detailPinjam) {

                    $alat = Alat::lockForUpdate()
                        ->findOrFail($detailPinjam->alat_id);

                    $alat->increment(
                        'stok',
                        $detailPinjam->jumlah
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UBAH STATUS PEMINJAMAN
                |--------------------------------------------------------------------------
                */

                $peminjaman->update([
                    'status' => 'dikembalikan'
                ]);

                /*
                |--------------------------------------------------------------------------
                | LOG AKTIVITAS - TAMBAH
                |--------------------------------------------------------------------------
                |
                | created_at otomatis mengikuti waktu saat aksi dilakukan.
                |
                */

                LogAktivitas::create([
                    'user_id' => auth()->id(),
                    'aktivitas' =>
                        'Menambahkan pengembalian untuk peminjaman atas nama ' .
                        ($peminjaman->user->name ?? 'Tidak diketahui') .
                        ' dengan kondisi ' .
                        $request->kondisi_kembali .
                        '.'
                ]);
            });

            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Pengembalian berhasil disimpan. Peminjaman sekarang berstatus dikembalikan.'
                );

        } catch (Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' . $e->getMessage()
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
            'petugas',
            'detailPengembalian.alat'
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        $peminjamans = Peminjaman::with('user')->get();

        return view(
            'admin.pengembalian.edit',
            compact(
                'pengembalian',
                'peminjamans'
            )
        );
    }


    /**
     * Update pengembalian
     */
    public function update(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak ringan,rusak berat',

            'denda_kerusakan' => 'required|numeric|min:0',
        ]);

        try {

            $pengembalian = Pengembalian::with([
                'peminjaman.user',
                'peminjaman.detailPinjams.alat',
                'detailPengembalian.alat'
            ])->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | SIMPAN KONDISI LAMA
            |--------------------------------------------------------------------------
            */

            $kondisiLama = $pengembalian->kondisi_kembali;

            /*
            |--------------------------------------------------------------------------
            | HITUNG ULANG DENDA KETERLAMBATAN
            |--------------------------------------------------------------------------
            */

            $tglKembali = Carbon::parse(
                $pengembalian->tgl_kembali
            )->startOfDay();

            $tglKembaliPlan = Carbon::parse(
                $pengembalian->peminjaman->tgl_kembali_plan
            )->startOfDay();

            $hariTerlambat = 0;

            if ($tglKembali->greaterThan($tglKembaliPlan)) {
                $hariTerlambat = $tglKembaliPlan->diffInDays(
                    $tglKembali
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DENDA KETERLAMBATAN
            | Rp1.000 per hari
            |--------------------------------------------------------------------------
            */

            $dendaKeterlambatan = $hariTerlambat * 1000;

            /*
            |--------------------------------------------------------------------------
            | DENDA KERUSAKAN
            |--------------------------------------------------------------------------
            */

            $dendaKerusakan = (int) $request->denda_kerusakan;

            /*
            |--------------------------------------------------------------------------
            | TOTAL DENDA
            |--------------------------------------------------------------------------
            */

            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            /*
            |--------------------------------------------------------------------------
            | DETAIL PENGEMBALIAN
            |--------------------------------------------------------------------------
            */

            foreach ($pengembalian->detailPengembalian as $detail) {

                $detail->update([
                    'denda' => 0
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE DATA PENGEMBALIAN
            |--------------------------------------------------------------------------
            */

            $pengembalian->update([
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
                'petugas_id' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | LOG AKTIVITAS - UPDATE
            |--------------------------------------------------------------------------
            */

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' =>
                    'Mengubah pengembalian untuk peminjaman atas nama ' .
                    ($pengembalian->peminjaman->user->name ?? 'Tidak diketahui') .
                    ' dari kondisi ' .
                    ($kondisiLama ?? '-') .
                    ' menjadi ' .
                    $request->kondisi_kembali .
                    '.'
            ]);

            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Data pengembalian berhasil diperbarui.'
                );

        } catch (Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' . $e->getMessage()
                );
        }
    }


    /**
     * Hapus pengembalian
     */
    public function destroy($id)
    {
        try {

            DB::transaction(function () use ($id) {

                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA PENGEMBALIAN
                |--------------------------------------------------------------------------
                */

                $pengembalian = Pengembalian::with([
                    'peminjaman.user'
                ])->findOrFail($id);

                /*
                |--------------------------------------------------------------------------
                | SIMPAN NAMA PEMINJAM SEBELUM DATA DIHAPUS
                |--------------------------------------------------------------------------
                */

                $namaPeminjam =
                    $pengembalian->peminjaman->user->name
                    ?? 'Tidak diketahui';

                /*
                |--------------------------------------------------------------------------
                | HAPUS DETAIL PENGEMBALIAN
                |--------------------------------------------------------------------------
                */

                $pengembalian->detailPengembalian()->delete();

                /*
                |--------------------------------------------------------------------------
                | HAPUS DATA PENGEMBALIAN
                |--------------------------------------------------------------------------
                */

                $pengembalian->delete();

                /*
                |--------------------------------------------------------------------------
                | LOG AKTIVITAS - HAPUS
                |--------------------------------------------------------------------------
                */

                LogAktivitas::create([
                    'user_id' => auth()->id(),
                    'aktivitas' =>
                        'Menghapus data pengembalian untuk peminjaman atas nama ' .
                        $namaPeminjam .
                        '.'
                ]);
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