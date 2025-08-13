<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSection;

class AboutSectionSeeder extends Seeder
{
    public function run()
    {
        AboutSection::create([
            'logo' => 'path/to/default-logo.png',
            'banner' => 'path/to/default-banner.jpg',
            'about_section' => 'Write your default about section text here.',
            'whatsapp' => 'https://wa.me/123456789',
            'instagram' => 'https://instagram.com/',
            'facebook' => 'https://facebook.com/',
            'twitter' => 'https://twitter.com/',
        ]);
    }
}
