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
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-5">Department</label>
            <div class="col-sm-5" align="left">
              {{$training_report->department}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Section</label>
            <div class="col-sm-5" align="left">
              {{$training_report->section}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Product</label>
            <div class="col-sm-5" align="left">
              {{$training_report->product}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Periode</label>
            <div class="col-sm-5" align="left">
              {{$training_report->periode}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Tanggal</label>
            <div class="col-sm-5" align="left">
              {{$training_report->date}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Waktu</label>
            <div class="col-sm-5" align="left">
              <?php 
                $timesplit=explode(':',$training_report->time);
                $min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
              {{$min.' Min'}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Trainer</label>
            <div class="col-sm-5" align="left">
              {{$training_report->trainer}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Theme</label>
            <div class="col-sm-5" align="left">
              {{$training_report->theme}}
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-5">Isi Training</label>
            <div class="col-sm-5" align="left">
              <?php echo $training_report->isi_training ?>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Tujuan</label>
            <div class="col-sm-5" align="left">
              {{$training_report->tujuan}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Standard</label>
            <div class="col-sm-5" align="left">
              {{$training_report->standard}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Leader</label>
            <div class="col-sm-5" align="left">
              {{$training_report->leader}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Foreman</label>
            <div class="col-sm-5" align="left">
              {{$training_report->foreman}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Catatan</label>
            <div class="col-sm-5" align="left">
              <?php echo $training_report->notes?>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Created By</label>
            <div class="col-sm-5" align="left">
              {{$training_report->user->name}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Created At</label>
            <div class="col-sm-5" align="left">
              {{$training_report->created_at}}
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-5">Last Update</label>
            <div class="col-sm-5" align="left">
              {{$training_report->updated_at}}
            </div>
          </div>
        </div>
        <a class="btn btn-info" href="{{ url('index/training_report/index/'.$id) }}">Cancel</a>
    </div>
    
  </div>

  @endsection
