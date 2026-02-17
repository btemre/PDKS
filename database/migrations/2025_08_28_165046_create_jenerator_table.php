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
        Schema::create('jenerator', function (Blueprint $table) {
            $table->id('jenerator_id');
            $table->unsignedBigInteger('jenerator_kurumid');
            $table->unsignedBigInteger('jenerator_bina');
            $table->string('jenerator_ad', 100);
            $table->string('jenerator_marka', 100)->nullable();
            $table->string('jenerator_model', 100)->nullable();
            $table->string('jenerator_yil', 4);
            $table->string('jenerator_kva', 10)->nullable();
            $table->string('jenerator_tck', 100)->nullable();
            $table->string('jenerator_kod', 100)->nullable();
            $table->date('jenerator_akutarihi')->nullable();
            $table->date('jenerator_bakimtarihi')->nullable();
            $table->string('jenerator_yakitmiktari', 50)->nullable();
            $table->string('jenerator_yakitseviyesi', 50)->nullable();
            $table->string('jenerator_aciklama', 150)->nullable();
            $table->tinyInteger('jenerator_durum')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenerator');
    }
};
