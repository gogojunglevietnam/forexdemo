<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\PriceHistory;

use Log;

use Debugbar;

class PriceHistoryController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');

        // $this->middleware('log', ['only' => ['fooAction', 'barAction']]);

        $this->middleware('web', ['except' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //return view('pricehistory.index');
        $result = PriceHistory::all();
        return $result;


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //echo "Got Post";
        Log::info('Got post');
        Log::info($request);
        //print_r($request);
        
        //Debugbar::info($request);
         // $pricehistory = new PriceHistory();

         // $pricehistory=>
        $pricehistory=PriceHistory::create([
            'price' => '1.5',
            'description' => 'Price update at'            
        ]);

        Log::info('Price History added');
        Log::info($pricehistory);

        return $pricehistory;

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $pricehistory = PriceHistory::find($id);
        return $pricehistory;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
