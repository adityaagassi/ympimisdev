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
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/user', $user->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Name<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="name" placeholder="Enter Full Name" value="{{$user->name}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Username<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="username" placeholder="Enter Username" value="{{$user->username}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">E-mail<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="email" class="form-control" name="email" placeholder="Enter E-mail" value="{{$user->email}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Password</label>
          <div class="col-sm-4">
            <input type="password" class="form-control" name="password" placeholder="Enter Password">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Confirm Password</label>
          <div class="col-sm-4">
            <input type="password" class="form-control" name="password_confirmation" placeholder="Enter Confirm Password">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">User Role<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="role_code" style="width: 100%;" >
              @foreach($roles as $role)
              @if($role->role_code == $user->role_code)
              <option value="{{ $role->role_code }}" selected>{{ $role->role_name }}</option>
              @else
              <option value="{{ $role->role_code }}">{{ $role->role_name }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/user') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection

@section('scripts')
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

  })
</script>
@stop

