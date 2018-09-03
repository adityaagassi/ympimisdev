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
    <form role="form" method="post" action="{{url('create/container_schedule')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Container<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="container_code" style="width: 100%;" data-placeholder="Choose a Container Code..." required>
              <option value=""></option>
              @foreach($containers as $container)
              <option value="{{ $container->container_code }}">{{ $container->container_code }} - {{ $container->container_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Destination<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="destination_code" style="width: 100%;" data-placeholder="Choose a Destination Code..." required>
              <option value=""></option>
              @foreach($destinations as $destination)
              <option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Shipment Date<span class="text-red">*</span></label>
          <div class="col-sm-4">
           <div class="input-group">
            <input type="date" class="form-control" name="shipment_date" placeholder="Enter Shipment Date" required>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row" align="right">
      <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
      <div class="col-sm-4">
        <div class="input-group">
          <input type="number" class="form-control" name="quantity" placeholder="Enter Quantity" required>
          <span class="input-group-addon">unit(s)</span>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer form-group row">
      <div class="col-sm-4"></div>
      <div class="btn-group">
        <a class="btn btn-danger col-sm-14" href="{{ url('index/container_schedule') }}">Cancel</a>
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
    //Initialize Select2 Elements
    $('.select2').select2()
  })
    //Turn off input number wheel
    $(document).on("wheel", "input[type=number]", function (e) {
      $(this).blur();
    })
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })
  </script>
  @stop

