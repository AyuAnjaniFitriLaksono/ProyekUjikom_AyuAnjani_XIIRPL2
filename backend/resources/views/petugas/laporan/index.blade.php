@extends('layouts.app')

@section('title', 'Laporan Peminjaman - Dashboard Petugas')

@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')

@section('content')

<div class="space-y-6">

{{-- Header halaman --}}
<div>
    <h1 class="text-2xl font-bold text-gray-800">
        Laporan Peminjaman
    </h1>

    <p class="text-sm text-gray-500 mt-1">
        Lihat, filter, dan cetak rekap data peminjaman serta pengembalian alat.
    </p>
</div>

{{-- Pesan sukses --}}
@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Pesan error --}}
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Filter Laporan --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <div>
            <h3 class="text-lg font-bold text-gray-800">
                Filter Laporan
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Gunakan filter untuk menampilkan data laporan sesuai kebutuhan.
            </p>
        </div>

    </div>

    <form
        action="{{ route('petugas.laporan.index') }}"
        method="GET"
        class="p-5"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Status --}}
            <div>

                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Status Peminjaman
                </label>

                <select
                    name="status"
                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                    <option value="">Semua Status</option>

                    <option
                        value="diajukan"
                        {{ request('status') == 'diajukan' ? 'selected' : '' }}
                    >
                        Diajukan
                    </option>

                    <option
                        value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}
                    >
                        Dipinjam
                    </option>

                    <option
                        value="dikembalikan"
                        {{ request('status') == 'dikembalikan' ? 'selected' : '' }}
                    >
                        Dikembalikan
                    </option>

                    <option
                        value="telat"
                        {{ request('status') == 'telat' ? 'selected' : '' }}
                    >
                        Telat
                    </option>

                </select>

            </div>

            {{-- Dari tanggal --}}
            <div>

                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Dari Tanggal (Pinjam)
                </label>

                <input
                    type="date"
                    name="dari_tanggal"
                    value="{{ request('dari_tanggal') }}"
                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >

            </div>

            {{-- Sampai tanggal --}}
            <div>

                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Sampai Tanggal (Pinjam)
                </label>

                <input
                    type="date"
                    name="sampai_tanggal"
                    value="{{ request('sampai_tanggal') }}"
                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >

            </div>

            {{-- Tombol filter --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 text-sm font-semibold rounded-lg transition shadow-sm"
                >
                    Filter
                </button>

                <a
                    href="{{ route('petugas.laporan.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 text-sm font-semibold rounded-lg transition flex items-center justify-center"
                >
                    Reset
                </a>

            </div>

        </div>

    </form>

</div>

{{-- Hasil Laporan --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header hasil --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Hasil Rekap Laporan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Data peminjaman dan pengembalian berdasarkan filter yang dipilih.
                </p>
            </div>

            <a
                href="{{ route('petugas.laporan.cetak', request()->all()) }}"
                target="_blank"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 text-sm font-semibold rounded-lg transition shadow-sm flex items-center justify-center"
            >
                Cetak / Print Laporan
            </a>

        </div>

    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-gray-800 text-white text-sm uppercase">

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        No
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        Peminjam
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        Tanggal Pinjam
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        Rencana Kembali
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold text-center whitespace-nowrap">
                        Status
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold">
                        Detail Alat
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        Denda
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($laporans as $index => $item)

                    <tr class="hover:bg-gray-50 transition align-top">

                        {{-- No --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap text-gray-600">
                            {{ $index + 1 }}
                        </td>

                        {{-- Peminjam --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap">

                            <div class="font-semibold text-gray-900">
                                {{ $item->user->name ?? '-' }}
                            </div>

                            @if($item->user)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $item->user->email ?? '-' }}
                                </div>
                            @endif

                        </td>

                        {{-- Tanggal Pinjam --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap">

                            @if($item->tgl_pinjam)
                                {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d-m-Y') }}
                            @else
                                -
                            @endif

                        </td>

                        {{-- Rencana Kembali --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap">

                            @if($item->tgl_kembali_plan)
                                {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d-m-Y') }}
                            @else
                                -
                            @endif

                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-4 border-b border-gray-200 text-center whitespace-nowrap">

                            @if($item->status === 'diajukan')

                                <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded">
                                    Diajukan
                                </span>

                            @elseif($item->status === 'dipinjam')

                                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">
                                    Dipinjam
                                </span>

                            @elseif($item->status === 'dikembalikan')

                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded">
                                    Dikembalikan
                                </span>

                            @elseif($item->status === 'telat')

                                <span class="text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded">
                                    Telat
                                </span>

                            @else

                                <span class="text-xs font-semibold text-gray-700 bg-gray-50 px-2.5 py-1 rounded">
                                    {{ ucfirst($item->status) }}
                                </span>

                            @endif

                        </td>

                        {{-- Detail Alat --}}
                        <td class="py-4 px-4 border-b border-gray-200">

                            @if($item->detailPinjams && $item->detailPinjams->count())

                                <div class="space-y-2">

                                    @foreach($item->detailPinjams as $detail)

                                        <div class="flex items-start gap-2">

                                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>

                                            <div>

                                                <div class="font-semibold text-gray-800">
                                                    {{ $detail->alat->nama_alat ?? '-' }}
                                                </div>

                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    Jumlah: {{ $detail->jumlah }}
                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <span class="text-gray-400 text-xs">
                                    Tidak ada detail alat
                                </span>

                            @endif

                        </td>

                        {{-- Denda --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap">

                            <span class="font-semibold text-gray-800">
                                Rp {{ number_format($item->pengembalian->denda ?? 0, 0, ',', '.') }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="py-10 px-4 text-center text-gray-500"
                        >
                            Tidak ada data laporan yang sesuai dengan filter.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

@endsection
