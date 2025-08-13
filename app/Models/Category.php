<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['key', 'display_name'];

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
