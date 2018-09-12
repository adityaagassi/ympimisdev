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
    <form role="form" method="post" action="{{url('create/material_volume')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material..." required>
              <option value=""></option>
              @foreach($materials as $material)
              <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Category<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="category" style="width: 100%;" data-placeholder="Choose a Category..." required>
              <option value=""></option>
              <option value="FG">FG - Finished Goods</option>
              <option value="KD">KD - Knock Down Parts</option>
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Lot Completion<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="lot_completion" placeholder="Enter Lot Completion" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Lot Transfer<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="lot_transfer" placeholder="Enter Lot Transfer" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Lot Row<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="lot_row" placeholder="Enter Lot Row" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Lot Pallet<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="lot_pallet" placeholder="Enter Lot Pallet" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Lot Carton<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="lot_carton" placeholder="Enter Lot Carton" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Length<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="length" placeholder="Enter Length" required>
              <span class="input-group-addon">meter(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Width<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="width" placeholder="Enter Width" required>
              <span class="input-group-addon">meter(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Height<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <div class="input-group">
              <input min="0" type="number" class="form-control" name="height" placeholder="Enter Height" required>
              <span class="input-group-addon">meter(s)</span>
            </div>
          </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer form-group row">
          <div class="col-sm-4"></div>
          <div class="btn-group">
            <a class="btn btn-danger col-sm-14" href="{{ url('index/material_volume') }}">Cancel</a>
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

  });
    $(document).on("wheel", "input[type=number]", function (e) {
    $(this).blur();
});
</script>
@stop

