@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('header-title', 'Log Aktivitas')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div>

        <h1 class="text-2xl font-bold text-gray-800">
            Log Aktivitas
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Menampilkan seluruh aktivitas yang terjadi dalam sistem.
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- CONTAINER --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">


        {{-- ===================================================== --}}
        {{-- SEARCH --}}
        {{-- ===================================================== --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <form
                action="{{ route('admin.logaktivitas.index') }}"
                method="GET"
                class="flex flex-col md:flex-row gap-3"
            >

                {{-- Input Search --}}
                <div class="flex-1">

                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari aktivitas atau nama user..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                </div>


                {{-- Tombol Cari --}}
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition"
                >
                    Cari
                </button>


                {{-- Tombol Reset --}}
                @if(!empty($search))

                    <a
                        href="{{ route('admin.logaktivitas.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center justify-center transition"
                    >
                        Reset
                    </a>

                @endif

            </form>

        </div>


        {{-- ===================================================== --}}
        {{-- TABLE --}}
        {{-- ===================================================== --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">


                {{-- ================================================= --}}
                {{-- HEADER TABLE --}}
                {{-- ================================================= --}}
                <thead>

                    <tr class="bg-gray-800 text-white text-sm uppercase">

                        <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                            No
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                            Waktu
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                            User
                        </th>

                        <th class="py-4 px-4 border-b border-gray-700 font-semibold whitespace-nowrap">
                            Aktivitas
                        </th>

                    </tr>

                </thead>


                {{-- ================================================= --}}
                {{-- BODY TABLE --}}
                {{-- ================================================= --}}
                <tbody class="text-gray-700 text-sm">

                    @forelse($logs as $index => $log)

                        <tr class="hover:bg-gray-50 transition">


                            {{-- NO --}}
                            <td class="py-3 px-4 border-b border-gray-200 whitespace-nowrap">

                                {{ $logs->firstItem() + $index }}

                            </td>


                            {{-- WAKTU --}}
                            <td class="py-3 px-4 border-b border-gray-200 whitespace-nowrap">

                                {{ $log->created_at?->timezone('Asia/Jakarta')->format('d-m-Y, H:i:s') ?? '-' }}

                            </td>


                            {{-- USER --}}
                            <td class="py-3 px-4 border-b border-gray-200 whitespace-nowrap">

                                <span class="font-medium text-gray-900">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </span>

                            </td>


                            {{-- AKTIVITAS --}}
                            <td class="py-3 px-4 border-b border-gray-200">

                                {{ $log->aktivitas ?? '-' }}

                            </td>


                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="py-10 px-4 text-center text-gray-500"
                            >
                                Belum ada log aktivitas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ===================================================== --}}
        {{-- PAGINATION --}}
        {{-- ===================================================== --}}
        @if($logs->hasPages())

            <div class="p-4 border-t border-gray-200">

                {{ $logs->withQueryString()->links() }}

            </div>

        @endif


    </div>

</div>

@endsection
