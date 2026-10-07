@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')

@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')

    {{-- ALERT SELAMAT DATANG --}}
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang,
        <strong class="font-semibold">
            {{ auth()->user()->name }}
        </strong>!

        Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">
            {{ auth()->user()->role }}
        </span>.
    </div>


    {{-- STATISTIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        {{-- TOTAL USER --}}
        <div class="bg-blue-500 text-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">
                        Total User
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ $totalUser }}
                    </p>
                </div>

                <div class="bg-white/20 rounded-full p-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </div>
            </div>
        </div>


        {{-- TOTAL KATEGORI --}}
        <div class="bg-purple-500 text-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">
                        Total Kategori
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ $totalKategori }}
                    </p>
                </div>

                <div class="bg-white/20 rounded-full p-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </div>
            </div>
        </div>


        {{-- TOTAL ALAT --}}
        <div class="bg-orange-500 text-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">
                        Total Alat
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ $totalAlat }}
                    </p>
                </div>

                <div class="bg-white/20 rounded-full p-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v11a2 2 0 002 2h10a2 2 0 002-2V8"
                        />
                    </svg>
                </div>
            </div>
        </div>


        {{-- TOTAL PEMINJAMAN --}}
        <div class="bg-emerald-500 text-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">
                        Total Peminjaman
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ $totalPeminjaman }}
                    </p>
                </div>

                <div class="bg-white/20 rounded-full p-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                </div>
            </div>
        </div>


        {{-- TOTAL PENGEMBALIAN --}}
        <div class="bg-red-500 text-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">
                        Total Pengembalian
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ $totalPengembalian }}
                    </p>
                </div>

                <div class="bg-white/20 rounded-full p-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- AKTIVITAS TERBARU --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- HEADER CARD --}}
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">

            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Log Aktivitas Terbaru
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Menampilkan aktivitas terbaru dalam sistem.
                </p>
            </div>

            <a
                href="{{ route('admin.logaktivitas.index') }}"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap"
            >
                Lihat Semua
            </a>

        </div>


        {{-- TABEL AKTIVITAS --}}
        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                {{-- HEADER TABEL --}}
                <thead>
                    <tr class="bg-slate-800 text-white">

                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wide border-b border-slate-700">
                            Waktu
                        </th>

                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wide border-b border-slate-700">
                            User
                        </th>

                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wide border-b border-slate-700">
                            Aktivitas
                        </th>

                    </tr>
                </thead>


                {{-- ISI TABEL --}}
                <tbody class="text-sm text-gray-700">

                    @forelse($logs as $log)

                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">

                            {{-- WAKTU --}}
                            <td class="py-3 px-4 text-left whitespace-nowrap align-middle">
                                {{ $log->created_at?->format('Y-m-d H:i:s') ?? '-' }}
                            </td>


                            {{-- USER --}}
                            <td class="py-3 px-4 text-left font-medium text-gray-800 align-middle whitespace-nowrap">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>


                            {{-- AKTIVITAS --}}
                            <td class="py-3 px-4 text-left align-middle">
                                {{ $log->aktivitas ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="3"
                                class="py-8 px-4 text-center text-gray-500"
                            >
                                Belum ada aktivitas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection