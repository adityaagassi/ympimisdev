@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Detail User</h3> --}}
    </div>  
    <form role="form">
      <div class="box-body">
        <div class="form-group row" align="right">
          <label class="col-sm-5">Container ID</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->container_id}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Container Code</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->container_code}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Container Name</label>
          <div class="col-sm-5" align="left">
            @if(isset($container_schedule->container->container_name))
            {{$container_schedule->container->container_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Destination Code</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->destination_code}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Destination Name</label>
          <div class="col-sm-5" align="left">
            @if(isset($container_schedule->destination->destination_name))
            {{$container_schedule->destination->destination_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Shipment Date</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->shipment_date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Week</label>
          <div class="col-sm-5" align="left">
            @if(isset($container_schedule->weeklycalendar->week_name))
            {{$container_schedule->weeklycalendar->week_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$container_schedule->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

