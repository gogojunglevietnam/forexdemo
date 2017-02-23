<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Debugbar;
use App\Models\ForexPrice;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        

        return view('home');

    }

    public function pricepage(Request $request)
    {
        //Get 100 records order by 'id' DESC.
        $pricerecords=ForexPrice::orderBy('id','desc')->take(100)->get();
        //Debugbar::info($pricerecords);
        //Get current date time
        $updatedat=Carbon::now(env('ShowTimeZone','Asia/Tokyo'));
        return view('price')->with('pricerecords',$pricerecords)->with('updatedat',$updatedat->toDateTimeString());

    }
}
