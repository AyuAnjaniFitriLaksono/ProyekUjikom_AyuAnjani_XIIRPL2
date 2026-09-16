@extends('layouts.app')

@section('title', 'Tambah Pengembalian')

@section('header-title', 'Tambah Pengembalian')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                Tambah Pengembalian
            </h2>

            <p class="text-gray-500 text-sm mt-1">
                Masukkan data pengembalian alat
            </p>

        </div>


        {{-- ERROR VALIDASI --}}
        @if ($errors->any())

            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5">

                <strong>Terjadi kesalahan:</strong>

                <ul class="list-disc ml-5 mt-2">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM --}}
        <form
            action="{{ route('admin.pengembalian.store') }}"
            method="POST"
        >

            @csrf


            {{-- PEMINJAMAN --}}
            <div class="mb-5">

                <label
                    for="peminjaman_id"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Peminjaman
                </label>

                <select
                    name="peminjaman_id"
                    id="peminjaman_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    required
                >

                    <option value="">
                        -- Pilih Peminjaman --
                    </option>

                    @foreach ($peminjamans as $peminjaman)

                        <option
                            value="{{ $peminjaman->id }}"
                            {{ old('peminjaman_id') == $peminjaman->id ? 'selected' : '' }}
                        >

                            #{{ $peminjaman->id }}
                            -
                            {{ $peminjaman->user->name ?? '-' }}
                            -
                            {{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') ?? '-' }}
                            -
                            {{ ucfirst($peminjaman->status) }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- TANGGAL KEMBALI --}}
            <div class="mb-5">

                <label
                    for="tgl_kembali"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Tanggal Kembali
                </label>

                <input
                    type="date"
                    name="tgl_kembali"
                    id="tgl_kembali"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    value="{{ old('tgl_kembali', date('Y-m-d')) }}"
                    required
                >

            </div>


            {{-- KONDISI --}}
            <div class="mb-5">

                <label
                    for="kondisi_kembali"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Kondisi Kembali
                </label>

                <select
                    name="kondisi_kembali"
                    id="kondisi_kembali"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    required
                >

                    <option value="">
                        -- Pilih Kondisi --
                    </option>

                    <option
                        value="baik"
                        {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}
                    >
                        Baik
                    </option>

                    <option
                        value="rusak ringan"
                        {{ old('kondisi_kembali') == 'rusak ringan' ? 'selected' : '' }}
                    >
                        Rusak Ringan
                    </option>

                    <option
                        value="rusak berat"
                        {{ old('kondisi_kembali') == 'rusak berat' ? 'selected' : '' }}
                    >
                        Rusak Berat
                    </option>

                </select>

            </div>


            {{-- DENDA --}}
            <div class="mb-5">

                <label
                    for="denda"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Total Denda
                </label>

                <input
                    type="number"
                    name="denda"
                    id="denda"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    value="{{ old('denda', 0) }}"
                    min="0"
                    required
                >

            </div>


            {{-- PETUGAS --}}
            <div class="mb-6">

                <label
                    for="petugas_id"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Petugas
                </label>

                <select
                    name="petugas_id"
                    id="petugas_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    required
                >

                    <option value="">
                        -- Pilih Petugas --
                    </option>

                    @foreach ($petugas as $user)

                        <option
                            value="{{ $user->id }}"
                            {{ old('petugas_id') == $user->id ? 'selected' : '' }}
                        >
                            {{ $user->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BUTTON --}}
            <div class="flex gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg"
                >
                    Simpan Pengembalian
                </button>

                <a
                    href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection