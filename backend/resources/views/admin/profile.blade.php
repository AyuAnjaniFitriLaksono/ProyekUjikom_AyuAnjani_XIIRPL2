@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Saya')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- PESAN SUKSES --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- PESAN ERROR --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ERROR VALIDASI --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <!-- HEADER -->
        <div class="bg-gray-800 px-6 py-5">
            <h2 class="text-xl font-bold text-white">
                Profil Saya
            </h2>

            <p class="text-sm text-gray-300 mt-1">
                Informasi akun pengguna yang sedang login
            </p>
        </div>


        <!-- CONTENT -->
        <div class="p-6">

            <div class="flex flex-col md:flex-row gap-8">


                <!-- FOTO PROFIL -->
                <div class="flex justify-center md:w-1/3">

                    <div class="text-center">

                        @if($user->foto_profile)

                            <img
                                src="{{ asset($user->foto_profile) }}"
                                alt="Foto Profil"
                                class="w-40 h-40 rounded-full object-cover border-4 border-gray-200 shadow mx-auto"
                            >

                        @else

                            <div class="w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center border-4 border-gray-300 mx-auto">

                                <span class="text-5xl font-bold text-gray-500">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>

                            </div>

                        @endif


                        <!-- FORM FOTO -->
                        <form
                            action="{{ route('admin.profile.updateFoto') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="mt-4"
                        >

                            @csrf

                            <input
                                type="file"
                                name="foto_profile"
                                accept="image/jpeg,image/png"
                                required
                                class="block w-full text-sm text-gray-600"
                            >

                            <button
                                type="submit"
                                class="mt-3 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition"
                            >
                                Upload Foto
                            </button>

                        </form>

                    </div>

                </div>



                <!-- DATA USER -->
                <div class="flex-1">

                    <form
                        action="{{ route('admin.profile.update') }}"
                        method="POST"
                        class="space-y-5"
                    >

                        @csrf
                        @method('PUT')


                        <!-- NAMA -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-500 mb-1">
                                Nama
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            >

                        </div>



                        <!-- EMAIL -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-500 mb-1">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            >

                        </div>



                        <!-- ROLE -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-500 mb-1">
                                Role
                            </label>

                            <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg">

                                <span class="inline-block px-3 py-1 bg-gray-800 text-white text-xs font-semibold rounded-full">
                                    {{ ucfirst($user->role) }}
                                </span>

                            </div>

                            <p class="text-xs text-gray-400 mt-1">
                                Role tidak dapat diubah melalui halaman profil.
                            </p>

                        </div>



                        <!-- NO HP -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-500 mb-1">
                                No. HP
                            </label>

                            <input
                                type="text"
                                name="no_hp"
                                value="{{ old('no_hp', $user->no_hp) }}"
                                placeholder="Masukkan nomor HP"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            >

                        </div>



                        <!-- ALAMAT -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-500 mb-1">
                                Alamat
                            </label>

                            <textarea
                                name="alamat"
                                rows="3"
                                placeholder="Masukkan alamat"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            >{{ old('alamat', $user->alamat) }}</textarea>

                        </div>



                        <!-- SIMPAN -->
                        <div class="pt-2">

                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-semibold text-sm transition shadow-sm"
                            >
                                Simpan Perubahan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection