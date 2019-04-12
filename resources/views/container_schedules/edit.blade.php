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
    <form role="form" method="post" action="{{url('edit/container_schedule', $container_schedule->id)}}">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="form-group row" align="right">
          <label class="col-sm-4">Container ID<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="container_id" placeholder="Enter Container ID" value="{{ $container_schedule->container_id }}" required disabled>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Container<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="container_code" style="width: 100%;" data-placeholder="Choose a Container Code..." required>
              @foreach($containers as $container)
              @if($container_schedule->container_code == $container->container_code)
              <option value="{{ $container->container_code }}" selected>{{ $container->container_code }} - {{ $container->container_name }}</option>
              @else
              <option value="{{ $container->container_code }}">{{ $container->container_code }} - {{ $container->container_name }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Destination<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="destination_code" style="width: 100%;" data-placeholder="Choose a Destination Code..." required>
              @foreach($destinations as $destination)
              @if($container_schedule->destination_code == $destination->destination_code)
              <option value="{{ $destination->destination_code }}" selected>{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
              @else
              <option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Shipment Date<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="shipment_date" value="{{ date('d/m/Y', strtotime($container_schedule->shipment_date)) }}" name="shipment_date" placeholder="Select Shipment Date" required>
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/container_schedule') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
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
    //Turn off input number wheel
    $(document).on("wheel", "input[type=number]", function (e) {
      $(this).blur();
    })
    //Date picker
    $('#shipment_date').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true
    })
  </script>
  @stop

