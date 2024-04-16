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
        Schema::table('quick_books_configs', function (Blueprint $table) {
            $table->string('client_secrete')->nullable();
            $table->string('qbo_realm_id')->nullable();
            $table->string('client_id')->nullable();
            $table->integer('intuit_company_id')->nullable();
            $table->integer('exempt')->nullable();
            $table->integer('standard')->nullable();
            $table->integer('zero_rated')->nullable();
            $table->integer('deemed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quick_books_configs', function (Blueprint $table) {
            //
        });
    }
};
