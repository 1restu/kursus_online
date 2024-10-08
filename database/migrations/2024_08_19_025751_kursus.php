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
        Schema::create('kursus', function(Blueprint $table) {
            $table->id();
            $table->string('nama_krs');
            $table->string('gambar');
            $table->text('deskripsi');
            $table->decimal('biaya_krs');
            $table->string('durasi');
            $table->integer('jam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kursus');
    }
};
