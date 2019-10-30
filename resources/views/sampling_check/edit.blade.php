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
    <form role="form" method="post" action="{{url('index/sampling_check/update/'.$id.'/'.$sampling_check->id)}}" enctype="multipart/form-data">
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
                  @if($sampling_check->section == $section->section_name)
                    <option value="{{ $section->section_name }}" selected>{{ $section->section_name }}</option>
                  @else
                    <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Sub Section<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
                <option value=""></option>
                @foreach($subsection as $subsection)
                  @if($sampling_check->subsection == $subsection->sub_section_name)
                    <option value="{{ $subsection->sub_section_name }}" selected>{{ $subsection->sub_section_name }}</option>
                  @else
                    <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
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
                  @if($sampling_check->product == $product->origin_group_name)
                    <option value="{{ $product->origin_group_name }}" selected>{{ $product->origin_group_name }}</option>
                  @else
                    <option value="{{ $product->origin_group_name }}">{{ $product->origin_group_name }}</option>
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
                <input type="text" class="form-control pull-right" id="date" name="date" value="{{ $sampling_check->date }}">
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nomor Seri / Part<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="no_seri_part" placeholder="Enter Nomor Seri / Part" required value="{{ $sampling_check->no_seri_part }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Jumlah Cek<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="number" class="form-control" name="jumlah_cek" placeholder="Enter Jumlah Cek" required value="{{ $sampling_check->jumlah_cek }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="leader" style="width: 100%;" data-placeholder="Choose a Leader..." required>
                <option value=""></option>
                @foreach($leaderForeman as $leaderForeman)
                  @if($sampling_check->leader == $leaderForeman->name)
                    <option value="{{ $leaderForeman->name }}" selected>{{ $leaderForeman->employee_id }} - {{ $leaderForeman->name }}</option>
                  @else
                    <option value="{{ $leaderForeman->name }}">{{ $leaderForeman->employee_id }} - {{ $leaderForeman->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="foreman" style="width: 100%;" data-placeholder="Choose a Foreman..." required>
                <option value=""></option>
                @foreach($foreman as $foreman)
                  @if($sampling_check->foreman == $foreman->name)
                    <option value="{{ $foreman->name }}" selected>{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
                  @else
                    <option value="{{ $foreman->name }}">{{ $foreman->employee_id }} - {{ $foreman->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/sampling_check/index/'.$id) }}">Cancel</a>
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

