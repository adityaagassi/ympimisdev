@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
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
    <div class="box-header with-border">
    </div>  
    <form role="form" method="post" action="{{url('create/role')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row">
          <label class="col-sm-4" style="text-align: right;">Role Code<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_code" placeholder="Enter Role Code" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4" style="text-align: right;">Role Name<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_name" placeholder="Enter Role Name" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4" style="text-align: right;">Role Permissions</label>
          <br>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-3">
              @foreach($nav_admins as $nav_admin)<label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_admin->navigation_code }}"> {{ $nav_admin->navigation_name }}</label><br>
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_masters as $nav_master)<label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_master->navigation_code }}"> {{ $nav_master->navigation_name }}</label><br>
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_services as $nav_service)<label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_service->navigation_code }}"> {{ $nav_service->navigation_name }}</label><br>
              @endforeach
            </div>
            <div class="col-md-3">
              @foreach($nav_reports as $nav_report)<label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_report->navigation_code }}"> {{ $nav_report->navigation_name }}</label><br>
              @endforeach
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/role') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
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

