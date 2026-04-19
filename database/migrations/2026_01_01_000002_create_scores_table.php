<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->integer('akhlak_nilai')->nullable();
            $table->string('akhlak_predikat', 2)->nullable();
            $table->integer('disiplin_nilai')->nullable();
            $table->string('disiplin_predikat', 2)->nullable();
            $table->integer('tanggung_jawab_nilai')->nullable();
            $table->string('tanggung_jawab_predikat', 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
