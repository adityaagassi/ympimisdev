@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $activity_name }} - {{ $departments }}
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
    <div class="box-body">
      {{-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"> --}}
        <div class="form-group row" align="right">
          <label class="col-sm-5">Department</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->department}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Sub Section</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->subsection}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Proses / Nama Mesin</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->proses}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Jenis</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->jenis}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Standar Kualitas</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->standar_kualitas}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Tool Check</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->tool_check}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Jumlah Cek</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->jumlah_cek}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Leader</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->leader}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foreman</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->foreman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$first_product_audit->updated_at}}
          </div>
        </div>
      {{-- </div> --}}
      <a class="btn btn-info" href="{{ url('index/first_product_audit/list_proses/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
