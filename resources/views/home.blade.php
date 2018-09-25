@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Dashboard
    <small>it all starts here</small>
</h1>
<ol class="breadcrumb">
       {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">Blank page</li> --}}
    </ol>
</section>
@endsection

@section('content')

<section class="content">
    <div class="row">
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center">
            <h5 class="box-title">Raw Material</h5>
        </div>
        <div class="box-body">
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center">
            <h5 class="box-title">Work In Process</h5>
        </div>
        <div class="box-body">
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center">
            <h5 class="box-title">Finished Goods</h5>
        </div>
        <div class="box-body">
            <a class="list-group-item" href="">Sales Budget-Forecast-Actual</a>
            <a class="list-group-item" href="">Sales Amount Progress</a>
            <a class="list-group-item" href="">Daily Finished Goods Achievement</a>
            <a class="list-group-item" href="">Finished Goods Stock</a>
            <a class="list-group-item" href="">Finished Goods Tracking</a>
            <a class="list-group-item" href="">Weekly Shipment Achievement</a>
            <a class="list-group-item" href="">Shipping Container</a>
            <a class="list-group-item" href="">Weekly Shipment Summary</a>
        </div>
    </div>
</div>
</div>
</section>

@stop


{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 --}}