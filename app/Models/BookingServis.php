<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_kendaraan',
        'plat',
        'keluhan',
        'status',
        'user_id',
        'bengkel_id'
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Bengkel model
    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class, 'bengkel_id');
    }
}
