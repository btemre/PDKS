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
        Schema::create('personel', function (Blueprint $table) {
            $table->id('personel_id');
            //$table->integer('personel_id')->autoIncrement();
            $table->timestamp('personel_tarih')->useCurrent();
            $table->string('personel_adsoyad', 50);
            $table->string('personel_tc', 11);
            $table->integer('personel_durumid');
            $table->integer('personel_gorev');
            $table->integer('personel_unvan');
            $table->integer('personel_gorevyeri');
            $table->integer('personel_birim')->nullable();
            $table->string('personel_aciklama', 500)->nullable();
            $table->string('personel_resim', 50)->nullable();
            $table->string('personel_telefon', 20)->nullable();
            $table->string('personel_mail', 50)->nullable();
            $table->string('personel_kan', 50)->nullable();
            $table->string('personel_sicilno', 50)->nullable();
            $table->string('personel_adres', 150)->nullable();
            $table->date('personel_dogumtarihi')->nullable();
            $table->integer('personel_il');
            $table->integer('personel_ilce');
            $table->string('personel_dosya', 50)->nullable();
            $table->date('personel_isegiristarih');
            $table->date('personel_kurumisegiristarih')->nullable();
            $table->string('personel_iban', 30)->nullable();
            $table->string('personel_eposta', 50)->nullable();
            $table->string('personel_ekleyen', 25)->nullable();
            $table->string('personel_duzenleyen', 25)->nullable();
            $table->string('personel_silen', 25)->nullable();
            $table->integer('personel_siralama')->nullable();
            $table->integer('personel_sozlesmelimi');
            $table->integer('personel_engellimi');
            $table->integer('personel_mesai')->nullable();
            $table->integer('personel_pantolon')->nullable();
            $table->integer('personel_ayakkabi')->nullable();
            $table->string('personel_tshort', 5)->nullable();
            $table->string('personel_mont', 5)->nullable();
            $table->integer('personel_ogrenim')->nullable();
            $table->string('personel_okul', 100)->nullable();
            $table->integer('personel_derece')->nullable();
            $table->integer('personel_kademe')->nullable();
            $table->integer('personel_kurumid');
            $table->integer('personel_bolge');
            $table->integer('personel_kartkullanim')->default(1); // Default değer 1
            $table->integer('personel_durum')->default(1); // Default değer 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personels');
    }
};
