<?php
namespace Modules\QuickbooksDashboard\Services;

use Carbon\Carbon;
use App\Models\QuickBooksInvoice;
use Illuminate\Support\Facades\DB;

class DashboardServices{
    public function fiscalInvoiceCount($type = 'year')
    {
        if ($type == 'year') {

            $arrLable = [];
            $arrValue = [];

            for ($i = 0; $i < 12; $i++) {
                $arrLable[] = Carbon::now()->subMonth($i)->format('F');
                $arrValue[Carbon::now()->subMonth($i)->format('M')] = 0;
            }
            $arrLable = array_reverse($arrLable);
            $arrValue = array_reverse($arrValue);
            if (tenant('id') == null) {

                // $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at,"%b") AS invoice_month,COUNT(id) AS invoice_cnt, WHERE(fiscalStatus==1) AS status'))
                // // ->where('fiscalStatus',1)
                //     ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                //     ->orwhere('created_at', '<=', Carbon::now()->toDateString())
                //     ->groupBy(DB::raw('DATE_FORMAT(created_at,"%b") '))
                //     ->get()
                //     ->pluck('invoice_cnt', 'invoice_month','status')
                //     ->toArray();

                //    $t= QuickBooksInvoice::select(
                //         DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month'),
                //         // DB::raw('COUNT(id) AS invoice_cnt'),
                //         DB::raw('SUM(fiscalStatus = 1) AS status')
                //     )
                //         ->where(function ($query) {
                //             $query->where('fiscalStatus', 1)
                //                 ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                //                 ->orWhere('created_at', '<=', Carbon::now()->toDateString());
                //         })
                //         ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
                //         ->get()
                //         ->pluck('invoice_cnt', 'invoice_month', 'status')
                //         ->toArray();

                $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month,
                                              SUM(CASE WHEN fiscalStatus = 1 THEN 1 ELSE 0 END) AS invoice_cnt'))
                    ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                    ->orWhere('created_at', '<=', Carbon::now()->toDateString())
                    ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
                    ->get()
                    ->pluck('invoice_cnt', 'invoice_month')
                    ->toArray();

                foreach ($t as $key => $val) {
                    $arrValue[$key] = $val;
                }
                $arrValue = array_values($arrValue);
                return response()->json(['lable' => $arrLable, 'value' => $arrValue], 200);
            }


        }

      return $this->extracted($type);
    }

    public function notFiscalised($type = 'year')
    {

        if ($type == 'year') {

            $arrLable = [];
            $arrValue = [];

            for ($i = 0; $i < 12; $i++) {
                $arrLable[] = Carbon::now()->subMonth($i)->format('F');
                $arrValue[Carbon::now()->subMonth($i)->format('M')] = 0;
            }
            $arrLable = array_reverse($arrLable);
            $arrValue = array_reverse($arrValue);
            if (tenant('id') == null) {

                // $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at,"%b") AS invoice_month,COUNT(id) AS invoice_cnt, WHERE(fiscalStatus==1) AS status'))
                // // ->where('fiscalStatus',1)
                //     ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                //     ->orwhere('created_at', '<=', Carbon::now()->toDateString())
                //     ->groupBy(DB::raw('DATE_FORMAT(created_at,"%b") '))
                //     ->get()
                //     ->pluck('invoice_cnt', 'invoice_month','status')
                //     ->toArray();

                //    $t= QuickBooksInvoice::select(
                //         DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month'),
                //         // DB::raw('COUNT(id) AS invoice_cnt'),
                //         DB::raw('SUM(fiscalStatus = 1) AS status')
                //     )
                //         ->where(function ($query) {
                //             $query->where('fiscalStatus', 1)
                //                 ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                //                 ->orWhere('created_at', '<=', Carbon::now()->toDateString());
                //         })
                //         ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
                //         ->get()
                //         ->pluck('invoice_cnt', 'invoice_month', 'status')
                //         ->toArray();

                $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month,
                                              SUM(CASE WHEN fiscalStatus = 0 THEN 1 ELSE 0 END) AS invoice_cnt'))
                    ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
                    ->orWhere('created_at', '<=', Carbon::now()->toDateString())
                    ->groupBy(DB::raw('DATE_FORMAT(created_at, "%b")'))
                    ->get()
                    ->pluck('invoice_cnt', 'invoice_month')
                    ->toArray();

                foreach ($t as $key => $val) {
                    $arrValue[$key] = $val;
                }
                $arrValue = array_values($arrValue);
                return response()->json(['lable' => $arrLable, 'value' => $arrValue], 200);
            }


        }

      return $this->extracted($type);
    }

  /**
   * @param mixed $type
   * @return array|\Illuminate\Http\JsonResponse|void
   */
  public function extracted(mixed $type)
  {
    if ($type == 'month') {

      $arrLable = [];
      $arrValue = [];

      for ($i = 0; $i < 30; $i++) {
        $arrLable[] = date("d M", strtotime('-' . $i . ' days'));

        $arrValue[date("d-m", strtotime('-' . $i . ' days'))] = 0;
      }
      $arrLable = array_reverse($arrLable);
      $arrValue = array_reverse($arrValue);
      if (tenant('id') == null) {

        $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month,
                SUM(CASE WHEN fiscalStatus = 1 THEN 1 ELSE 0 END) AS invoice_cnt'))
          ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
          ->orwhere('created_at', '<=', Carbon::now()->toDateString())
          ->groupBy(DB::raw('DATE_FORMAT(created_at,"%d-%m") '))
          ->get()
          ->pluck('invoice_cnt', 'invoice_month')
          ->toArray();
      }
      foreach ($t as $key => $val) {
        $arrValue[$key] = $val;
      }
      $arrValue = array_values($arrValue);

      return response()->json(['lable' => $arrLable, 'value' => $arrValue], 200);
    }
    if ($type == 'week') {

      $arrLable = [];
      $arrValue = [];

      for ($i = 0; $i < 7; $i++) {
        $arrLable[] = date("d M", strtotime('-' . $i . ' days'));

        $arrValue[date("d-m", strtotime('-' . $i . ' days'))] = 0;
      }
      $arrLable = array_reverse($arrLable);
      $arrValue = array_reverse($arrValue);
      if (tenant('id') == null) {
        $t = QuickBooksInvoice::select(DB::raw('DATE_FORMAT(created_at, "%b") AS invoice_month,
                SUM(CASE WHEN fiscalStatus = 1 THEN 1 ELSE 0 END) AS invoice_cnt'))
          ->where('created_at', '>=', Carbon::now()->subDays(365)->toDateString())
          ->orwhere('created_at', '<=', Carbon::now()->toDateString())
          ->groupBy(DB::raw('DATE_FORMAT(created_at,"%d-%m") '))
          ->pluck('invoice_cnt', 'invoice_month')
          ->toArray();
      }
      foreach ($t as $key => $val) {
        $arrValue[$key] = $val;
      }
      $arrValue = array_values($arrValue);

      return ['lable' => $arrLable, 'value' => $arrValue];
    }
  }
}

