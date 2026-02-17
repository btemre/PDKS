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
        Schema::create('birim', function (Blueprint $table) {
            $table->integer('birim_id')->autoIncrement();
            $table->string('birim_ad', 90);
            $table->string('birim_yetkili', 90)->nullable();
            $table->string('birim_yetkiliunvan', 90)->nullable();
            $table->string('birim_sorumlu', 90)->nullable();
            $table->string('birim_sorumluunvan', 90)->nullable();
            $table->integer('birim_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birim');
    }
};
