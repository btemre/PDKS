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
        Schema::create('tunel_jetfan', function (Blueprint $table) {
            $table->id();
            $table->string('jetfan_ad', 25);
            $table->integer('jetfan_bina')->default(0);
            $table->integer('jetfan_tunel')->default(0);

            // Foreign key alanları
            $table->unsignedBigInteger('jetfan_scadatest')->nullable();
            $table->unsignedBigInteger('jetfan_fizikseltest')->nullable();

            $table->string('jetfan_aciklama', 250)->nullable();
            $table->dateTime('jetfan_tarih')->nullable();
            $table->integer('jetfan_bolge')->default(0);
            $table->integer('jetfan_kurum')->default(0);
            $table->integer('jetfan_birim')->default(0);
            $table->date('jetfan_montajtarih')->nullable();
            $table->date('jetfan_demontajtarih')->nullable();
            $table->string('jetfan_panoyonu', 3)->nullable();
            $table->enum('jetfan_durum', [0, 1])->default(1);
            $table->timestamps();

            // İlişkiler
            $table->foreign('jetfan_scadatest')
                ->references('test_id')->on('tunel_jetfantesttur')
                ->onDelete('set null');

            $table->foreign('jetfan_fizikseltest')
                ->references('test_id')->on('tunel_jetfantesttur')
                ->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunel_jetfan');
    }
};
