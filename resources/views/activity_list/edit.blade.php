@extends('layouts.master')
@section('header')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Edit {{ $page }}
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
    @if($id_department != 0)
      <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('index/activity_list/update_by_department/'.$activity_list->id.'/'.$id_department.'/'.$no)}}">
    @else
      <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('index/activity_list/update', $activity_list->id)}}">
    @endif
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Name<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="activity_name" placeholder="Enter Activity Name" required value="{{ $activity_list->activity_name }}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Alias<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="activity_alias" placeholder="Enter Activity Alias" required value="{{ $activity_list->activity_alias }}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Frequency<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="frequency" style="width: 100%;" data-placeholder="Choose a Frequency..." required>
              <option value=""></option>
              <option value="Daily" @if($activity_list->frequency == "Daily") selected @endif>Daily</option>
              <option value="Weekly" @if($activity_list->frequency == "Weekly") selected @endif>Weekly</option>
              <option value="Monthly" @if($activity_list->frequency == "Monthly") selected @endif>Monthly</option>
              <option value="Conditional" @if($activity_list->frequency == "Conditional") selected @endif>Conditional</option>
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Activity Type<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="activity_type" style="width: 100%;" data-placeholder="Choose a Activity Type..." required>
              <option value=""></option>
              @foreach($activity_type as $activity_type)
                @if($activity_list->activity_type == $activity_type)
                    <option value="{{ $activity_type }}" selected>{{ $activity_type }}</option>
                  @else
                    <option value="{{ $activity_type }}">{{ $activity_type }}</option>
                  @endif
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
                @if($activity_list->department_id == $department->id)
                  <option value="{{ $department->id }}" selected>{{ $department->department_name }}</option>
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
                @if($leader->name == $activity_list->leader_dept)
                  <option value="{{ $leader->name }}" selected>{{ $leader->employee_id }} - {{ $leader->name }}</option>
                @else
                  <option value="{{ $leader->name }}">{{ $leader->employee_id }} - {{ $leader->name }}</option>
                @endif
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
                @if($foreman->name == $activity_list->foreman_dept)
                  <option value="{{ $foreman->name }}" selected>{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
                @else
                  <option value="{{ $foreman->name }}">{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
                @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
            <label class="col-sm-4">Time<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <input type="text" id="plan_time" name="plan_time" class="form-control timepicker" value="{{ $activity_list->plan_time }}">
            </div>
          </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            @if($id_department != 0)
              <a class="btn btn-danger" href="{{ url('index/activity_list/filter/'.$id_department.'/'.$no) }}">Cancel</a>
            @else
              <a class="btn btn-danger" href="{{ url('index/activity_list') }}">Cancel</a>
            @endif
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script>
  $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

  })
</script>
@stop

