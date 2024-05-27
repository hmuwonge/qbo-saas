<?php

namespace App\Services;

use App\Models\EfrisItem;
use App\Traits\DataServiceConnector;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class EfrisItemsService
{
    use DataServiceConnector;

    /**
     * Sync LocalDatabase with EFRIS platform
     */
    public function syncItems()
    {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action
        //Quickbooks Items
        $qb_items = $this->urlQueryBuilderAll('item');
      $queryString = "/query?query=select * from Item maxresults 1000&minorversion=57";
      $quickbooks_invoices = $this->queryString($queryString);

    //   dd($quickbooks_invoices);
      // JSON decode
      $db_items_and_services = json_decode(json_encode($quickbooks_invoices['QueryResponse']['Item']),true);
        $indexed = collect($db_items_and_services)->keyBy('Sku');

        // //URA Items
        // Initialize variables
        $pageNumber = 1;
        $efris = new ApiRequestHelper('efris1');
        // Initialize variables
        // Fetch first page data to determine total pages
        $query = [
            'pageSize' => 99,
            'pageNo' => 1,
        ];

        $efris_response = $efris->makePost('goods-and-services', $query);
        $ef_items = json_decode($efris_response, true);

        // Extract total pages from first page response
        $totalPages = $ef_items['data']['page']['pageCount'];

        $efris_indexed = collect($ef_items['data']['records']);

        // Process remaining pages if necessary
        while ($pageNumber < $totalPages) {
            $pageNumber++;

            $query = [
                'pageSize' => 99,
                'pageNo' => $pageNumber,
            ];

            $efris_response = $efris->makePost('goods-and-services', $query);
            $ef_items = json_decode($efris_response, true);

            $efris_indexed = $efris_indexed->merge($ef_items['data']['records']);
        }

//        dd($efris_indexed);


        // perform a batch insert of matched data
        EfrisItem::batchInsert($efris_indexed, $indexed);

        return redirect()->back()->with('success', 'You have successfully synced goods and services with EFRIS platform');
    }

    public function actionModifyRegisteredProduct($id)
    {
        $api = new ApiRequestHelper('qb');
        $efris_api = new ApiRequestHelper('efris1');
        $item = $api->makeGet('qb/item-details/' . $id);

        if (Request::isMethod('post')) {
            $posted = Request::post();
            $form = $posted['EfrisItem'];

            //Sync products
            $efris_api->makePost('sync-products', []);
            $product = EfrisItem::prepareModifyStockObject($form);
            $stockIn_response = $efris_api->makePost('register-product', $product);

            $efrisdb = EfrisItem::find($id);
            $efrisdb->fill($posted);

            if ($efrisdb->save()) {
                Session::flash('success', 'The item has been successfully updated');

            } else {
                Session::flash('danger', 'Sorry there was a problem updating your local db');

            }
          return Redirect::to('quickbooks/items');
        } else {
            $efrisdb = EfrisItem::find($id);
            $measure_unit = $efris_api->makeGet('master-data');

            return Inertia::render('modify-registered-product', [
                'item' => json_decode($item),
                'efris' => $efrisdb,
                'measureunit' => json_decode($measure_unit),
            ]);
        }
    }

    public function getTotalPages($data)
    {
      return $data['data']['page']['pageCount'] ?? 1;
    }
}
