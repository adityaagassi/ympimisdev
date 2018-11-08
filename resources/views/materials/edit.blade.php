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
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/material', $material->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material Number<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="material_number" placeholder="Enter Full Name" value="{{$material->material_number}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Material Description<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="material_description" placeholder="Enter Material Description" value="{{$material->material_description}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Base Unit<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="base_unit" placeholder="Enter Base Unit" value="{{$material->base_unit}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Issue Storage Location<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="issue_storage_location" placeholder="Enter Issue Storage Location" value="{{$material->issue_storage_location}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Origin Group<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="origin_group_code" style="width: 100%;" >
              @foreach($origin_groups as $origin_group)
              @if($material->origin_group_code == $origin_group->origin_group_code)
              <option value="{{ $origin_group->origin_group_code }}" selected>{{ $origin_group->origin_group_name }}</option>
              @else
              <option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
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
              @if($material->hpl == $hpl)
              <option value="{{ $hpl }}" selected>{{ $hpl }}</option>
              @else
              <option value="{{ $hpl }}">{{ $hpl }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Category<span class="text-red">*</span></label>
          <div class="col-sm-4" align="left">
            <select class="form-control select2" name="category" style="width: 100%;" data-placeholder="Choose a Category..." required>
              @foreach($categories as $category)
              @if($material->category == $category)
              <option value="{{ $category }}" selected>{{ $category }}</option>
              @else
              <option value="{{ $category }}">{{ $category }}</option>
              @endif
              @endforeach
            </select>
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

