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
        Schema::table('files', function (Blueprint $table) {
            $table->boolean('is_spi')->nullable();
        });

        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->boolean('is_spi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('is_spi');
        });

        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->dropColumn('is_spi');
        });
    }
};
