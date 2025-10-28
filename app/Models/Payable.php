<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    protected $fillable = [
        'transaction_date',
        'supplier_id',
        'no_of_bags',
        'amount_per_bag',
        'total_amount',
        'tons',
        'bilti_no',
        'truck_no',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

        public function payments()
    {
        return $this->hasMany(PayablePayment::class, 'payable_id');
    }


    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->payments->sum('amount_paid');
    }
}
