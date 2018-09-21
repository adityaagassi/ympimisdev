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
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>150</h3>

              <p>New Orders</p>
          </div>
          <div class="icon">
            &nbsp;              
            <i class="ion ion-bag"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
</div>
<!-- ./col -->
<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div class="small-box bg-green">
    <div class="inner">
      <h3>53<sup style="font-size: 20px">%</sup></h3>

      <p>Bounce Rate</p>
  </div>
  <div class="icon">
     &nbsp; 
      <i class="ion ion-stats-bars"></i>
  </div>
  <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>
<!-- ./col -->
<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div class="small-box bg-yellow">
    <div class="inner">
      <h3>44</h3>

      <p>User Registrations</p>
  </div>
  <div class="icon">
     &nbsp; 
      <i class="ion ion-person-add"></i>
  </div>
  <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>
<!-- ./col -->
<div class="col-lg-3 col-xs-6">
  <!-- small box -->
  <div class="small-box bg-red">
    <div class="inner">
      <h3>65</h3>

      <p>Unique Visitors</p>
  </div>
  <div class="icon">
     &nbsp; 
      <i class="ion ion-pie-graph"></i>
  </div>
  <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>
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