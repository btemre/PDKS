<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pdks_gecis_turu', function (Blueprint $table) {
            $table->id('gecis_id');
            $table->string('gecis_turu')->nullable();
            $table->string('gecis_aciklama')->nullable();
            $table->enum('gecis_durum', ['0', '1'])->default('1');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pdks_gecis_turu');
    }
};
