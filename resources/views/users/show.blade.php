@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
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
    <form role="form" method="post" action="{{url('edit/user', $user->id)}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_method" />
        <div class="form-group row" align="right">
          <label class="col-sm-5">Name</label>
          <div class="col-sm-5" align="left">
            {{$user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Username</label>
          <div class="col-sm-5" align="left">
            {{$user->username}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">E-mail</label>
          <div class="col-sm-5" align="left">
            {{$user->email}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">User Role</label>
          <div class="col-sm-5" align="left">
            {{$user->role->role_name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            @foreach($created_bys as $created_by)
            @if($user->created_by == $created_by->id)
            {{ $created_by->name }}
            @endif
            @endforeach
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$user->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$user->created_at}}
          </div>
        </div>

      </form>
    </div>
    
  </div>

  @endsection
