<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'no_hp',
        'nama_kendaraan',
        'plat',
        'keluhan',
        'status',
        'user_id',
        'bengkel_id',
        'tgl_booking',
        'jam_booking',
        'tgl_ambil',
        'jam_ambil',
        'jenis_layanan',
        'detail_servis',
    ];

    protected function casts(): array
    {
        return [
            'tgl_booking' => 'date',
            'tgl_ambil' => 'date',
            'jam_booking' => 'string',
            'jam_ambil' => 'string',
            'jenis_layanan' => 'array',
            'detail_servis' => 'array',
            'status' => 'integer',
        ];
    }

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
