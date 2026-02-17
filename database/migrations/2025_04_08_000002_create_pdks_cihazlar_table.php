<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_cihazlar', function (Blueprint $table) {
            $table->id('cihaz_id');
            $table->string('cihaz_adi')->nullable();
            $table->string('cihaz_ip')->nullable();
            $table->integer('cihaz_port')->nullable();
            $table->boolean('cihaz_durum')->nullable();
            $table->dateTime('son_baglanti_zamani')->nullable();
            $table->boolean('baglanti_durumu')->nullable();
            $table->string('seri_no')->nullable();
            $table->string('mac_adresi')->nullable();
            $table->integer('kart_sayisi')->nullable();
            $table->string('cihaz_model', 50)->nullable();
            $table->string('cihaz_birim', 50)->nullable();
            $table->string('cihaz_aciklama', 200)->nullable();
            $table->string('cihaz_ekleyenip', 25)->nullable();
            $table->integer('cihaz_ekleyenkullanici')->nullable();
            $table->integer('cihaz_silenkullanici')->nullable();
            $table->string('cihaz_silenip', 25)->nullable();
            $table->integer('cihaz_gecistipi')->nullable();
            $table->integer('cihaz_kurumid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_cihazlar');
    }
};
