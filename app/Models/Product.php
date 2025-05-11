<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','description','price','stock','metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'price'    => 'decimal:2',
    ];
}
