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
        Schema::create('credit_memos', function (Blueprint $table) {
            $table->id();
            $table->string('qb_refNumber')->nullable();
            $table->string('invoice_fdn')->nullable();
            $table->text('efris_refNumber')->nullable();
            $table->integer('fiscalStatus')->nullable();
            $table->string('record_type')->nullable();
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
        Schema::dropIfExists('credit_memos');
    }
};
