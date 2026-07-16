<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Smart Assessment Builder: 3 jenis asesmen spesifik
            $table->enum('assessment_type', ['sumatif', 'formatif', 'diagnostik'])
                ->default('sumatif')->after('learning_objectives');

            // 8 Dimensi Profil Lulusan yang dipilih guru (checkbox, disimpan array key)
            $table->json('dimensions')->nullable()->after('assessment_type');

            // Adaptive Worksheet: LKPD versi Game HTML interaktif (terpisah dari
            // generated_lkpd yang berupa teks/HTML statis)
            $table->longText('generated_lkpd_game')->nullable()->after('generated_lkpd');

            // Media Ajar: array {type: flashcard|image, label, url}
            $table->json('generated_media')->nullable()->after('generated_tips');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['assessment_type', 'dimensions', 'generated_lkpd_game', 'generated_media']);
        });
    }
};
