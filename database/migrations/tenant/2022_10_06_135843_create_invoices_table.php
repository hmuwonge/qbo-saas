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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('DocNumber');
            $table->string('TxnDate');
            $table->string('DueDate');
            $table->decimal('TotalAmt', 22, 2)->default(0.00);
            $table->decimal('Balance', 22, 2)->default(0.00);
            $table->json('CustomField');
            $table->json('CurrencyRef');
            $table->json('Line');
            $table->json('TxnTaxDetail');
            $table->json('CustomerRef');
            $table->json('BillAddr');
            $table->json('ShipAddr');
            $table->string('PrintStatus');
            $table->integer('Deposit')->nullable();
            $table->string('EmailStatus');
            $table->json('MetaData');
            $table->integer('InvNo');
            // $table->json('InvId');
            // $table->string('time');
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
        Schema::dropIfExists('invoices');
    }
};
