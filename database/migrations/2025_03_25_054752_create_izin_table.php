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
        Schema::create('izin', function (Blueprint $table) {
            $table->integer('izin_id')->autoIncrement();
            $table->integer('izin_turid');
            $table->integer('izin_personel');
            $table->string('izin_yil', 4);
            $table->string('izin_vefat', 20)->nullable();
            $table->integer('izin_ekleyen_personel')->nullable();
            $table->integer('izin_silen_personel')->nullable();
            $table->string('izin_ekleyen_ip', 25)->nullable();
            $table->string('izin_silen_ip', 25)->nullable();
            $table->integer('izin_onaylayan')->nullable();
            $table->date('izin_baslayis');
            $table->date('izin_bitis');
            $table->date('izin_isebaslayis');
            $table->string('izin_suresi', 4);
            $table->string('izin_aciklama', 150)->nullable();
            $table->string('izin_saglikkurumu', 50)->nullable();
            $table->string('izin_adresi', 150)->nullable();
            $table->smallInteger('izin_onay')->default(0); // Default değer 1
            $table->smallInteger('izin_kurumid');
            $table->smallInteger('izin_birim');
            $table->smallInteger('izin_bolge');
            $table->smallInteger('izin_durum')->default(1); // Default değer 1 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};
