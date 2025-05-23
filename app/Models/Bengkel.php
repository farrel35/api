<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bengkel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'deskripsi',
        'jam_buka',
        'jam_selesai',
        'lat',
        'long',
        'image',
        'owner_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function getImageAttribute($value)
    {
        return asset('storage/' . $value);
    }
}