<?php

namespace Modules\StockAdjustments\Http\Services;

use App\Models\StockAdjustment;
use App\Traits\DataServiceConnector;
use Illuminate\Support\Arr;

class StockAdjustmentsServices
{
  use DataServiceConnector;
  public function actionSyncStockAdjustment() {
    ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action
    //Set path Alias

    $decreaseResponse = (new self())->queryString('reports/GeneralLedger?date_macro=This Fiscal Year&summarize_column_by=ProductsAndServices&columns=quantity,rate,tx_date,txn_type,doc_num,item_name&minorversion=57');

    $items = $decreaseResponse;
//    $items = json_decode($decreaseResponse);
    $stock_adjust = (object) self::getStockAdjustments($items);
    dd($stock_adjust);

    $adjust_items = [];
    if (is_countable($stock_adjust)) {
      for ($i = 0; $i < count($stock_adjust); $i++) {
        //Create an instance of the a stock adjustment object
        $stockadj = new StockAdjustment;
        $stockadj->transact_id = $stock_adjust[$i][2]->value;
        $stockadj->transact_date = $stock_adjust[$i][0]->value;
        $stockadj->item_name = $stock_adjust[$i][3]->value;
        $stockadj->item_id = $stock_adjust[$i][3]->id;
        $stockadj->quantity = intval($stock_adjust[$i][4]->value);
        $stockadj->unit_price = $stock_adjust[$i][5]->value;
        $stockadj->adjust_type = 0;
        $stockadj->ura_sync_status = 0;
        //Build a list of [StockAdjustment] Objects
        //1. Is the quantity negative?
        //2. Is the qiantity positive?
        //3. This item is not deleted
        if (intval($stockadj->quantity) < 0 && $stockadj->unit_price > 0 && !strpos($stockadj->item_name, '(deleted)')) {
          $adjust_items[] = $stockadj;
        }
      }
    }
    //Bulk Insert
    echo 'Recording ' . count($adjust_items) . ' records to the DB...\n';
    StockAdjustment::batchInsert($adjust_items);
    return 0; //All is well
  }

  public static function getStockAdjustments($array): array
  {
    $row = Arr::get($array, 'Rows.Row');
    $rrr = Arr::pluck($row, 'Rows.Row');

    $data = [];

    foreach ($rrr as $rw) {
      foreach ($rw as $rx) {
        dd($rx);
        if (property_exists($rx, 'ColData')) {
          if ($rx['ColData'][1]['value'] === 'Stock Qty Adjust') {
            $data[] = $rx;
          }
        }
      }
    }

    return $data;
  }
}
