<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // [FITUR OPSI 1] Guru memilih saat membuat modul apakah Game HTML
            // Interaktif untuk LKPD ingin ikut disiapkan (tab baru muncul jika true).
            $table->boolean('include_game')->default(false)->after('dimensions');

            // [FITUR OPSI 2] Guru memilih saat membuat modul apakah Media Ajar /
            // Flashcard (AI Image Generation) ingin ikut disiapkan (tab baru
            // muncul jika true).
            $table->boolean('include_flashcard')->default(false)->after('include_game');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['include_game', 'include_flashcard']);
        });
    }
};
