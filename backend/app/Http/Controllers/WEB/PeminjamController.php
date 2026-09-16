<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // =========================================================
    // PROFILE PEMINJAM
    // =========================================================

    public function profile()
    {
        $user = auth()->user();

        return view('peminjam.profile', compact('user'));
    }

    // =========================================================
    // UPDATE FOTO PROFIL
    // =========================================================

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

            // Buat folder jika belum ada
            $folder = public_path('storage/profil');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Simpan foto baru
            $file = $request->file('foto_profile');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(
                $folder,
                $filename
            );

            // Simpan path ke database
            $user->foto_profile = 'storage/profil/' . $filename;

            $user->save();
        }

        return redirect()
            ->route('peminjam.profile')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    // =========================================================
    // UPDATE DATA PROFIL
    // =========================================================

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

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

        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()
            ->route('peminjam.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // =========================================================
    // KATALOG ALAT
    // =========================================================

    public function katalogAlat()
    {
        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->get();

        return view('peminjam.katalog', compact('alats'));
    }

    // =========================================================
    // MENGAJUKAN PEMINJAMAN
    // =========================================================

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id'          => 'required|array',
            'jumlah'           => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            // Header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            // Detail peminjaman
            foreach ($request->alat_id as $index => $alatId) {

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $request->jumlah[$index],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('peminjam.riwayat')
                ->with(
                    'success',
                    'Pengajuan peminjaman berhasil dikirim.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    // =========================================================
    // RIWAYAT PEMINJAMAN
    // =========================================================

    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view(
            'peminjam.riwayat',
            compact('peminjamans')
        );
    }
}
