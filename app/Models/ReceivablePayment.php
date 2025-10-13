<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivablePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id',
        'transaction_date',
        'amount_received',
        'payment_mode',
        'transaction_type',
    ];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }
public function payable()
{
    return $this->belongsTo(Payable::class);
}

}
