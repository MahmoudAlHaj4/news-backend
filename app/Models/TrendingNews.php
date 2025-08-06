<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrendingNews extends Model
{
    use HasFactory;

    protected $table = 'trending_news';

    protected $fillable = [
        'title',
        'link',
        'image',
    ];
}
