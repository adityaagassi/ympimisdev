@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
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
  <div class="box box-primary">
    <div class="box-header with-border">
    </div>  
    <form role="form">
      <div class="box-body">
        <div class="form-group row" align="right">
          <label class="col-sm-5">Role Code</label>
          <div class="col-sm-5" align="left">
            {{$role->role_code}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Role Name</label>
          <div class="col-sm-5" align="left">
            {{$role->role_name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Permissions</label>
          <div class="col-sm-5" align="left">
            @foreach($permissions as $permission)
            {{ $permission->navigation->navigation_name }};
            @endforeach
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$role->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$role->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$role->created_at}}
          </div>
        </div>
      </form>
    </div>
  </div>

  @endsection