<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockOut extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'uuid';
    // Memberitahu bahwa primary key bertype string
    protected $keyType = 'string';
    // Menonaktifkan auto incrementing
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'KdProduct');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}
