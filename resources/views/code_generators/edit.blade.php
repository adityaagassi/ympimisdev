
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
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/code_generator', $code_generator->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Prefix</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="prefix" placeholder="Enter Prefix" value="{{$code_generator->prefix}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Index Length</label>
          <div class="col-sm-4">
            <input type="number" class="form-control" name="length" placeholder="Enter Length" value="{{$code_generator->length}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Last Index</label>
          <div class="col-sm-4">
            <input type="number" class="form-control" name="index" placeholder="Enter Index" value="{{$code_generator->index}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Note</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="note" placeholder="Enter Note" value="{{$code_generator->note}}">
          </div>
        </div>
        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/code_generator') }}">Cancel</a>
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
  $(document).on("wheel", "input[type=number]", function (e) {
    $(this).blur();
  })
</script>
@stop

