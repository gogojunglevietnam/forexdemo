
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Price History - Freshed at: {{$updatedat}}</div>

                <div class="panel-body">
                    <table class="table table-striped">
                       <thead class="thead-inverse">
                        <tr>
                            <td>Id </td>
                            <th>Open Price </th>
                            <th>Original amount</th>
                            <th>Amount</th>
                            <th>Close Time</th>
                            <th>Stop Loss Price</th>
                            <th>Requested Amount</th>
                            <th>Order Command</th>
                            <th>Take Profit Price</th>
                            <th>Close Price</th>
                            <th>State</th>
                            <th>Instrument</th>
                            <th>Fill time</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>    

                    <tbody>
                    
                    @foreach($pricerecords as $record)
                    
                    <tr>
                        <td>{{$record["id"]}}</td>
                        <td>{{$record["openprice"]}}</td>
                        <td>{{$record["originalamount"]}}</td>
                        <td>{{$record["amount"]}}</td>
                        <td>{{$record["closetime"]}}</td>
                        <td>{{$record["stoplossprice"]}}</td>
                        <td>{{$record["requestedamount"]}}</td>
                        <td>{{$record["ordercommand"]}}</td>
                        <td>{{$record["takeprofitprice"]}}</td>
                        <td>{{$record["closeprice"]}}</td>
                        <td>{{$record["state"]}}</td>
                        <td>{{$record["instrument"]}}</td>
                        <td>{{$record["filltime"]}}</td>
                        <td>{{$record["price_at"]}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            
        </div>
    </div>
</div>
</div>
</div>
@endsection
