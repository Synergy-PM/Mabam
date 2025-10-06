<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayablePayment extends Model
{ 
    use SoftDeletes;

   protected $fillable  = [
    'payable_id',
    'supplier_id',
    'transaction_date',
    'amount',   // yeh correct hoga
    'transaction_type',
    'payment_mode',
    'proof_of_payment',
    'notes'
];


        public function payable()
    {
        return $this->belongsTo(Payable::class);
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
}
