<?php

namespace Modules\QuickbooksDashboard\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\EfrisItem;
use Illuminate\Http\Request;
use App\Models\AutoSyncActivity;
use App\Models\QuickBooksInvoice;
use App\Models\QuickBooksPurchase;
use App\Services\ApiRequestHelper;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\QuickbooksDashboard\Services\DashboardServices;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class QuickbooksDashboardController extends Controller
{
    protected $dashboardServices;
    public function __construct(DashboardServices $dashboardServices)
    {
        $this->dashboardServices = $dashboardServices;
    }

  /**
   * Display a listing of the resource.
   * @return Renderable
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
    public function index()
    {
        $errors = AutoSyncActivity::where('msg_category', 'ERROR')->with('autoSync')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $page = request()->get('page', 1);
        $query = [
            'invoiceKind' => 2,
            'invoiceType' => 1,
            'pageNo' => $page,
            'pageSize' => 20,
        ];

        $fiscal_efris_invoices = (new ApiRequestHelper('efris1'))->makePost('invoice-receipt-query', $query) ?? [];
        $init = [];

        $inv_count = count($init);
        $purchase_count = $this->getPurchases();
        $invoice_count = QuickBooksInvoice::all()->count();

        $invoices_not_fiscalised = QuickBooksInvoice::where('fiscalStatus', 0)->count();
        $invoices_fiscalised = QuickBooksInvoice::where('fiscalStatus', 1)->count();

        $counters = [
            'efrisInvoices' => $inv_count,
            'purchases' => $purchase_count,
            'invoice_count' => $invoice_count,
            'invoices_fiscalised' => $invoices_fiscalised,
            'invoice_not_fiscalised' => $invoices_not_fiscalised,
            'items' => EfrisItem::getItemsRegistered(),
        ];

        $latestInvoices = self::getLatestFiscalisedInvoices() ?? [];

        $non = $this->dashboardServices->fiscalInvoiceCount('year');
        $non1 = $this->dashboardServices->notFiscalised();

        return view('quickbooksdashboard::index', compact('errors', 'counters', 'latestInvoices', 'non','non1'));
    }


    private function getPurchases()
    {
        $data = QuickBooksPurchase::where('uraSyncStatus', 0)->count();
        return $data;
    }

    private static function getLatestFiscalisedInvoices()
    {
        return QuickBooksInvoice::where('fiscalStatus', 1)->latest()->take(10)->get();
    }


}
