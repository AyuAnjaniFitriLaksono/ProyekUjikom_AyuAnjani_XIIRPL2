@extends('layouts.app')

@section('title', 'Proses Pengembalian')

@section('header-title', 'Proses Pengembalian')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-lg shadow p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Proses Pengembalian
            </h2>

            <p class="text-gray-500 text-sm mt-1">
                Catat pengembalian alat dan denda.
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


        {{-- ERROR SESSION --}}
        @if(session('error'))

            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5">
                {{ session('error') }}
            </div>

        @endif


        {{-- INFORMASI PEMINJAMAN --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6">

            <h3 class="font-bold text-gray-800 mb-4">
                Informasi Peminjaman
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>

                    <span class="text-gray-500">
                        Peminjam
                    </span>

                    <p class="font-semibold text-gray-800">
                        {{ $peminjaman->user->name ?? '-' }}
                    </p>

                </div>


                <div>

                    <span class="text-gray-500">
                        Status
                    </span>

                    <p class="font-semibold">

                        @if($peminjaman->status === 'telat')

                            <span class="text-red-600">
                                Telat
                            </span>

                        @else

                            <span class="text-blue-600">
                                Dipinjam
                            </span>

                        @endif

                    </p>

                </div>


                <div>

                    <span class="text-gray-500">
                        Tanggal Pinjam
                    </span>

                    <p class="font-semibold text-gray-800">
                        {{ $peminjaman->tgl_pinjam?->format('Y-m-d') ?? '-' }}
                    </p>

                </div>


                <div>

                    <span class="text-gray-500">
                        Jatuh Tempo
                    </span>

                    <p class="font-semibold text-gray-800">
                        {{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') ?? '-' }}
                    </p>

                </div>

            </div>

        </div>


        {{-- FORM --}}
        <form
            action="{{ route('admin.pengembalian.store') }}"
            method="POST"
        >

            @csrf


            <input
                type="hidden"
                name="peminjaman_id"
                value="{{ $peminjaman->id }}"
            >


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


            {{-- INFORMASI DENDA KETERLAMBATAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">

                <h3 class="font-bold text-gray-800 mb-2">
                    Denda Keterlambatan
                </h3>

                <p class="text-sm text-gray-600">
                    Tarif keterlambatan adalah
                    <strong>Rp1.000 per hari</strong>.
                </p>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <span class="text-sm text-gray-500">
                            Hari Terlambat
                        </span>

                        <p class="text-lg font-bold text-gray-800">
                            {{ $hariTerlambat }} hari
                        </p>

                    </div>


                    <div>

                        <span class="text-sm text-gray-500">
                            Denda Keterlambatan
                        </span>

                        <p class="text-lg font-bold text-red-600">
                            Rp {{ number_format($dendaKeterlambatan, 0, ',', '.') }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- KONDISI UMUM --}}
            <div class="mb-6">

                <label
                    for="kondisi_kembali"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Kondisi Umum Pengembalian
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
                        {{ old('kondisi_kembali') === 'baik' ? 'selected' : '' }}
                    >
                        Baik
                    </option>

                    <option
                        value="rusak ringan"
                        {{ old('kondisi_kembali') === 'rusak ringan' ? 'selected' : '' }}
                    >
                        Rusak Ringan
                    </option>

                    <option
                        value="rusak berat"
                        {{ old('kondisi_kembali') === 'rusak berat' ? 'selected' : '' }}
                    >
                        Rusak Berat
                    </option>

                </select>

            </div>


            {{-- DETAIL ALAT --}}
            <div class="mb-6">

                <h3 class="text-lg font-bold text-gray-800 mb-3">
                    Detail Kondisi Alat
                </h3>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">

                    <table class="w-full">

                        <thead class="bg-gray-800 text-white">

                            <tr class="bg-gray-800 text-white text-sm">

                                <th class="py-3 px-4 border-b border-gray-700">
                                    Alat
                                </th>

                                <th class="py-3 px-4 border-b border-gray-700">
                                    Jumlah
                                </th>

                                <th class="py-3 px-4 border-b border-gray-700">
                                    Kondisi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($peminjaman->detailPinjams as $index => $detail)

                                <tr class="border-b">

                                    {{-- ALAT --}}
                                    <td class="px-4 py-3 font-medium">

                                        {{ $detail->alat->nama_alat ?? '-' }}

                                        <input
                                            type="hidden"
                                            name="detail[{{ $index }}][alat_id]"
                                            value="{{ $detail->alat_id }}"
                                        >

                                    </td>


                                    {{-- JUMLAH --}}
                                    <td class="px-4 py-3 text-center">

                                        {{ $detail->jumlah }}

                                        <input
                                            type="hidden"
                                            name="detail[{{ $index }}][jumlah]"
                                            value="{{ $detail->jumlah }}"
                                        >

                                    </td>


                                    {{-- KONDISI --}}
                                    <td class="px-4 py-3">

                                        <select
                                            name="detail[{{ $index }}][kondisi]"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                            required
                                        >

                                            <option
                                                value="baik"
                                                {{ old("detail.$index.kondisi", 'baik') === 'baik' ? 'selected' : '' }}
                                            >
                                                Baik
                                            </option>

                                            <option
                                                value="rusak ringan"
                                                {{ old("detail.$index.kondisi") === 'rusak ringan' ? 'selected' : '' }}
                                            >
                                                Rusak Ringan
                                            </option>

                                            <option
                                                value="rusak berat"
                                                {{ old("detail.$index.kondisi") === 'rusak berat' ? 'selected' : '' }}
                                            >
                                                Rusak Berat
                                            </option>

                                        </select>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Pilih kondisi alat sesuai keadaan saat dikembalikan.
                    Jika terdapat kerusakan, masukkan nominal dendanya secara manual pada bagian denda kerusakan.
                </p>

            </div>


            {{-- DENDA KERUSAKAN --}}
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-5 mb-6">

                <h3 class="font-bold text-gray-800 mb-2">
                    Denda Kerusakan
                </h3>

                <p class="text-sm text-gray-600 mb-4">
                    Masukkan nominal denda kerusakan secara manual sesuai hasil pemeriksaan petugas.
                    Jika tidak ada kerusakan, isi dengan Rp0.
                </p>


                <label
                    for="denda_kerusakan"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Nominal Denda Kerusakan
                </label>


                <div class="flex items-center">

                    <span class="bg-gray-100 border border-gray-300 border-r-0 rounded-l-lg px-4 py-2 text-gray-700">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="denda_kerusakan"
                        id="denda_kerusakan"
                        class="w-full border border-gray-300 rounded-r-lg px-4 py-2"
                        value="{{ old('denda_kerusakan', 0) }}"
                        min="0"
                        step="1000"
                        required
                    >

                </div>


                <p class="text-xs text-gray-500 mt-2">
                    Contoh: jika denda kerusakan Rp50.000, masukkan angka 50000.
                </p>

            </div>


            {{-- RINGKASAN DENDA --}}
            <div class="bg-red-50 border border-red-200 rounded-lg p-5 mb-6">

                <h3 class="font-bold text-gray-800 mb-4">
                    Ringkasan Denda
                </h3>


                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">

                        <span>
                            Denda keterlambatan
                        </span>

                        <span class="font-medium">
                            Rp {{ number_format($dendaKeterlambatan, 0, ',', '.') }}
                        </span>

                    </div>


                    <div class="flex justify-between">

                        <span>
                            Denda kerusakan
                        </span>

                        <span class="font-medium">
                            Diisi manual oleh petugas
                        </span>

                    </div>


                    <div class="border-t border-red-200 pt-3">

                        <div class="flex justify-between font-bold text-lg">

                            <span>
                                Total Denda
                            </span>

                            <span class="text-red-600">
                                Dihitung saat disimpan
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PETUGAS --}}
            <div class="mb-6">

                <label
                    for="petugas_id"
                    class="block font-medium text-gray-700 mb-2"
                >
                    Petugas
                </label>

                {{-- 
                    Petugas otomatis mengikuti akun yang sedang login.
                    Tidak menggunakan foreach karena $petugas
                    adalah satu object User.
                --}}

                <input
                    type="text"
                    id="petugas_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-700"
                    value="{{ $petugas->name ?? auth()->user()->name }}"
                    readonly
                >

                <input
                    type="hidden"
                    name="petugas_id"
                    value="{{ $petugas->id ?? auth()->id() }}"
                >

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
