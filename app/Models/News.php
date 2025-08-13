<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'main_image', 'content', 'category_id', 'published_at', 'add_to_tinker',
    'priority',];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
