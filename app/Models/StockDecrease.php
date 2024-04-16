<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDecrease extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjust_type',
        'adjust_reason',
        'transact_id',
    ];
}
