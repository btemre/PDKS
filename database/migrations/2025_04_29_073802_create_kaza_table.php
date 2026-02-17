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
        Schema::create('kaza', function (Blueprint $table) {
            $table->id('kaza_id');
            $table->timestamp('kaza_eklenmetarihi')->nullable();
            $table->string('kaza_ekleyenkullanici', 25)->nullable();
            $table->string('kaza_ekleyenip', 25)->nullable();
            $table->string('kaza_silenkullanici', 25)->nullable();
            $table->string('kaza_silenip', 25)->nullable();
            $table->time('kaza_saat')->nullable();
            $table->string('kaza_plaka', 100)->nullable();
            $table->string('kaza_arac', 100)->nullable();
            $table->date('kaza_tarih')->nullable();
            $table->string('kaza_kkno', 11)->nullable();
            $table->string('kaza_km', 50)->nullable();
            $table->string('kaza_yeri', 50)->nullable();
            $table->integer('kaza_sayisi')->nullable();
            $table->integer('kaza_vefat')->nullable();
            $table->integer('kaza_yarali')->nullable();
            $table->integer('kaza_carpisma')->nullable();
            $table->integer('kaza_devrilme')->nullable();
            $table->integer('kaza_cismecarpma')->nullable();
            $table->integer('kaza_duranaracacarpma')->nullable();
            $table->integer('kaza_yayacarpma')->nullable();
            $table->integer('kaza_aractandusme')->nullable();
            $table->integer('kaza_diger')->nullable();
            $table->integer('kaza_maddihasar')->nullable();
            $table->string('kaza_aciklama', 155)->nullable();
            $table->integer('kaza_kurumid')->nullable();
            $table->integer('kaza_bolgeid')->nullable();
            $table->string('kaza_istikamet', 50)->nullable();
            $table->tinyInteger('kaza_durum')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaza');
    }
};
