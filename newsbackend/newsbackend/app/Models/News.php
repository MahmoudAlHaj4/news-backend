<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'link',
        'date'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Optional: Scope for ordering by date
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }
}