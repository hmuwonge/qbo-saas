<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsAndServices extends Model
{
    use HasFactory;

    protected $casts = [
        'payment_method_ref' => 'array',
        'income_account_ref' => 'array',
        'expense_account_ref' => 'array',
        'COGS_account_ref' => 'array',
        'asset_account_ref' => 'array',
        'deposit_to_account_ref' => 'array',
        'sales_tax_code_ref' => 'array',
        'purchase_tax_code_ref' => 'array',
        'tax_classification_ref' => 'array',
        'class_ref' => 'array',
        'pref_vendor_ref' => 'array',
        'meta_data' => 'array',
    ];
}
