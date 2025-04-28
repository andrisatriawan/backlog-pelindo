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
        Schema::table('departemen', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('divisi', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('files', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('jabatan', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('lha', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
            $table->char('status', 1)->default('0')->change();
        });
        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
            $table->char('status', 1)->default('0')->change();
        });
        Schema::table('stage', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('temuan', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
            $table->char('status', 1)->default('0')->change();
        });
        Schema::table('tindaklanjut', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('unit', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->char('deleted', 1)->default('0')->change();
            $table->char('is_active', 1)->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('char', function (Blueprint $table) {
            //
        });
    }
};
