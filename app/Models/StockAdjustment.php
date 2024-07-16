<?php

namespace App\Models;

use App\Services\QuickBooksServiceHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class StockAdjustment extends Model
{
    use HasFactory;

    public $fillable = [
        'transact_id',
        'transact_date',
        'item_name',
        'item_id',
        'quantity',
        'unit_price',
        'ura_sync_status',
        'adjust_type',
        'adjust_reason',
    ];

    protected $appends = [
        'reason',
    ];

    // protected $appends = ['adjustment_type'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (! $model->exists) { // isNewRecord check
                // Handle new record
            } else {
                $model->updated_at = now();
            }
        });
    }

    public function getreasonAttribute()
    {
        if ($this->adjust_type !=0) {
            $code = $this->adjust_type;
            $reason_code = [
                101 => 'Expired Goods',
                102 => 'Damaged Goods',
                103 => 'Personal Uses',
                104 => 'Raw Materials',
            ];

            return isset($code) ? ($reason_code[$code]) : ('N/A');
        }

        return 'N/A';
    }

    // public function scopeSearch($query, $keyword)
    // {
    //     // grid filtering conditions
    //     $query->where([
    //         ['transact_date', '=', $this->transact_date],
    //         ['item_id', '=', $this->item_id],
    //         ['adjust_type', '=', $this->adjust_type],
    //         ['ura_sync_status', '=', $this->ura_sync_status]
    //     ]);

    //     $query->where('item_name', 'like', '%' . $keyword . '%');
    //     $query->where('transact_id', 'like', '%' . $keyword . '%');

    //     //        usage
    //     //        $publishedPosts = Post::published()->get();
    //     //        $popularPublishedPosts = Post::published()->orderBy('views', 'desc')->limit(10)->get();
    //     return $query;
    // }

    public function scopeSearch($query, $params)
    {
        // add conditions that should always apply here

        if (isset($params['transact_date'])) {
            $query->where('transact_date', '=', $params['transact_date']);
        }

        if (isset($params['item_id'])) {
            $query->where('item_id', '=', $params['item_id']);
        }

        if (isset($params['adjust_type'])) {
            $query->where('adjust_type', '=', $params['adjust_type']);
        }

        if (isset($params['ura_sync_status'])) {
            $query->where('ura_sync_status', '=', $params['ura_sync_status']);
        }

        if (isset($params['item_name'])) {
            $query->where('item_name', 'LIKE', '%'.$params['item_name'].'%');
        }

        if (isset($params['transact_id'])) {
            $query->where('transact_id', 'LIKE', '%'.$params['transact_id'].'%');
        }

        return $query;
    }

    public function item()
    {
        return $this->hasOne(EfrisItem::class, 'id', 'item_id');
    }

    public static function prepareEfrisRequestObject($id)
    {
        // 1. Find items from DB
        $stock = static::where('transact_id', $id)->has('item')->get();
        //Pick first item to prepare general details [AdjustType, etc]
        $decode_stock=json_decode($stock);

        if (count($decode_stock) !=0) {
            $first = $stock[0];
            $items = []; //Items in this Transaction
            $efris = [
                'adjustType' => $first->adjust_type,
                //"stockInType" => "",//Relevant when we are stocking in
                'remarks' => (is_null($first->adjust_reason) || $first->adjust_type != 105) ? 'Over stayed in store' : $first->adjust_reason,
            ];
            //2. prepare Items
            foreach ($stock as $stk) {
                // QuickBooksServiceHelper::logToFile($stk->item->itemCode);
                $items[] = [
                    'itemCode' => $stk->item->itemCode,
                    'quantity' => abs($stk->quantity), //Convert to a positive number
                    'unitPrice' => $stk->unit_price,
                ];
            }
            //3. Add Items to the EFRIS Object
            $efris['stockInItem'] = $items;
            //Return the Object
            return $efris;
        }else{
            return  redirect()->back()->with('failed','No stock items found');
        }


    }

    public static function batchInsert($records)
    {
        foreach ($records as $record) {
            self::customInsert($record);
        }

        return true;
    }

    protected static function customInsert($record)
    {
        //if the unit price is positive and the quantity is negative
        if (
            intval($record->unit_price) > 0
            /**&& intval($record->quantity) < 0**/
        ) {
            DB::table('stock_adjustment')
                ->updateOrInsert(
                    ['transact_id' => $record->transact_id],
                    [
                        'transact_date' => $record->transact_date,
                        'item_name' => $record->item_name,
                        'item_id' => $record->item_id,
                        'quantity' => $record->quantity,
                        'unit_price' => $record->unit_price,
                        'ura_sync_status' => 0,
                        'created_at' => now(),
                    ]
                );

            return true;
        } else {
            return false;
        }
    }

    /**
     * The composite primary key value
     */
    public function getIdentifierAttribute()
    {
        return $this->transact_id.'-'.$this->item_id.'-'.$this->quantity.'-'.$this->transact_date;
    }

    /**
     * Stock Adjustment type for this transaction
     */
    public function getAdjustmentTypeAttribute()
    {
        $types = $this->getEfrisAdjustmentTypes();

        return (intval($this->adjust_type) < 100) ? ('') : ($types[$this->adjust_type]);
    }

    /**
     * List of Stock Adjustment Types
     */
    protected function getEfrisAdjustmentTypes()
    {
        return [
            101 => 'Expired Goods',
            102 => 'Damaged Goods',
            103 => 'Personal Uses',
            104 => 'Raw Materials',
            105 => 'Other',
        ];
    }

    /**
     * Pick the stock adjustment records from the ledger
     *
     * @param  type  $array
     */
    public static function getStockAdjustments($array)
    {
        $row = Arr::get($array, 'Rows.Row', []);
        $rrr = Arr::pluck($row, 'Rows.Row', []);
        $data = collect($rrr)
            ->flatten(1)
            ->filter(function ($rx) {
                return Arr::get($rx, 'ColData.1.value') === auth()->user()->quickbooks->taxpayerConfig->quickbooks_stockadjustment_keyword;
            })
            ->all();

        return $data;
    }
}
