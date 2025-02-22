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
        Schema::create('log_temuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temuan_id')->nullable();
            $table->longText('properties');
            $table->text("keterangan");
            $table->string("aksi");
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_temuan');
    }
};
