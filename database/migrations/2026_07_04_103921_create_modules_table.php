<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke tabel guru (users)
            $table->string('subject'); // Mata Pelajaran
            $table->string('phase'); // Fase Kurikulum Merdeka (A-F)
            $table->string('topic'); // Topik/Bab
            $table->string('duration'); // Alokasi Waktu
            $table->text('learning_objectives'); // Tujuan Pembelajaran
            $table->longText('generated_content')->nullable(); // Hasil AI akan disimpan di sini nanti
            $table->string('status')->default('draft'); // draft, processing, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
