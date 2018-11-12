
@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Edit {{ $page }}
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
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/navigation', $navigation->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Navigation Code</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="navigation_code" placeholder="Enter Navigation Code" value="{{$navigation->navigation_code}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Navigation Name</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="navigation_name" placeholder="Enter Navigation Name" value="{{$navigation->navigation_name}}">
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/navigation') }}">Cancel</a>
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
  </script>
  @stop

