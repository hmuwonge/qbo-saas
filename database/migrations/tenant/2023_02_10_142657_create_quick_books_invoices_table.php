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
        Schema::create('quick_books_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('refNumber');
            $table->string('fiscalNumber')->nullable();
            $table->string('qb_created_at');
            $table->string('qrCode')->nullable();
            $table->string('verificationCode')->nullable();
            $table->longText('validationError')->nullable();
            $table->string('fiscalStatus');
            $table->string('validationStatus')->nullable();
            $table->string('customerName');
            $table->string('registerDate')->nullable();
            $table->string('buyerTin')->nullable();
            $table->string('buyerType')->nullable();
            $table->string('totalAmount');
            $table->string('dueDate');
            $table->string('invoice_kind');
            $table->string('purchase_order')->nullable();
            $table->string('balanceDue');
            $table->string('industryCode')->nullable();
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
        Schema::dropIfExists('quick_books_invoices');
    }
};
