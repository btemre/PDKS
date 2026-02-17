<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('tunel', function (Blueprint $table) {
        $table->id('tunel_id');
        $table->string('tunel_ad', 50)->nullable();
        $table->string('tunel_kod', 3)->nullable();
        $table->string('tunel_kordinat', 50)->nullable();
        $table->integer('tunel_bolge')->nullable();
        $table->integer('tunel_kurum')->nullable();
        $table->integer('tunel_birim')->nullable();
        $table->enum('tunel_durum', ['0', '1'])->default('1')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunel');
    }
};
