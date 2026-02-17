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
        Schema::create('mesai_saati', function (Blueprint $table) {
            $table->integer('mesai_id')->autoIncrement();
            $table->string('mesai_ad', 90);
            $table->string('mesai_aciklama', 90);
            $table->integer('mesai_calismaturu')->nullable();
            $table->time('mesai_giris');
            $table->time('mesai_oglengiris')->nullable();
            $table->time('mesai_oglencikis')->nullable();
            $table->time('mesai_cikis');
            $table->integer('mesai_bolge')->nullable();
            $table->integer('mesai_kurum');
            $table->integer('mesai_birim');
            $table->integer('mesai_unvan');
            $table->integer('mesai_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesai_saati');
    }
};
