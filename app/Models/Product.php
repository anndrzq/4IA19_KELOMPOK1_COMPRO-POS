<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'KdProduct';
    protected $keyType = 'string';
    public $incrementing = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'KdCategory', 'KdCategory');
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class, 'KdUnit', 'KdUnit');
    }

    public function StockIn()
    {
        return $this->hasMany(StockIn::class, 'uuid');
    }

    public function StockOut()
    {
        return $this->hasMany(StockIn::class, 'uuid');
    }
}
