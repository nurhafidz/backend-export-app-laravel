<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class ChildProduct extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'media_id',
        'product_id',
        'title',
        'price',
        'details',
        'description',
        'location',
        'minimum',
        'status',
    ];

    public function medias()
    {
        return $this->belongsToMany(Media::class);
    }
    public function parentproduct()
    {
        return $this->belongsTo(Product::class);
    }
}
