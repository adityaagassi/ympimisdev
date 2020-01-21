@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Edit {{ $activity_name }}
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
    <form role="form" method="post" action="{{url('index/training_report/update/'.$id.'/'.$training_report->id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Department<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="department" placeholder="Enter Department" required value="{{ $departments }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Section<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="section" style="width: 100%;" data-placeholder="Choose a Section..." required>
                <option value=""></option>
                @foreach($section as $section)
                @if($training_report->section == $section->section_name)
                  <option value="{{ $section->section_name }}" selected>{{ $section->section_name }}</option>
                @else
                  <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose a Product..." required>
                <option value=""></option>
                @foreach($product as $product)
                @if($training_report->product == $product->origin_group_name)
                  <option value="{{ $product->origin_group_name }}" selected>{{ $product->origin_group_name }}</option>
                @else
                  <option value="{{ $product->origin_group_name }}">{{ $product->origin_group_name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Periode<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="periode" style="width: 100%;" data-placeholder="Choose a Periode..." required>
                <option value=""></option>
                @foreach($periode as $periode)
                @if($training_report->periode == $periode->fiscal_year)
                  <option value="{{ $periode->fiscal_year }}" selected>{{ $periode->fiscal_year }}</option>
                @else
                  <option value="{{ $periode->fiscal_year }}">{{ $periode->fiscal_year }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Date<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="date" value="{{ $training_report->date }}">
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Waktu<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" id="time" name="time" class="form-control timepicker" value="{{ $training_report->time }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tema<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="theme" placeholder="Enter Theme" required value="{{ $training_report->theme }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Isi Training<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 250px;" name="isi_training">{{ $training_report->isi_training }}</textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tujuan<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="tujuan" placeholder="Enter Tujuan" required value="{{ $training_report->tujuan }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Standard<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="standard" placeholder="Enter Standard" required value="{{ $training_report->standard }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Trainer<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="trainer" style="width: 100%;" data-placeholder="Choose a Trainer..." required>
                <option value=""></option>
                @foreach($trainer as $trainer)
                @if($training_report->trainer == $trainer->name)
                  <option value="{{ $trainer->name }}" selected="">{{ $trainer->employee_id }} - {{ $trainer->name }}</option>
                @else
                  <option value="{{ $trainer->name }}">{{ $trainer->employee_id }} - {{ $trainer->name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="Enter Leader" required value="{{ $training_report->leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="foreman" placeholder="Enter Foreman" required value="{{ $training_report->foreman }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Notes / Catatan<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor2" class="form-control" style="height: 250px;" name="notes">{{ $training_report->notes }}</textarea>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/training_report/index/'.$id) }}">Cancel</a>
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

