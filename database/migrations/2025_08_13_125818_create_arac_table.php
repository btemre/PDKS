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
        Schema::create('arac', function (Blueprint $table) {
            $table->id('arac_id')->unsigned()->notNullable();
            $table->string('arac_marka', 50)->nullable();
            $table->string('arac_cins', 50)->nullable();
            $table->string('arac_tck', 50)->nullable();
            $table->string('arac_kod', 50)->nullable();
            $table->string('arac_sase', 50)->nullable();
            $table->string('arac_plaka', 15)->nullable();
            $table->string('arac_km', 15)->nullable();
            $table->date('arac_ilkmuayene')->nullable();
            $table->string('arac_model', 4)->nullable();
            $table->integer('arac_tur')->nullable();
            $table->string('arac_surucusu', 50)->nullable();
            $table->date('arac_ilksigorta')->nullable();
            $table->tinyInteger('arac_durum')->default(1);
            $table->integer('arac_kurumid')->unsigned()->notNullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arac');
    }
};
