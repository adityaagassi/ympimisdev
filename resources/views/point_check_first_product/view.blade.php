@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Detail Point Check
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Detail User</h3> --}}
    </div>  
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_method" />
        <div class="form-group row" align="right">
          <label class="col-sm-5">Proses</label>
          <div class="col-sm-5" align="left">
            {{$pointCheckFirstProduct->proses}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Point Pengecekan</label>
          <div class="col-sm-5" align="left">
            <?php echo $pointCheckFirstProduct->point_check ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Cara Cek</label>
          <div class="col-sm-5" align="left">
          <?php echo $pointCheckFirstProduct->standar ?>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created By</label>
          <div class="col-sm-5" align="left">
            {{$pointCheckFirstProduct->user->name}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Created At</label>
          <div class="col-sm-5" align="left">
            {{$pointCheckFirstProduct->created_at}}
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-5">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$pointCheckFirstProduct->updated_at}}
          </div>
        </div>
        <a class="btn btn-info" href="{{ url('index/point_check_first_product/index/'.$id) }}">Cancel</a>
    </div>
  </div>

  @endsection
