<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_type_id')->unsigned();
            $table->string('telephone', 20)->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->timestamp('last_access')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            // // $table->foreign('user_type_id')->references('id')->on('user_types');
            // // $table->foreign('lab_id')->references('id')->on('labs');
            // // $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
