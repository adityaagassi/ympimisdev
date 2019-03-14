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
            {{$material->material_number}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Material Description</label>
          <div class="col-sm-5" align="left">
            {{$material->material_description}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Base Unit</label>
          <div class="col-sm-5" align="left">
            {{$material->base_unit}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Storage Location</label>
          <div class="col-sm-5" align="left">
            {{$material->issue_storage_location}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Work Center</label>
          <div class="col-sm-5" align="left">
            {{$material->work_center}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Origin Group</label>
          <div class="col-sm-5" align="left">
            @if(isset($material->origingroup->origin_group_name))
            {{$material->origin_group_code}} - {{$material->origingroup->origin_group_name}}
            @else
            {{$material->origin_group_code}} - Not registered
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">HPL</label>
          <div class="col-sm-5" align="left">
            {{$material->hpl}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Category</label>
          <div class="col-sm-5" align="left">
            {{$material->category}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$material->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$material->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$material->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

