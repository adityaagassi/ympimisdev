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
    <form role="form" method="post" action="{{url('edit/shipment_schedule', $shipment_schedule->id)}}">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Shipment Month<span class="text-red">*</span></label>
          <div class="col-sm-4">
           <div class="input-group">
            <input id="datepicker" class="form-control" name="st_month" placeholder="mm / yyyy" value="{{ date('m/Y', strtotime($shipment_schedule->st_month))}}" required>

            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          </div>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Sales Order</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="sales_order " placeholder="Enter Sales Order" value="{{$shipment_schedule->sales_order}}">
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Shipment Condition<span class="text-red">*</span></label>
        <div class="col-sm-4" align="left">
          <select class="form-control select2" name="shipment_condition_code" style="width: 100%;" data-placeholder="Choose a Shipment Condition..." required>
            @foreach($shipment_conditions as $shipment_condition)
            @if($shipment_schedule->shipment_condition_code == $shipment_condition->shipment_condition_code)
            <option value="{{ $shipment_condition->shipment_condition_code }}" selected>{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
            @else
            <option value="{{ $shipment_condition->shipment_condition_code }}">{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
            @endif
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Destination<span class="text-red">*</span></label>
        <div class="col-sm-4" align="left">
          <select class="form-control select2" name="destination_code" style="width: 100%;" data-placeholder="Choose a Destination..." required>
            @foreach($destinations as $destination)
            @if($shipment_schedule->destination_code == $destination->destination_code)
            <option value="{{ $destination->destination_code }}" selected>{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
            @else
            <option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
            @endif
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Material<span class="text-red">*</span></label>
        <div class="col-sm-4" align="left">
          <select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material..." required>
            @foreach($materials as $material)
            @if($shipment_schedule->material_number == $material->material_number)
            <option value="{{ $material->material_number }}" selected>{{ $material->material_number }} - {{ $material->material_description }}</option>
            @else
            <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
            @endif
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">HPL<span class="text-red">*</span></label>
        <div class="col-sm-4" align="left">
          <select class="form-control select2" name="hpl" style="width: 100%;" data-placeholder="Choose a HPL..." required>
            @foreach($hpls as $hpl)
            @if($shipment_schedule->hpl == $hpl)
            <option value="{{ $hpl }}" selected>{{ $hpl }}</option>
            @else
            <option value="{{ $hpl }}">{{ $hpl }}</option>
            @endif
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Shipment Date<span class="text-red">*</span></label>
        <div class="col-sm-4">
         <div class="input-group">
          <input type="date" class="form-control" name="st_date" placeholder="Enter Shipment Date" value="{{ $shipment_schedule->st_date }}" required>
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
      </div>
    </div>
    <div class="form-group row" align="right">
      <label class="col-sm-4">Bill of Lading<span class="text-red">*</span></label>
      <div class="col-sm-4">
       <div class="input-group">
        <input type="date" class="form-control" name="bl_date" placeholder="Enter Bill of Lading Date" value="{{ $shipment_schedule->bl_date }}" required>
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
    </div>
  </div>
  <div class="form-group row" align="right">
    <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
    <div class="col-sm-4">
      <div class="input-group">
        <input min="1" type="number" class="form-control" name="quantity" placeholder="Enter Quantity" value="{{ $shipment_schedule->quantity }}" required>
        <span class="input-group-addon">pc(s)</span>
      </div>
    </div>
  </div>
  <div class="col-sm-4 col-sm-offset-6">
    <div class="btn-group">
      <a class="btn btn-danger" href="{{ url('index/shipment_schedule') }}">Cancel</a>
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
      autoclose: true,
      format: "mm/yyyy",
      viewMode: "months", 
      minViewMode: "months"
    })

  </script>
  @stop

