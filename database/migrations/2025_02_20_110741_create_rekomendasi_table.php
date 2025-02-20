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
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temuan_id');
            $table->string('nomor');
            $table->longText('deskripsi');
            $table->date('batas_tanggal');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', [0, 1, 2, 3])->default(0); // 0: BD, 1:BS, 2:Selesai, 3:TPTD
            $table->enum('deleted', [0, 1])->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('temuan_id')->references('id')->on('temuan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
