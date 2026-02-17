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
        Schema::create('ogrenim', function (Blueprint $table) {
            $table->id('ogrenim_id');
            $table->string('ogrenim_tur', 90);
            $table->string('ogrenim_ad', 90);
            $table->string('ogrenim_aciklama', 90);
            $table->integer('ogrenim_durum')->default(1); // 1: aktif, 0: pasif gibi
            $table->timestamps(); // created_at ve updated_at alanlarını otomatik ekler
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ogrenim');
    }
};
