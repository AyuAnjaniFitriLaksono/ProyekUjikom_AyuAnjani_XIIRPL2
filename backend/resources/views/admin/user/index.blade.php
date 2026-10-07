@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

    {{-- NOTIFIKASI SUKSES --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- CARD UTAMA --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- ====================================================== --}}
        {{-- HEADER CARD --}}
        {{-- ====================================================== --}}
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                {{-- JUDUL --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Pengguna Sistem
                    </h3>
                </div>


                {{-- SEARCH + TAMBAH --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">

                    {{-- FORM SEARCH --}}
                    <form
                        action="{{ route('admin.user.index') }}"
                        method="GET"
                        class="flex w-full sm:w-auto"
                    >

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, email, role..."
                            class="w-full sm:w-64 lg:w-72 px-3 py-2 text-sm border border-gray-300 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                        <button
                            type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition"
                        >
                            Cari
                        </button>

                        @if(request('search'))

                            <a
                                href="{{ route('admin.user.index') }}"
                                class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm font-medium rounded-lg flex items-center justify-center transition"
                                title="Reset Pencarian"
                            >
                                Reset
                            </a>

                        @endif

                    </form>


                    {{-- TOMBOL TAMBAH USER --}}
                    <a
                        href="{{ route('admin.user.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap text-center shadow-sm"
                    >
                        + Tambah User
                    </a>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- TABEL USER --}}
        {{-- ====================================================== --}}
        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                {{-- HEADER TABEL --}}
                <thead>

                    <tr class="bg-slate-800 text-white">

                        {{-- FOTO --}}
                        <th class="py-3 px-4 text-center text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            Foto
                        </th>


                        {{-- NAMA --}}
                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            Nama
                        </th>


                        {{-- EMAIL --}}
                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            Email
                        </th>


                        {{-- ROLE --}}
                        <th class="py-3 px-4 text-center text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            Role / Hak Akses
                        </th>


                        {{-- NO HP --}}
                        <th class="py-3 px-4 text-center text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            No. HP
                        </th>


                        {{-- AKSI --}}
                        <th class="py-3 px-4 text-center text-sm font-semibold uppercase tracking-wide border-b border-slate-700 whitespace-nowrap">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ISI TABEL --}}
                <tbody class="text-sm text-gray-700">

                    @forelse($users as $user)

                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">

                            {{-- FOTO --}}
                            <td class="py-3 px-4 text-center align-middle">

                                @if($user->foto_profile)

                                    <img
                                        src="{{ asset($user->foto_profile) }}"
                                        alt="Foto {{ $user->name }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-300 mx-auto"
                                    >

                                @else

                                    <div class="w-10 h-10 rounded-full bg-gray-700 text-white flex items-center justify-center font-bold mx-auto">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                @endif

                            </td>


                            {{-- NAMA --}}
                            <td class="py-3 px-4 text-left align-middle font-medium text-gray-900 whitespace-nowrap">
                                {{ $user->name }}
                            </td>


                            {{-- EMAIL --}}
                            <td class="py-3 px-4 text-left align-middle text-gray-700">
                                {{ $user->email }}
                            </td>


                            {{-- ROLE --}}
                            <td class="py-3 px-4 text-center align-middle">

                                <span
                                    class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap

                                    @if($user->role == 'admin')
                                        bg-purple-100 text-purple-800

                                    @elseif($user->role == 'petugas')
                                        bg-blue-100 text-blue-800

                                    @else
                                        bg-green-100 text-green-800
                                    @endif"
                                >
                                    {{ ucfirst($user->role) }}
                                </span>

                            </td>


                            {{-- NO HP --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                {{ $user->no_hp ?? '-' }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3 px-4 text-center align-middle">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.user.edit', $user->id) }}"
                                        class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold transition"
                                    >
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('admin.user.destroy', $user->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold transition"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-8 px-4 text-center text-gray-500"
                            >
                                Belum ada data pengguna.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ====================================================== --}}
        {{-- PAGINATION --}}
        {{-- ====================================================== --}}
        <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">

            {{ $users->links() }}

        </div>

    </div>

@endsection