<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->unsigned();
            //$table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->string('name');
            $table->integer('location_id')->nullable()->change();
            $table->integer('contact_id_1');
            $table->integer('contact_id_2')->nullable()->change();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('billing_date')->nullable()->change();
            $table->date('payment_date')->nullable()->change();
            $table->text('terms_and_conditions')->nullable()->change();
            $table->text('notes')->nullable()->change();
            $table->timestamps();
            $table->softDeletes(); 
            $table->integer('user_id')->unsigned();
            //$table->foreign('user_id')->references('id')->on('action_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //$table->dropForeign('contracts_store_id_foreign');
            //$table->dropForeign('contracts_user_id_foreign');
        });
    }
}
