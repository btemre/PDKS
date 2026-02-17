<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bilgisayar_envanter', function (Blueprint $table) {
            $table->id();

            // Temel Bilgiler
            $table->string('bilgisayar_adi')->nullable();
            $table->string('kullanici_adi')->nullable();
            $table->string('isletim_sistemi')->nullable();
            $table->string('isletim_sistemi_surumu')->nullable();

            // Donanım Bilgileri
            $table->string('islemci_modeli')->nullable();
            $table->integer('islemci_cekirdek_sayisi')->nullable();
            $table->integer('islemci_thread_sayisi')->nullable();
            $table->string('ram_boyutu')->nullable(); // Örn: 16 GB
            $table->string('ram_turu')->nullable();   // DDR3 / DDR4
            $table->string('disk_turu')->nullable();  // HDD / SSD / NVMe
            $table->string('disk_boyutu')->nullable(); // Örn: 512 GB
            $table->string('ekran_karti')->nullable();
            $table->string('anakart_modeli')->nullable();

            // Ağ Bilgileri
            $table->string('ip_adresi')->nullable();
            $table->string('mac_adresi')->nullable();
            $table->string('baglanti_turu')->nullable(); // Kablolu / Kablosuz

            // Ek Bilgiler
            $table->string('antivirus')->nullable();
            $table->string('ofis_versiyonu')->nullable();
            $table->string('domain')->nullable();
            $table->string('seri_numarasi')->nullable();
            $table->string('bios_surumu')->nullable();
            $table->integer('durum')->default(1); // 1: aktif, 0: pasif gibi
            $table->date('envanter_tarihi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilgisayar_envanter');
    }
};
