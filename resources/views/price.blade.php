
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Price History - Updated at: {{$updatedat}}</div>

                <div class="panel-body">
                    <table class="table table-striped">
                       <thead class="thead-inverse">
                        <tr>
                            <td>Id </td>
                            <th>Open Price </th>
                            <th>originalamount</th>
                            <th>amount</th>
                            <th>closetime</th>
                            <th>stoplossprice</th>
                            <th>requestedamount</th>
                            <th>ordercommand</th>
                            <th>takeprofitprice</th>
                            <th>closeprice</th>
                            <th>state</th>
                            <th>instrument</th>
                            <th>filltime</th>
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
