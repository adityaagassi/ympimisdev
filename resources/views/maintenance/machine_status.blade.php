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
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <div class="col-xs-3">
            <div class="form-group">
              <label>Periode</label>
              <div class="input-group" style="width: 100%;">
                <input type="text" placeholder="Pilih Tanggal" class="form-control pull-right date" id="period">
              </div>
            </div>
          </div>

          <div class="col-xs-3">
            <div class="form-group">
              <label>Machine Group</label>
              <div class="input-group" style="width: 100%;">
                <select class="select2 form-control pull-right" id="machine_group" data-placeholder="Pilih Grup Mesin">
                  <option value=""></option>
                  @foreach($machine_group as $mg)
                  <option value="{{ $mg->machine_group }}">{{ $mg->machine_group }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="col-xs-3">
            <div class="form-group">
              <label>Location</label>
              <div class="input-group" style="width: 100%;">
                <!-- <input type="text" placeholder="Pilih Lokasi Mesin" class="form-control pull-right" id="machine_loc"> -->
                <select class="select2 form-control pull-right" id="machine_loc" data-placeholder="Pilih Lokasi Mesin">
                  <option value=""></option>
                  @foreach($machine_location as $mloc)
                  <option value="{{ $mloc->location }}">{{ $mloc->location }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="col-xs-3">
            <div class="form-group">
              <label>Machine ID</label>
              <div class="input-group" style="width: 100%;">
                <input type="text" placeholder="Input Kode Mesin" class="form-control pull-right" id="machine_id">
              </div>
            </div>
          </div>

          <div class="col-xs-12">
           <div class="row">
             <div class="col-xs-2 pull-right">
               <button class="btn btn-primary pull-right" onclick="getMachine()"><i class="fa fa-search"></i>&nbsp; Filter</button>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>

   <div class="col-xs-12" style="margin-bottom: 5px">
    <!-- <h2 style="color: white; text-align: center" id="judul">MTBF MACHINE ___ ON __ </h2> -->
    <center>
<!--         <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('Soldering')">SOLDERING</button>
        <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('Buffing & Tumbling')">BARREL</button>
        <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" onclick="getMachine('NC Kira - CL-Body')">NC KIRA</button>
      -->
    </center>
  </div>


  <div class="col-xs-12">
    <table class="table table-bordered" width="100%" id="table_master">
      <thead>
        <tr>
          <th>No.</th>
          <th>PERIODE</th>
          <th>MACHINE GROUP</th>
          <th>MACHINE ID</th>
          <th>MACHINE DESC</th>
          <th>LOCATION</th>
          <th>LOAD HOUR(S)</th>
          <th>SHIFT <br> NUMBER</th>
          <th>DOWN TIME <br> (number)</th>
          <th>DOWN TIME <br> (min)</th>
          <th>REPAIR TIME <br> (min)</th>
          <th>MTBF</th>
          <th>MTTR</th>
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

  $('.select2').select2({allowClear: true});

  $('.date').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
  });

  function getMachine() {
    var data = {
      'period' : $("#period").val(),
      'location' : $("#machine_loc").val(),
      'machine_group' : $("#machine_group").val(),
      'machine_code' : $("#machine_id").val()
    }

    $.get('{{ url("fetch/maintenance/mttbf/list") }}', data, function(result, status, xhr) {
      $("#tb_body").empty();
      var body = "";

      $.each(result.l_hours, function(index, value){
        var dt_num = 0;
        var dt_min = 0;
        var re_min = 0;
        var mtbf = 0;

        $.each(result.datas, function(index2, value2){
          if (value.machine_id == value2.machine_name) {
            dt_num = value2.down_time_count;
            dt_min = parseInt(value2.down_time_min);
            re_min = parseInt(value2.repair_time);
          }
        })
        // $("#judul").text("MTBF MACHINE ON "+value.mon);

        body += '<tr>';
        body += '<td>'+(index+1)+'</td>';
        body += '<td>'+value.mon2+'</td>';
        body += '<td>'+(value.machine_group || '')+'</td>';
        body += '<td>'+value.machine_id+'</td>';
        body += '<td>'+value.description+'</td>';
        body += '<td>'+value.area+'</td>';
        body += '<td>'+value.load_hour+'</td>';
        body += '<td>'+value.shift_number+'</td>';
        body += '<td>'+dt_num+'</td>';
        body += '<td>'+dt_min+'</td>';
        body += '<td>'+re_min+'</td>';
        body += '<td>'+(value.load_hour / dt_num).toFixed(0)+'</td>';
        body += '<td>'+((re_min / dt_num) | '')+'</td>';
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