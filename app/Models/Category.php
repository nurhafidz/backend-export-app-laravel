<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Category extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'title',
        'type',
        'parent_id',
    ];

    public function __get($key)
    {
        return $this->getAttribute($key);
    }



    public function child()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
