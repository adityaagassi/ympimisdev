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
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_method" />
        <div class="form-group row" align="right">
          <label class="col-sm-5">Activity Name</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->activity_name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Activity Alias</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->activity_alias}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Frequency</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->frequency}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Department</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->departments->department_name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$activity_list->updated_at}}
          </div>
        </div>
    </div>
    
  </div>

  @endsection
