<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'site_name_ar' => 'سمارت فيجن',
            'site_name_en' => 'Smart Vision',
            'phone' => '+971500000000',
            'whatsapp' => '+971500000000',
            'email' => 'info@smartvision.test',
            'address_ar' => 'دبي، الإمارات العربية المتحدة',
            'address_en' => 'Dubai, United Arab Emirates',
            'logo' => 'images/logo.png',
            'facebook' => '#',
            'instagram' => '#',
            'x' => '#',
            'linkedin' => '#',
            'tiktok' => '#',
            'youtube' => '#',
        ];

        foreach ($data as $k => $v) {
            Setting::setValue($k, $v);
        }
    }
}
