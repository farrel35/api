<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('bengkels', function (Blueprint $table) {
            $table->index(['lat', 'long'], 'bengkels_lat_long_index');
        });
    }

    public function down()
    {
        Schema::table('bengkels', function (Blueprint $table) {
            $table->dropIndex('bengkels_lat_long_index');
        });
    }
};
