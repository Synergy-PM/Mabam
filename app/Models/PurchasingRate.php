<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasingRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_date',
        'to_date',
        'supplier_id',
        'city_id',
        'amount_per_ton',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

