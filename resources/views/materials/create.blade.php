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
    <form role="form" method="post" action="{{url('create/material')}}">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material Number<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="material_number" placeholder="Enter Material Number" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material Description<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="material_description" placeholder="Enter Material Description" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Base Unit<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="base_unit" placeholder="Enter Base Unit" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Issue Storage Location<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="issue_storage_location" placeholder="Enter Issue Storage Location" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Work Center<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="work_center" placeholder="Enter Work Center" required>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Origin Group<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="origin_group_code" style="width: 100%;" data-placeholder="Choose an Origin Group..." required>
              <option value=""></option>
              @foreach($origin_groups as $origin_group)
              <option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">HPL<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="hpl" style="width: 100%;" data-placeholder="Choose a HPL..." required>
              <option value=""></option>
              @foreach($hpls as $hpl)
              <option value="{{ $hpl }}">{{ $hpl }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Category<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="category" style="width: 100%;" data-placeholder="Choose a Category..." required>
              <option value=""></option>
              @foreach($categories as $category)
              <option value="{{ $category }}">{{ $category }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Model<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="model" placeholder="Enter Model" required>
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/material') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
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
</script>
@stop

