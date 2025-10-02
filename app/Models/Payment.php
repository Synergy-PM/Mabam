<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receivable_id',
        'dealer_id',
        'bilti_no',
        'bags',
        'rate',
        'freight',
        'tons',
        'total',
        'payment_type',
        'proof_of_payment',
    ];

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }
}
