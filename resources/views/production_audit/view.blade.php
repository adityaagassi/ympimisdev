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
          <label class="col-sm-5">Product</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->point_check_audit->product}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Proses</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->point_check_audit->proses}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Date</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->date}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Point Pengecekan</label>
          <div class="col-sm-5" align="left">
            <?php echo $production_audit->point_check_audit->point_check ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Cara Cek</label>
          <div class="col-sm-5" align="left">
            <?php echo $production_audit->point_check_audit->cara_cek ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Foto Kondisi Aktual</label>
          <div class="col-sm-5" align="left">
            <img width="200px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Kondisi</label>
          <div class="col-sm-5" align="left">
            @if($production_audit->kondisi == "Good")
              <label class="label label-success">{{$production_audit->kondisi}}</label>
            @else
              <label class="label label-danger">{{$production_audit->kondisi}}</label>
            @endif
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">PIC</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->employee_pic->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Auditor</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->employee_auditor->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$production_audit->updated_at}}
          </div>
        </div>
        <a class="btn btn-info" href="{{ url('index/production_audit/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
