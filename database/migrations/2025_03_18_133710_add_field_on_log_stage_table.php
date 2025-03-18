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
        Schema::table('log_stage', function (Blueprint $table) {
            $table->unsignedBigInteger('lha_id')->nullable()->change();
            $table->string('nama')->nullable();
            $table->string('action')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_stage', function (Blueprint $table) {
            $table->unsignedBigInteger('lha_id')->nullable(false)->change();
            $table->dropColumn('nama');
            $table->dropColumn('action');
            $table->dropColumn('model_id');
            $table->dropColumn('model_type');
        });
    }
};
