<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bengkels', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->longText('alamat');
            $table->text('deskripsi')->nullable();
            $table->time('jam_buka');
            $table->time('jam_selesai');
            $table->decimal('lat', 10, 7);
            $table->decimal('long', 10, 7);
            $table->longText('image')->nullable();
            $table->foreignId('owner_id')->unique()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bengkels');
    }
};
