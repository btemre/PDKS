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
        Schema::create('il', function (Blueprint $table) {
            $table->integer('il_id')->autoIncrement();
            $table->string('il_ad', 90);
            $table->integer('il_plaka');
            $table->integer('il_telefon');
            $table->integer('il_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('il');
    }
};
