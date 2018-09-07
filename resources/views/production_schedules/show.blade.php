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
          <label class="col-sm-5">Material Number</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->material_number}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Material Description</label>
          <div class="col-sm-5" align="left">
            @if(isset($production_schedule->material->material_description))
            {{$production_schedule->material->material_description}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Origin Group</label>
          <div class="col-sm-5" align="left">
            @if(isset($production_schedule->material->origin_group_code))
            {{$production_schedule->material->origin_group_code}} - {{$production_schedule->material->origingroup->origin_group_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Destination Code</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->destination_code}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Destination Name</label>
          <div class="col-sm-5" align="left">
            @if(isset($dproduction_schedule->destination->destination_name))
            {{$dproduction_schedule->destination->destination_name}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Due Date</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->due_date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Quantity</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->quantity}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$production_schedule->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

