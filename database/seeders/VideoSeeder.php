<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['title' => 'Official Highlights 2024', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'section' => 'Highlights', 'sort_order' => 1],
            ['title' => 'Keynote Address', 'youtube_url' => 'https://youtu.be/ysz5S6PUM-U', 'section' => 'Highlights', 'sort_order' => 2],
            ['title' => 'Speakers Reel', 'youtube_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 'section' => 'Speakers', 'sort_order' => 1],
        ];
        foreach ($samples as $v) {
            Video::query()->firstOrCreate(['youtube_url' => $v['youtube_url']], $v + ['is_active' => true]);
        }
    }
}


