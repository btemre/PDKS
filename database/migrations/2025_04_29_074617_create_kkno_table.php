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
        Schema::create('kkno', function (Blueprint $table) {
            $table->id('kkno_id');
            $table->string('kkno_ad', 25)->nullable();
            $table->integer('kkno_kurumid')->nullable();
            $table->integer('kkno_bolgeid')->nullable();
            $table->tinyInteger('kkno_durum')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkno');
    }
};
