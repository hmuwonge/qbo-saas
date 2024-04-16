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
        Schema::create('commodity_codes', function (Blueprint $table) {
            $table->id();
            $table->text('segment_name');
            $table->integer('family_code');
            $table->integer('class_code');
            $table->integer('commodity_code');
            $table->string('class_name');
            $table->string('commodity_name');
            $table->string('is_it_a_service');
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
        Schema::dropIfExists('commodity_codes');
    }
};
