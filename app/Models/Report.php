<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory;

    public function getItemsRegistered()
    {
        return DB::table('items')
            ->selectRaw('COUNT(*) AS all_items')
            ->first();
    }

    public function getPurchases()
    {
        return DB::table(DB::raw('(SELECT COUNT(*) FROM purchases WHERE uraSyncStatus = 1) AS synched,
		(SELECT COUNT(*) FROM purchases WHERE uraSyncStatus = 0) AS not_synched'))
            ->first();
    }

    public function getInvoicesFiscalised()
    {
        return DB::table(DB::raw('(SELECT COUNT(*) FROM invoices WHERE fiscalStatus = 1) AS fiscalised,
		(SELECT COUNT(*) FROM invoices WHERE fiscalStatus = 0) AS not_fiscalised'))
            ->first();
    }
}
