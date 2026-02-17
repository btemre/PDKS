<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Geç gelen / erken çıkan kayıtlar için kısa açıklama/not (4.4).
     * UI ile not ekleme/gösterme sonradan bağlanabilir.
     */
    public function up(): void
    {
        Schema::create('pdks_gunluk_aciklama', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personel_id');
            $table->date('tarih');
            $table->string('tip', 20); // 'gec_gelen' | 'erken_cikan'
            $table->string('aciklama', 500)->nullable();
            $table->unsignedBigInteger('ekleyen_user_id')->nullable();
            $table->timestamps();

            $table->unique(['personel_id', 'tarih', 'tip']);
            $table->foreign('personel_id')->references('personel_id')->on('personel')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdks_gunluk_aciklama');
    }
};
