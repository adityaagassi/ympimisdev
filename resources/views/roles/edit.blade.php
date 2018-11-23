
@extends('layouts.master')
@section('header')
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
  <div class="box box-primary">
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/role', $role->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Role Code</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_code" placeholder="Enter Role Code" value="{{$role->role_code}}" disabled>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Role Name</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_name" placeholder="Enter Role Name" value="{{$role->role_name}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4" style="text-align: right;">Role Permissions</label>
          <br>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-3">
              @foreach($nav_admins as $nav_admin)
              @if(in_array($nav_admin->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_admin->navigation_code }}" checked> {{ $nav_admin->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_admin->navigation_code }}"> {{ $nav_admin->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_masters as $nav_master)
              @if(in_array($nav_master->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_master->navigation_code }}" checked> {{ $nav_master->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_master->navigation_code }}"> {{ $nav_master->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_services as $nav_service)
              @if(in_array($nav_service->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_service->navigation_code }}" checked> {{ $nav_service->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_service->navigation_code }}"> {{ $nav_service->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_reports as $nav_report)
              @if(in_array($nav_report->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_report->navigation_code }}" checked> {{ $nav_report->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_report->navigation_code }}"> {{ $nav_report->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
          </div>
        </div>

{{-- 
        <div class="col-sm-12">
          @foreach($navigations as $navigation)
          <div class="col-sm-3">
            @if(in_array($navigation->navigation_code, $permissions))
            <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $navigation->navigation_code }}" checked>
              {{ $navigation->navigation_name }}</label>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $navigation->navigation_code }}">
                {{ $navigation->navigation_name }}</label>
                @endif
              </div>
              @endforeach
            </div> --}}


            <div class="col-sm-4 col-sm-offset-6">
              <div class="btn-group">
                <a class="btn btn-danger" href="{{ url('index/role') }}">Cancel</a>
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
      <script>
        $(function () {
          $('.select2').select2()
        })

        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
          checkboxClass: 'icheckbox_minimal-red',
          radioClass   : 'iradio_minimal-red'
        })
      </script>
      @stop

