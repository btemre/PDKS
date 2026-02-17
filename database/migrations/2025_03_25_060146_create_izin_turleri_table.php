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
        Schema::create('izin_turleri', function (Blueprint $table) {
            $table->integer('izin_turid')->autoIncrement();
            $table->string('izin_ad', 50);
            $table->integer('izin_statu');
            $table->smallInteger('izin_durum')->default(1); // Default değer 1 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_turleri');
    }
};
