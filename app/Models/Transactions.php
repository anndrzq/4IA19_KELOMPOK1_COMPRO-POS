<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'invoice_number';
    protected $keyType = 'string';
    public $incrementing = false;

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'invoice_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Members::class, 'membership_id');
    }
}
