<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{

	 protected $fillable = [
        'price', 'description', 'currency1','currency2',
    ];

   }
