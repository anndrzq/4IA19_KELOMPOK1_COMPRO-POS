<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    // Memberitahu bahwa primary key bertype string
    protected $keyType = 'string';
    // Menonaktifkan auto incrementing
    public $incrementing = false;

    protected $guarded = [];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}
