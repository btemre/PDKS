<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_mail', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->smallInteger('kurumid');
            $table->smallInteger('birim');
            $table->smallInteger('bolge');
            $table->boolean('durum')->default(1); // İstediğinde kapatabilmek için
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_mail');
    }
};
