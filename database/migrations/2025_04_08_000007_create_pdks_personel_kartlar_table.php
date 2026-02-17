<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_personel_kartlar', function (Blueprint $table) {
            $table->id('kart_personel_id');
            $table->integer('kart_id')->nullable();
            $table->integer('personel_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_personel_kartlar');
    }
};
