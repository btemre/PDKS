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
        Schema::create('pdks_cihazlar', function (Blueprint $table) {
            $table->bigIncrements('cihaz_id');
            $table->string('cihaz_adi', 255);
            $table->string('cihaz_ip', 255);
            $table->integer('cihaz_port');
            $table->tinyInteger('cihaz_durum')->default(1);
            $table->datetime('son_baglanti_zamani')->nullable();
            $table->tinyInteger('baglanti_durumu')->nullable();
            $table->string('seri_no', 255);
            $table->string('mac_adresi', 255);
            $table->integer('kart_sayisi');
            $table->string('cihaz_model', 50);
            $table->string('cihaz_birim', 50);
            $table->string('cihaz_aciklama', 200);
            $table->string('cihaz_eklenenip', 25);
            $table->integer('cihaz_ekleyenkullanici');
            $table->integer('cihaz_silenkullanici')->nullable();
            $table->string('cihaz_silenip', 25)->nullable();
            $table->integer('cihaz_gecistip')->nullable();
            $table->integer('cihaz_kurumid')->nullable();
            $table->timestamps(); // created_at ve updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cihaz');
    }
};
