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
        Schema::create('gorev', function (Blueprint $table) {
            $table->integer('gorev_id')->autoIncrement();
            $table->string('gorev_ad', 50);
            $table->integer('gorev_sira')->nullable();
            $table->integer('gorev_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gorev');
    }
};
