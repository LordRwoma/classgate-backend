<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject', 'phase', 'topic', 'duration', 'learning_objectives',
        'assessment_type', 'dimensions', 'include_game', 'include_flashcard',
        'generated_content', 'generated_lkpd', 'generated_lkpd_game',
        'generated_assessment', 'generated_tips', 'generated_media',
        'status',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'generated_media' => 'array',
        'include_game' => 'boolean',
        'include_flashcard' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * [FIX BUG 1] `generated_media[]['url']` disimpan sebagai PATH RELATIF
     * ("media-ajar/uuid.png") oleh ImageGenerationService, BUKAN URL utuh.
     * Saat request generate berlangsung, controller mengembalikan URL utuh
     * lewat asset() di response JSON sehingga gambar yang BARU dibuat tampak
     * normal — tapi begitu halaman di-refresh/dibuka ulang, Alpine memuat
     * ulang array ini langsung dari kolom DB (path relatif apa adanya),
     * sehingga <img :src="item.url"> mencoba memuat path relatif terhadap
     * URL halaman saat ini (mis. /modul/5/media-ajar/xxx.png) -> 404 -> hanya
     * teks alt yang tampil. Accessor ini menjadi satu-satunya sumber
     * kebenaran: SELALU mengembalikan URL publik yang valid, di mana pun
     * data itu dipakai (tab Media Ajar, gallery LKPD/Asesmen, atau PDF).
     */
    public function getGeneratedMediaUrlsAttribute(): array
    {
        return collect($this->generated_media ?? [])
            ->map(function ($item) {
                $path = $item['url'] ?? '';
                $item['url'] = preg_match('/^https?:\/\//i', $path)
                    ? $path
                    : \Storage::disk('public')->url($path);
                return $item;
            })
            ->values()
            ->all();
    }
}
