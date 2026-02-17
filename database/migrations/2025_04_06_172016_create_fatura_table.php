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
        Schema::create('fatura', function (Blueprint $table) {
            $table->integer('fatura_id')->autoIncrement();
            $table->integer('fatura_abone');
            $table->integer('fatura_beslendigiyer')->nullable();
            $table->integer('fatura_kurum');
            $table->integer('fatura_bolge');
            $table->integer('fatura_tur');
            $table->string('fatura_donem', 25)->nullable(); // 2025-04 gibi
            $table->string('fatura_aciklama', 150)->nullable();
            $table->string('fatura_sayacad', 50)->nullable();
            $table->string('fatura_sayacserino', 50)->nullable();
            $table->date('fatura_ilkokumatarih')->nullable();
            $table->date('fatura_sonokumatarih')->nullable();
            $table->date('fatura_duzenlemetarih')->nullable();
            $table->date('fatura_sonodemetarih')->nullable();
            $table->decimal('fatura_t1_ilk',10,3)->nullable();
            $table->decimal('fatura_t2_ilk',10,3)->nullable();
            $table->decimal('fatura_t3_ilk',10,3)->nullable();
            $table->decimal('fatura_t1_son',10,3)->nullable();
            $table->decimal('fatura_t2_son',10,3)->nullable();
            $table->decimal('fatura_t3_son',10,3)->nullable();
            $table->decimal('fatura_toplam',10,3)->nullable();
            $table->decimal('fatura_enerjibirim', 10, 6)->nullable();
            $table->decimal('fatura_dagitimbirim', 10, 6)->nullable();
            $table->decimal('fatura_tuketimvergi', 10, 2)->nullable();
            $table->decimal('fatura_kdv', 10, 2)->nullable();
            $table->decimal('fatura_geneltoplam', 10, 2)->nullable();
            $table->tinyInteger('fatura_durum')->default(1);
            $table->tinyInteger('fatura_odemedurum')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura');
    }
};
