@extends('layouts.app')

@section('title', 'Kelola Pengembalian')

@section('header-title', 'Kelola Pengembalian')

@section('content')

<div class="container mx-auto">

    {{-- ========================================================= --}}
    {{-- PESAN BERHASIL --}}
    {{-- ========================================================= --}}

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PESAN ERROR --}}
    {{-- ========================================================= --}}

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PEMINJAMAN AKTIF --}}
    {{-- ========================================================= --}}

    <div class="mb-8">

        {{-- JUDUL --}}
        <div class="mb-4">

            <h3 class="text-xl font-bold text-gray-800">
                Peminjaman Aktif
            </h3>

            <p class="text-gray-500 text-sm mt-1">
                Peminjaman yang sedang dipinjam atau sudah melewati batas pengembalian.
            </p>

        </div>


        {{-- CARD TABEL --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    {{-- HEADER TABEL --}}
                    <thead>

                        <tr class="bg-gray-800 text-white text-sm uppercase">

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                No
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Peminjam
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Alat
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Tanggal Pinjam
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Jatuh Tempo
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Status
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    {{-- ISI TABEL --}}
                    <tbody class="text-gray-700 text-sm">

                        @forelse($peminjamanAktif as $peminjaman)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- NO --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">
                                    {{ $loop->iteration }}
                                </td>


                                {{-- PEMINJAM --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle font-medium text-gray-900">

                                    {{ $peminjaman->user->name ?? '-' }}

                                </td>


                                {{-- ALAT --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    @forelse($peminjaman->detailPinjams as $detail)

                                        <div class="mb-1 last:mb-0">

                                            <span class="font-medium text-gray-800">
                                                {{ $detail->alat->nama_alat ?? '-' }}
                                            </span>

                                            <span class="text-gray-500 text-xs">
                                                ({{ $detail->jumlah }} unit)
                                            </span>

                                        </div>

                                    @empty

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endforelse

                                </td>


                                {{-- TANGGAL PINJAM --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle whitespace-nowrap">

                                    {{ $peminjaman->tgl_pinjam?->format('Y-m-d') ?? '-' }}

                                </td>


                                {{-- JATUH TEMPO --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle whitespace-nowrap">

                                    {{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') ?? '-' }}

                                </td>


                                {{-- STATUS --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    @if($peminjaman->status === 'telat')

                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            Telat
                                        </span>

                                    @else

                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            Dipinjam
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    <a
                                        href="{{ route('admin.pengembalian.create', $peminjaman->id) }}"
                                        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition whitespace-nowrap"
                                    >
                                        Proses Pengembalian
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="py-8 px-4 text-center text-gray-500"
                                >
                                    Tidak ada peminjaman aktif.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- DATA PENGEMBALIAN --}}
    {{-- ========================================================= --}}

    <div class="mb-4">

        {{-- JUDUL --}}
        <div class="mb-4">

            <h3 class="text-xl font-bold text-gray-800">
                Data Pengembalian
            </h3>

            <p class="text-gray-500 text-sm mt-1">
                Daftar peminjaman yang sudah dikembalikan.
            </p>

        </div>


        {{-- ========================================================= --}}
        {{-- SEARCH --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-5">

            <form
                method="GET"
                action="{{ route('admin.pengembalian.index') }}"
                class="flex gap-2"
            >

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama peminjam, alat, kondisi, atau denda..."
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-semibold transition"
                >
                    Cari
                </button>

                @if($search ?? false)

                    <a
                        href="{{ route('admin.pengembalian.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold flex items-center transition"
                    >
                        Reset
                    </a>

                @endif

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- TABEL DATA PENGEMBALIAN --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    {{-- HEADER TABEL --}}
                    <thead>

                        <tr class="bg-gray-800 text-white text-sm uppercase">

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                No
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Peminjam
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Alat
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Tanggal Kembali
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Kondisi
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Total Denda
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                                Petugas
                            </th>

                            <th class="py-3 px-4 border-b border-gray-700 font-semibold whitespace-nowrap text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    {{-- ISI TABEL --}}
                    <tbody class="text-gray-700 text-sm">

                        @forelse($pengembalians as $pengembalian)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- NO --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    {{ $pengembalians->firstItem() + $loop->index }}

                                </td>


                                {{-- PEMINJAM --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle font-medium text-gray-900">

                                    {{ $pengembalian->peminjaman->user->name ?? '-' }}

                                </td>


                                {{-- ALAT --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    @forelse($pengembalian->peminjaman->detailPinjams as $detail)

                                        <div class="mb-1 last:mb-0">

                                            <span class="font-medium text-gray-800">
                                                {{ $detail->alat->nama_alat ?? '-' }}
                                            </span>

                                            <span class="text-gray-500 text-xs">
                                                ({{ $detail->jumlah }} unit)
                                            </span>

                                        </div>

                                    @empty

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endforelse

                                </td>


                                {{-- TANGGAL KEMBALI --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle whitespace-nowrap">

                                    {{ $pengembalian->tgl_kembali?->format('Y-m-d') ?? '-' }}

                                </td>


                                {{-- KONDISI --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    @if($pengembalian->kondisi_kembali)

                                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                            {{ ucfirst($pengembalian->kondisi_kembali) }}
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- TOTAL DENDA --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle whitespace-nowrap">

                                    Rp {{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}

                                </td>


                                {{-- PETUGAS --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    {{ $pengembalian->petugas->name ?? '-' }}

                                </td>


                                {{-- AKSI --}}
                                <td class="py-3 px-4 border-b border-gray-200 align-middle">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('admin.pengembalian.edit', $pengembalian->id) }}"
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition"
                                        >
                                            Edit
                                        </a>


                                        {{-- HAPUS --}}
                                        <form
                                            action="{{ route('admin.pengembalian.destroy', $pengembalian->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data pengembalian ini?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="py-8 px-4 text-center text-gray-500"
                                >
                                    Belum ada data pengembalian.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}

        <div class="mt-5">

            {{ $pengembalians->links() }}

        </div>

    </div>

</div>

@endsection