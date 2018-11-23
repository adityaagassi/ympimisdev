
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">

    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New User</h3> --}}
    </div>  
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
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/role') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </form>
    </div>
    
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

