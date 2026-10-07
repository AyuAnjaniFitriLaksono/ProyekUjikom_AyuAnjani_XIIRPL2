<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Http\Requests\Pengembalian\UpdatePengembalianRequest;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class PengembalianController extends Controller
{
    //Menampilkan riwayat pengembalian
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $query = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ]);

        if ($user->role === 'peminjam') {
            $query->whereHas('peminjaman', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $pengembalian = $query->latest()->get();

        return response()->json([
            'message' => 'Riwayat pengembalian berhasil diambil.',
            'data' => $pengembalian
        ]);
    }

    //Menampilkan detail pengembalian
    public function show(Pengembalian $pengembalian): JsonResponse
    {
        $user = auth()->user();

        $pengembalian->load([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ]);

        if (
            $user->role === 'peminjam' &&
            $pengembalian->peminjaman->user_id !== $user->id
        ) {
            return response()->json([
                'message' => 'Akses ditolak.'
            ], 403);
        }

        return response()->json([
            'message' => 'Detail pengembalian berhasil diambil.',
            'data' => $pengembalian
        ]);
    }

    //Menyimpan data pengembalian
    public function store(StorePengembalianRequest $request): JsonResponse
    {
        try {

            $pengembalian = DB::transaction(function () use ($request) {

                $peminjaman = Peminjaman::with([
                    'detailPinjam.alat'
                ])
                    ->lockForUpdate()
                    ->find($request->peminjaman_id);

                if (!$peminjaman) {
                    throw new Exception(
                        'Data peminjaman tidak ditemukan.'
                    );
                }
                
                //CEK STATUS PEMINJAMAN
                if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                    throw new Exception(
                        "Data ditolak. Peminjaman ini berstatus '{$peminjaman->status}' dan tidak dapat dikembalikan."
                    );
                }

                //DENDA KETERLAMBATAN
                $hariTerlambat = 0;

                if ($peminjaman->tgl_kembali_plan) {

                    $tglKembaliPlan = Carbon::parse(
                        $peminjaman->tgl_kembali_plan
                    )->startOfDay();

                    $tglKembali = Carbon::now()->startOfDay();

                    if ($tglKembali->greaterThan($tglKembaliPlan)) {

                        $hariTerlambat = $tglKembaliPlan->diffInDays(
                            $tglKembali
                        );
                    }
                }

                //Rp1.000 per hari
                $dendaKeterlambatan = $hariTerlambat * 1000;

                $dendaKerusakan = 0;

                $detailDenda = [];

                foreach ($peminjaman->detailPinjam as $detail) {

                    $alat = Alat::lockForUpdate()
                        ->find($detail->alat_id);

                    if (!$alat) {
                        throw new Exception(
                            "Alat dengan ID {$detail->alat_id} tidak ditemukan."
                        );
                    }

                    $jumlah = (int) $detail->jumlah;

                    //Kondisi alat diambil dari request jika tersedia
                    //Jika API tidak mengirim detail kondisi, gunakan kondisi umum pengembalian
                    $kondisi = $request->kondisi_kembali ?? 'baik';

                    $kondisi = strtolower(
                        trim($kondisi)
                    );


                    if ($kondisi === 'rusak ringan') {

                        $dendaDetail =
                            (int) $alat->denda_ringan * $jumlah;

                    } elseif ($kondisi === 'rusak berat') {

                        $dendaDetail =
                            (int) $alat->denda_berat * $jumlah;

                    } else {

                        //Baik = Rp0
                        $dendaDetail = 0;
                    }

                    $dendaKerusakan += $dendaDetail;

                    $detailDenda[] = [
                        'alat_id' => $alat->id,
                        'jumlah' => $jumlah,
                        'kondisi' => $kondisi,
                        'denda' => $dendaDetail,
                    ];
                }

                //TOTAL DENDA
                $totalDenda =
                    $dendaKeterlambatan +
                    $dendaKerusakan;
                
                //SIMPAN PENGEMBALIAN
                $pengembalian = Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => now()->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $totalDenda,
                    'petugas_id' => auth()->id(),
                ]);

                //SIMPAN DETAIL PENGEMBALIAN
                foreach ($detailDenda as $detail) {

                    DB::table('detail_pengembalian')->insert([
                        'pengembalian_id' => $pengembalian->id,
                        'alat_id' => $detail['alat_id'],
                        'jumlah' => $detail['jumlah'],
                        'kondisi' => $detail['kondisi'],
                        'denda' => $detail['denda'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                //UBAH STATUS PEMINJAMAN
                $peminjaman->update([
                    'status' => 'dikembalikan'
                ]);

                //KEMBALIKAN STOK ALAT
                foreach ($peminjaman->detailPinjam as $detail) {

                    $alat = Alat::lockForUpdate()
                        ->find($detail->alat_id);

                    if (!$alat) {
                        throw new Exception(
                            "Alat dengan ID {$detail->alat_id} tidak ditemukan."
                        );
                    }

                    $alat->increment(
                        'stok',
                        $detail->jumlah
                    );
                }

                //LOG AKTIVITAS
                if (auth()->user()) {

                    auth()->user()->logAktivitas()->create([
                        'aktivitas' =>
                            "Memproses pengembalian peminjaman ID: #{$peminjaman->id}. Total denda: Rp " .
                            number_format(
                                $totalDenda,
                                0,
                                ',',
                                '.'
                            )
                    ]);
                }

                //LOAD DATA UNTUK RESPONSE
                return $pengembalian->load([
                    'peminjaman.user',
                    'petugas'
                ]);
            });


            return response()->json([
                'message' =>
                    'Proses pengembalian alat berhasil diselesaikan.',
                'data' => $pengembalian
            ], 201);


        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    //Memperbaharui data pengembalian
    public function update(
        UpdatePengembalianRequest $request,
        Pengembalian $pengembalian
    ): JsonResponse {

        try {

            $peminjaman = $pengembalian->peminjaman;

            $dendaKeterlambatan = 0;

            if (
                $peminjaman &&
                $peminjaman->tgl_kembali_plan &&
                $pengembalian->tgl_kembali
            ) {

                $tglRencana = Carbon::parse(
                    $peminjaman->tgl_kembali_plan
                )->startOfDay();

                $tglKembali = Carbon::parse(
                    $pengembalian->tgl_kembali
                )->startOfDay();

                if ($tglKembali->greaterThan($tglRencana)) {

                    $hariTerlambat = $tglRencana->diffInDays(
                        $tglKembali
                    );

                    $dendaKeterlambatan =
                        $hariTerlambat * 1000;
                }
            }

            //DENDA KERUSAKAN
            $dendaKerusakan = 0;

            $peminjaman->load('detailPinjam');

            foreach ($peminjaman->detailPinjam as $detail) {

                $alat = Alat::find($detail->alat_id);

                if (!$alat) {
                    continue;
                }

                $kondisi = strtolower(
                    trim($request->kondisi_kembali)
                );

                $jumlah = (int) $detail->jumlah;

                if ($kondisi === 'rusak ringan') {

                    $dendaKerusakan +=
                        (int) $alat->denda_ringan * $jumlah;

                } elseif ($kondisi === 'rusak berat') {

                    $dendaKerusakan +=
                        (int) $alat->denda_berat * $jumlah;
                }
            }

            //TOTAL DENDA BARU
            $totalDenda =
                $dendaKeterlambatan +
                $dendaKerusakan;


            $pengembalian->update([
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
            ]);


            return response()->json([
                'message' =>
                    'Data pengembalian berhasil diperbarui.',
                'data' => $pengembalian->load([
                    'peminjaman.user',
                    'petugas'
                ])
            ]);


        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    //Menghapus/membatalkan data pengembalian
    public function destroy(
        Pengembalian $pengembalian
    ): JsonResponse {

        try {

            DB::transaction(function () use ($pengembalian) {

                $peminjaman = Peminjaman::with(
                    'detailPinjam'
                )
                    ->lockForUpdate()
                    ->findOrFail(
                        $pengembalian->peminjaman_id
                    );

                //TARIK KEMBALI STOK
                foreach ($peminjaman->detailPinjam as $detail) {

                    $alat = Alat::lockForUpdate()
                        ->findOrFail(
                            $detail->alat_id
                        );

                    if ($alat->stok < $detail->jumlah) {

                        throw new Exception(
                            "Gagal membatalkan pengembalian. Stok alat '{$alat->nama_alat}' saat ini tidak mencukupi untuk ditarik kembali."
                        );
                    }

                    $alat->decrement(
                        'stok',
                        $detail->jumlah
                    );
                }

                //KEMBALIKAN STATUS
                $peminjaman->update([
                    'status' => 'dipinjam'
                ]);

                //LOG AKTIVITAS
                if (auth()->user()) {

                    auth()->user()->logAktivitas()?->create([
                        'aktivitas' =>
                            "Membatalkan pengembalian ID: #{$pengembalian->id}"
                    ]);
                }

                //HAPUS DATA
                $pengembalian->delete();
            });


            return response()->json([
                'message' =>
                    'Data pengembalian berhasil dihapus. Stok dan status peminjaman telah dikembalikan ke kondisi semula.'
            ]);


        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}