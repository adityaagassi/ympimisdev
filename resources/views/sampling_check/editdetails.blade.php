@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Edit Poin Sampling Check
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
    <form role="form" method="post" action="{{url('index/sampling_check/updatedetails/'.$id.'/'.$sampling_check_details_id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Point Check<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 250px;" name="point_check">{{ $sampling_check_details->point_check }}</textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Hasil Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor2" class="form-control" style="height: 250px;" name="hasil_check">{{ $sampling_check_details->hasil_check }}</textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Picture Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <img width="100px" src="{{ url('/data_file/sampling_check/'.$sampling_check_details->picture_check) }}">
              <br>
              <input type="hidden" class="form-control" name="picture_check" placeholder="Enter File" required value="{{ $sampling_check_details->picture_check }}">   
              <input type="file" class="form-control" name="file" placeholder="Enter File" onchange="readURL(this)">              
              <img width="200px" id="blah" src="" style="display: none" alt="your image" />
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">PIC Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="pic_check" style="width: 100%;" data-placeholder="Choose a PIC Check..." required>
                <option value=""></option>
                @foreach($operator as $operator)
                 @if($sampling_check_details->pic_check == $operator->name)
                   <option value="{{ $operator->name }}" selected>{{ $operator->employee_id }} - {{ $operator->name }}</option>
                 @else
                   <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
                 @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Sampling By<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="sampling_by" style="width: 100%;" data-placeholder="Choose a Sampling By..." required>
                <option value=""></option>
                @foreach($leaderForeman as $leaderForeman)
                  @if($sampling_check_details->sampling_by == $leaderForeman->name)
                   <option value="{{ $leaderForeman->name }}" selected>{{ $leaderForeman->employee_id }} - {{ $leaderForeman->name }}</option>
                 @else
                   <option value="{{ $leaderForeman->name }}">{{ $leaderForeman->employee_id }} - {{ $leaderForeman->name }}</option>
                 @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/sampling_check/details/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Update</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    $('#date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
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

