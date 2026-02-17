<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdks_cihaz_gecisler', function (Blueprint $table) {
            $table->index('gecis_tarihi');
            $table->index('kart_id');
            $table->index(['gecis_tarihi', 'kart_id']);
        });

        Schema::table('personel', function (Blueprint $table) {
            $table->index('personel_birim');
            $table->index('personel_bolge');
        });
    }

    public function down(): void
    {
        Schema::table('pdks_cihaz_gecisler', function (Blueprint $table) {
            $table->dropIndex(['gecis_tarihi']);
            $table->dropIndex(['kart_id']);
            $table->dropIndex(['gecis_tarihi', 'kart_id']);
        });

        Schema::table('personel', function (Blueprint $table) {
            $table->dropIndex(['personel_birim']);
            $table->dropIndex(['personel_bolge']);
        });
    }
};
