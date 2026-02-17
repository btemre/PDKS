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
        Schema::create('evrak', function (Blueprint $table) {
            $table->id('evrak_id');
            $table->integer('evrak_sira')->nullable();
            $table->string('evrak_tur', 50)->nullable();
            $table->integer('evrak_kategori')->nullable();
            $table->string('evrak_birim', 100)->nullable();
            $table->date('evrak_tarihi')->nullable();
            $table->date('evrak_cikistarihi')->nullable();
            $table->string('evrak_konu', 100)->nullable();
            $table->string('evrak_sayi', 155)->nullable();
            $table->string('evrak_aciklama', 155)->nullable();
            $table->timestamp('evrak_eklemetarih')->nullable();
            $table->integer('evrak_bolgeid')->nullable();
            $table->integer('evrak_kurumid')->nullable();
            $table->integer('evrak_birimid')->nullable();
            $table->string('evrak_ekleyenip', 50)->nullable();
            $table->integer('evrak_ekleyenpersonel')->nullable();
            $table->integer('evrak_silenpersonel')->nullable();
            $table->string('evrak_silenip', 50)->nullable();
            $table->tinyInteger('evrak_durum')->default(1); // 0 veya 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evrak');
    }
};
