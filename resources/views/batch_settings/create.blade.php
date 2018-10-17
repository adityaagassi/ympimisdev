@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
@endsection
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
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
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif


  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New User</h3> --}}
    </div>  
    <form role="form" method="post" action="{{url('create/batch_setting')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Batch Time<span class="text-red">*</span></label>
          <div class="col-sm-2">
            <div class="input-group">
              <input type="text" name="batch_time" class="form-control timepicker">
              <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Upload<span class="text-red">*</span></label>
          <div class="col-sm-1">
            <input id="toggle_lock" name="upload" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox" value="1">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Download<span class="text-red">*</span></label>
          <div class="col-sm-1">
            <input id="toggle_lock" name="download" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox" value="1">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Remark</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
          </div>
        </div>
        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/batch_setting') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
  <script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
  </script>
  @stop

