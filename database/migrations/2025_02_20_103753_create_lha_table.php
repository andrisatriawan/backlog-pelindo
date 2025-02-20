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
        Schema::create('lha', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('no_lha');
            $table->string('judul');
            $table->string('periode');
            $table->longText('deskripsi');
            $table->enum('status', [0, 1, 2, 3])->default(0); //'draf', 'proses', 'tolak', 'selesai'
            $table->tinyInteger('last_stage')->default(0);
            $table->enum('deleted', [0, 1])->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lha');
    }
};
