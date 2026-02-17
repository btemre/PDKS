<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_cihaz_sync', function (Blueprint $table) {
            $table->id();
            $table->integer('cihaz_id')->nullable();
            $table->integer('kart_id')->nullable();
            $table->integer('islem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_cihaz_sync');
    }
};
