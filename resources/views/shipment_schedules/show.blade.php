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
          <label class="col-sm-5">Ship. Month</label>
          <div class="col-sm-5" align="left">
            {{ date('F Y', strtotime($shipment_schedule->st_month))}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Ship. Week</label>
          <div class="col-sm-5" align="left">
            @if(isset($shipment_schedule->weeklycalendar->week_name))
            {{$shipment_schedule->weeklycalendar->week_name}}
            @else
            Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Sales Order</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->sales_order}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Ship. Condition</label>
          <div class="col-sm-5" align="left">
            @if(isset($shipment_schedule->shipmentcondition->shipment_condition_name))
            {{$shipment_schedule->shipment_condition_code}} - {{$shipment_schedule->shipmentcondition->shipment_condition_name}}
            @else
            {{$shipment_schedule->shipment_condition_code}} - Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Destination</label>
          <div class="col-sm-5" align="left">
            @if(isset($shipment_schedule->destination->destination_name))
            {{$shipment_schedule->destination->destination_code}} - {{$shipment_schedule->destination->destination_name}}
            @else
            {{$shipment_schedule->destination->destination_code}} - Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Material</label>
          <div class="col-sm-5" align="left">
            @if(isset($shipment_schedule->material->material_description))
            {{$shipment_schedule->material->material_number}} - {{$shipment_schedule->material->material_description}}
            @else
            {{$shipment_schedule->material->material_number}} - Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">HPL</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->hpl}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Origin Group</label>
          <div class="col-sm-5" align="left">
            @if(isset($shipment_schedule->material->origin_group_code))
            {{$shipment_schedule->material->origin_group_code}} - {{$shipment_schedule->material->origingroup->origin_group_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Shipment Date</label>
          <div class="col-sm-5" align="left">
            {{ date('d F Y', strtotime($shipment_schedule->st_date))}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Bill of Lading Date</label>
          <div class="col-sm-5" align="left">
            {{ date('d F Y', strtotime($shipment_schedule->bl_date))}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Quantity</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->quantity}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$shipment_schedule->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

