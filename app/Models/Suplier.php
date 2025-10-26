<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suplier extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'kdSuppliers';
    protected $keyType = 'string';
    public $incrementing = false;

    public function StockIn()
    {
        return $this->hasMany(StockIn::class, 'uuid');
    }
}
