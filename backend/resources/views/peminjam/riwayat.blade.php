@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header-title', 'Riwayat Peminjaman')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Pengajuan & Peminjaman Saya</h3>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-600 text-xs uppercase">
                    <th class="p-3">No</th>
                    <th class="p-3">Tgl Pinjam</th>
                    <th class="p-3">Rencana Kembali</th>
                    <th class="p-3">Daftar Alat</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($peminjamans as $index => $item)
                    <tr>
                        <td class="p-3 font-medium">{{ $index + 1 }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y') }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d/m/Y') }}</td>
                        <td class="p-3">
                            <ul class="list-disc list-inside">
                                @foreach($item->detailPinjams as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }} ({{ $detail->jumlah }} unit)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="p-3">
                            @if($item->status === 'diajukan')
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 font-semibold">Diajukan</span>
                            @elseif($item->status === 'disetujui')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-semibold">Disetujui</span>
                            @elseif($item->status === 'dikembalikan')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-semibold">Selesai</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-semibold">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection