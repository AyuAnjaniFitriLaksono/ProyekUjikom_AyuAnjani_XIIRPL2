<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengembalian extends Model
{
    protected $table = 'detail_pengembalian';

    protected $fillable = [
        'pengembalian_id',
        'alat_id',
        'jumlah',
        'kondisi',
        'denda',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'denda' => 'integer',
        ];
    }

    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(
            Pengembalian::class,
            'pengembalian_id'
        );
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(
            Alat::class,
            'alat_id'
        );
    }
}