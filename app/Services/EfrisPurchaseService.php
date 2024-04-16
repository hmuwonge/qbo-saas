<?php

namespace App\Services;

use App\Models\QuickBooksPurchase;

class EfrisPurchaseService
{
    public static function actionIncreaseStock($id)
    {
        $efris = new ApiRequestHelper('efris1');
        $stock = QuickBooksPurchase::prepareEfrisStockIncrease($id);
        $response = $efris->makePost('increase-stock', $stock);
        return json_decode($response, true);
    }
}
