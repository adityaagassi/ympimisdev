@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail Guidance {{ $activity_name }} - {{ $departments }}
    <small>{{ $leader }}</small>
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
          <label class="col-sm-5">Nama Dokumen</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->nama_dokumen}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Nomor Dokumen</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->no_dokumen}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Bulan</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->month}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Periode</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->periode}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Status</label>
          <div class="col-sm-5" align="left">
            @if($audit_guidance->status == "Belum Dikerjakan")
              <label class="label label-danger">Belum Dikerjakan</label>
            @else
              <label class="label label-success">Sudah Dikerjakan</label>
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Leader</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->leader}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foreman</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->foreman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$audit_guidance->updated_at}}
          </div>
        </div>
      {{-- </div> --}}
      <a class="btn btn-info" href="{{ url('index/audit_guidance/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
