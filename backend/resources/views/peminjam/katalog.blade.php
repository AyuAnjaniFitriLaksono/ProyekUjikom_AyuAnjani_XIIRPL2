@extends('layouts.app')

@section('title', 'Katalog Alat')
@section('header-title', 'Katalog Alat')

@section('content')

<div class="space-y-6">

{{-- Notifikasi sukses --}}
@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Notifikasi error --}}
@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Header halaman --}}
<div>
    <h1 class="text-2xl font-bold text-gray-800">
        Katalog Alat
    </h1>

    <p class="text-sm text-gray-500 mt-1">
        Pilih alat yang ingin dipinjam dan tentukan rencana tanggal pengembaliannya.
    </p>
</div>

<form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
    @csrf

    {{-- Pengajuan Peminjaman --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Pengajuan Peminjaman
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Tentukan tanggal rencana pengembalian sebelum memilih alat.
            </p>

        </div>

        <div class="p-5">

            <div class="max-w-md">

                <label
                    for="tgl_kembali_plan"
                    class="block text-sm font-semibold text-gray-700 mb-1.5"
                >
                    Rencana Tanggal Pengembalian
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="date"
                    name="tgl_kembali_plan"
                    id="tgl_kembali_plan"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    required
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >

                @error('tgl_kembali_plan')
                    <p class="text-red-500 text-xs mt-1.5">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>

    {{-- Katalog Alat --}}
    <div class="mt-6">

        <div class="mb-4">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Alat
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Pilih alat yang tersedia dan tentukan jumlah yang ingin dipinjam.
            </p>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse($alats as $index => $alat)

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col">

                    {{-- Informasi alat --}}
                    <div class="p-5 flex-1">

                        <div class="flex items-center justify-between gap-3 mb-3">

                            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">
                                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                            </span>

                            @if($alat->stok > 0)

                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded">
                                    Tersedia
                                </span>

                            @else

                                <span class="text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded">
                                    Habis
                                </span>

                            @endif

                        </div>

                        <h4 class="text-lg font-bold text-gray-900 mb-2">
                            {{ $alat->nama_alat }}
                        </h4>

                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            {{ $alat->deskripsi ?? 'Tidak ada deskripsi.' }}
                        </p>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">

                            <span class="text-xs text-gray-500">
                                Stok tersedia
                            </span>

                            <span class="text-sm font-bold text-gray-800">
                                {{ $alat->stok }}
                            </span>

                        </div>

                    </div>

                    {{-- Pilihan alat --}}
                    <div class="p-5 bg-gray-50 border-t border-gray-200 space-y-4">

                        {{-- Checkbox --}}
                        <div class="flex items-center gap-2">

                            <input
                                type="checkbox"
                                name="alat_id[]"
                                value="{{ $alat->id }}"
                                id="alat_{{ $alat->id }}"
                                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 toggle-input"
                                data-target="#jumlah_{{ $alat->id }}"
                                {{ $alat->stok <= 0 ? 'disabled' : '' }}
                            >

                            <label
                                for="alat_{{ $alat->id }}"
                                class="text-sm font-semibold text-gray-700 cursor-pointer {{ $alat->stok <= 0 ? 'text-gray-400 cursor-not-allowed' : '' }}"
                            >
                                {{ $alat->stok > 0 ? 'Pilih Alat Ini' : 'Stok Tidak Tersedia' }}
                            </label>

                        </div>

                        {{-- Jumlah --}}
                        <div>

                            <label
                                for="jumlah_{{ $alat->id }}"
                                class="block text-xs font-semibold text-gray-600 mb-1.5"
                            >
                                Jumlah Pinjam
                            </label>

                            <input
                                type="number"
                                name="jumlah[]"
                                id="jumlah_{{ $alat->id }}"
                                min="1"
                                max="{{ $alat->stok }}"
                                value="1"
                                disabled
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:opacity-50"
                            >

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full bg-white rounded-lg border border-gray-200 shadow-sm">

                    <div class="py-12 px-6 text-center">

                        <h3 class="text-lg font-semibold text-gray-700">
                            Tidak Ada Alat Tersedia
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Belum ada alat yang tersedia untuk dipinjam saat ini.
                        </p>

                    </div>

                </div>

            @endforelse

        </div>

    </div>

    {{-- Tombol Submit --}}
    @if($alats->count() > 0)

        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-5">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>

                    <p class="text-sm font-semibold text-gray-700">
                        Sudah memilih alat?
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        Pastikan jumlah alat dan tanggal pengembalian sudah sesuai.
                    </p>

                </div>

                <button
                    type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm"
                >
                    Kirim Pengajuan Peminjaman
                </button>

            </div>

        </div>

    @endif

</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const checkboxes = document.querySelectorAll('.toggle-input');

        checkboxes.forEach(checkbox => {

            checkbox.addEventListener('change', function () {

                const targetInput = document.querySelector(this.dataset.target);

                if (this.checked) {

                    targetInput.removeAttribute('disabled');
                    targetInput.classList.remove('bg-gray-100');

                } else {

                    targetInput.setAttribute('disabled', 'disabled');
                    targetInput.classList.add('bg-gray-100');

                }

            });

        });

    });
</script>

@endsection
