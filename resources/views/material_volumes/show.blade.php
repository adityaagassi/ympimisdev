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
            {{$material_volume->material_number}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Material Description</label>
          <div class="col-sm-5" align="left">
            @if(isset($material_volume->material->material_description))
            {{$material_volume->material->material_description}}
            @else
            Not registered.
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Origin Group</label>
          <div class="col-sm-5" align="left">
            @if(isset($material_volume->material->origin_group_code))
            {{$material_volume->material->origin_group_code}} - {{$material_volume->material->origingroup->origin_group_name}}
            @else
            Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Category</label>
          <div class="col-sm-5" align="left">
            @if($material_volume->category == "FG")
            FG - Finished Goods
            @else
            KD - Knock Down Parts
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Lot Completion</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->lot_completion}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Lot Transfer</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->lot_transfer}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Lot Row</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->lot_row}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Lot Pallet</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->lot_pallet}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Lot Carton</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->lot_carton}} pc(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Length</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->length}} m
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Width</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->width}} m
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Height</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->height}} m
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Volume</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->height*$material_volume->width*$material_volume->length}} m&sup3;
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$material_volume->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

