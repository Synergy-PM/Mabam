<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_date',
        'bilti_no',
        'truck_no',       
        'no_of_bags',
        'amount_per_bag',
        'tons',
        'total_amount',
    ];

    protected $dates = ['transaction_date','deleted_at'];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'transaction_id');
    }
}
