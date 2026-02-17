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
    Schema::create('ayar', function (Blueprint $table) {
        $table->id('ayar_id'); // AI olan ID
        $table->string('ayar_logo', 250)->nullable();
        $table->string('ayar_url', 50)->nullable();
        $table->string('ayar_title', 250)->nullable();
        $table->string('ayar_description', 250)->nullable();
        $table->string('ayar_bolge_adi', 50)->nullable();
        $table->string('ayar_bakanlik', 50)->nullable();
        $table->string('ayar_keywords', 250)->nullable();
        $table->string('ayar_author', 250)->nullable();
        $table->string('ayar_tel', 50)->nullable();
        $table->string('ayar_gsm', 50)->nullable();
        $table->string('ayar_faks', 50)->nullable();
        $table->string('ayar_mail', 50)->nullable();
        $table->string('ayar_ilce', 50)->nullable();
        $table->string('ayar_il', 50)->nullable();
        $table->string('ayar_adres', 250)->nullable();
        $table->string('ayar_mesai', 250)->nullable();
        $table->string('ayar_maps', 250)->nullable();
        $table->string('ayar_analystic', 50)->nullable();
        $table->string('ayar_zopim', 250)->nullable();
        $table->string('ayar_facebook', 50)->nullable();
        $table->string('ayar_twitter', 50)->nullable();
        $table->string('ayar_google', 50)->nullable();
        $table->string('ayar_youtube', 50)->nullable();
        $table->string('ayar_smtphost', 50)->nullable();
        $table->string('ayar_smtpuser', 50)->nullable();
        $table->string('ayar_smtppassword', 50)->nullable();
        $table->string('ayar_smtpport', 50)->nullable();
        $table->string('ayar_versiyon', 50)->nullable();
        $table->enum('ayar_bakim', ['0', '1'])->default('1')->nullable();
        $table->string('ayar_yonetici', 25)->nullable();
        $table->string('ayar_kurum', 50)->nullable();
        $table->string('ayar_yoneticiunvan', 255)->nullable();
        $table->string('ayar_mudur', 255)->nullable();
        $table->string('ayar_mudurunvan', 255)->nullable();
        $table->string('ayar_basmuhendisunvan', 50)->nullable();
        $table->string('ayar_basmuhendis', 50)->nullable();
        $table->string('ayar_duzenleyen', 25)->nullable();
        $table->char('ayar_duzenleyenip', 25)->nullable();
        $table->string('ayar_baslik', 25)->nullable();
        $table->integer('ayar_kurumid')->nullable();
        $table->timestamps(); // created_at ve updated_at
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayar');
    }
};
