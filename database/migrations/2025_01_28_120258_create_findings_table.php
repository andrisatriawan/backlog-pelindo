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
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('division_id');
            $table->string('no_reg');
            $table->string('title');
            $table->text('summary');
            $table->enum('is_approve', [0, 1])->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->enum('is_deleted', [0, 1])->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
