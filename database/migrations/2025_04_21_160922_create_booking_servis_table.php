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
            $table->string('jenis_kendaraan');
            $table->string('plat');
            $table->text('keluhan');
            $table->enum('status', ['pending', 'onprogress', 'completed'])->default('pending');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key for User
            $table->foreignId('bengkel_id')->constrained()->onDelete('cascade'); // Foreign key for Bengkel
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_servis');
    }
}
