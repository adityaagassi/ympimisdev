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
    <form role="form" method="post" action="{{url('edit/production_schedule', $production_schedule->id)}}">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
              @foreach($materials as $material)
              @if($production_schedule->material_number == $material->material_number)
              <option value="{{ $material->material_number }}" selected>{{ $material->material_number }} - {{ $material->material_description }}</option>
              @else
              <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Due Date<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" value="{{ date('m/d/Y', strtotime($production_schedule->due_date))}}" id="due_date" nama="due_date">
            </div>
           {{-- <div class="input-group">
            <input type="date" class="form-control" name="due_date" placeholder="Enter Due Date" value="{{ $production_schedule->due_date }}" required>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          </div> --}}
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
        <div class="col-sm-4">
          <div class="input-group">
            <input min="1" type="number" class="form-control" name="quantity" placeholder="Enter Quantity" value="{{ $production_schedule->quantity }}" required>
            <span class="input-group-addon">pc(s)</span>
          </div>
        </div>
      </div>
      <div class="col-sm-4 col-sm-offset-6">
        <div class="btn-group">
          <a class="btn btn-danger" href="{{ url('index/production_schedule') }}">Cancel</a>
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
    $('#due_date').datepicker({
      autoclose: true
    })
  </script>
  @stop

