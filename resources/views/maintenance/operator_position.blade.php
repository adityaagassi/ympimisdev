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
  .kotak {
    border: 2px solid red;
    position: absolute;
    color: white;
    background: blue;
    text-align: center;
    vertical-align: middle;
  }
  .op {
    background-color: green;
    border-radius: 50%;
    display: inline-block;
    height: 22px;
    width: 22px;
    border: 1px solid yellow;
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
          <img src="{{ url("/images/ympi_map.png") }}" width="100%">
          <div id="tri_d" class="kotak" style="left: 495px; top: 515px; height: 25px">3D <br>
            <div class="isi"></div>
          </div>
          <div id="anz" class="kotak" style="left: 170px; top: 300px; height: 25px">ANZ <br>
            <div class="isi"></div>
          </div>
          <div id="assy" class="kotak" style="left: 245px; top: 390px; width: 150px; height: 110px">ASSY <br>
            <div class="isi"></div>
          </div>
          <div id="bpro" class="kotak" style="left: 410px; top: 390px; width: 170px; height: 110px">BPRO <br>
            <div class="isi"></div>
          </div>
          <div id="bea" class="kotak" style="left: 750px; top: 70px; width: 70px">BEA <br>
            <div class="isi"></div>
          </div>
          <div id="sec" class="kotak" style="left: 650px; top: 70px; width: 50px">SEC <br>
            <div class="isi"></div>
          </div>
          <div id="buff" class="kotak" style="left: 160px; top: 515px; width: 50px; height: 90px">BUFF <br>
            <div class="isi"></div>
          </div>
          <div id="tumb" class="kotak" style="left: 100px; top: 515px; width: 50px; height: 90px">TUMB <br>
            <div class="isi"></div>
          </div>
          <div id="case" class="kotak" style="left: 530px; top: 540px; width: 50px; height: 70px">CASE <br>
            <div class="isi"></div>
          </div>
          <div id="cl" class="kotak" style="left: 420px; top: 515px; width: 70px; height: 65px">CL <br>
            <div class="isi"></div>
          </div>
          <div id="rpl" class="kotak" style="left: 400px; top: 585px; width: 80px; height: 25px">RPL <br>
            <div class="isi"></div>
          </div>
          <div id="eng" class="kotak" style="left: 220px; top: 515px; width: 30px; height: 30px">ENG <br>
            <div class="isi"></div>
          </div>
          <div id="clc" class="kotak" style="left: 455px; top: 180px; width: 30px; height: 30px">CLC <br>
            <div class="isi"></div>
          </div>
          <div id="gtc" class="kotak" style="left: 600px; top: 500px; width: 80px; height: 25px">GTC <br>
            <div class="isi"></div>
          </div>
          <div id="ofc" class="kotak" style="left: 550px; top: 140px; width: 90px; height: 130px">OFC <br>
            <div class="isi"></div>
          </div>
          <div id="plt" class="kotak" style="left: 100px; top: 390px; width: 110px; height: 60px">PLT <br>
            <div class="isi"></div>
          </div>
          <div id="lcq" class="kotak" style="left: 100px; top: 455px; width: 110px; height: 50px">LCQ <br>
            <div class="isi"></div>
          </div>
          <div id="pnc" class="kotak" style="left: 590px; top: 540px; width: 90px; height: 70px">PNC <br>
            <div class="isi"></div>
          </div>
          <div id="rcd" class="kotak" style="left: 870px; top: 540px; width: 70px; height: 30px">RCD <br>
            <div class="isi"></div>
          </div>
          <div id="ctn" class="kotak" style="left: 370px; top: 140px; width: 70px; height: 130px">CTN <br>
            <div class="isi"></div>
          </div>
          <div id="wld" class="kotak" style="left: 255px; top: 515px; width: 140px; height: 97px">WLD <br>
            <div class="isi"></div>
          </div>
          <div id="tnp" class="kotak" style="left: 485px; top: 585px; width: 30px; height: 25px">TNP <br>
            <div class="isi"></div>
          </div>
          <div id="vnv" class="kotak" style="left: 1200px; top: 585px; width: 30px; height: 25px">VNV <br>
            <div class="isi"></div>
          </div>
          <div id="wrh" class="kotak" style="left: 820px; top: 390px; width: 140px; height: 110px">WRH <br>
            <div class="isi"></div>
          </div>
          <div id="wrk" class="kotak" style="left: 750px; top: 540px; width: 40px; height: 70px">WRK <br>
            <div class="isi"></div>
          </div>
          <div id="wwt" class="kotak" style="left: 180px; top: 130px; width: 100px; height: 150px">WWT <br>
            <div class="isi"></div>
          </div>
          <div id="mpr" class="kotak" style="left: 1050px; top: 390px; width: 140px; height: 130px">MPR <br>
            <div class="isi"></div>
          </div>
          <div id="prs" class="kotak" style="left: 1070px; top: 540px; width: 140px; height: 30px">PRS <br>
            <div class="isi"></div>
          </div>
          <div id="inj" class="kotak" style="left: 860px; top: 580px; width: 150px; height: 30px">INJ <br>
            <div class="isi"></div>
          </div>
          <div id="qa" class="kotak" style="left: 820px; top: 500px; width: 50px; height: 25px">QA <br>
            <div class="isi"></div>
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
  var loc;

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    machine = <?php echo json_encode($machine); ?>;
    loc = <?php echo json_encode($loc_arr); ?>;

    console.log(loc);

    getOp();
  });

  function getOp() {  
    $.get('{{ url("fetch/maintenance/operator/position/") }}', function(result, status, xhr) {

      var loc_op = [];
      $(".isi").empty();

      $.each(result.emp_loc, function(index, value){
        $.each(loc, function(index2, value2){
          $.each(value2.area, function(index3, value3){
            if (value3 == value.location) {
              loc_op.push({emp_id: value.employee_id, name: value.employee_name, loc: value2.alias});
              $("#"+value2.alias).find(".isi").append('<div class="op" >&nbsp;<span class="tooltiptext">'+value.employee_name+'</span></div>');
            }
          })
        })
      })

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