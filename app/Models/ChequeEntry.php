<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChequeEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cheque_entries';

    protected $fillable = [
        'date',
        'party_type',
        'party_id',
        'expense_description',
        'credit',
        'debit',
        'payment_type',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'party_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'party_id');
    }

    public function getPartyNameAttribute()
    {
        if ($this->party_type === 'expense') {
            return $this->expense_description ?? 'Expense';
        } elseif ($this->party_type === 'supplier') {
            return $this->supplier?->supplier_name ?? 'N/A';
        } elseif ($this->party_type === 'dealer') {
            return $this->dealer?->dealer_name ?? 'N/A';
        }

        return 'N/A';
    }
}
