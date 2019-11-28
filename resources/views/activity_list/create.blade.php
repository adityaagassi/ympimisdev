@extends('layouts.master')
@section('header')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Create {{ $page }} - @if($dept_name != null)
                  {{ $dept_name }}
                @endif
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
    @if($id != 0)
      <form role="form" method="post" action="{{url('index/activity_list/store_by_department/'.$id.'/'.$no)}}">
    @else
      <form role="form" method="post" action="{{url('index/activity_list/store')}}">
    @endif  
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Name<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="activity_name" placeholder="Enter Activity Name" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Alias<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="activity_alias" placeholder="Enter Activity Alias" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Frequency<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="frequency" style="width: 100%;" data-placeholder="Choose a Frequency..." required>
              <option value=""></option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
              <option value="Conditional">Conditional</option>
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Type<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="activity_type" style="width: 100%;" data-placeholder="Choose a Activity Type..." required>
              <option value=""></option>
              @foreach($activity_type as $activity_type)
                <option value="{{ $activity_type }}">{{ $activity_type }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Department<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="department_id" style="width: 100%;" data-placeholder="Choose a Department..." required>
              <option value=""></option>
              @foreach($department as $department)
                @if($id != 0)
                  @if($id == $department->id)
                    <option value="{{ $department->id }}" selected>{{ $department->department_name }}</option>
                  @endif
                @else
                  <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Leader<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="leader" style="width: 100%;" data-placeholder="Choose a Leader..." required>
              <option value=""></option>
              @foreach($leader as $leader)
                <option value="{{ $leader->name }}">{{ $leader->employee_id }} - {{ $leader->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="foreman" style="width: 100%;" data-placeholder="Choose a Foreman..." required>
              <option value=""></option>
              @foreach($foreman as $foreman)
                <option value="{{ $foreman->name }}">{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
            <label class="col-sm-4">Time<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <input type="text" id="plan_time" name="plan_time" class="form-control timepicker" value="01:00">
            </div>
          </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
                @if($id != 0)
                  <a class="btn btn-danger" href="{{ url('index/activity_list/filter/'.$id.'/'.$no) }}">Cancel</a>
                @else
                  <a class="btn btn-danger" href="{{ url('index/activity_list') }}">Cancel</a>
                @endif
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
  <script>
    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('#email').val('');
      $('#password').val('');
    });
    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
  </script>
  @stop

