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
        Schema::create('izin_mazeret', function (Blueprint $table) {
            $table->id('izinmazeret_id');
            $table->unsignedBigInteger('izinmazeret_turid');
            $table->unsignedBigInteger('izinmazeret_personel');
            $table->string('izinmazeret_yil', 4);
            $table->timestamp('izinmazeret_tarih')->useCurrent();
            $table->unsignedBigInteger('izinmazeret_ekleyen_personel')->nullable();
            $table->unsignedBigInteger('izinmazeret_silen_personel')->nullable();
            $table->string('izinmazeret_ekleyen_ip', 30)->nullable();
            $table->string('izinmazeret_silen_ip', 30)->nullable();
            $table->date('izinmazeret_baslayis');
            $table->time('izinmazeret_baslayissaat');
            $table->time('izinmazeret_bitissaat');
            $table->unsignedBigInteger('izinmazeret_bolge');
            $table->unsignedBigInteger('izinmazeret_kurumid');
            $table->unsignedBigInteger('izinmazeret_birim');
            $table->string('izinmazeret_aciklama', 255)->nullable();
            $table->time('izinmazeret_suresi');
            $table->tinyInteger('izinmazeret_durum')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_mazeret');
    }
};
