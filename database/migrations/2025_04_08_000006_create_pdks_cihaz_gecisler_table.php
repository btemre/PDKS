<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_cihaz_gecisler', function (Blueprint $table) {
            $table->id('gecis_id');
            $table->integer('kart_id')->nullable();
            $table->timestamp('gecis_tarihi')->nullable();
            $table->integer('cihaz_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_cihaz_gecisler');
    }
};
