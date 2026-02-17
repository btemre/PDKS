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
        Schema::create('ilce', function (Blueprint $table) {
            $table->integer('ilce_id')->autoIncrement();
            $table->string('ilce_ad', 90);
            $table->integer('ilce_ilkodu');
            $table->integer('ilce_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilce');
    }
};
