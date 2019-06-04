<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('mobile', 15)->nullable()->index();
            $table->string('name', 60)->nullable()->index();
            $table->string('email', 60)->nullable()->index();

            // 1 for MPesa
            // 2 for Cash
            $table->tinyInteger('payment_type_id')->unsigned()->default(1);
            $table->integer('payment_amount')->unsigned();

            // Used For Mpesa Transaction ID
            $table->string('payment_code', 70)->nullable()->index();


            $table->string('bill_reference', 20)->nullable()->index();

            // Used for Lipa na Mpesa Online
            $table->string('merchant_request_id', 40)->nullable()->index();
            $table->string('checkout_request_id', 80)->nullable()->index();

            // 1 for pending
            // 2 for successful
            // 3 for failed
            $table->tinyInteger('payment_status_id')->unsigned()->default(1);

            $table->timestamp('payment_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
