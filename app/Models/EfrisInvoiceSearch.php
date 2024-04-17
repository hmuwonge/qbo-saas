<?php

namespace App\Models;
use App\Models\QuickBooksInvoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class EfrisInvoiceSearch extends EfrisInvoice
{
    use HasFactory;

    protected $fillable = [
        'fiscalNumber',
        'fiscalStatus',
        'verificationCode',
        'fiscalNumber',
        'fiscalNumber',
        'fiscalNumber',
    ];

    /**
     * Prepare a list of invoices for display using DataTables
     *
     * @return array
     */
    protected static function prepareInvoiceList($dbInvoices, array $qbInvoices): array
    {
        $invoices = [];

        // Debugging: Check $dbInvoices before the loop

        foreach ($qbInvoices as $inv) {
                $_id = $inv['Id'];
                $dbRec = $dbInvoices[$_id] ?? null;

                $invoices[] = new QuickBooksInvoicesDatatable($dbRec, $inv);
        }

        return $invoices;
    }

    /**
     * Filter invoices by Status
     *
     * @return mixed
     */
    public function scopeSearch($query, $params)
    {
        // add conditions that should always apply here
        $dataProvider = $query->get();

        $params = collect($params);

        if (! $params->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->where([
            ['id', $params->get('id')],
            ['fiscal_number', $params->get('fiscalNumber')],
            ['fiscal_status', $params->get('fiscalStatus')],
            ['verification_code', $params->get('verificationCode')],
            ['validation_status', $params->get('validationStatus')],
        ])->where(function ($q) use ($params) {
            $q->where('ref_number', 'like', '%'.$params->get('refNumber').'%')
                ->orWhere('validation_error', 'like', '%'.$params->get('validationError').'%');
        });

        return $dataProvider;
    }

    /**
     * Filter invoices by Status
     *
     * @param int|null $status
     * @param int|null $fiscalised
     * @return array
     */
    public static function findQbInvoicesByStatus($invoices, int $status = null, int $fiscalised = null): array
    {
        //1. Pick Invoices

        $list = $invoices;
        //        if ($list) {
        if ($status == 2 && $fiscalised == 2) {
            $invoiceStatus = QuickBooksInvoice::where(['invoice_kind' => 'INVOICE'])->get()->keyBy('id');
            $indexed = collect($list)->keyBy('Id');

            return self::prepareInvoiceList($invoiceStatus, collect($indexed)->toArray());
        }

        //Invoices
        $invoiceStatus = QuickBooksInvoice::where(['invoice_kind' => 'INVOICE', 'validationStatus' => $status,
            'fiscalStatus' => $fiscalised])->get()->keyBy('id');

        //IDS to be displayed...
        $list_ids = $invoiceStatus->pluck('id')->toArray();

        //Index the Array
        $indexed = collect($list)->keyBy('Id');
        $filteredList = $indexed->only($list_ids);

        return self::prepareInvoiceList(collect($invoiceStatus)->toArray(), collect($filteredList)->toArray());
        //        } else {
        //            return [];
        //        }
    }


    /**
     * Filter invoices by Status
     *
     * @param int|null $status
     * @param int|null $fiscalised
     * @return array
     */
    public static function findQbReceiptsByStatus($receipts, int $status = null, int $fiscalised = null)
    {
        // 1. Pick Invoices
        //        $list = $invoices['QueryResponse']['SalesReceipt'];

        if ($receipts) {
            if ($status == 2 && $fiscalised == 2) {
                $invoiceStatus = QuickbooksInvoice::where(['invoice_kind' => 'RECEIPT'])
                    ->get()
                    ->keyBy('id')
                    ->all();

                $indexed = collect($receipts)->keyBy('Id');

                return self::prepareInvoiceList(collect($invoiceStatus)->toArray(), collect($indexed)->toArray());
            }

            // Invoices
            $invoiceStatus = QuickbooksInvoice::where([
                'invoice_kind' => 'RECEIPT',
                'validationStatus' => $status,
                'fiscalStatus' => $fiscalised,
            ])
                ->get()
                ->keyBy('id')
                ->all();

            // IDS to be displayed
            $listIds = array_column($invoiceStatus, 'id');

            // Index the Array
            $indexed = collect($receipts)->keyBy('Id');
            $filteredList = $indexed->filter(function ($value, $key) use ($listIds) {
                return in_array($key, $listIds);
            });

            return self::prepareInvoiceList(collect($invoiceStatus)->toArray(), collect($filteredList)->toArray());
        } else {
            return [];
        }
    }

}
