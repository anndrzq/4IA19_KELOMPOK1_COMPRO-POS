<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RefundsDetail extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function refund()
    {
        return $this->belongsTo(Refunds::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'KdProduct', 'KdProduct');
    }
}
