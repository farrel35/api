<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingServisTable extends Migration
{
    public function up()
    {
        Schema::create('booking_servis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp');
            $table->string('nama_kendaraan');
            $table->string('plat');
            $table->text('keluhan');
            $table->date('tgl_booking');
            $table->time('jam_booking');
            $table->json('jenis_layanan')->nullable();
            $table->json('detail_servis')->nullable();
            $table->date('tgl_ambil')->nullable();
            $table->time('jam_ambil')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key for User
            $table->foreignId('bengkel_id')->constrained('bengkels')->onDelete('cascade'); // Foreign key for Bengkel
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_servis');
    }
}
