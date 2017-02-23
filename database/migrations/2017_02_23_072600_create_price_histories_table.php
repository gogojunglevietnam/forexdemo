<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreatePriceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency01',15)->default('Cur1');
            $table->string('currency02',15)->default('Cur2');;            
            $table->float('price',8,2)->default(1);;            
            $table->timestamp('price_at')->default(DB::raw('CURRENT_TIMESTAMP'));            
            $table->string('description')->nullable();                        
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
        Schema::drop('price_histories');
    }
}
