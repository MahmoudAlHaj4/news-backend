<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $table = 'about_section';

    protected $fillable = [
        'logo',
        'banner',
        'about_section',
        'whatsapp',
        'instagram',
        'facebook',
        'twitter',
    ];
}
