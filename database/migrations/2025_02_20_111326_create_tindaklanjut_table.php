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
        Schema::create('tindaklanjut', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rekomendasi_id');
            $table->longText('deskripsi');
            $table->date('tanggal');
            $table->enum('deleted', [0, 1])->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('rekomendasi_id')->references('id')->on('rekomendasi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindaklanjut');
    }
};
