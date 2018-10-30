@extends('layouts.master')
@section('header')
<section class="content-header" style="text-align: center;">
    <span style="font-weight: bold; font-size: 30px">YMPI Information System</span>
    <ol class="breadcrumb">
    </ol>
</section>
@endsection

@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Raw Material</h5>
                </div>
                <div class="box-body">
          {{--   <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a> --}}
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
            <h5 class="box-title" style="font-weight: bold;">Work In Process</h5>
        </div>
        <div class="box-body">
           {{--  <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a> --}}
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
            <h5 class="box-title" style="font-weight: bold;">Finished Goods</h5>
        </div>
        <div class="box-body">
            <a class="list-group-item" style="font-weight: bold;" href="{{ url("/index/fg_production") }}"><span class="fa fa-bar-chart"></span> Production Result</a>
            <a class="list-group-item" style="font-weight: bold;" href="{{ url("/index/fg_stock") }}"><span class="fa fa-bar-chart"></span> Finsihed Goods Stock</a>
            <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-bar-chart"></span> Weekly Shipment</a>
            <a class="list-group-item" style="font-weight: bold;" href="{{ url("/index/fg_container_departure") }}"><span class="fa fa-bar-chart"></span> Container Departure</a>
            <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-table"></span> Weekly Summary</a>
            <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-table"></span> Monthly Summary</a>
            <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-table"></span> Traceability</a>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
        <div class="box box-default box-solid">
            <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                <h5 class="box-title" style="font-weight: bold;">Chorei</h5>
            </div>
            <div class="box-body">
                <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-bar-chart"></span> Daily Production Result</a>
                <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-bar-chart"></span> Daily Production Accuracy</a>
                <a class="list-group-item" style="font-weight: bold;" href=""><span class="fa fa-bar-chart"></span> Weekly Shipment</a>
            </div>
        </div>
    </div>
</div>
</section>

@stop