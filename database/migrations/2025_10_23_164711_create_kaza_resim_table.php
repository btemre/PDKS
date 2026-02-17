<?php

// xxxx_xx_xx_xxxxxx_create_kaza_resim_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kaza_resim', function (Blueprint $table) {
            $table->id('resim_id'); // Resim için primary key
            $table->unsignedBigInteger('kaza_id'); // İlişkili kaza ID'si
            $table->string('resim_yolu'); // Resmin sunucudaki yolu
            $table->timestamps();

            // kaza_id'yi kazalar tablosunun kaza_id'sine bağlayan foreign key
            $table->foreign('kaza_id')->references('kaza_id')->on('kazalar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kaza_resim');
    }
};