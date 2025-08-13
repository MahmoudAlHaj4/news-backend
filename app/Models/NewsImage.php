<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
public function images()
{
    return $this->hasMany(NewsImage::class);
}

}
