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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('transact_id');
            $table->string('transact_date');
            $table->string('item_name');
            $table->integer('item_id');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('ura_sync_status');
            $table->string('adjust_reason')->nullable();
            $table->string('adjust_type')->nullable();
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
        Schema::dropIfExists('stock_adjustments');
    }
};
