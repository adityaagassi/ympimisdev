@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
    display: none;
  }
  .morelink {
    display: block;
  }

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
  tfoot>tr>td{
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
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
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

  .panel {
    margin-bottom: 0px !important;
    border-top-color: #605ca8;
  }
  .box-header:hover {
    cursor: pointer;
    /*background-color: #3c3c3c;*/
  }

  .alert2 {
    -webkit-animation: alerts 1s infinite;  /* Safari 4+ */
    -moz-animation: alerts 1s infinite;  /* Fx 5+ */
    -o-animation: alerts 1s infinite;  /* Opera 12+ */
    animation: alerts 1s infinite;  /* IE 10+, Fx 29+ */
  }
  
  @-webkit-keyframes alerts {
    0%, 49% {
      background: #fffcb7; 
      color: #333;
    }
    50%, 100% {
      background-color: #262626;
      color: #fff;
    }
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
    <div class="col-xs-12" style="margin-bottom: 5px">
      <h2 style="color: white; text-align: center" id="judul">MTTBF MACHINE ___ ON __ </h2>
      <center>
        <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('Soldering')">SOLDERING</button>
        <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('Buffing & Tumbling')">BARREL</button>
        <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('NC Kira - CL-Body')">NC KIRA</button>

      </center>
    </div>

    <div class="col-xs-12">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th>No.</th>
            <th>MACHINE GROUP</th>
            <th>MACHINE ID</th>
            <th>MACHINE DESC</th>
            <th>LOCATION</th>
            <th>MTTBF BEFORE</th>
            <th>LOAD HOUR(S)</th>
            <th>TROUBLE</th>
            <th>MTTBF</th>
          </tr>
        </thead>
        <tbody id='tb_body'>
        </tbody>
      </table>
    </div>
  </div>
</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
  });

  function getMachine(param) {
    var data = {
      'location' : param
    }

    $.get('{{ url("fetch/maintenance/mttbf/list") }}', data, function(result, status, xhr) {
      $("#tb_body").empty();
      var body = "";

      $.each(result.l_hours, function(index, value){
        $("#judul").text("MTTBF MACHINE "+param+" ON "+value.mon);

        body += '<tr>';
        body += '<td>'+(index+1)+'</td>';
        body += '<td>'+value.location+'</td>';
        body += '<td>'+value.machine_id+'</td>';
        body += '<td>'+value.description+'</td>';
        body += '<td>'+value.area+'</td>';
        body += '<td></td>';
        body += '<td>'+value.load_hour+'</td>';
        body += '<td></td>';
        body += '<td></td>';
        body += '</tr>';
      })
      $("#tb_body").append(body);
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