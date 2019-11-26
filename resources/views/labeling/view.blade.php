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
            {{$labeling->department}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Section</label>
          <div class="col-sm-5" align="left">
            {{$labeling->section}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Product</label>
          <div class="col-sm-5" align="left">
            {{$labeling->product}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Periode</label>
          <div class="col-sm-5" align="left">
            {{$labeling->periode}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Date</label>
          <div class="col-sm-5" align="left">
            {{$labeling->date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Nama Mesin</label>
          <div class="col-sm-5" align="left">
            {{$labeling->nama_mesin}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foto Arah Putaran</label>
          <div class="col-sm-5" align="left">
            <img width="200px" src="{{ url('/data_file/labeling/'.$labeling->foto_arah_putaran) }}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foto Sisa Putaran</label>
          <div class="col-sm-5" align="left">
            <img width="200px" src="{{ url('/data_file/labeling/'.$labeling->foto_sisa_putaran) }}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Keterangan</label>
          <div class="col-sm-5" align="left">
            {{$labeling->keterangan}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Leader</label>
          <div class="col-sm-5" align="left">
            {{$labeling->leader}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foreman</label>
          <div class="col-sm-5" align="left">
            {{$labeling->foreman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$labeling->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$labeling->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$labeling->updated_at}}
          </div>
        </div>
      {{-- </div> --}}
      <a class="btn btn-info" href="{{ url('index/labeling/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
