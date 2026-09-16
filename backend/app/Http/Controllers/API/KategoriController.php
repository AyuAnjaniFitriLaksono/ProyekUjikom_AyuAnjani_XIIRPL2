<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kategori\StoreKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;

class KategoriController extends Controller
{
    public function index(): JsonResponse
    {
        $kategori = Kategori::latest()->get();

        return response()->json([
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => KategoriResource::collection($kategori)
        ]);
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => new KategoriResource($kategori)
        ], 201);
    }

    public function show(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        return response()->json([
            'message' => 'Detail kategori berhasil diambil.',
            'data' => new KategoriResource($kategori)
        ]);
    }

    public function update(UpdateKategoriRequest $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);

        $kategori->update($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil diperbarui.',
            'data' => new KategoriResource($kategori)
        ]);
    }

    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        $kategori->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}