<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suplier extends Model
{
    use HasFactory;
    // Menetapkan primary key yang digunakan
    protected $primaryKey = 'kdSuppliers';
    // Memberitahu bahwa primary key bertype string
    protected $keyType = 'string';
    // Menonaktifkan auto incrementing
    public $incrementing = false;
    protected $guarded = [];
}
