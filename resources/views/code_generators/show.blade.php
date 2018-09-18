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
    <form role="form">
      <div class="box-body">
        <div class="form-group row" align="right">
          <label class="col-sm-5">Prefix</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->prefix}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Length Index</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->length}} Character(s)
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Index</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->index}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Note</label>
          <div class="col-sm-5" align="left">
            {{ strtoupper($code_generator->note)}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->updated_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$code_generator->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

