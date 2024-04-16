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
        Schema::create('efris_clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('address');
            $table->string('contact_person_name');
            $table->string('contact_person_telephone');
            $table->string('email');
            $table->string('subscription_start_date');
            $table->string('subscription_end_date');
            // $table->string('safe');
            $table->string('alternative_email');
            $table->integer('tin');
            $table->integer('branches');
            $table->integer('accounting_software');
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
        Schema::dropIfExists('efris_clients');
    }
};
