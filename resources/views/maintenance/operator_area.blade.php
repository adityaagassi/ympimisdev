@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
  thead>tr>th{
    text-align:center;
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    text-align: center;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }

  .content-wrapper {
    padding-top: 0px !important;
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    {{ $title }}
    <small><span class="text-purple"> {{ $title_jp }}</span></small>
  </h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%; border: 1px solid #222f3e; border-radius: 5px" id="this_area">
            <div class="input-group input-group-lg">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
                <i class="fa fa-qrcode"></i>
              </div>
              <input type="text" class="form-control" placeholder="SCAN QR MESIN / AREA" id="item_scan">
              <div class="input-group-btn">
                <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modalScan"><i class="fa fa-qrcode"></i> Scan QR</button>
              </div>
            </div>
            <br>
            <input type="hidden" id="code">

            <table class="table" width="100%">
              <tr>
                <th width="30%">Nama Operator</th>
                <td><input type="text" id="op_name" class="form-control" readonly></td>
              </tr>
              <tr>
                <th>Deskripsi Mesin / Area</th>
                <td><input type="text" id="desc" class="form-control" readonly></td>
              </tr>
              <tr>
                <th>Lokasi</th>
                <td><input type="text" id="loc" class="form-control" readonly></td>
              </tr>
              <tr>
                <th>Waktu Logged-In</th>
                <td>
                  <input type="text" id="login_time" class="form-control" readonly>
                  <span id="login_time_2" style="font-size: 25px; font-weight: bold"></span>
                </td>
              </tr>
            </table>

          </div>

          <center id='ket' style='display: none'>
            <div class="col-xs-12">
              <span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> PILIH PEKERJAAN <i class="fa fa-angle-double-down"></i></span>
            </div>

            <div class="col-xs-4">
              <button class="btn btn-primary" style="width: 100%; height: 50px" onclick="logged('spk')">SPK</button>
            </div>
            <div class="col-xs-4">
              <button class="btn btn-success" style="width: 100%; height: 50px" onclick="logged('planned')">PLANNED</button>
            </div>
            <div class="col-xs-4">
              <button class="btn btn-danger" style="width: 100%; height: 50px" onclick="logged('job')">JOB MAINTENANCE</button>
            </div>
          </center>

          <center id='logout' style='display: none'>
            <div class="col-xs-12">
              <span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> PILIH PEKERJAAN <i class="fa fa-angle-double-down"></i></span>
            </div>

            <div class="col-xs-12">
              <button class="btn btn-danger" style="width: 100%; height: 50px" onclick="logged('logout')">LOGGED OUT</button>
            </div>
          </center>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalScan">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div class="col-xs-12" style="background-color: #3c8dbc;">
            <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">SCAN QR MACHINE / AREA</h1>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div id='scanner' class="col-xs-12">
                <div class="col-xs-12">
                  <div id="loadingMessage">
                    🎥 Unable to access video stream (please make sure you have a webcam enabled)
                  </div>
                  <canvas style="width: 100%;" id="canvas" hidden></canvas>
                  <div id="output" hidden>
                    <div id="outputMessage">No QR code detected.</div>
                  </div>
                </div>                  
              </div>

              <br>
              <p style="visibility: hidden;">camera</p>
              <!-- <input type="hidden" id="apar_code"> -->
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var machine;
  var area;
  var loc;
  var time;

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $("#item_scan").val("");
    $("#code").val("");
    $("#desc").val("");
    $("#loc").val("");
    $("#login_time").val("");
    $("#login_time_2").text("");
    $("#op_name").val("{{ Auth::user()->name }}");

    machine = <?php echo json_encode($machine); ?>;
    area = <?php echo json_encode($area); ?>;
    loc = <?php echo json_encode($loc_arr); ?>;

    console.log(loc);

    getOpLoc();
  });


  function stopScan() {
    $('#modalScan').modal('hide');

  }

  function videoOff() {
    vdo.pause();
    vdo.src = "";
    vdo.srcObject.getTracks()[0].stop();
  }

  $( "#modalScan" ).on('shown.bs.modal', function(){
    showCheck();
  });

  $('#modalScan').on('hidden.bs.modal', function () {
    videoOff();
  });

  function showCheck() {
    var video = document.createElement("video");
    vdo = video;
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

          // document.getElementById("qr_apar").value = code.data;

          checkCode(video, code.data);

        } else {
          outputMessage.hidden = false;
        }
      }
      requestAnimationFrame(tick);
    }

    $('#scanner').show();
  }

  function checkCode(video, code) {
    stat = false;
    var machine_detail = "";
    var loc = "";

    $.each(machine, function(index, value){
      if (value.machine_id == code) {
        stat = true;
        machine_detail = value.description+"   -   "+value.area;
        loc = value.location;
      }
    })

    $.each(area, function(index, value){
      if (value.area_code == code) {
        stat = true;
        machine_detail = value.area;
        loc = value.area;
      }
    })

    if (stat) {
      $('#scanner').hide();
      videoOff();

      $("#item_scan").val(code);
      $("#code").val(code);
      $("#loc").val(loc);
      $("#desc").val(machine_detail);
      openSuccessGritter('Success', 'QR Code Successfully');
      $("#modalScan").modal('hide');

      $.get('{{ url("fetch/maintenance/operator/position") }}', function(result, status, xhr) {
        if (result.status) {
          var emp_stat = 0;
          $.each(result.loc_temp, function(index, value){
            var uname = "{{ Auth::user()->username }}";
            if (value.employee_id.toUpperCase() == uname.toUpperCase() && value.qr_code == code) {
              emp_stat = 1;
            }
          })


          if (emp_stat == 1) {
            $("#ket").hide();
            $("#logout").show();
          } else {
            $("#logout").hide();
            $("#ket").show();
          }
        }
      })

    } else {
      openErrorGritter('Error', 'QR Code Not Registered');
      audio_error.play();
    }

  }

  function getOpLoc() {
    $.get('{{ url("fetch/maintenance/operator/position") }}', function(result, status, xhr) {
      if (result.status) {
        $.each(result.loc_temp, function(index, value){
          if (value.employee_id == "{{ Auth::user()->username }}") {
            $("#this_area").css('background-color', '#2ed573');
            $("#desc").val(value.description);
            $("#loc").val(value.location);
            $("#login_time").val(value.created_at);
            time = value.created_at;
            var utc = new Date(time).toUTCString();
            var dateNow = new Date();

            console.log(utc);
          }
        })
      }
    })
  }

  function cek_time() {

  }

  function logged(param) {
    var data = {
      remark : param,
      code : $("#code").val(),
      location : $("#loc").val(),
      desc : $("#desc").val()
    }

    $.post('{{ url("post/maintenance/operator/position") }}', data, function(result, status, xhr) {
      if (result.status) {
        if (result.remark == 'logged_in') {
          openSuccessGritter('Success', 'Logged In');
          $("#login_time").val(result.op_time.updated_at);
          $("#this_area").css('background-color', '#2ed573');
          $("#ket").hide();
        } else {
          openSuccessGritter('Success', 'Logged Out');
          $("#code").val("");
          $("#desc").val("");
          $("#loc").val("");
          $("#login_time").val("");
          $("#item_scan").val("");
          $("#logout").hide();
          $("#this_area").css('background-color', '');
        }
      } else {
        openErrorGritter('Error', 'Harus Logout Area terlebih dahulu');
      }
    })
  }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '3000'
    });
  }	
</script>
@endsection