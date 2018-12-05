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
    <form role="form" method="post" action="{{url('create/production_schedule')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
              <option value=""></option>
              @foreach($materials as $material)
              <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
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
            <input type="text" class="form-control pull-right" id="due_date" name="due_date">
          </div>
        </div>
      </div>
      <div class="form-group row" align="right">
        <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
        <div class="col-sm-4">
          <div class="input-group">
            <input min="1" type="number" class="form-control" name="quantity" placeholder="Enter Quantity" required>
            <span class="input-group-addon">pc(s)</span>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
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

