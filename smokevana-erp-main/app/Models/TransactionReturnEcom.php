<?php

namespace App\Models;

use App\Product;
use App\Transaction;
use App\TransactionSellLine;
use App\Variation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionReturnEcom extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variations()
    {
        return $this->belongsTo(Variation::class);
    }
    public function parent_sell_line()
    {
        return $this->belongsTo(TransactionSellLine::class, 'parent_sell_line_id');
    }
}
