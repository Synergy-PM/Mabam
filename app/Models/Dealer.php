<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealer_name',
        'company_name',
        'city_id',
        'email',
        'whatsapp',
        'contact_person',
        'contact_no',
        'contact_email',
        'address',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reference_id')->where('type', 'dealer');
    }
     public function receivablePayments()
    {
        return $this->hasMany(ReceivablePayment::class, 'dealer_id');
    }
}
