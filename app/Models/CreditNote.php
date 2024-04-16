<?php

namespace App\Models;

use App\Services\ApiRequestHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'referenceNo',
        'approveStatus',
        'credit_note_id',
        'reason_code',
        'credit_note_invoice_number',
        'invoiceNo',
        'grossAmount',
        'oriInvoiceNo',
        'currency',
        'taskId',
        'reason',
        'remark',
        'invoiceApplyCategoryCode',
        'referenceNo',
    ];

    public static function batchInsert($records)
    {
        foreach ($records as $record) {
            self::customInsert($record);
        }

        return true;
    }

    protected static function customInsert($record)
    {
        $existingRecord = self::updateOrCreate(
            ['referenceNo' => $record->referenceNo],
            [
                'approveStatus' => $record->approveStatus,
                'credit_note_id' => $record->credit_note_id,
                'credit_note_invoice_number' => $record->credit_note_invoice_number,
                'invoiceNo' => $record->invoiceNo,
                'grossAmount' => $record->grossAmount,
                'oriInvoiceNo' => $record->oriInvoiceNo,
                'currency' => $record->currency,
                'taskId' => $record->taskId,
            ]
        );

        return true;
    }

    /**
     * Details of the originalInvoice from  efris
     */
    public function originalInvoice()
    {
        $invoice_no = $this->invoice_fdn;
        $efris = new ApiRequestHelper('efris');
        $invoice = $efris->makeGet('invoice-details/'.$invoice_no);

        return json_decode($invoice);
    }

    /**
     * Build the REquest Object to cancel a credit note from EFRIS
     */
    public function buildEfrisRequestObject()
    {
        $efrisInvoice = $this->originalInvoice()->data;

        return [
            'generalInfo' => [
                'orInvoiceID' => $efrisInvoice->basicInformation->invoiceNo,
                'invoiceNo' => 'invoiceNo',
                'reasonCode' => '102',
                'reason' => 'Desription of the reason why credit note was cancelled',
                'invoiceApplyCategoryCode' => '103',
                'remarks' => 'Remarks',
            ],

        ];
    }
}
