@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    User {{ $page }}
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
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  @if (session('status'))
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
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('setting/user')}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Name<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="name" placeholder="Enter Full Name" value="{{$user->name}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">E-mail<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="email" class="form-control" name="email" placeholder="Enter E-mail" value="{{$user->email}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Old Password</label>
          <div class="col-sm-4">
            <input type="password" class="form-control" name="oldPassword" placeholder="Enter Old Password">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">New Password</label>
          <div class="col-sm-4">
            <input type="password" class="form-control" name="newPassword" placeholder="Enter New Password">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Confirm New Password</label>
          <div class="col-sm-4">
            <input type="password" class="form-control" name="confirmPassword" placeholder="Enter Confirm New Password">
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('setting/user') }}">Cancel</a>
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
</script>
@stop

