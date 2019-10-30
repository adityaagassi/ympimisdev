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
              {{$sampling_check->department}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Section</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->section}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Sub Section</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->subsection}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Month</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->month}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Date</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->date}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Product</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->product}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">No. Seri / Part</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->no_seri_part}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Jumlah Cek</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->jumlah_cek}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Leader</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->leader}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Foreman</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->foreman}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Created By</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->user->name}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Created At</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->created_at}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Last Update</label>
            <div class="col-sm-5" align="left">
              {{$sampling_check->updated_at}}
            </div>
          </div>
        {{-- </div> --}}
        <a class="btn btn-info" href="{{ url('index/sampling_check/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
