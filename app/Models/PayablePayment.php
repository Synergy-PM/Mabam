<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayablePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'transaction_date',
        'transaction_type',
        'amount',
        'payment_mode',
        'proof_of_payment',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
