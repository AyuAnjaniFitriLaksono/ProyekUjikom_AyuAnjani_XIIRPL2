<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanObserver
{
    private function catatLog(string $pesan): void
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => $pesan,
            ]);
        }
    }

    public function created(Peminjaman $peminjaman): void
    {
        $namaPeminjam = $peminjaman->user ? $peminjaman->user->name : 'User';
        $this->catatLog("Peminjaman ({$namaPeminjam}) membuat peminjaman baru (ID: {$peminjaman->id})");
    }

    public function updated(Peminjaman $peminjaman): void
    {
        if ($peminjaman->isDirty('status')) {
            $this->catatLog("Status peminjaman (ID: {$peminjaman->id}) berubah menjadi '{$peminjaman->status}'");
        } else {
            if (!empty($peminjaman->getChanges())) {
                $this->catatLog("Memperbarui detail data peminjaman (ID: {$peminjaman->id})");
            }
        }
    }

    public function deleted(Peminjaman $peminjaman): void
    {
        $this->catatLog("Membatalkan/menghapus peminjaman peminjaman (ID: {$peminjaman->id})");
    }
}