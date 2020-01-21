@extends('layouts.master')
@section('header')
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
    <form role="form" method="post" action="{{url('index/audit_report_activity/store/'.$id)}}" enctype="multipart/form-data">
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
                <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
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
                <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Audit Schedule<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="audit_guidance_id" style="width: 100%;" data-placeholder="Choose a Schedule This Month..." required>
                <option value=""></option>
                @foreach($guidance as $guidance)
                  <option value="{{ $guidance->id }}">{{ $guidance->no_dokumen }} - {{ $guidance->nama_dokumen }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nama Dokumen<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="nama_dokumen" placeholder="Enter Nama Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nomor Dokumen<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="no_dokumen" placeholder="Nomor Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian Aktual Proses<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="kesesuaian_aktual_proses"></textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tindakan Perbaikan<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="tindakan_perbaikan" placeholder="Tindakan Perbaikan" value="-">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Target<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="target" placeholder="Select Date">
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kelengkapan Point Safety<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kelengkapan_point_safety" placeholder="Kelengkapan Point Safety">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian QC Kouteihyo<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kesesuaian_qc_kouteihyo" placeholder="Kesesuaian QC Kouteihyo">
            </div>
          </div>
          <!-- <div class="form-group row" align="right">
            <label class="col-sm-4">Operator<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="operator" style="width: 100%;" data-placeholder="Choose an Operator..." required>
                <option value=""></option>
                @foreach($operator as $operator)
                <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->
          <div class="form-group row" align="right" id='scanner'>
            <label class="col-sm-4">Scan QR Code<span class="text-red">*</span></label>
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
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">NIK</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="operator" id="input_employee_id" placeholder="Enter NIK">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="" value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
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
      $('#email').val('');
      $('#password').val('');

      var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");

    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true);
      video.play();
      requestAnimationFrame(tick);
    });

    function tick() {
      loadingMessage.innerText = "⌛ Loading video..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        if (code) {
          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
          outputMessage.hidden = true;
          $('#scanner').hide();
          // document.getElementById("input_employee_id").value = code.data.substr(0, 9);
          var data = {
            employee_id : code.data.substr(0, 9)
          }
          $.get('{{ url("index/getemployee") }}', data, function(result, status, xhr){
            if(result.status){
              document.getElementById("input_employee_id").value = result.name;
            }
            else{
              alert('Attempt to retrieve data failed');
            }
          });
        } else {
          outputMessage.hidden = false;
        }
      }
      requestAnimationFrame(tick);
    }
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

