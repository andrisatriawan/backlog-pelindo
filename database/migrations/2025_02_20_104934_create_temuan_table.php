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
        Schema::create('temuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lha_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->unsignedBigInteger('departemen_id')->nullable();
            $table->string('nomor');
            $table->string('judul');
            $table->longText('deskripsi')->nullable();
            $table->enum('status', [0, 1, 2, 3, 4])->default(0);
            $table->tinyInteger('last_stage')->default(1);
            $table->enum('deleted', [0, 1])->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('lha_id')->references('id')->on('lha')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('unit_id')->references('id')->on('unit')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('divisi_id')->references('id')->on('divisi')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('departemen_id')->references('id')->on('departemen')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuan');
    }
};
