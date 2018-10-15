@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
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
    <form role="form" method="post" action="{{url('create/code_generator')}}">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Prefix<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="prefix" placeholder="Enter Prefix" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Index Length<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="number" class="form-control" name="length" placeholder="Enter Index Length" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Last Index<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="number" class="form-control" name="index" placeholder="Enter Last Index" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Note<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="note" placeholder="Enter Note" required>
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


  @endsection

  @section('scripts')

  <script>
   $(document).on("wheel", "input[type=number]", function (e) {
    $(this).blur();
  })
   $(function () {

    $('.select2').select2()

  })
</script>
@stop

