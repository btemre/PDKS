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
        Schema::create('dosya', function (Blueprint $table) {
            $table->id('dosya_id');
            $table->string('dosya_ad', 50);
            $table->integer('dosya_personel');
            $table->string('dosya_sicilno', 20);
            $table->string('dosya_yol', 150);
            $table->string('dosya_aciklama', 50);
            $table->string('dosya_boyut', 50);
            $table->string('dosya_tur', 10);
            $table->timestamp('dosya_tarih');
            $table->integer('dosya_bolge');
            $table->integer('dosya_kurum');
            $table->integer('dosya_birim');
            $table->integer('dosya_kullanici');
            $table->enum('dosya_durum', ['0', '1'])->default(value: '1'); // ENUM değerlerini ihtiyaca göre düzenleyin
            $table->timestamps(); // created_at ve updated_at alanlarını otomatik ekler
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosya');
    }
};
