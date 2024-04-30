<?php

namespace App\Services\QBOServices;

use App\Models\EfrisInvoiceSearch;
use App\Traits\DataServiceConnector;
use Carbon\Carbon;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;

class QboQueryService
{
    use DataServiceConnector;
    public static function queryInvoicesOrReceipts($list, $type): array
    {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        $period = request()->input('invoice_period');
        $page = request()->input('page', 1);

        $dates = explode(' to ', $period);

        $oneMonthAgo = new DateTime('1 year ago');
        $month_ago = $oneMonthAgo->format('Y-m-d');

        $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
        $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));

        $startPosition = intval($page - 1) * 10;

        // Define query string based on type (Invoice or SalesReceipt)
        $tableName = ($type === 'invoice') ? 'Invoice' : 'SalesReceipt';
        $countQuery = "/query?query=select count(*) from " . $tableName;
        $quickbooks_items_count = (new self)->makeQuery($countQuery);
        $totalRecords = $quickbooks_items_count['QueryResponse']['totalCount'];

        $query = 'select * from ' . $tableName . ' WHERE TxnDate >= \'' . Carbon::parse($startdate)->format('Y-m-d')
            . '\' AND TxnDate <= \'' . Carbon::parse($enddate)->format('Y-m-d')
            . '\' startposition' . ' ' . $startPosition . ' maxresults 10';

        $queryString = '/query?query=' . $query;
        $quickbooks_items = (new self())->queryString($queryString);

//    dd($quickbooks_items);

        //check if we have a search query
//    if (request()->has('q')) {
//      $new_query = request()->input('q');
//      $query = "SELECT * FROM " . $tableName . " WHERE DocNumber LIKE '%" . $new_query . "%'";
//
//      $queryString = '/query?query=' . $query;
//      $quickbooks_items = (new self())->queryString($queryString);
//
//      $totalRecords = $quickbooks_items['QueryResponse']['totalCount'];
//    }

        $items = json_decode(json_encode($quickbooks_items['QueryResponse'][$tableName])) ?? [];

        $paginator = new LengthAwarePaginator($items, (int)$totalRecords, 10);

        //will test new approach
        $list_to_route_map = [
            'receipt' => [
                'all' => 'qbo.receipts.index',
                'passed' => 'qbo.receipts.passed',
                'failed' => 'qbo.receipts.failed',
                'ura' => 'qbo.receipts.ura',
            ],
            'invoice' => [
                'all' => 'qbo.invoices.all',
                'passed' => 'qbo.invoices.passed',
                'failed' => 'qbo.invoices.failed',
                'ura' => 'qbo.invoices.ura',
            ],
        ];

        $route_var = $type . '_routes'; // Create a dynamic variable name
        $routes = isset($list_to_route_map[$type][$list]) ? route($list_to_route_map[$type][$list]) : '';

// Assign the route to the appropriate variable based on $type
        $$route_var = $routes;  // Double dollar sign for variable variable assignment

        $paginator->setPath(${$type . '_routes'});
        $itemsQbo = $paginator->items();
        $items_decode = json_decode(json_encode($itemsQbo), true);

        $filteredList = [];
        $filteredList[] = ($type === 'invoice')
            ?  (new self)->filterInvoices($items_decode, $list)
            : (new self)->filterReceipts($items_decode, $list);

        $filt = $filteredList[0];


        return [
            'filteredList' =>$filt,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'date' => $period,
            'period' => $period,
            'links' => empty($filt)?null: $paginator->links(),
            'total' => count($filt)?0: $totalRecords
        ];
    }

    private function filterInvoices($invoices, $list)
    {
        switch ($list) {
            case 'failed':
            default:
                return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 0, 0);

            case 'passed':
                return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 0);

            case 'ura':
                return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 1);

            case 'all':
                return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 2, 2);
        }
    }

    private function filterReceipts($invoices, $list)
    {
        switch ($list) {
            case 'failed':
            default:
                return EfrisInvoiceSearch::findQbReceiptsByStatus($invoices, 0, 0);

            case 'passed':
                return EfrisInvoiceSearch::findQbReceiptsByStatus($invoices, 1, 0);

            case 'ura':
                return EfrisInvoiceSearch::findQbReceiptsByStatus($invoices, 1, 1);

            case 'all':
                return EfrisInvoiceSearch::findQbReceiptsByStatus($invoices, 2, 2);
        }
    }

}
