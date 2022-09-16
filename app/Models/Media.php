<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Media extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'title',
        'alt',
        'link',
        'type',
        'status',
        'original_name'
    ];

    public function Product()
    {
        return $this->hasMany(Product::class);
    }
}
