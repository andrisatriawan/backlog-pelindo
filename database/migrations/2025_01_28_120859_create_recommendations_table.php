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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finding_id');
            $table->string('no_reg');
            $table->string('title');
            $table->date('due_date');
            $table->string('status');
            $table->enum('is_deleted', [0, 1])->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('finding_id')->references('id')->on('findings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
