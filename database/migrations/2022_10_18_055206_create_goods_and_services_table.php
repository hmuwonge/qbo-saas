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
        Schema::create('goods_and_services', function (Blueprint $table) {
            $table->id();
            $table->integer('qbo_id');
            $table->integer('sync_token');
            $table->string('name');
            $table->string('fully_qualified_name')->nullable();
            $table->string('sku')->nullable();
            $table->string('status')->nullable();
            $table->string('sparse')->nullable();
            $table->string('taxable')->nullable();
            $table->string('percent_based')->nullable();
            $table->string('sales_tax_included')->nullable();
            $table->text('description')->nullable();
            $table->string('active')->default('true')->nullable();
            $table->integer('sub_item')->nullable();
            $table->string('parent_ref')->nullable();
            $table->string('level')->nullable();
            $table->string('domain')->default('QBO')->nullable();
            $table->string('type');
            $table->integer('unit_price');
            $table->json('payment_method_ref')->nullable();
            $table->json('income_account_ref')->nullable();
            $table->json('expense_account_ref')->nullable();
            $table->json('COGS_account_ref')->nullable();
            $table->json('asset_account_ref')->nullable();
            $table->json('deposit_to_account_ref')->nullable();
            $table->json('sales_tax_code_ref')->nullable();
            $table->json('purchase_tax_code_ref')->nullable();
            $table->json('tax_classification_ref')->nullable();
            $table->json('class_ref')->nullable();
            $table->json('pref_vendor_ref')->nullable();
            $table->text('purchase_desc')->nullable();
            $table->integer('purchase_cost')->nullable();
            $table->string('purchase_tax_included')->nullable();
            $table->integer('avg_cost')->nullable();
            $table->string('track_qty_on_hand')->default('false')->nullable();
            $table->integer('qty_on_hand')->nullable();
            $table->integer('qty_on_purchase_order')->nullable();
            $table->integer('qty_on_sales_order')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->integer('man_part_num')->nullable();
            $table->string('inv_start_date')->nullable();
            $table->string('service_type')->nullable();
            $table->string('item_category_type')->nullable();
            $table->string('item_ex')->nullable();
            $table->string('UQC_display_text')->nullable();
            $table->string('UQCId')->nullable();
            $table->string('Source')->nullable();
            $table->string('deferred_revenue')->nullable();
            $table->json('meta_data')->nullable();
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
        Schema::dropIfExists('goods_and_services');
    }
};
