<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'bengkel_id',
    ];

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }
}
