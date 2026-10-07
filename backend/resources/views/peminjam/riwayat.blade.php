@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header-title', 'Riwayat Peminjaman')

@section('content')

<div class="space-y-6">

{{-- Header halaman --}}
<div>
    <h1 class="text-2xl font-bold text-gray-800">
        Riwayat Peminjaman
    </h1>

    <p class="text-sm text-gray-500 mt-1">
        Menampilkan seluruh riwayat pengajuan dan peminjaman alat yang pernah dilakukan.
    </p>
</div>

{{-- Notifikasi --}}
@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Card riwayat --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header card --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50">

        <h3 class="text-lg font-bold text-gray-800">
            Daftar Pengajuan & Peminjaman Saya
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Periksa status, tanggal peminjaman, dan alat yang pernah kamu ajukan.
        </p>

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
                        Tanggal Pinjam
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                        Rencana Kembali
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold">
                        Daftar Alat
                    </th>

                    <th class="py-4 px-4 border-b border-gray-700 font-semibold text-center whitespace-nowrap">
                        Status
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

                        {{-- Daftar Alat --}}
                        <td class="py-4 px-4 border-b border-gray-200">

                            @if($item->detailPinjams && $item->detailPinjams->count())

                                <div class="space-y-2">

                                    @foreach($item->detailPinjams as $detail)

                                        <div class="flex items-start gap-2">

                                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>

                                            <div>

                                                <div class="font-semibold text-gray-800">
                                                    {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                                </div>

                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    Jumlah: {{ $detail->jumlah }} unit
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

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="py-10 px-4 text-center text-gray-500"
                        >
                            Belum ada riwayat peminjaman.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

@endsection
