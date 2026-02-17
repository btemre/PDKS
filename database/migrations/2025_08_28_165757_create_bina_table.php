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
        Schema::create('bina', function (Blueprint $table) {
            $table->id('bina_id');
            $table->unsignedBigInteger('kurum_id');
            $table->string('bina_adi', 150);
            $table->string('bina_adres')->nullable();
            $table->tinyInteger('bina_durum')->default(1); 
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bina');
    }
};
