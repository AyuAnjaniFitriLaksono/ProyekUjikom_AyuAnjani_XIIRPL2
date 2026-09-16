@extends('layouts.app')

@section('title', 'Katalog Alat')
@section('header-title', 'Katalog Alat')

@section('content')
<div class="space-y-6">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf

        {{-- Input Tanggal Rencana Kembali --}}
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Pengajuan Peminjaman</h3>
            <div class="max-w-md">
                <label for="tgl_kembali_plan" class="block text-sm font-medium text-gray-700 mb-1">
                    Rencana Tanggal Pengembalian <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="tgl_kembali_plan" 
                       id="tgl_kembali_plan" 
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali_plan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Daftar Alat / Katalog --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($alats as $index => $alat)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">
                                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Stok: <strong class="text-gray-800">{{ $alat->stok }}</strong>
                            </span>
                        </div>

                        <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $alat->nama_alat }}</h4>
                        <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                            {{ $alat->deskripsi ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    <div class="p-5 bg-gray-50 border-t border-gray-100 space-y-3">
                        {{-- Checkbox Pilih Alat --}}
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" 
                                   name="alat_id[]" 
                                   value="{{ $alat->id }}" 
                                   id="alat_{{ $alat->id }}"
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 toggle-input"
                                   data-target="#jumlah_{{ $alat->id }}">
                            <label for="alat_{{ $alat->id }}" class="text-sm font-medium text-gray-700 cursor-pointer">
                                Pilih Alat Ini
                            </label>
                        </div>

                        {{-- Input Jumlah Pinjam --}}
                        <div>
                            <label for="jumlah_{{ $alat->id }}" class="block text-xs font-medium text-gray-500 mb-1">
                                Jumlah
                            </label>
                            <input type="number" 
                                   name="jumlah[]" 
                                   id="jumlah_{{ $alat->id }}" 
                                   min="1" 
                                   max="{{ $alat->stok }}" 
                                   value="1" 
                                   disabled
                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-8 text-center rounded-lg border border-gray-200">
                    <p class="text-gray-500">Tidak ada alat yang tersedia untuk dipinjam saat ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Tombol Submit --}}
        @if($alats->count() > 0)
            <div class="mt-8 flex justify-end">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Kirim Pengajuan Peminjaman
                </button>
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