@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admin')
@section('header-title', 'Manajemen Data Alat')

@section('content')

    {{-- ====================================================== --}}
    {{-- NOTIFIKASI --}}
    {{-- ====================================================== --}}

    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- ====================================================== --}}
    {{-- CARD UTAMA --}}
    {{-- ====================================================== --}}

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">


        {{-- ================================================== --}}
        {{-- HEADER CARD --}}
        {{-- ================================================== --}}

        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">


                {{-- JUDUL --}}
                <div class="shrink-0">

                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Alat Laboratorium
                    </h3>

                </div>


                {{-- SEARCH + TAMBAH --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">


                    {{-- SEARCH --}}
                    <form
                        action="{{ route('admin.alat.index') }}"
                        method="GET"
                        class="flex w-full sm:w-auto"
                    >

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama alat, kategori..."
                            class="w-full sm:w-64 lg:w-72 h-10 px-3 text-sm border border-gray-300 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                        <button
                            type="submit"
                            class="h-10 bg-gray-800 hover:bg-gray-900 text-white px-4 text-sm font-semibold rounded-r-lg transition"
                        >
                            Cari
                        </button>


                        @if(request('search'))

                            <a
                                href="{{ route('admin.alat.index') }}"
                                class="ml-2 h-10 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 text-sm font-medium rounded-lg flex items-center justify-center transition"
                            >
                                Reset
                            </a>

                        @endif

                    </form>


                    {{-- TAMBAH ALAT --}}
                    <a
                        href="{{ route('admin.alat.create') }}"
                        class="h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 rounded-lg transition whitespace-nowrap flex items-center justify-center"
                    >
                        + Tambah Alat
                    </a>

                </div>

            </div>

        </div>


        {{-- ================================================== --}}
        {{-- TABEL --}}
        {{-- ================================================== --}}

        <div class="overflow-x-auto">

            <table class="w-full table-fixed border-collapse">


                {{-- ================================================== --}}
                {{-- UKURAN KOLOM --}}
                {{-- ================================================== --}}

                <colgroup>

                    {{-- GAMBAR --}}
                    <col class="w-[10%]">

                    {{-- NAMA ALAT --}}
                    <col class="w-[25%]">

                    {{-- KATEGORI --}}
                    <col class="w-[25%]">

                    {{-- STOK --}}
                    <col class="w-[10%]">

                    {{-- KONDISI --}}
                    <col class="w-[15%]">

                    {{-- AKSI --}}
                    <col class="w-[15%]">

                </colgroup>


                {{-- ================================================== --}}
                {{-- HEADER TABEL --}}
                {{-- ================================================== --}}

                <thead>

                    <tr class="h-[46px] bg-gray-800 text-white">


                        {{-- GAMBAR --}}
                        <th
                            class="px-4 text-center align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Gambar
                        </th>


                        {{-- NAMA ALAT --}}
                        <th
                            class="px-4 text-left align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Nama Alat
                        </th>


                        {{-- KATEGORI --}}
                        <th
                            class="px-4 text-left align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Kategori
                        </th>


                        {{-- STOK --}}
                        <th
                            class="px-4 text-center align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Stok
                        </th>


                        {{-- KONDISI --}}
                        <th
                            class="px-4 text-center align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Kondisi
                        </th>


                        {{-- AKSI --}}
                        <th
                            class="px-4 text-center align-middle text-sm font-semibold uppercase tracking-wide border-b border-gray-700"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ================================================== --}}
                {{-- DATA --}}
                {{-- ================================================== --}}

                <tbody class="text-sm text-gray-700">

                    @forelse($alats as $alat)

                        <tr
                            class="h-[70px] border-b border-gray-200 hover:bg-gray-50 transition"
                        >


                            {{-- ====================================== --}}
                            {{-- GAMBAR --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-center align-middle"
                            >

                                @if($alat->gambar)

                                    <img
                                        src="{{ asset($alat->gambar) }}"
                                        alt="{{ $alat->nama_alat }}"
                                        class="w-12 h-12 object-cover rounded-lg border border-gray-200 mx-auto"
                                    >

                                @else

                                    <div class="flex justify-center">

                                        <span class="text-xs text-gray-400 italic">
                                            Tidak ada
                                        </span>

                                    </div>

                                @endif

                            </td>


                            {{-- ====================================== --}}
                            {{-- NAMA ALAT --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-left align-middle"
                            >

                                <span class="font-medium text-gray-900">
                                    {{ $alat->nama_alat }}
                                </span>

                            </td>


                            {{-- ====================================== --}}
                            {{-- KATEGORI --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-left align-middle"
                            >

                                <span class="text-gray-700">
                                    {{ $alat->kategori->nama_kategori ?? '-' }}
                                </span>

                            </td>


                            {{-- ====================================== --}}
                            {{-- STOK --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-center align-middle"
                            >

                                <span class="font-semibold text-gray-800">
                                    {{ $alat->stok }}
                                </span>

                            </td>


                            {{-- ====================================== --}}
                            {{-- KONDISI --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-center align-middle"
                            >

                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    @if(strtolower($alat->status_kondisi) == 'baik')
                                        bg-emerald-100 text-emerald-800
                                    @else
                                        bg-amber-100 text-amber-800
                                    @endif"
                                >
                                    {{ $alat->status_kondisi }}
                                </span>

                            </td>


                            {{-- ====================================== --}}
                            {{-- AKSI --}}
                            {{-- ====================================== --}}

                            <td
                                class="px-4 text-center align-middle"
                            >

                                <div class="flex items-center justify-center gap-2">


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.alat.edit', $alat->id) }}"
                                        class="h-8 min-w-[48px] px-3 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-xs font-semibold inline-flex items-center justify-center transition"
                                    >
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('admin.alat.destroy', $alat->id) }}"
                                        method="POST"
                                        class="m-0 p-0"
                                        onsubmit="return confirm('Yakin ingin menghapus alat ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="h-8 min-w-[52px] px-3 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs font-semibold inline-flex items-center justify-center transition"
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
                                colspan="6"
                                class="h-[70px] px-4 text-center align-middle text-gray-500"
                            >
                                Belum ada data alat.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ================================================== --}}
        {{-- PAGINATION --}}
        {{-- ================================================== --}}

        <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">

            {{ $alats->links() }}

        </div>

    </div>

@endsection