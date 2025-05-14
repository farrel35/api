<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            // Rename column
            if (Schema::hasColumn('booking_servis', 'jenis_kendaraan')) {
                $table->renameColumn('jenis_kendaraan', 'nama_kendaraan');
            }

            // Add new columns
            $table->string('no_hp')->after('nama');
            $table->date('tgl_booking')->after('keluhan');
            $table->time('jam_booking')->after('tgl_booking');
            $table->json('jenis_layanan')->nullable()->after('jam_booking');
            $table->json('detail_servis')->nullable()->after('jenis_layanan');
            $table->date('tgl_ambil')->nullable()->after('detail_servis');
            $table->time('jam_ambil')->nullable()->after('tgl_ambil');

            // Change status to integer if it exists
            $table->unsignedTinyInteger('status')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            // Rename column back
            if (Schema::hasColumn('booking_servis', 'nama_kendaraan')) {
                $table->renameColumn('nama_kendaraan', 'jenis_kendaraan');
            }

            // Drop added columns
            $table->dropColumn([
                'no_hp',
                'tgl_booking',
                'jam_booking',
                'jenis_layanan',
                'detail_servis',
                'tgl_ambil',
                'jam_ambil'
            ]);

            // Revert status back to string
            $table->string('status')->change();
        });
    }
};
