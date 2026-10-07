<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dashboard')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ========================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================================= --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex flex-shrink-0">

        {{-- JUDUL PANEL --}}
        <div class="h-[72px] px-5 flex items-center border-b border-gray-800">

            <div class="text-xl font-bold tracking-wide">

                @if(auth()->user()->role === 'admin')
                    PANEL ADMIN

                @elseif(auth()->user()->role === 'petugas')
                    PANEL PETUGAS

                @elseif(auth()->user()->role === 'peminjam')
                    PANEL PEMINJAM
                @endif

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- MENU --}}
        {{-- ===================================================== --}}
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">

            {{-- ==================== ADMIN ==================== --}}
            @if(auth()->user()->role === 'admin')

                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Dashboard
                </a>


                {{-- Kelola User --}}
                <a
                    href="{{ route('admin.user.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.user.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Kelola User
                </a>


                {{-- Kelola Kategori --}}
                <a
                    href="{{ route('admin.kategori.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.kategori.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Kelola Kategori
                </a>


                {{-- Kelola Alat --}}
                <a
                    href="{{ route('admin.alat.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.alat.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Kelola Alat
                </a>


                {{-- Kelola Peminjaman --}}
                <a
                    href="{{ route('admin.peminjaman.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.peminjaman.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Kelola Peminjaman
                </a>


                {{-- Kelola Pengembalian --}}
                <a
                    href="{{ route('admin.pengembalian.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.pengembalian.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Kelola Pengembalian
                </a>


                {{-- Log Aktivitas --}}
                <a
                    href="{{ route('admin.logaktivitas.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.logaktivitas.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Log Aktivitas
                </a>

            @endif


            {{-- ==================== PETUGAS ==================== --}}
            @if(auth()->user()->role === 'petugas')

                {{-- Persetujuan Peminjaman --}}
                <a
                    href="{{ route('petugas.peminjaman.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('petugas.peminjaman.*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Persetujuan Peminjaman
                </a>


                {{-- Pemantauan Pengembalian --}}
                <a
                    href="{{ url('/petugas/pengembalian') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->is('petugas/pengembalian*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Pemantauan Pengembalian
                </a>


                {{-- Cetak Laporan --}}
                <a
                    href="{{ url('/petugas/laporan') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->is('petugas/laporan*')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Cetak Laporan
                </a>

            @endif


            {{-- ==================== PEMINJAM ==================== --}}
            @if(auth()->user()->role === 'peminjam')

                {{-- Katalog Alat --}}
                <a
                    href="{{ route('peminjam.katalog') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('peminjam.katalog')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Katalog Alat
                </a>


                {{-- Riwayat Peminjaman --}}
                <a
                    href="{{ route('peminjam.riwayat') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('peminjam.riwayat')
                        ? 'bg-gray-800 text-white font-semibold shadow-sm'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                >
                    Riwayat Peminjaman
                </a>

            @endif

        </nav>


        {{-- ===================================================== --}}
        {{-- PROFIL USER --}}
        {{-- ===================================================== --}}
        <div class="px-4 py-4 border-t border-gray-800">

            @if(auth()->user()->role === 'admin')

                <a
                    href="{{ route('admin.profile') }}"
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition"
                >

            @elseif(auth()->user()->role === 'petugas')

                <a
                    href="{{ route('petugas.profile') }}"
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition"
                >

            @elseif(auth()->user()->role === 'peminjam')

                <a
                    href="{{ route('peminjam.profile') }}"
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition"
                >

            @endif


                {{-- FOTO PROFIL --}}
                @if(auth()->user()->foto_profile)

                    <img
                        src="{{ asset(auth()->user()->foto_profile) }}"
                        alt="Foto Profil"
                        class="w-10 h-10 rounded-full object-cover border border-gray-600 flex-shrink-0"
                    >

                @else

                    <div class="w-10 h-10 rounded-full bg-gray-700 text-white flex items-center justify-center font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                @endif


                {{-- NAMA DAN ROLE --}}
                <div class="min-w-0">

                    <div class="text-sm font-semibold text-white truncate">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-gray-400 capitalize mt-0.5">
                        {{ auth()->user()->role }}
                    </div>

                </div>

            </a>

        </div>

    </aside>


    {{-- ========================================================= --}}
    {{-- KONTEN UTAMA --}}
    {{-- ========================================================= --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">


        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}
        <header class="bg-white border-b border-gray-200 shadow-sm min-h-[72px] flex items-center justify-between px-6 z-10 flex-shrink-0">

            {{-- JUDUL HALAMAN --}}
            <div class="flex items-center min-h-[72px] min-w-0">

                <h1 class="text-xl font-semibold text-gray-800 leading-tight truncate">
                    @yield('header-title', 'Dashboard')
                </h1>

            </div>


            {{-- LOGOUT --}}
            <form
                action="{{ route('logout') }}"
                method="POST"
                class="flex-shrink-0 ml-6"
            >

                @csrf

                <button
                    type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm"
                >
                    Logout
                </button>

            </form>

        </header>


        {{-- ===================================================== --}}
        {{-- CONTENT --}}
        {{-- ===================================================== --}}
        <main class="flex-1 overflow-y-auto p-6">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>