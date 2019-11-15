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
        <input type="hidden" value="{{csrf_token()}}" name="_method" />
        <div class="form-group row" align="right">
          <label class="col-sm-5">Section</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->section}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Sub Section</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->subsection}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Date</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Nama Dokumen</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->nama_dokumen}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">No. Dokumen</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->no_dokumen}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kesesuaian Aktual Proses</label>
          <div class="col-sm-5" align="left">
            <?php echo $audit_report_activity->kesesuaian_aktual_proses ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Tindakan Perbaikan</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->tindakan_perbaikan}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Target</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->target}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kelengkapan Point Safety</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->kelengkapan_point_safety}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kesesuaian QC Kouteihyo</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->kesesuaian_qc_kouteihyo}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Operator</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->operator}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Leader</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->leader}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foreman</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->foreman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$audit_report_activity->updated_at}}
          </div>
        </div>
        <a class="btn btn-info" href="{{ url('index/audit_report_activity/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
