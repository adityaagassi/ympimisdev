@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Blank page
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li>
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
      <h3 class="box-title">Create New User</h3>
    </div>  
    <form role="form" method="post" action="{{url('edit/user', $user->id)}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_method" />
        <div class="form-group row">
          <label class="col-sm-2">Name</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="name" placeholder="Enter Full Name" value="{{$user->name}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Username</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="username" placeholder="Enter Username" value="{{$user->username}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">E-mail</label>
          <div class="col-sm-5">
            <input type="email" class="form-control" name="email" placeholder="Enter E-mail" value="{{$user->email}}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Password</label>
          <span class="margin" style="color:red">*Do not fill the password if changes are not needed</span>
          <div class="col-sm-5">
            <input type="password" class="form-control" name="password" placeholder="Enter Password">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Confirm Password</label>
          <span class="margin" style="color:red">*Do not fill the password if changes are not needed</span>
          <div class="col-sm-5">
            <input type="password" class="form-control" name="password_confirmation" placeholder="Enter Confirm Password">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">User Level</label>
          <div class="col-sm-5">
            <select class="form-control select2" name="level" style="width: 100%;" >
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
            </select>
          </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer form-group row">
        	<div class="col-sm-6"></div>
          <a class="btn btn-danger col-sm-1" href="{{ url('index/user') }}">Cancel</a>
          <button type="submit" class="btn btn-primary col-sm-1">Submit</button>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

  @section('scripts')
  <script>
    $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

  })
</script>
@stop

