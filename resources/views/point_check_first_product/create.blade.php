@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Buat Point Audit Produk Pertama
  </h1>
  <ol class="breadcrumb">
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
  <div class="box box-solid">
    <form role="form" method="post" action="{{url('index/point_check_first_product/store/'.$id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        {{-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"> --}}
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <select class="form-control select2" name="proses" style="width: 100%;" data-placeholder="Pilih Proses" required>
                <option value=""></option>
                @foreach($proses as $proses)
                  <option value="{{ $proses }}">{{ $proses }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Point Check<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="point_check"></textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Standard<span class="text-red">*</span></label>
            <div class="col-sm-4" align="left">
              <input type="text" class="form-control" name="standar" placeholder="Masukkan Standard" required>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/point_check_first_product/index/'.$id) }}">Cancel</a>
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
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
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

