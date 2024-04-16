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
        Schema::create('efris_items', function (Blueprint $table) {
            $table->id();
            $table->string('itemCode')->nullable();
            $table->string('currency')->nullable();
            $table->string('unitOfMeasure')->nullable();
            $table->integer('commodityCategoryId')->nullable();
            $table->string('havePieceUnit')->nullable();
            $table->string('pieceUnitPrice')->nullable();
            $table->string('hasOpeningStock')->nullable();
            $table->string('stockin_quantity')->nullable();
            $table->string('stockin_remarks')->nullable();
            $table->string('opening_stock_remarks')->nullable();
            $table->string('stockin_date')->nullable();
            $table->string('stockin_supplier')->nullable();
            $table->string('stockin_supplier_tin')->nullable();
            $table->string('stockin_measureUnit')->nullable();
            $table->string('otherUnit')->nullable();
            $table->string('otherPrice')->nullable();
            $table->string('otherScaled')->nullable();
            $table->string('packageScaled')->nullable();
            $table->string('haveExciseTax')->nullable();
            $table->string('stockStatus')->nullable();
            $table->string('exciseDutyCode')->nullable();
            $table->string('pieceScaledValue')->nullable();
            $table->string('packageScaleValue')->nullable();
            $table->string('pieceMeasureUnit')->nullable();
            $table->string('haveOtherUnit')->nullable();
            $table->string('item_tax_rule')->default('URA');
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
        Schema::dropIfExists('efris_items');
    }
};
