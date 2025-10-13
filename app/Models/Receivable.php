<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receivable_id',
        'supplier_id',
        'dealer_id',
        'code',
        'bilti_no',
        'bags',
        'rate',
        'freight',
        'tons',
        'total',
        'payment_type',
        'proof_of_payment',
    ];

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function payment()
{
    return $this->belongsTo(ReceivablePayment::class, 'receivable_payment_id');
}

}
