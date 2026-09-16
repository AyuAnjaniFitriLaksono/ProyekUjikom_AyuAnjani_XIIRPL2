@extends('layouts.app')

@section('title', 'Edit Pengembalian')

@section('header-title', 'Edit Pengembalian')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                Edit Pengembalian
            </h2>

            <p class="text-gray-500 text-sm mt-1">
                Perbarui data pengembalian alat
            </p>

        </div>


        {{-- ERROR --}}
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


        <form
            action="{{ route('admin.pengembalian.update', $pengembalian->id) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


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

                    @foreach ($peminjamans as $peminjaman)

                        <option
                            value="{{ $peminjaman->id }}"
                            {{ old('peminjaman_id', $pengembalian->peminjaman_id) == $peminjaman->id ? 'selected' : '' }}
                        >

                            #{{ $peminjaman->id }}
                            -
                            {{ $peminjaman->user->name ?? '-' }}
                            -
                            {{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') ?? '-' }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- TANGGAL --}}
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
                    value="{{ old('tgl_kembali', $pengembalian->tgl_kembali?->format('Y-m-d')) }}"
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

                    <option
                        value="baik"
                        {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) == 'baik' ? 'selected' : '' }}
                    >
                        Baik
                    </option>

                    <option
                        value="rusak ringan"
                        {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) == 'rusak ringan' ? 'selected' : '' }}
                    >
                        Rusak Ringan
                    </option>

                    <option
                        value="rusak berat"
                        {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) == 'rusak berat' ? 'selected' : '' }}
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
                    value="{{ old('denda', $pengembalian->denda) }}"
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

                    @foreach ($petugas as $user)

                        <option
                            value="{{ $user->id }}"
                            {{ old('petugas_id', $pengembalian->petugas_id) == $user->id ? 'selected' : '' }}
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
                    Simpan Perubahan
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