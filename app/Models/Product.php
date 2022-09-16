<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Product extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'category_id',
        'media_id',
        'title',
        'description',
        'divider_type',
        'unit',
        'slug',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
