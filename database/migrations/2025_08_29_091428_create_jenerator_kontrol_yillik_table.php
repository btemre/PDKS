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
        Schema::create('jenerator_kontrol_yil', function (Blueprint $table) {
            $table->id('yillik_id');
            $table->unsignedBigInteger('jenerator_id');
            $table->date('kontrol_tarihi');
            // yıllık maddeler
            $table->boolean('filtre_degisim')->default(1);
            $table->boolean('kayis_kontrol')->default(1);
            $table->boolean('yakittank_temizligi')->default(1);
            $table->boolean('subap_kompresyon')->default(1);
            $table->boolean('elektronik_regulator')->default(1);
            $table->boolean('gosterge_panelleri')->default(1);
            $table->boolean('alarm_kontrol')->default(1);
            $table->boolean('blok_isitici')->default(1);
            $table->boolean('vibrasyon_takoz')->default(1);
            $table->boolean('scada_kontrolu')->default(1);
            $table->boolean('trafik_emniyet')->default(1);
            $table->text('aciklama')->nullable();
            $table->unsignedBigInteger('kontrol_eden')->nullable(); 
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenerator_kontrol_yil');
    }
};
