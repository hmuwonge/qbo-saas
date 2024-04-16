<?php

namespace App\Services;

use App\Models\QuickBooksItemsDatatable;
use App\Models\TaxpayerConfig;

class QuickBooksServiceHelper
{
    public $tin;

    public function __construct()
    {
        if (app()->runningInConsole()) {
            $this->tin = config('app.tin');
        } else {
            $this->tin = session('TIN');
        }
    }

    public static function calculateTaxInclusivePrice($unitPrice, $taxCode)
    {
        $quickbooksConfig = config('quickbooks.taxpayerConfig');
        $finalPrice = 0;

        if ($taxCode == $quickbooksConfig['quickbooks_taxinclusive_taxcoderef']) {
            $finalPrice = round((0.18 * $unitPrice) + $unitPrice, 4);
        } else {
            $finalPrice = $unitPrice;
        }

        //@TODO. Round of value to two decimal places
        return $finalPrice;
    }

    /**
     * The QB API for this company
     */
    public function apiUrl()
    {
        $quickbooksConfig = config('quickbooks.taxpayerConfig');

        return $quickbooksConfig['quickbooks_api_url'];
    }

    /**
     * QB API links for the different clients
     */
    protected function clientsQbApis(): array
    {
        return [
            1001302007 => 'https://mwh-quickbooks-live-api.kakasa.app',
            1015650841 => 'https://kingdomtrading-quickbooks-online-live.kakasa.app',
            1007473185 => 'https://jibu-quickbooks-api.kakasa.app',
            1007546028 => 'https://gasnmore-api-live.kakasa.app',
            1022933107 => 'https://babu-enterprises.kakasa.app',
            1000021003 => 'https://livercot.kakasa.app', //Livercot,
            1000156178 => 'https://sharesuganda.kakasa.app', //Shares Uganda
            1000112269 => 'https://swangsavenue.kakasa.app', //Swangs Avenue
            1000030306 => 'https://huskyoutdoor.kakasa.app', //Husky Outdoor
            1010014782 => 'https://bluecranecom.kakasa.app', //Blue Crane Communications
            1017106355 => 'https://abarrane.kakasa.app', //Abarrane
            1002205695 => 'https://khambati.kakasa.app', //Khambati
            1000293121 => 'https://itcug.kakasa.app', //ITC
            1000026633 => 'https://greatlakescoffee.kakasa.app', //Great Lakes coffee
            1006956441 => 'https://africadataedge.kakasa.app', //Africa Data Edge
            1000467400 => 'https://buzzevents.kakasa.app', //Buzz Events
            1008121763 => 'https://kenoils.kakasa.app', //KenOils
            1000023654 => 'https://panyahululu.kakasa.app', //Panyahululu
            1009966154 => 'https://sinotextile.kakasa.app', //Sino Textile
        ];
    }

    /**
     * Prepare a list of items for display using DataTables
     *
     * @param  array  $qbInvoices
     */
    public static function prepareItemList($dbItems, $efris, $qbItems): array
    {
        $listOfItems = [];
        $registered = collect($efris)->pluck('goodsCode')->toArray();
        foreach ($qbItems as $item) {
            $_id = $item['Id'];
            $_sku = $item['Sku'] ?? null;
            $dbRec = $dbItems[$_id] ?? null;
            $is_registered = in_array($_sku, $registered) ? 'YES' : 'NO';
            if ($is_registered === 'NO') {
                $efris_item = ['stock' => 0];
                $listOfItems[] = new QuickBooksItemsDatatable($dbRec, $efris_item, $item);
            } else {
                $efris_item = collect($efris)->firstWhere('goodsCode', $_sku);
                $listOfItems[] = new QuickBooksItemsDatatable($dbRec, $efris_item, $item);
            }
        }

        return $listOfItems;
    }

    public function getPlaceOfBusiness(): void
    {
    }

    public function getOperator()
    {
        return auth()->user();
    }

    public function taxpayerConfig()
    {
        return auth()->user()->company->config;
    }

    public function getConfig(): array
    {
        return [];
    }

    /**
     * Get the config when all we have is TIN
     * Useful when calling methods from the terminal
     */
    public function getConfigByTin()
    {
        return TaxpayerConfig::where(['tin' => $this->tin])->first();
    }

    public static function logToFile($data, $desc = ''): bool
    {
        $filename = date('Ymd');
        $logfile = storage_path("logs/{$filename}.log");

        //Current Log time
        $activity = empty($desc) ? '' : ($desc.' | ');
        $timenow = $activity.date('Y-m-d h:i:sA').' |-------------------------------------------------------------------';
        file_put_contents($logfile, "\n".$timenow, FILE_APPEND);

        //Log Contents, append to already existing info
        file_put_contents($logfile, "\n".print_r($data, true), FILE_APPEND);

        return true;
    }
}
