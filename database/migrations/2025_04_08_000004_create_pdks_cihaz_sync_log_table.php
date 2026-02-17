<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_cihaz_sync_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->integer('log_cihaz_id')->nullable();
            $table->integer('log_kart_id')->nullable();
            $table->integer('log_islem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_cihaz_sync_log');
    }
};
