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
    Schema::create('tunel_jetfantesttur', function (Blueprint $table) {
        $table->id('test_id');
        $table->string('test_tur', 255)->nullable();
        $table->integer('test_statu')->nullable();
        $table->integer('test_tarife')->nullable();
        $table->enum('test_durum', ['0', '1'])->default('1')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jetfan_testtur');
    }
};
