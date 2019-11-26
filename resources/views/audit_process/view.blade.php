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
            {{$audit_process->department}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Section</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->section}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Product</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->product}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Periode</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->periode}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Date</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Proses</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->proses}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Operator</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->operator}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Aditor</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->auditor}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Cara Proses</label>
          <div class="col-sm-5" align="left">
            <?php echo $audit_process->cara_proses ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kondisi Cara Proses</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->kondisi_cara_proses}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Pemahaman</label>
          <div class="col-sm-5" align="left">
            <?php echo $audit_process->pemahaman ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kondisi Pemahaman</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->kondisi_pemahaman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Keterangan</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->keterangan}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Leader</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->leader}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foreman</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->foreman}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$audit_process->updated_at}}
          </div>
        </div>
      {{-- </div> --}}
      <a class="btn btn-info" href="{{ url('index/audit_process/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
