<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personel_kart_gecmisi', function (Blueprint $table) {
            $table->id();
            $table->integer('personel_id');
            $table->unsignedBigInteger('kart_id')->nullable(); // kart yoksa null olabilir
            $table->date('baslangic_tarihi');
            $table->date('bitis_tarihi')->nullable(); // devam ediyorsa null
            $table->tinyInteger('durum')->default(1);
            $table->timestamps();
            $table->foreign('personel_id')->references('personel_id')->on('personel')->onDelete('cascade');
            $table->foreign('kart_id')->references('kart_id')->on('pdks_kartlar')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_kart_gecmisi');
    }
};
