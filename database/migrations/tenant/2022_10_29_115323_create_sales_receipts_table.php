<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('qbo_id')->default(0)->nullable();
            $table->string('sync_token')->default(0)->nullable();
            $table->string('domain')->default('QBO')->nullable();
            $table->string('TrackingNum')->nullable();
            $table->integer('TotalAmt')->nullable();
            $table->string('status')->nullable();
            $table->string('PrintStatus')->nullable();
            $table->string('EmailStatus')->nullable();
            $table->integer('Balance')->nullable();
            $table->integer('DepositToAccountRef')->nullable();
            $table->string('GovtTxnRefIdentifier')->nullable();
            $table->string('sparse')->nullable();
            $table->string('ApplyTaxAfterDiscount')->nullable();
            $table->string('DocNumber')->nullable();
            $table->string('TxnDate')->nullable();
            $table->string('TxnStatus')->nullable();
            $table->string('ExchangeRate')->nullable();
            $table->string('PrivateNote')->nullable();
            $table->string('TxnSource')->nullable();
            $table->string('TaxFormType')->nullable();
            $table->string('TaxFormNum')->nullable();
            $table->string('TransactionLocationType')->nullable();
            $table->string('Tag')->nullable();
            $table->json('meta_data')->nullable();
            $table->json('AttachableRef')->nullable();
            $table->json('DepartmentRef')->nullable();
            $table->json('CurrencyRef')->nullable();
            $table->json('LinkedTxn')->nullable();
            $table->json('sales_items')->nullable();
            $table->json('TxnTaxDetail')->nullable();
            $table->json('BillAddr')->nullable();
            $table->json('ShipAddr')->nullable();
            $table->json('BillEmail')->nullable();
            $table->json('ARAccountRef')->nullable();
            $table->json('PaymentMethodRef')->nullable();
            $table->text('CustomerMemo')->nullable();
            $table->string('FreeFormAddress')->nullable();
            $table->string('DueDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_receipts');
    }
};
