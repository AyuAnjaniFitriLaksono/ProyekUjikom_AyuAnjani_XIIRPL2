@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan & Proses Pengembalian Alat')

@section('content')

<div class="space-y-6">

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

{{-- Header halaman --}}
<div>
    <h1 class="text-2xl font-bold text-gray-800">
        Pemantauan Pengembalian
    </h1>

    <p class="text-sm text-gray-500 mt-1">
        Menampilkan seluruh peminjaman alat yang masih aktif dan menunggu proses pengembalian.
    </p>
</div>

{{-- Card utama --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header + Search --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Daftar Peminjaman Aktif
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Periksa kondisi alat dan proses pengembalian peminjaman.
                </p>
            </div>

            <form
                action="{{ route('petugas.pengembalian.index') }}"
                method="GET"
                class="flex w-full lg:w-auto"
            >
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama peminjam..."
                    class="w-full lg:w-64 px-4 py-2.5 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 text-sm font-medium rounded-r-lg transition"
                >
                    Cari
                </button>

                @if(request('search'))
                    <a
                        href="{{ route('petugas.pengembalian.index') }}"
                        class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 text-sm font-medium rounded-lg flex items-center justify-center transition"
                    >
                        Reset
                    </a>
                @endif
            </form>

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

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold text-center whitespace-nowrap">
                        Aksi Pengembalian
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($peminjamans as $index => $item)

                    <tr class="hover:bg-gray-50 transition align-top">

                        {{-- No --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap text-gray-600">
                            {{ $index + 1 }}
                        </td>

                        {{-- Peminjam --}}
                        <td class="py-4 px-4 border-b border-gray-200 whitespace-nowrap">

                            <div class="font-semibold text-gray-900">
                                {{ $item->user->name ?? 'User Dihapus' }}
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

                            @if($item->status === 'dipinjam')

                                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">
                                    Dipinjam
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
                                                    {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
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

                        {{-- Aksi Pengembalian --}}
                        <td class="py-4 px-4 border-b border-gray-200">

                            <form
                                action="{{ route('petugas.pengembalian.proses', $item->id) }}"
                                method="POST"
                                class="w-64 mx-auto bg-gray-50 p-4 rounded-lg border border-gray-200 text-left space-y-3"
                            >

                                @csrf

                                {{-- Kondisi Kembali --}}
                                <div>

                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Kondisi Kembali
                                    </label>

                                    <select
                                        name="kondisi_kembali"
                                        required
                                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>

                                </div>

                                {{-- Denda --}}
                                <div>

                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Denda (Rp)
                                    </label>

                                    <input
                                        type="number"
                                        name="denda"
                                        value="0"
                                        min="0"
                                        placeholder="0"
                                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >

                                </div>

                                {{-- Tombol --}}
                                <button
                                    type="submit"
                                    onclick="return confirm('Proses pengembalian alat ini?')"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm"
                                >
                                    Terima Pengembalian
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="py-10 px-4 text-center text-gray-500"
                        >
                            Tidak ada peminjaman yang sedang aktif saat ini.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

@endsection
