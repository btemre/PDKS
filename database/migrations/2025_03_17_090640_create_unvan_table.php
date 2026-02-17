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
        Schema::create('unvan', function (Blueprint $table) {
            $table->integer('unvan_id')->autoIncrement();
            $table->string('unvan_ad', 90);
            $table->integer('unvan_sira')->nullable();
            $table->integer('unvan_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unvan');
    }
};
