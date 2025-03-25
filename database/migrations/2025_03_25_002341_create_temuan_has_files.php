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
        Schema::create('temuan_has_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temuan_id');
            $table->unsignedBigInteger('file_id');
            $table->timestamps();

            $table->foreign('temuan_id')->references('id')->on('temuan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuan_has_files');
    }
};
