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

<!-- SIDEBAR -->
<aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">

    <!-- JUDUL PANEL -->
    <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">

        @if(auth()->user()->role === 'admin')

            PANEL ADMIN

        @elseif(auth()->user()->role === 'petugas')

            PANEL PETUGAS

        @elseif(auth()->user()->role === 'peminjam')

            PANEL PEMINJAM

        @endif

    </div>


    <!-- MENU -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">


        <!-- ==================== MENU ADMIN ==================== -->
        @if(auth()->user()->role === 'admin')

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.dashboard')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Dashboard
            </a>


            <!-- Kelola User -->
            <a href="{{ route('admin.user.index') }}"
               class="block px-4 py-2 mb-1 rounded-lg transition
               {{ request()->routeIs('admin.user.*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Kelola User
            </a>


            <!-- Kelola Kategori -->
            <a href="{{ route('admin.kategori.index') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.kategori*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Kelola Kategori
            </a>


            <!-- Kelola Alat -->
            <a href="{{ route('admin.alat.index') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.alat*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Kelola Alat
            </a>


            <!-- Kelola Peminjaman -->
            <a href="{{ route('admin.peminjaman.index') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.peminjaman*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Kelola Peminjaman
            </a>


            <!-- Kelola Pengembalian -->
            <a href="{{ route('admin.pengembalian.index') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.pengembalian*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Kelola Pengembalian
            </a>

        @endif



        <!-- ==================== MENU PETUGAS ==================== -->
        @if(auth()->user()->role === 'petugas')

            <!-- Persetujuan Peminjaman -->
            <a href="{{ route('petugas.peminjaman.index') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('petugas.peminjaman*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Persetujuan Peminjaman
            </a>


            <!-- Pemantauan Pengembalian -->
            <a href="{{ url('/petugas/pengembalian') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('petugas/pengembalian*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Pemantauan Pengembalian
            </a>


            <!-- Cetak Laporan -->
            <a href="{{ url('/petugas/laporan') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('petugas/laporan*')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Cetak Laporan
            </a>

        @endif



        <!-- ==================== MENU PEMINJAM ==================== -->
        @if(auth()->user()->role === 'peminjam')

            <!-- Katalog Alat -->
            <a href="{{ route('peminjam.katalog') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('peminjam.katalog')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Katalog Alat
            </a>


            <!-- Riwayat Peminjaman -->
            <a href="{{ route('peminjam.riwayat') }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->routeIs('peminjam.riwayat')
                   ? 'bg-gray-800 text-white font-medium shadow'
                   : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                Riwayat Peminjaman
            </a>

        @endif

    </nav>



    <!-- ==================== PROFIL USER ==================== -->
    <div class="p-4 border-t border-gray-800">

        @if(auth()->user()->role === 'admin')

            <!-- PROFIL ADMIN -->
            <a href="{{ route('admin.profile') }}"
               class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer">

        @elseif(auth()->user()->role === 'petugas')

            <!-- PROFIL PETUGAS -->
            <a href="{{ route('petugas.profile') }}"
               class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer">

        @elseif(auth()->user()->role === 'peminjam')

            <!-- PROFIL PEMINJAM -->
            <a href="{{ route('peminjam.profile') }}"
               class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer">

        @endif


            <!-- FOTO PROFIL -->
            @if(auth()->user()->foto_profile)

                <img
                    src="{{ asset(auth()->user()->foto_profile) }}"
                    alt="Foto Profil"
                    class="w-10 h-10 rounded-full object-cover border border-gray-600">

            @else

                <div class="w-10 h-10 rounded-full bg-gray-700 text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            @endif


            <!-- NAMA DAN ROLE -->
            <div class="min-w-0">

                <div class="text-sm font-semibold text-white truncate">
                    {{ auth()->user()->name }}
                </div>

                <div class="text-xs text-gray-400 capitalize">
                    {{ auth()->user()->role }}
                </div>

            </div>


        @if(
            auth()->user()->role === 'admin' ||
            auth()->user()->role === 'petugas' ||
            auth()->user()->role === 'peminjam'
        )

            </a>

        @endif

    </div>


</aside>



<!-- ==================== KONTEN UTAMA ==================== -->
<div class="flex-1 flex flex-col overflow-y-auto">


    <!-- HEADER -->
    <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">

        <div class="text-lg font-semibold text-gray-800">

            @yield('header-title', 'Dashboard')

        </div>


        <!-- LOGOUT -->
        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button
                type="submit"
                class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">

                Logout

            </button>

        </form>

    </header>



    <!-- CONTENT -->
    <main class="flex-1 p-6">

        @yield('content')

    </main>


</div>

</div>

</body>
</html>