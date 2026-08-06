<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    public function run(): void
    {
        AboutUs::create([
            'title_ar' => 'من نحن',
            'title_en' => 'About Smart Vision',
            'content_ar' => 'سمارت فيجن شركة متخصصة في تنظيم المعارض والمؤتمرات...',
            'content_en' => 'Smart Vision is a leading company specialized in organizing exhibitions and summits...',
            'image' => null,
            'is_active' => true,
        ]);
    }
}
