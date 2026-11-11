@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')

@section('content')

    {{-- ========================================================= --}}
    {{-- NOTIFIKASI --}}
    {{-- ========================================================= --}}

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- CARD UTAMA --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">


        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div class="p-5 border-b border-gray-200 bg-gray-50
                    flex flex-col md:flex-row
                    md:justify-between md:items-center
                    gap-4">

            {{-- JUDUL --}}
            <h3 class="text-lg font-bold text-gray-800 whitespace-nowrap">
                Daftar Transaksi Peminjaman
            </h3>


            {{-- SEARCH + TAMBAH --}}
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full md:w-auto">

                {{-- FORM SEARCH --}}
                <form
                    action="{{ route('admin.peminjaman.index') }}"
                    method="GET"
                    class="flex w-full md:w-80"
                >

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peminjam / status..."
                        class="flex-1 min-w-0 px-3 py-2 text-sm border border-gray-300 rounded-l-lg
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <button
                        type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white
                               px-4 py-2 text-sm font-semibold
                               rounded-r-lg transition whitespace-nowrap"
                    >
                        Cari
                    </button>

                    @if(request('search'))

                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400
                                   text-gray-700 px-3 py-2 text-sm rounded-lg
                                   flex items-center justify-center transition whitespace-nowrap"
                        >
                            Reset
                        </a>

                    @endif

                </form>


                {{-- TAMBAH PEMINJAMAN --}}
                <a
                    href="{{ route('admin.peminjaman.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white
                           text-sm font-semibold px-4 py-2 rounded-lg
                           transition whitespace-nowrap text-center"
                >
                    + Tambah Peminjaman
                </a>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- TABEL --}}
        {{-- ===================================================== --}}

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">


                {{-- ================================================= --}}
                {{-- HEADER TABEL --}}
                {{-- ================================================= --}}

                <thead>

                    <tr class="bg-gray-800 text-white text-sm uppercase">

                        <th class="py-4 px-4 border-b border-gray-700
                                   font-semibold whitespace-nowrap">
                            Peminjam
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700
                                   font-semibold whitespace-nowrap">
                            Alat yang Dipinjam
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700
                                   font-semibold whitespace-nowrap">
                            Tgl Pinjam / Rencana Kembali
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700
                                   font-semibold whitespace-nowrap">
                            Status
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700
                                   font-semibold whitespace-nowrap">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ================================================= --}}
                {{-- ISI TABEL --}}
                {{-- ================================================= --}}

                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjamans as $peminjaman)

                        <tr class="hover:bg-gray-50 transition">


                            {{-- ===================================== --}}
                            {{-- PEMINJAM --}}
                            {{-- ===================================== --}}

                            <td class="py-4 px-4 border-b border-gray-200
                                       align-middle font-medium text-gray-900">

                                {{ $peminjaman->user->name ?? 'User Dihapus' }}

                            </td>


                            {{-- ===================================== --}}
                            {{-- ALAT --}}
                            {{-- ===================================== --}}

                            <td class="py-4 px-4 border-b border-gray-200 align-middle">

                                <ul class="space-y-1">

                                    @forelse(($peminjaman->detailPinjams ?? collect()) as $detail)

                                        <li class="flex items-center gap-2">

                                            <span class="text-gray-700">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>

                                            <span class="text-xs bg-gray-100
                                                         text-gray-600 px-2 py-1
                                                         rounded-md whitespace-nowrap">
                                                {{ $detail->jumlah ?? 0 }} pcs
                                            </span>

                                        </li>

                                    @empty

                                        <li class="text-gray-400">
                                            Tidak ada alat
                                        </li>

                                    @endforelse

                                </ul>

                            </td>


                            {{-- ===================================== --}}
                            {{-- TANGGAL --}}
                            {{-- ===================================== --}}

                            <td class="py-4 px-4 border-b border-gray-200
                                       align-middle text-sm text-gray-600">

                                <div class="space-y-1">

                                    <div class="whitespace-nowrap">
                                        <span class="text-gray-500">
                                            Pinjam:
                                        </span>

                                        <span class="font-medium text-gray-700">
                                            {{ $peminjaman->tgl_pinjam ? \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') : '-' }}
                                    </div>

                                    <div class="whitespace-nowrap">
                                        <span class="text-gray-500">
                                            Rencana:
                                        </span>

                                        <span class="font-semibold text-gray-800">
                                            {{ $peminjaman->tgl_kembali_plan ? \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d-m-Y') : '-' }}
                                        </span>
                                    </div>

                                </div>

                            </td>


                            {{-- ===================================== --}}
                            {{-- STATUS --}}
                            {{-- ===================================== --}}

                            <td class="py-4 px-4 border-b border-gray-200 align-middle">

                                @if($peminjaman->status == 'diajukan')

                                    <span class="inline-block px-3 py-1
                                                 text-xs font-semibold
                                                 rounded-full
                                                 bg-yellow-100 text-yellow-800
                                                 whitespace-nowrap">
                                        Diajukan
                                    </span>

                                @elseif($peminjaman->status == 'dipinjam')

                                    <span class="inline-block px-3 py-1
                                                 text-xs font-semibold
                                                 rounded-full
                                                 bg-blue-100 text-blue-800
                                                 whitespace-nowrap">
                                        Dipinjam
                                    </span>

                                @elseif($peminjaman->status == 'dikembalikan')

                                    <span class="inline-block px-3 py-1
                                                 text-xs font-semibold
                                                 rounded-full
                                                 bg-emerald-100 text-emerald-800
                                                 whitespace-nowrap">
                                        Selesai
                                    </span>

                                @elseif($peminjaman->status == 'telat')

                                    <span class="inline-block px-3 py-1
                                                 text-xs font-semibold
                                                 rounded-full
                                                 bg-red-100 text-red-800
                                                 whitespace-nowrap">
                                        Telat
                                    </span>

                                @else

                                    <span class="inline-block px-3 py-1
                                                 text-xs font-semibold
                                                 rounded-full
                                                 bg-gray-100 text-gray-800
                                                 whitespace-nowrap">
                                        {{ ucfirst($peminjaman->status ?? '-') }}
                                    </span>

                                @endif

                            </td>


                            {{-- ===================================== --}}
                            {{-- AKSI --}}
                            {{-- ===================================== --}}

                            <td class="py-4 px-4 border-b border-gray-200 align-middle">

                                <div class="flex flex-col gap-2 min-w-[110px]">


                                    {{-- FORM UBAH STATUS --}}
                                    <form
                                        action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}"
                                        method="POST"
                                        class="w-full"
                                    >

                                        @csrf
                                        @method('PUT')

                                        <select
                                            name="status"
                                            onchange="this.form.submit()"
                                            class="w-full text-xs
                                                   border border-gray-300
                                                   rounded-lg px-2 py-2
                                                   bg-white
                                                   focus:outline-none
                                                   focus:ring-2
                                                   focus:ring-blue-500"
                                        >

                                            <option
                                                value="diajukan"
                                                {{ $peminjaman->status == 'diajukan' ? 'selected' : '' }}
                                            >
                                                Diajukan
                                            </option>

                                            <option
                                                value="dipinjam"
                                                {{ $peminjaman->status == 'dipinjam' ? 'selected' : '' }}
                                            >
                                                Dipinjam
                                            </option>

                                            <option
                                                value="dikembalikan"
                                                {{ $peminjaman->status == 'dikembalikan' ? 'selected' : '' }}
                                            >
                                                Selesai
                                            </option>

                                            <option
                                                value="telat"
                                                {{ $peminjaman->status == 'telat' ? 'selected' : '' }}
                                            >
                                                Telat
                                            </option>

                                        </select>

                                    </form>


                                    {{-- ================================= --}}
                                    {{-- TOMBOL HAPUS --}}
                                    {{-- HANYA JIKA SUDAH DIKEMBALIKAN --}}
                                    {{-- ================================= --}}

                                    @if($peminjaman->status == 'dikembalikan')

                                        <form
                                            action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')"
                                            class="w-full"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-full bg-red-500 hover:bg-red-600
                                                       text-white px-3 py-2
                                                       rounded-lg text-xs
                                                       font-semibold transition"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="py-8 px-4 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ===================================================== --}}
        {{-- PAGINATION --}}
        {{-- ===================================================== --}}

        <div class="p-4 border-t border-gray-200 bg-gray-50">

            {{ $peminjamans->links() }}

        </div>

    </div>

@endsection