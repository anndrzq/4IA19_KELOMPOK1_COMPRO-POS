<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'KdCategory';
    protected $keyType = 'string';
    public $incrementing = false;
    public function products()
    {
        return $this->hasMany(Product::class, 'KdCategory', 'KdProduct');
    }
}
