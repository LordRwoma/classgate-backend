<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // generated_content (sudah ada) dipakai khusus untuk Modul Ajar / Lesson Plan.
            $table->longText('generated_lkpd')->nullable()->after('generated_content');
            $table->longText('generated_assessment')->nullable()->after('generated_lkpd');
            $table->text('generated_tips')->nullable()->after('generated_assessment'); // AI Pedagogical Recommender
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['generated_lkpd', 'generated_assessment', 'generated_tips']);
        });
    }
};
