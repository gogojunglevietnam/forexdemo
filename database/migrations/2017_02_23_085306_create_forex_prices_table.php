<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForexPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forex_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency01',15)->default('Cur1');
            $table->string('currency02',15)->default('Cur2');;            
            $table->float('price',8,2)->default(1);;            
            $table->timestamp('price_at')->default(DB::raw('CURRENT_TIMESTAMP'));            
            $table->string('description')->nullable();          

            $table->float('openprice',8,5)->default(0);            
            $table->float('originalamount',8,5)->default(0);            
            $table->float('amount',8,5)->default(0);            
            $table->string('closetime')->nullable();          
            $table->string('stoplossprice')->nullable();          
            $table->float('requestedamount',8,5)->default(0);     
            $table->string('ordercommand');                 
            $table->float('takeprofitprice',8,5)->default(0);     
            $table->float('closeprice',8,5)->default(0);     
            $table->string('state');                 
            $table->string('instrument');                 
            $table->string('filltime');                 

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
        Schema::drop('forex_prices');
    }
}
