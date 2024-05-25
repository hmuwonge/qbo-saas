<?php

namespace App\Console\Commands;

use App\Services\AutoSyncActivityServices;
use Illuminate\Console\Command;

class FiscaliseInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fiscalise-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        AutoSyncActivityServices::fiscaliseInvoices();
        AutoSyncActivityServices::fiscaliseReceipts();
        AutoSyncActivityServices::syncStockAdjustment();
        AutoSyncActivityServices::increaseStock();
        return 0;
    }
}
