@extends('layouts.master')
@section('header')
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Buat Audit IK
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
    <form role="form" method="post" action="{{url('index/audit_report_activity/store/'.$id)}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Department</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="department" placeholder="Masukkan Department" required value="{{ $departments }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Section</label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="section" style="width: 100%;" data-placeholder="Pilih Section" required>
                <option value=""></option>
                @foreach($section as $section)
                <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Group</label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group" required>
                <option value=""></option>
                @foreach($subsection as $subsection)
                <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Audit Schedule</label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="audit_guidance_id" style="width: 100%;" data-placeholder="Pilih Schedule" required>
                <option value=""></option>
                @foreach($guidance as $guidance)
                  <option value="{{ $guidance->id }}">({{ date('M Y',strtotime($guidance->month)) }}) {{ $guidance->no_dokumen }} - {{ $guidance->nama_dokumen }}</option>
                @endforeach
              </select>
              <br>
              <br>
              <a class="btn btn-info pull-right" target="_blank" style="margin-left: 5px" href="{{url('index/audit_guidance/index/'.$id)}}">
                Manage Schedule
              </a>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nama Dokumen</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="nama_dokumen" placeholder="Masukkan Nama Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nomor Dokumen</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="no_dokumen" placeholder="Nomor Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian Aktual Proses</label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="kesesuaian_aktual_proses"></textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tindakan Perbaikan</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="tindakan_perbaikan" placeholder="Tindakan Perbaikan" value="-">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Target</label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="target" placeholder="Pilih Tanggal Target">
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kelengkapan Point Safety</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kelengkapan_point_safety" placeholder="Kelengkapan Point Safety">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian QC Kouteihyo</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kesesuaian_qc_kouteihyo" placeholder="Kesesuaian QC Kouteihyo">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4">Hasil Keseluruhan</label>
            <div class="col-sm-8">
              <div class="radio">
                <label><input type="radio" name="condition" value="Sesuai">Sesuai</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="condition" value="Tidak Sesuai">Tidak Sesuai</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Operator</label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="operator" style="width: 100%;" data-placeholder="Pilih Operator" required>
                <option value=""></option>
                @foreach($operator as $operator)
                <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
                @endforeach
              </select>
              <!-- <input type="text" name="scan_operator" style="width: 100%;" class="form-control" placeholder="Scan ID Card Here">
              <input type="text" name="operator" style="width: 100%;" class="form-control" placeholder="Scan ID Card Here" disabled> -->
            </div>
          </div>
          <!-- <div class="form-group row" align="right" id='scanner'>
            <label class="col-sm-4">Scan QR Code</label>
            <div class="col-sm-8" align="left">
              <div class="col-xs-12">
                <div class="col-xs-6 col-xs-offset-3">
                  <div id="loadingMessage">
                    🎥 Unable to access video stream (please make sure you have a webcam enabled)
                  </div>
                  <canvas style="width: 240px; height: 160px;" id="canvas" hidden></canvas>
                  <div id="output" hidden>
                    <div id="outputMessage">No QR code detected.</div>
                  </div>
                </div>                  
              </div>
            </div>
          </div> -->
          <!-- <div class="form-group row" align="right">
            <label class="col-sm-4">NIK</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="operator" id="input_employee_id" placeholder="Masukkan NIK">
            </div>
          </div> -->
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="" value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="foreman" placeholder="" value="{{ $foreman }}" readonly>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/audit_report_activity/index/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
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

