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
        Schema::table('scores', function (Blueprint $table) {
            $table->integer('ibadah_nilai')->nullable()->after('tanggung_jawab_predikat');
            $table->string('ibadah_predikat', 2)->nullable()->after('ibadah_nilai');
            $table->integer('kepemimpinan_nilai')->nullable()->after('ibadah_predikat');
            $table->string('kepemimpinan_predikat', 2)->nullable()->after('kepemimpinan_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn(['ibadah_nilai', 'ibadah_predikat', 'kepemimpinan_nilai', 'kepemimpinan_predikat']);
        });
    }
};
