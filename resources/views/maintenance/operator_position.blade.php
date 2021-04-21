@extends('layouts.display')
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
    padding: 0px !important;
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
  table > thead > tr > th{
    text-align: center;
    padding: 0px 5px 0px 5px !important;
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
  .kotak {
    border: 2px solid red;
    position: absolute;
    color: white;
    background: blue;
    text-align: center;
    vertical-align: middle;
  }

  .kotak2 {
    border: 2px solid black;
    position: absolute;
    color: red;
    background: white;
    text-align: center;
    vertical-align: middle;
  }

  .op {
    border-radius: 50%;
    display: inline-block;
    height: 22px;
    width: 22px;
    border: 1px solid yellow;
    font-size: 11px;
    position: relative;
    z-index: 10000;
  }

  /* Tooltip text */
  .op .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: #555;
    color: #fff;
    text-align: center;
    padding: 5px 0;
    border-radius: 6px;

    /* Position the tooltip text */
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -60px;

    /* Fade in tooltip */
    opacity: 0;
    transition: opacity 0.3s;
  }

  /* Tooltip arrow */
  .op .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
  }

  /* Show the tooltip text when you mouse over the tooltip container */
  .op:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
    cursor: pointer;
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
          <div id="map" style="height: 768px;">
            <img src="{{ url("/images/ympi_map.png") }}" height="100%" width="100%">
            <div id="boi" class="kotak" style="left: 60px; top: 155px; height: 50px; width: 80px">BOI <br>
              <div class="isi"></div>
            </div>
            <div id="tri_d" class="kotak" style="height: 25px; top: 540px; left: 495px">3D <br>
              <div class="isi"></div>
            </div>
            <div id="assy" class="kotak" style="left: 245px; top: 415px; width: 150px; height: 110px">ASSY <br>
              <div class="isi"></div>
            </div>
            <div id="com1" class="kotak" style="left: 300px; top: 380px; height: 35px">COM1 <br>
              <div class="isi"></div>
            </div>
            <div id="bpro" class="kotak" style="left: 410px; top: 415px; width: 170px; height: 110px">BPRO <br>
              <div class="isi"></div>
            </div>
            <div id="anz" class="kotak" style="left: 170px; top: 325px; height: 25px">ANZ <br>
              <div class="isi"></div>
            </div>
            <div id="bea" class="kotak" style="left: 750px; top: 75px; width: 70px; height: 25px">BEA <br>
              <div class="isi"></div>
            </div>
            <div id="sec" class="kotak" style="left: 650px; top: 75px; width: 50px; height: 25px">SEC <br>
              <div class="isi"></div>
            </div>
            <div id="buff" class="kotak" style="left: 160px; top: 540px; width: 50px; height: 90px">BUFF <br>
              <div class="isi"></div>
            </div>
            <div id="tumb" class="kotak" style="left: 100px; top: 540px; width: 50px; height: 90px">TUMB <br>
              <div class="isi"></div>
            </div>
            <div id="case" class="kotak" style="left: 530px; top: 565px; width: 50px; height: 70px">CASE <br>
              <div class="isi"></div>
            </div>
            <div id="cl" class="kotak" style="left: 420px; top: 540px; width: 70px; height: 65px">CL <br>
              <div class="isi"></div>
            </div>
            <div id="rpl" class="kotak" style="left: 400px; top: 610px; width: 80px; height: 25px">RPL <br>
              <div class="isi"></div>
            </div>
            <div id="eng" class="kotak" style="left: 220px; top: 540px; width: 30px; height: 30px">ENG <br>
              <div class="isi"></div>
            </div>
            <div id="clc" class="kotak" style="left: 455px; top: 200px; width: 30px; height: 30px">CLC <br>
              <div class="isi"></div>
            </div>
            <div id="gtc" class="kotak" style="left: 600px; top: 525px; width: 80px; height: 25px">GTC <br>
              <div class="isi"></div>
            </div>
            <div id="ofc" class="kotak" style="left: 550px; top: 165px; width: 90px; height: 130px">OFC <br>
              <div class="isi"></div>
            </div>
            <div id="plt" class="kotak" style="left: 100px; top: 415px; width: 110px; height: 60px">PLT <br>
              <div class="isi"></div>
            </div>
            <div id="lcq" class="kotak" style="left: 100px; top: 480px; width: 110px; height: 50px">LCQ <br>
              <div class="isi"></div>
            </div>
            <div id="pnc" class="kotak" style="left: 590px; top: 565px; width: 90px; height: 70px">PNC <br>
              <div class="isi"></div>
            </div>
            <div id="trf1" class="kotak" style="left: 590px; top: 640px; width: 50px; height: 40px">TRF1 <br>
              <div class="isi"></div>
            </div>
            <div id="trf2" class="kotak" style="left: 750px; top: 640px; width: 50px; height: 40px">TRF2 <br>
              <div class="isi"></div>
            </div>
            <div id="rcd" class="kotak" style="left: 870px; top: 565px; width: 70px; height: 30px">RCD <br>
              <div class="isi"></div>
            </div>
            <div id="mpc" class="kotak" style="left: 970px; top: 565px; width: 70px; height: 30px">MPC <br>
              <div class="isi"></div>
            </div>
            <div id="ctn" class="kotak" style="left: 370px; top: 165px; width: 70px; height: 130px">CTN <br>
              <div class="isi"></div>
            </div>
            <div id="wld" class="kotak" style="left: 255px; top: 540px; width: 140px; height: 97px">WLD <br>
              <div class="isi"></div>
            </div>
            <div id="tnp" class="kotak" style="left: 485px; top: 610px; width: 30px; height: 25px">TNP <br>
              <div class="isi"></div>
            </div>
            <div id="vnv" class="kotak" style="left: 1200px; top: 610px; width: 30px; height: 25px">VNV <br>
              <div class="isi"></div>
            </div>
            <div id="com3" class="kotak" style="left: 1230px; top: 605px; height: 35px">COM3 <br>
              <div class="isi"></div>
            </div>
            <div id="trf3" class="kotak" style="left: 1200px; top: 640px; height: 35px">TRF3 <br>
              <div class="isi"></div>
            </div>
            <div id="wrh" class="kotak" style="left: 820px; top: 415px; width: 140px; height: 110px">WRH <br>
              <div class="isi"></div>
            </div>
            <div id="wrk" class="kotak" style="left: 750px; top: 565px; width: 40px; height: 70px">WRK <br>
              <div class="isi"></div>
            </div>
            <div id="mtc2" class="kotak" style="left: 1030px; top: 640px; width: 150px; height: 50px">MTC <br>
              <div class="isi">
                <!-- <div class="op">HW<span class="tooltiptext">Achmad Hagi Wahyudi</span></div> -->
              </div>
            </div>
            <div id="wwt" class="kotak" style="left: 180px; top: 155px; width: 100px; height: 150px">WWT <br>
              <div class="isi"></div>
            </div>
            <div id="mpr" class="kotak" style="left: 1050px; top: 415px; width: 140px; height: 130px">MPR <br>
              <div class="isi"></div>
            </div>
            <div id="prs" class="kotak" style="left: 1070px; top: 565px; width: 140px; height: 30px">PRS <br>
              <div class="isi"></div>
            </div>
            <div id="inj" class="kotak" style="left: 860px; top: 605px; width: 150px; height: 30px">INJ <br>
              <div class="isi"></div>
            </div>
            <div id="com2" class="kotak" style="left: 860px; top: 640px; height: 35px">COM2 <br>
              <div class="isi"></div>
            </div>
            <div id="qa" class="kotak" style="left: 820px; top: 525px; width: 50px; height: 25px">QA <br>
              <div class="isi"></div>
            </div>
            <div id="oa" class="kotak" style="left: 100px; top: 675px; width: 250px; height: 30px; background: #ad1d35">OUTDOOR <br>
              <div class="isi"></div>
            </div>

            <div id="legend" class="kotak2" style="left: 740px; top: 103px; width: 520px; height: 290px; overflow: auto;">
              <table class="table">
                <thead>
                  <tr>
                    <th colspan="6" style="background-color: #bffa8e; padding: 0 5px 0px 5px">Legends : </th>
                  </tr>
                  <tr>
                    <th>Shift</th>
                    <th>Absence</th>
                    <th>Alias</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Job</th>
                  </tr>
                </thead>
                <tbody id="legend_body">
                </tbody>
              </table>
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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    machine = <?php echo json_encode($machine); ?>;
    area = <?php echo json_encode($area); ?>;
    loc = <?php echo json_encode($loc_arr); ?>;

    console.log(loc);

    getOp();

    setInterval(getOp, 5000);

  });

  function getOp() {  
    $.get('{{ url("fetch/maintenance/operator/position/") }}', function(result, status, xhr) {

      var loc_op = [];
      $(".isi").empty();
      $("#legend_body").empty();

      body = '';

      $.each(result.emp_loc, function(index, value){
        $.each(loc, function(index2, value2){
          $.each(value2.area, function(index3, value3){
            if (value3 == value.location) {
              loc_op.push({emp_id: value.employee_id, name: value.name, loc: value2.alias});

              stile = "";
              if (value.remark == 'Machine Production') {
                stile = "style='background-color:green'";
              } else if(value.remark == 'Utility') {
                stile = "style='background-color:#de68b3'";
              } else {
                stile = "style='background-color:#ed6137'";
              }

              $("#"+value2.alias).find(".isi").append('<div class="op" '+stile+'>'+value.short_name+'<span class="tooltiptext">'+value.name+'</span></div>');
            }
          })
        })

        body += '<tr style="font-size: 11px;">';
        body += '<td>'+value.shiftdaily_code.match(/\d+/)+'</td>';
        body += '<td>'+value.attend_code+'</td>';
        body += '<td>'+value.short_name+'</td>';
        body += '<td style="text-align: left">'+value.name+'</td>';
        body += '<td>'+(value.location || '')+'</td>';
        body += '<td>'+(value.job || '')+'</td>';
        body += '</tr>';

        if (typeof result.emp_loc[index+1] !== 'undefined') {
          if (value.shiftdaily_code != result.emp_loc[index+1].shiftdaily_code) {
            body += '<tr><td colspan="6" style="border-bottom: 2px solid black"></td></tr>';
          }
        }
      })

      $("#legend_body").append(body);

      $.each(loc_op, function(index2, value2){

      })
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