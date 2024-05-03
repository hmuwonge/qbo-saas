<?php

namespace App\Services\QBOServices;

use App\Traits\DataServiceConnector;

class QuickbooksApiClient
{

  use DataServiceConnector;
  public static function queryInvoiceData1000()
  {
    $queryString = '/query?query=select * from Invoice maxresults 1000&minorversion=57';
    $quickbooks_invoices = (new self())->queryString($queryString);

    return json_decode(json_encode($quickbooks_invoices), true)['QueryResponse']['Invoice']??[];
//    return json_decode(json_encode($invoices), false);
  }

    public static function queryReceiptsData1000()
    {
        $queryString = 'select * from SalesReceipt maxresults 1000';
        $qbo_receipts = (new self())->postQuery($queryString);

        return json_decode(json_encode($qbo_receipts), true)['QueryResponse']['SalesReceipt']??[];
    }

  public static function queryPurchasesData1000()
  {
    $queryString = 'select * from Bill maxresults 1000';
    $qbo_purchases = (new self())->postQuery($queryString);

    return json_decode(json_encode($qbo_purchases), true)['QueryResponse']['Bill']??[];
  }
    // get single invoice data
    public static function getSingleInvoice($id)
    {
        $id = "'" . $id . "'";
        // Constructing the SQL query with the provided $id
        $queryString = "SELECT * FROM Invoice WHERE Id={$id}";
        $quickbooks_invoices = (new self())->postQuery($queryString);
        return json_decode(json_encode($quickbooks_invoices), true)['QueryResponse'];
    }

    // get single receipts data
    public static function getSingleReceipts($id)
    {
        $id = "'" . $id . "'";
        // Constructing the SQL query with the provided $id
        $queryString = "SELECT * FROM SalesReceipt WHERE Id={$id}";
        $quickbooks_invoices = (new self())->postQuery($queryString);
        return json_decode(json_encode($quickbooks_invoices), true)['QueryResponse'];
    }

  public static function getSingleCustomerData($id)
  {
    $queryString = '/customer/'.$id;
    $quickbooks_invoices = (new self())->queryString($queryString);
//    dd($quickbooks_invoices);
    return json_decode(json_encode($quickbooks_invoices), true)['Customer']??[];
//    return json_decode(json_encode($invoices), true);
  }
}
