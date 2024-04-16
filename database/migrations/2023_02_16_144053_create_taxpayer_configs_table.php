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
        Schema::create('taxpayer_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('tin');
            $table->integer('is_vat_registered')->nullable();
            $table->text('legal_name');
            $table->text('business_name')->nullable();
            $table->text('address');
            $table->string('mobile_phone')->nullable();
            $table->string('brn');
            $table->string('email')->nullable();
            $table->string('taxpayer_id');
            $table->string('environments')->nullable();
            $table->text('device_no');
            $table->text('efris_middleware_url')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('taxpayer_configs');
    }
};
