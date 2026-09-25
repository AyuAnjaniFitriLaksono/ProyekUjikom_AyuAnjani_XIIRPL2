<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // =========================================================
    // DASHBOARD ADMIN
    // =========================================================

    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('logs'));
    }


    // =========================================================
    // PROFIL ADMIN
    // =========================================================

    // Menampilkan profil admin yang sedang login
    public function profile()
    {
        $user = auth()->user();

        return view('admin.profile', compact('user'));
    }

    // Mengupload foto profil admin
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

            // Pastikan folder tersedia
            $folder = public_path('storage/profil');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Ambil file foto
            $file = $request->file('foto_profile');

            // Buat nama file unik
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan file
            $file->move($folder, $filename);

            // Simpan lokasi foto ke database
            $user->foto_profile = 'storage/profil/' . $filename;
            $user->save();
        }

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    // Mengubah data profil admin
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }


    // =========================================================
    // KELOLA LOG AKTIVITAS
    // =========================================================

    // Menampilkan daftar log aktivitas
    public function indexLogAktivitas(Request $request)
    {
        $search = $request->input('search');

        $logs = LogAktivitas::with('user')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {

                    // Cari berdasarkan aktivitas
                    $q->where(
                        'aktivitas',
                        'like',
                        "%{$search}%"
                    )

                    // Cari berdasarkan nama pengguna
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.logaktivitas.index',
            compact('logs', 'search')
        );
    }


    // =========================================================
    // CRUD ALAT
    // =========================================================

    // 1. Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query
                    ->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where(
                            'nama_kategori',
                            'like',
                            "%{$search}%"
                        );
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.alat.index',
            compact('alats', 'search')
        );
    }

    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();

        return view(
            'admin.alat.create',
            compact('kategoris')
        );
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(
                public_path('storage/alat'),
                $filename
            );

            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat = Alat::create($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $alat->nama_alat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with(
                'success',
                'Data alat berhasil ditambahkan.'
            );
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();

        return view(
            'admin.alat.edit',
            compact('alat', 'kategoris')
        );
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle update gambar jika ada file baru
        if ($request->hasFile('gambar')) {

            // Hapus gambar lama jika ada
            if (
                $alat->gambar &&
                file_exists(public_path($alat->gambar))
            ) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(
                public_path('storage/alat'),
                $filename
            );

            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui alat: ' . $alat->nama_alat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with(
                'success',
                'Data alat berhasil diperbarui.'
            );
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        $namaAlat = $alat->nama_alat;

        // Hapus file gambar fisik jika ada
        if (
            $alat->gambar &&
            file_exists(public_path($alat->gambar))
        ) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus alat: ' . $namaAlat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with(
                'success',
                'Data alat berhasil dihapus.'
            );
    }


    // =========================================================
    // CRUD USER
    // =========================================================

    // 1. Menampilkan daftar user
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.user.index',
            compact('users', 'search')
        );
    }

    // 2. Menampilkan form tambah user
    public function createUser()
    {
        return view('admin.user.create');
    }

    // 3. Menyimpan user baru ke database
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Data user
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        // Upload foto profil jika ada
        if ($request->hasFile('foto_profile')) {

            // Folder penyimpanan
            $folder = public_path('storage/profil');

            // Buat folder jika belum ada
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Ambil file
            $file = $request->file('foto_profile');

            // Buat nama file unik
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan file
            $file->move($folder, $filename);

            // Simpan lokasi foto ke database
            $data['foto_profile'] = 'storage/profil/' . $filename;
        }

        // Simpan user
        $user = User::create($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan user baru: ' . $user->name,
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }

    // 4. Menampilkan form edit user
    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view(
            'admin.user.edit',
            compact('user')
        );
    }

    // 5. Memperbarui data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
            'foto_profile' => 'nullable|file|extensions:jpg,jpeg,png',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload foto profil jika memilih foto baru
        if ($request->hasFile('foto_profile')) {

            // Hapus foto lama jika ada
            if (
                $user->foto_profile &&
                file_exists(public_path($user->foto_profile))
            ) {
                unlink(public_path($user->foto_profile));
            }

            // Pastikan folder tersedia
            $folder = public_path('storage/profil');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Ambil file foto baru
            $file = $request->file('foto_profile');

            // Buat nama file unik
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan foto
            $file->move($folder, $filename);

            // Simpan lokasi foto ke database
            $data['foto_profile'] = 'storage/profil/' . $filename;
        }

        // Update data user
        $user->update($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui user: ' . $user->name,
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with(
                'success',
                'Data user berhasil diperbarui.'
            );
    }

    // 6. Menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        $namaUser = $user->name;

        $user->delete();

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus user: ' . $namaUser,
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }


    // =========================================================
    // CRUD KATEGORI
    // =========================================================

    // 1. Menampilkan daftar kategori
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategori = Kategori::when($search, function ($query, $search) {
            return $query->where(
                'nama_kategori',
                'like',
                "%{$search}%"
            );
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'admin.kategori.index',
            compact('kategori', 'search')
        );
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' =>
                'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' =>
                'Menambahkan kategori baru: ' .
                $kategori->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with(
                'success',
                'Kategori berhasil ditambahkan.'
            );
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view(
            'admin.kategori.edit',
            compact('kategori')
        );
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' =>
                'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' =>
                'Memperbarui kategori: ' .
                $kategori->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with(
                'success',
                'Kategori berhasil diperbarui.'
            );
    }

    // 6. Menghapus kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Cek apakah kategori masih digunakan oleh alat
        if ($kategori->alat()->count() > 0) {
            return redirect()
                ->route('admin.kategori.index')
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.'
                );
        }

        $namaKategori = $kategori->nama_kategori;

        $kategori->delete();

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' =>
                'Menghapus kategori: ' .
                $namaKategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with(
                'success',
                'Kategori berhasil dihapus.'
            );
    }


    // =========================================================
    // CRUD PEMINJAMAN
    // =========================================================

    // 1. Menampilkan daftar peminjaman + SEARCH
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {

                    // Cari berdasarkan status peminjaman
                    $q->where(
                        'status',
                        'like',
                        "%{$search}%"
                    )

                    // Cari berdasarkan nama peminjam
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })

                    // Cari berdasarkan nama alat
                    ->orWhereHas(
                        'detailPinjams.alat',
                        function ($alat) use ($search) {
                            $alat->where(
                                'nama_alat',
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

        return view(
            'admin.peminjaman.index',
            compact('peminjamans', 'search')
        );
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where(
            'role',
            'peminjam'
        )->get();

        $alats = Alat::where(
            'stok',
            '>',
            0
        )->get();

        return view(
            'admin.peminjaman.create',
            compact('users', 'alats')
        );
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' =>
                'required|exists:users,id',

            'tgl_pinjam' =>
                'required|date',

            'tgl_kembali_plan' =>
                'required|date|after_or_equal:tgl_pinjam',

            'alat_id' =>
                'required|array',

            'alat_id.*' =>
                'exists:alat,id',

            'jumlah' =>
                'required|array',

            'jumlah.*' =>
                'integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' =>
                    $request->user_id,

                'tgl_pinjam' =>
                    $request->tgl_pinjam,

                'tgl_kembali_plan' =>
                    $request->tgl_kembali_plan,

                'status' =>
                    'diajukan',
            ]);

            // Simpan detail alat yang dipinjam
            foreach (
                $request->alat_id
                as $index => $alatId
            ) {

                $jumlahPinjam =
                    $request->jumlah[$index];

                $alat =
                    Alat::findOrFail($alatId);

                // Validasi stok
                if (
                    $alat->stok <
                    $jumlahPinjam
                ) {
                    throw new \Exception(
                        "Stok alat '{$alat->nama_alat}' tidak mencukupi."
                    );
                }

                DetailPinjam::create([
                    'peminjaman_id' =>
                        $peminjaman->id,

                    'alat_id' =>
                        $alatId,

                    'jumlah' =>
                        $jumlahPinjam,
                ]);
            }

            DB::commit();

            return redirect()
                ->route(
                    'admin.peminjaman.index'
                )
                ->with(
                    'success',
                    'Data peminjaman berhasil diajukan.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    // 4. Mengubah status peminjaman
    public function updateStatusPeminjaman(
        Request $request,
        $id
    ) {
        $request->validate([
            'status' =>
                'required|in:diajukan,dipinjam,selesai,telat',
        ]);

        $peminjaman =
            Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' =>
                $request->status,
        ]);

        return redirect()
            ->route(
                'admin.peminjaman.index'
            )
            ->with(
                'success',
                'Status peminjaman berhasil diperbarui.'
            );
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman =
            Peminjaman::findOrFail($id);

        $peminjaman
            ->detailPinjams()
            ->delete();

        $peminjaman->delete();

        return redirect()
            ->route(
                'admin.peminjaman.index'
            )
            ->with(
                'success',
                'Data peminjaman berhasil dihapus.'
            );
    }
}