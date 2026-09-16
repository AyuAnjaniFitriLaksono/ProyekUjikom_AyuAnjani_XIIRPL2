@extends('layouts.app')

@section('title', 'Kelola Pengembalian')

@section('header-title', 'Kelola Pengembalian')

@section('content')

<div class="container mx-auto">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Data Pengembalian
            </h2>

            <p class="text-gray-500 text-sm mt-1">
                Kelola data pengembalian alat
            </p>
        </div>

    </div>


    {{-- PESAN BERHASIL --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif


    {{-- PESAN ERROR --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif


    {{-- SEARCH --}}
    <div class="bg-white rounded-lg shadow p-4 mb-5">

        <form
            method="GET"
            action="{{ route('admin.pengembalian.index') }}"
            class="flex gap-2"
        >

            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari nama peminjam, kondisi, atau denda..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >

            <button
                type="submit"
                class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg"
            >
                Cari
            </button>

            @if($search ?? false)
                <a
                    href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-5 py-2 rounded-lg"
                >
                    Reset
                </a>
            @endif

        </form>

    </div>


    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-800 text-white">

                    <tr>

                        <th class="px-4 py-3 text-left">
                            No
                        </th>

                        <th class="px-4 py-3 text-left">
                            Peminjam
                        </th>

                        <th class="px-4 py-3 text-left">
                            Tanggal Kembali
                        </th>

                        <th class="px-4 py-3 text-left">
                            Kondisi
                        </th>

                        <th class="px-4 py-3 text-left">
                            Total Denda
                        </th>

                        <th class="px-4 py-3 text-left">
                            Petugas
                        </th>

                        <th class="px-4 py-3 text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($pengembalians as $pengembalian)

                        <tr class="border-b hover:bg-gray-50">

                            {{-- NO --}}
                            <td class="px-4 py-3">
                                {{ $pengembalians->firstItem() + $loop->index }}
                            </td>


                            {{-- PEMINJAM --}}
                            <td class="px-4 py-3 font-medium text-gray-800">

                                {{ $pengembalian->peminjaman->user->name ?? '-' }}

                            </td>


                            {{-- TANGGAL KEMBALI --}}
                            <td class="px-4 py-3">

                                {{ $pengembalian->tgl_kembali?->format('Y-m-d') ?? '-' }}

                            </td>


                            {{-- KONDISI --}}
                            <td class="px-4 py-3">

                                @if($pengembalian->kondisi_kembali)

                                    <span class="px-2 py-1 bg-gray-100 rounded text-sm">
                                        {{ $pengembalian->kondisi_kembali }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- DENDA --}}
                            <td class="px-4 py-3">

                                Rp {{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}

                            </td>


                            {{-- PETUGAS --}}
                            <td class="px-4 py-3">

                                {{ $pengembalian->petugas->name ?? '-' }}

                            </td>


                            {{-- AKSI --}}
                            <td class="px-4 py-3">

                                <div class="flex justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.pengembalian.edit', $pengembalian->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm"
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
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
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
                                colspan="7"
                                class="px-4 py-10 text-center text-gray-500"
                            >
                                Belum ada data pengembalian.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- PAGINATION --}}
    <div class="mt-5">

        {{ $pengembalians->links() }}

    </div>

</div>

@endsection