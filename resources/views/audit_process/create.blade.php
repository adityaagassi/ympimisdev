@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor-full/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Create {{ $activity_name }}
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
    <form role="form" method="post" action="{{url('index/audit_process/store/'.$id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <input type="hidden" name="department" id="department" class="form-control" value="{{ $departments }}" readonly>
          <input type="hidden" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
          <input type="hidden" name="section" id="section" class="form-control" value="assembly process" readonly>
          <input type="hidden" name="periode" id="periode" class="form-control" value="{{ $fy }}" readonly>
          
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose a Product..." required id="product">
                  <option value=""></option>
                  @foreach($product as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="proses" id="proses" class="form-control" required placeholder="Enter Proses">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Operator<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="operator" style="width: 100%;" data-placeholder="Choose a Operator..." required id="operator">
                  <option value=""></option>
                  @foreach($operator as $operator)
                    <option value="{{ $operator->name }}">{{ $operator->name }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Auditor<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="auditor" style="width: 100%;" data-placeholder="Choose a Auditor..." required id="auditor">
                  <option value=""></option>
                  @foreach($auditor as $auditor)
                    <option value="{{ $auditor->name }}">{{ $auditor->name }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Cara Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="cara_proses"></textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi Cara Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="kondisi_cara_proses" value="Good">OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi_cara_proses" value="Not Good">NG</label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Pemahaman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor2" class="form-control" style="height: 200px;" name="pemahaman"></textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi Pemahaman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="kondisi_pemahaman" value="Good">OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi_pemahaman" value="Not Good">NG</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Keterangan<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="keterangan" id="keterangan" class="form-control" required placeholder="Enter Keterangan" value="-">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="leader" id="leader" class="form-control" value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="foreman" id="foreman" class="form-control" value="{{ $foreman }}" readonly>
            </div>
          </div>
        </div>
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/audit_process/index/'.$id) }}">Cancel</a>
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
    
    // var product = document.getElementById("product");
    // var proses = document.getElementById("proses");

    // var productselected= product.options[product.selectedIndex];
    // var prosesselected= proses.options[proses.selectedIndex];

    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editor2' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
  </script>
  @stop

