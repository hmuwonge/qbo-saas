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
//        $tenant_id = tenant('id');
        $this->info('Fiscalising increase stock .....................' . $tenant_id);
//        if (!is_null($tenant_id)){
            $this->info('Fiscalising invoices..............');
            AutoSyncActivityServices::fiscaliseInvoices();
            AutoSyncActivityServices::validateInvoices();

            $this->info('Fiscalising receipts .....................');
            AutoSyncActivityServices::fiscaliseReceipts();

            $this->info('Fiscalising sync stock adjustments .....................');
            AutoSyncActivityServices::syncStockAdjustment();

            $this->info('Fiscalising increase stock .....................');
            AutoSyncActivityServices::increaseStock();
            AutoSyncActivityServices::validatePurchases();
//            return 0;
//        }
        $this->info('Fiscalising increase stock .....................');
        return Command::SUCCESS;
    }
}
