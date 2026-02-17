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
        Schema::create('ogrenim', function (Blueprint $table) {
            $table->integer('ogrenim_id')->autoIncrement();
            $table->string('ogrenim_tur', 90);
            $table->string('ogrenim_ad', 90)->nullable();
            $table->string('ogrenim_aciklama', 90)->nullable();
            $table->integer('ogrenim_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ogrenim');
    }
};
