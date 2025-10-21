<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opening_balance',
        'supplier_name',
        'city_id',
        'email',
        'whatsapp',
        'address',
        'contact_person',
        'contact_no',
        'contact_email'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reference_id')->where('type', 'supplier');
    }
        public function payablePayments()
    {
        return $this->hasMany(PayablePayment::class, 'supplier_id');
    }

}
