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
        Schema::create('jenerator_kontrol_hafta', function (Blueprint $table) {
            $table->id('kontrol_id');
            $table->unsignedBigInteger('jenerator_id');
            $table->date('kontrol_tarihi');
            $table->integer('yakit_seviyesi')->nullable();
            $table->integer('yakit_miktari')->nullable();
            $table->integer('calisma_saati')->nullable();
            $table->boolean('sarj_redresoru')->default(1);
            $table->boolean('aku_durumu')->default(1);
            $table->boolean('su_durumu')->default(1);
            $table->boolean('temizlik')->default(1);
            $table->boolean('pano_temizlik')->default(1);
            $table->boolean('calistirma_testi')->default(1);
            $table->boolean('sizinti_kacak')->default(1);
            $table->boolean('radyator')->default(1);
            $table->boolean('isitici')->default(1);
            $table->boolean('lamba')->default(1);
            $table->boolean('egzoz')->default(1);
            $table->boolean('hava_filtresi')->default(1);
            $table->boolean('scada_kontrolu')->default(1);
            $table->integer('yag_seviyesi')->nullable();
            $table->integer('yag_miktari')->nullable();
            $table->decimal('frekans', 5, 2)->nullable();
            $table->text('aciklama')->nullable();
            $table->unsignedBigInteger('kontrol_eden')->nullable(); 
            $table->boolean('durum')->default(1);
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenerator_kontrol_hafta');
    }
};
