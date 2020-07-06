@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
    background-color: #7e5686;
    color: #FFD700;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }
  #queueTable.dataTable {
    margin-top: 0px!important;
  }
  #loading, #error { display: none; }
  .description-block {
    margin-top: 0px
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
      </p>
    </div>
    <div class="col-xs-12">
      <h3 style="color: white; text-align: center">Daftar APAR Yang Akan Kadaluarsa <br>(使用期限が切れた消火器・消火栓の一覧)</h3><br>
    </div>
    <div class="col-xs-6">
      <button class="btn btn-default btn-lg pull-right" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id='f1' onclick="page(this.id)">Factory I</button>
    </div>
    <div class="col-xs-6">
      <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id='f2' onclick="page(this.id)">Factory II</button>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;" id="fact1">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th colspan="8" style="font-size: 20pt">Factory I</th>
          </tr>
          <tr>
            <th>APAR Code</th>
            <th>APAR Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Exp. Date</th>
            <th>Exp. Remaining</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="exp_f1">
        </tbody>
      </table>
    </div>

    <div class="col-xs-12" style="padding-top: 10px; display: none" id="fact2">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th colspan="8" style="font-size: 20pt">Factory II</th>
          </tr>
          <tr>
            <th>APAR Code</th>
            <th>APAR Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Exp. Date</th>
            <th>Exp. Remaining</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="exp_f2">
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade in" id="modaledit" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title" id="modalTitle">APAR Replacement</h4>
          </div>
          <div class="modal-body">
            <div class="row">
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
            </div>

            <div class="col-xs-12" id="modalContent">
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group row">
                    <label class="col-xs-2" style="margin-top: 1%;">Code</label>
                    <div class="col-xs-5">
                      <input type="text" class="form-control" id="code" readonly>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-xs-2" style="margin-top: 1%;">Name</label>
                    <div class="col-xs-5">
                      <input type="text" class="form-control" id="name" readonly>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-xs-2" style="margin-top: 1%;">Location</label>
                    <div class="col-xs-10">
                      <input type="text" class="form-control" id="location" readonly>
                      <input type="hidden" id="type">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-xs-2" style="margin-top: 1%;">Capacity</label>
                    <div class="col-xs-5">
                      <div class="input-group">
                        <input type="text" class="form-control" id="capacity" readonly>
                        <span class="input-group-addon bg-purple">Kg</span>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-xs-2" style="margin-top: 1%;">Entry Date</label>
                    <div class="col-xs-5">
                      <div class="input-group">
                        <span class="input-group-addon bg-purple"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control datepicker" id="pengisian" placeholder="Pilih Tanggal Pengisian" style="background-color: white !important;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-left" id="btn_save" onclick="replace()"><i class="fa fa-check"></i> Save</button>
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          </div>
        </div>
      </div>
    </div>
  </section>
  @endsection
  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ url("js/jsQR.js")}}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var check = [];

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
      $('#check').children().hide();

      check = <?php echo json_encode($check_list); ?>;

      get_expire_data();
      $("#modalContent").hide();
      $("#btn_save").hide();

    });

    $(function () {
      jQuery( ".datepicker" ).datepicker({autoclose: true, format: "yyyy-mm-dd" }).attr('readonly','readonly');
    })

    function get_expire_data() {
      var fi = "", fii = "";

      $("#exp_f1").empty();
      $("#exp_f2").empty();

      var data = {
        mon: '2'
      }

      $.get('{{ url("fetch/maintenance/apar/expire") }}', data, function(result, status, xhr) {
        $.each(result.expired_list, function(index, value){

          if (value.exp < 0) {
            color = '#fa6161';
          } else if(value.exp == 0) {
            color = '#f79b5e';
          } else if(value.exp == 1) {
            color = '#fcdc65';
          } else {
            color = '#fffdd1';
          }

          // if (value.order_status == "Ready" && "{{Auth::user()->username}}".toUpperCase() == "PI2002021"){

          //   klik = "onclick='openModal(\""+value.utility_code+"\",\""+value.utility_name+"\",\""+value.group+"\",\""+value.id+"\", \""+value.type+"\", \""+value.exp_date+"\")'";
          // } else {
            klik = "";
          // }

          if (value.location == "Factory I") {
            fi += "<tr style='background-color: "+color+"' "+klik+">";
            fi += "<td>"+value.utility_code+"<input type='hidden' id='"+value.id+"' value='"+value.capacity+"'></td>";
            fi += "<td>"+value.utility_name+"</td>";
            fi += "<td>"+value.type+"</td>";
            fi += "<td>"+value.capacity+" Kg</td>";
            fi += "<td>"+value.group+"</td>";
            fi += "<td>"+value.exp_date+"</td>";
            fi += "<td>"+value.exp+" Month Left</td>";
            fi += "<td>"+(value.order_status || '-')+"</td>";
            fi += "</tr>";
          } else {
            fii += "<tr style='background-color: "+color+"' "+klik+">";
            fii += "<td>"+value.utility_code+"<input type='hidden' id='"+value.id+"' value='"+value.capacity+"'></td>";
            fii += "<td>"+value.utility_name+"</td>";
            fii += "<td>"+value.type+"</td>";
            fii += "<td>"+value.capacity+" Kg</td>";
            fii += "<td>"+value.group+"</td>";
            fii += "<td>"+value.exp_date+"</td>";
            fii += "<td>"+value.exp+" Month Left</td>";
            fii += "<td>"+(value.order_status || '-')+"</td>";
            fii += "</tr>";
          }
        })

        $("#exp_f1").append(fi);
        $("#exp_f2").append(fii);
      })
    }


    function openModal(kode, nama, lokasi, id, type, exp) {
      $("#modaledit").modal("show");

      $("#code").val(kode);
      $("#name").val(nama);
      $("#location").val(lokasi);
      $("#type").val(type);
      $("#capacity").val($('#'+id).val());
    }

    function stopScan() {
      $('#scanModal').modal('hide');
    }

    function videoOff() {
      vdo.pause();
      vdo.src = "";
      vdo.srcObject.getTracks()[0].stop();
    }

    $( "#modaledit" ).on('shown.bs.modal', function(){
      showCheck();
    });

    $('#modaledit').on('hidden.bs.modal', function () {
      videoOff();
      $("#modalContent").hide();
      $("#btn_save").hide();
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

    function checkCode(video, params) {
      if ($("#code").val() == params.split("/")[0]) {
        $('#scanner').hide();
        // $('#modaledit').modal('hide');
        $('#check').children().show();
        $("#modalContent").show();
        $("#btn_save").show();

        videoOff();
        openSuccessGritter('Success', 'QR Code Successfully');

      } else {
        openErrorGritter('Error', 'QR Code not Same');
        audio_error.play();
      }

    }

    function replace() {
      var data = {
        code : $("#code").val(),
        capacity : $("#capacity").val(),
        entry_date : $("#pengisian").val(),
        type : $("#type").val()
      }

      $("#loading").show();

      $.post('{{ url("post/maintenance/apar/replace") }}', data, function(result, status, xhr) {
        var hasil_check = "BAIK";
        $("#loading").hide();

        var cek_date = [];

        $.each(result.check, function(index, value){
          cek_date.push(value.cek_date);
        })

        if (cek_date[1]) {
          cek2 = cek_date[1];
        } else {
          cek2 = '-';
        }

        window.open('{{ url("print/apar/qr/") }}/'+$("#code").val()+'/'+$("#name").val()+'/'+result.new_exp+'/'+cek_date[0]+'/'+cek2+'/'+hasil_check+'/'+'APAR', '_blank');

        openSuccessGritter("Success", "APAR Was Successfully Replaced");
        $("#modaledit").modal("hide");
        get_expire_data();
      }).fail(function(result) {
        openErrorGritter( "Error", "" );
        $("#loading").hide();
      })
    }

    function page(id) {
      if (id == "f1") {
        $("#fact1").show();
        $("#fact2").hide();
      } else {
        $("#fact2").show();
        $("#fact1").hide();
      }
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