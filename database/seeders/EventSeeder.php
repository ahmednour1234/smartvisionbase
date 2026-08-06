<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $nameEn = 'Smart Vision Summit Dubai';
        Event::create([
            'name_ar' => 'قمة سمارت فيجن دبي',
            'name_en' => $nameEn,
            'slug' => Str::slug($nameEn),
            'image' => null,
            'start_at' => Carbon::now()->addMonth(),
            'end_at' => Carbon::now()->addMonth()->addDay(),
            'website_url_ar' => 'https://smartvisionsummit.com/ar',
            'website_url_en' => 'https://smartvisionsummit.com',
            'is_active' => true,
            'is_featured' => true,
        ]);
    }
}
