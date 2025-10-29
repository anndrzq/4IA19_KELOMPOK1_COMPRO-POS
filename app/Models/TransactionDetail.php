<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id', 'invoice_number');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'KdProduct', 'KdProduct');
    }
}
