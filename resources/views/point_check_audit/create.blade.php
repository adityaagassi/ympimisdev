@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Create Point Check
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
    <form role="form" method="post" action="{{url('index/point_check_audit/store/'.$id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        {{-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"> --}}
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose a Product..." required>
                <option value=""></option>
                @foreach($product as $product)
                  <option value="{{ $product->origin_group_name }}">{{ $product->origin_group_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-4">
              <input type="text" class="form-control" name="proses" placeholder="Enter Proses" required pattern="([A-z0-9À-ž\s]){2,}" title="Tidak Boleh Simbol">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Point Pengecekan<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="point_check"></textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Cara Cek<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <textarea id="editor2" class="form-control" style="height: 200px;" name="cara_cek"></textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <select class="form-control select2" name="foreman" style="width: 100%;" data-placeholder="Choose a Foreman..." required>
                <option value=""></option>
                @foreach($foreman as $foreman)
                  <option value="{{ $foreman->name }}">{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        {{-- </div> --}}
        {{-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"> --}}
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/point_check_audit/index/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
            </div>
          </div>
        {{-- </div> --}}
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script>
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
  <script language="JavaScript">
      function readURL(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    $('#blah').show();
                      $('#blah')
                          .attr('src', e.target.result);
                  };

                  reader.readAsDataURL(input.files[0]);
              }
          }
    </script>
  @stop

