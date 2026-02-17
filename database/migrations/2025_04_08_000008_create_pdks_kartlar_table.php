<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_kartlar', function (Blueprint $table) {
            $table->id('kart_id');
            $table->string('kart_adi')->nullable();
            $table->integer('kart_personelid')->nullable();
            $table->string('kart_numarasi')->nullable();
            $table->integer('yetkili')->nullable();
            $table->integer('kart_ekleyenkullanici')->nullable();
            $table->string('kart_ekleyenip', 25)->nullable();
            $table->timestamp('kart_eklemetarihi')->useCurrent();
            $table->integer('kart_bolge')->nullable();
            $table->integer('kart_kurumid')->nullable();
            $table->integer('kart_birim')->nullable();
            $table->integer('kart_silenkullanici')->nullable();
            $table->string('kart_silenip', 25)->nullable();
            $table->enum('kart_durum', ['1', '0'])->default('1');
            $table->string('kart_sifre', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_kartlar');
    }
};
