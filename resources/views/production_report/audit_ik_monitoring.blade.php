@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .gambar {
    width: 400px;
    height: 420px;
    background-color: white;
    border-radius: 15px;
    margin-left: 30px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }
  .content-wrapper{
    padding-top: 0px;
    margin-top: 0px
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
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
  td{
    overflow:hidden;
    text-overflow: ellipsis;
    border:1px solid black;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ecf0f5;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #e8e8e8;
  }

  #tableResume tr>td{
    text-align:left;
    padding-left: 7px;
  }
  .tableResumes tr td {
    cursor: pointer;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <div class="row" style="padding-left: 20px;padding-right: 20px;padding-top: 0px;margin-top: 0px">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="background-color: rgb(126,86,134);text-align: center;height: 35px;padding-right: 5px">
      <span style="color: white;font-size: 25px;font-weight: bold;" id="title_periode">
      </span>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Department" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="department_all">
        <option value=""></option>
        <option value="All">All</option>
        @foreach($department_all as $dept)
          <option value="{{$dept->id}}">{{$dept->department_shortname}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Fiscal Year" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="fiscal_year">
        <option value=""></option>
        @foreach($fiscal as $fiscal)
          <option value="{{$fiscal->fiscal_year}}">{{$fiscal->fiscal_year}}</option>
        @endforeach
      </select>
    </div>
    <!-- <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <div class="input-group date">
        <div class="input-group-addon" style="border-color: rgb(126,86,134);background-color: rgb(126,86,134);color: white">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker2" id="month_from" onchange="drawChart()" placeholder="Select Month From" style="border-color: #00a65a;height: 35px">
      </div>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 0px">
      <div class="input-group date">
        <div class="input-group-addon" style="border-color: rgb(126,86,134);background-color: rgb(126,86,134);color: white">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker2" id="month_to" onchange="drawChart()" placeholder="Select Month To" style="border-color: #00a65a;height: 35px">
      </div>
    </div> -->
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container" style="height: 500px"></div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container2" style="height: 500px"></div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px" id="div_resume">

    </div>
  </div>
    <div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="data-activity">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection

@section('scripts')
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<!-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script> -->

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    drawChart();

    $('.datepicker').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy",
      startView: "years", 
      minViewMode: "years",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });

    var interval;
    var statusx = "idle";

    $(document).on('mousemove keyup keypress',function(){
      clearTimeout(interval);
      settimeout();
      statusx = "active";
    })

    function settimeout(){
      interval=setTimeout(function(){
        statusx = "idle";
        drawChart()
      },10000)
    }
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
  });

  $(function () {
    $('.select2').select2();
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function drawChart(){
    // var month_from = $('#month_from').val();
    // var month_to = $('#month_to').val();
    var department = $('#department_all').val();
    var fiscal_year = $('#fiscal_year').val();
    var data = {
      // month_from: month_from,
      // month_to: month_to,
      department: department,
      fiscal_year: fiscal_year,
    }
    $.get('{{ url("fetch/audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          $('#title_periode').html('Periode '+result.fiscalTitle);
          var categories = [];
          var plan = [];
          var done = [];
          var not_yet = [];

          for(var i = 0; i< result.audit_ik.length;i++){
            categories.push(result.audit_ik[i].months);
            plan.push(parseInt(result.audit_ik[i].plan));
            done.push({y:parseInt(result.audit_ik[i].done),key:result.audit_ik[i].month});
            not_yet.push({y:parseInt(result.audit_ik[i].not_yet),key:result.audit_ik[i].month});
          }

          Highcharts.chart('container', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME HASIL AUDIT IK",
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: categories,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                },
                style: {
                  fontSize:"15px",
                }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"15px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                pointPadding: 0.93,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      showModal(this.options.key,this.series.name,"");
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Belum Dikerjakan',
              data: not_yet,
              color:'#a60000',
            },
            {
              name: 'Sudah Dikerjakan',
              data: done,
              color:'#00a65a',
            }]
          });

          var categories = [];
          var training_ulang = [];
          var revisi_ik = [];
          var revisi_qc = [];
          var jig = [];
          var obsolete = [];

          for(var i = 0; i< result.resume_penanganan.length;i++){
            categories.push(result.resume_penanganan[i].months);
            training_ulang.push({y:parseInt(result.resume_penanganan[i].training_ulang),key:result.resume_penanganan[i].month});
            revisi_ik.push({y:parseInt(result.resume_penanganan[i].revisi_ik),key:result.resume_penanganan[i].month});
            revisi_qc.push({y:parseInt(result.resume_penanganan[i].revisi_qc),key:result.resume_penanganan[i].month});
            jig.push({y:parseInt(result.resume_penanganan[i].jig),key:result.resume_penanganan[i].month});
            obsolete.push({y:parseInt(result.resume_penanganan[i].obsolete),key:result.resume_penanganan[i].month});
          }

          Highcharts.chart('container2', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME PENANGANAN AUDIT IK",
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: categories,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                },
                style: {
                  fontSize:"15px",
                }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"15px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                pointPadding: 0.93,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      showModal(this.options.key,this.series.name,"");
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Training Ulang IK',
              data: training_ulang,
              color:'#00a65a',
              stacking:true
            },
            {
              name: 'Revisi IK',
              data: revisi_ik,
              color:'#0061a6',
              stacking:true
            },
            {
              name: 'Revisi QC Kouteihyo',
              data: revisi_qc,
              color:'#a19d30',
              stacking:true
            },
            {
              name: 'Pembuatan Jig / Repair Jig',
              data: jig,
              color:'#a6007d',
              stacking:true
            },
            {
              name: 'IK Tidak Digunakan',
              data: obsolete,
              color:'#a60000',
              stacking:true
            }]
          });

          $("#div_resume").html('');
          var tableresume = "";

          for(var i = 0; i< result.department.length;i++){
            var lengthspan = 0;

            var total = [];

            for(var j = 0; j< result.resume_all.length;j++){
              if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                lengthspan = result.resume_all[j].length;
                for(var k = 0; k< result.resume_all[j].length;k++){
                  total[k] = 0;
                }
              }
            }

            tableresume += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #e7ffb8;text-align: center;height: 35px;padding-right: 5px;margin-top:20px">';
            tableresume += '<span style="font-size: 25px;font-weight: bold;">'+result.department[i].department_name+'</span>';
            tableresume += '</div>';
            tableresume += '<table id="tableResume" style="background-color: black;color: white;font-size: 15px;" class="table table-bordered tableResumes">'
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white">課</td>';
                tableresume += '<td style="border: 1px solid white">Item</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    lengthspan = result.resume_all[j].length;
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].months+'</td>';
                    }
                  }
                }
                var lengthspanfix = lengthspan+2;
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;font-weight: bold;">作業手順書数</td>';
                tableresume += '<td style="border: 1px solid white">Jumlah IK</ td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      var kondision = 'All';
                      tableresume += '<td style="border: 1px solid white;text-align:center" onclick="showModalResume(\''+result.resume_all[j][k].month+'\',\''+kondision+'\',\''+result.department[i].department_name+'\')">'+result.resume_all[j][k].plan+'</td>';
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white">監査が実施されました</td>';
                tableresume += '<td style="border: 1px solid white">Sudah Dilakukan Audit</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      var kondision = 'Sudak Dikerjakan';
                      tableresume += '<td style="border: 1px solid white;text-align:center" onclick="showModalResume(\''+result.resume_all[j][k].month+'\',\''+kondision+'\',\''+result.department[i].department_name+'\')">'+result.resume_all[j][k].done+'</td>';
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;font-weight: bold;">監査が実施されません</td>';
                tableresume += '<td style="border: 1px solid white">Belum Dilakukan Audit</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].not_yet+'</td>';
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white">IKの内容はQC工程表の内容と違います</td>';
                tableresume += '<td style="border: 1px solid white">Jumlah Proses yang Tidak Sesuai QC Koteihyo</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].tidak_sesuai+'</td>';
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr style="background-color: white;background-color: #ffd154">';
                tableresume += '<td colspan="'+lengthspanfix+'" style="border: 1px solid black;color: black;text-align: center;font-weight: bold;">Penanganan :</td>';
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書再教育</td>';
                tableresume += '<td style="border: 1px solid white">Traning Ulang IK</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].training_ulang+'</td>';
                      total[k] = total[k] + parseInt(result.resume_all[j][k].training_ulang);
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書修正→再教育（作業仕様書の内容が不適切だった場合）</td>';
                tableresume += '<td style="border: 1px solid white">Revisi IK, Training Ulang (Jika Ada Isi IK yang Tidak Sesuai)</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].revisi_ik+'</td>';
                      total[k] = total[k] + parseInt(result.resume_all[j][k].revisi_ik);
                    }
                  }
                }

              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">QC工程表の改定</td>';
                tableresume += '<td style="border: 1px solid white">Revisi QC Kouteihyo</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].revisi_qc+'</td>';
                      total[k] = total[k] + parseInt(result.resume_all[j][k].revisi_qc);
                    }
                  }
                }

              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">治具修正・作成等（治具摩耗、適切な治具を使用していなかった等の場合</td>';
                tableresume += '<td style="border: 1px solid white">Pembuatan Jig, Repair Jig (Jika Jig Aus atau Tidak Menggunakan Jig yang Benar)</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].jig+'</td>';
                      total[k] = total[k] + parseInt(result.resume_all[j][k].jig);
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;">使用されなくなったIK</td>';
                tableresume += '<td style="border: 1px solid white">IK Obsolete</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;text-align:center">'+result.resume_all[j][k].obsolete+'</td>';
                      total[k] = total[k] + parseInt(result.resume_all[j][k].obsolete);
                    }
                  }
                }
              tableresume += '</tr>';
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;background-color: yellow;border-top: 3px solid white;border-bottom:3px solid red">うち仕様書通り行われていなかった工程数</td>';
                tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;font-size:20px">Total Action</td>';
                for(var j = 0; j< result.resume_all.length;j++){
                  if (result.resume_all[j][0].department_id == result.department[i].id_department) {
                    for(var k = 0; k< result.resume_all[j].length;k++){
                      tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;border-left:2px solid black;text-align:center;font-size:20px">'+total[k]+'</td>';
                    }
                  }
                }
              tableresume += '</tr>';
            tableresume += '</table>';
          }

          $('#div_resume').html(tableresume);
        } else{
          openErrorGritter('Error!','Get Data Failed');          
        }
      }
    });
  }

  function showModal(month,kondisi,department) {
    $('#loading').show();
    var department = $('#department_all').val();
    var data = {
      month: month,
      department: department,
      kondisi: kondisi,
    }

    $.get('{{ url("fetch/detail_audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#data-activity').html('');
        var datatable = "";

        $('#data_log_sudah').DataTable().clear();
        $('#data_log_sudah').DataTable().destroy();

        if (kondisi === 'Belum Dikerjakan') {
          datatable += '<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">';
          datatable += '<thead>'
          datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
          datatable += '<th>No. Dokumen</th>';
          datatable += '<th>Nama Dokumen</th>';
          datatable += '<th>Target</th>';
          datatable += '<th>Leader</th>';
          datatable += '<th>Foreman</th>';
          datatable += '</tr>';
          datatable += '</thead>';

          for(var j = 0; j < result.datas.length;j++){
              datatable += '<tbody style="border:1px solid black">';
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td>'+result.datas[j].no_dokumen+'</td>';
              datatable += '<td>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td>'+result.datas[j].month+'</td>';
              datatable += '<td>'+result.datas[j].leader+'</td>';
              datatable += '<td>'+result.datas[j].foreman+'</td>';
              datatable += '</tr>';
              datatable += '</tbody>';
          }
          datatable += '</table>';
          datatable += '<hr style="border:2px solid black">';
        }else{
          datatable += '<table id="data_log_sudah" class="table table-striped table-bordered" style="width: 100%;">';
          datatable += '<thead>'
          datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#fcba03">';
          datatable += '<th>Dokumen</th>';
          datatable += '</tr>';
          datatable += '</thead>';
          datatable += '<tbody style="border:1px solid black">';
          for(var j = 0; j < result.datas.length;j++){
            $('#data-log-detail-'+j).DataTable().clear();
            $('#data-log-detail-'+j).DataTable().destroy();
            datatable += '<tr>';
            datatable += '<td>';
            datatable += '<div class="box-group" id="accordion">';
                datatable += '<div class="panel box box-solid">';
                  datatable += '<div class="box-header with-border" style="background-color:#7e5686;text-align:center">';
                    datatable += '<h4 class="box-title">';
                      datatable += '<a data-toggle="collapse" data-parent="#accordion" href="#collapse'+j+'" aria-expanded="false" class="collapsed" style="color:white;">'+result.datass[j][0].no_dokumen+' - '+result.datass[j][0].nama_dokumen+' ('+result.datass[j][0].leader+')';
                      datatable += '</a>';
                    datatable += '</h4>';
                    var hrefreport = '{{url("index/audit_report_activity/print_audit_report/")}}/'+result.datass[j][0].activity_list_id+'/'+result.datass[j][0].month;
                    datatable += '<a  href="'+hrefreport+'" target="_blank" class="btn btn-success btn-sm pull-right">Report <i class="fa fa-arrow-right"></i></a>';
                  datatable += '</div>';
                  datatable += '<div id="collapse'+j+'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">';
                    datatable += '<div class="box-body">';
                    datatable += '<table id="data-log-detail-'+j+'" class="table table-bordered table-striped" style="width: 100%;">';
                      datatable += '<thead>';
                      datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
                      datatable += '<th style="width:1%">Target : '+result.datass[j][0].month+'</th>';
                      datatable += '<th style="width:3%">Nama Dokumen : '+result.datass[j][0].nama_dokumen+'</th>';
                      datatable += '</tr>';
                      datatable += '</thead>';

                      datatable += '<tbody style="border:1px solid black">';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Dept</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].department+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Section</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].section+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Group</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].subsection+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Audit Date</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].date+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black;background-color:#a6ffc9">Kesesuaian Aktual</td>';
                      datatable += '<td style="border:1px solid black;background-color:#a6ffc9">'+result.datass[j][0].kesesuaian_aktual_proses+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black;background-color:#a6d4ff">Kesesuaian QC Kouteihyo</td>';
                      datatable += '<td style="border:1px solid black;background-color:#a6d4ff">'+result.datass[j][0].kesesuaian_qc_kouteihyo+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black;background-color:#ffa6a6">Kelengkapan Point Safety</td>';
                      datatable += '<td style="border:1px solid black;background-color:#ffa6a6">'+result.datass[j][0].kelengkapan_point_safety+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Tindakan Perbaikan</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].tindakan_perbaikan+'</td>';
                      datatable += '</tr>';
                      if (result.datass[j][0].tindakan_perbaikan == '-') {
                        datatable += '<tr>';
                        datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
                        datatable += '<td style="border:1px solid black"></td>';
                        datatable += '</tr>';
                      }else{
                        datatable += '<tr>';
                        datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
                        datatable += '<td style="border:1px solid black">'+result.datass[j][0].target+'</td>';
                        datatable += '</tr>';
                      }
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Kondisi</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].condition+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Penanganan</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].handling+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">PIC</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].operator+'</td>';
                      datatable += '</tr>';
                      datatable += '<tr>';
                      datatable += '<td style="border:1px solid black">Leader</td>';
                      datatable += '<td style="border:1px solid black">'+result.datass[j][0].leader+'</td>';
                      datatable += '</tr>';
                      datatable += '</tbody>';
                      datatable += '</table>';
                    datatable += '</div>';
                  datatable += '</div>';
                datatable += '</div>';
            datatable += '</div>';
            datatable += '</td>';
            datatable += '</tr>';
          }
          datatable += '</tbody>';
          datatable += '</table>';
        }

        $('#data-activity').append(datatable);

        for(var j = 0; j < result.datas.length;j++){

            // $('#data-log-detail-'+j).DataTable({
            //   'dom': 'Bfrtip',
            //   'responsive':true,
            //   'lengthMenu': [
            //   [ 10, 25, 50, -1 ],
            //   [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            //   ],
            //   'buttons': {
            //     buttons:[
            //     {
            //       extend: 'pageLength',
            //       className: 'btn btn-default',
            //     },
            //     {
            //       extend: 'copy',
            //       className: 'btn btn-success',
            //       text: '<i class="fa fa-copy"></i> Copy',
            //         exportOptions: {
            //           columns: ':not(.notexport)'
            //       }
            //     },
            //     {
            //       extend: 'excel',
            //       className: 'btn btn-info',
            //       text: '<i class="fa fa-file-excel-o"></i> Excel',
            //       exportOptions: {
            //         columns: ':not(.notexport)'
            //       }
            //     },
            //     {
            //       extend: 'print',
            //       className: 'btn btn-warning',
            //       text: '<i class="fa fa-print"></i> Print',
            //       exportOptions: {
            //         columns: ':not(.notexport)'
            //       }
            //     }
            //     ]
            //   },
            //   'paging': true,
            //   'lengthChange': true,
            //   'searching': true,
            //   'ordering': true,
            //   'order': [],
            //   'info': true,
            //   'autoWidth': true,
            //   "sPaginationType": "full_numbers",
            //   "bJQueryUI": true,
            //   "bAutoWidth": false,
            //   "processing": true
            // });
        }

        $('#data_log_sudah').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            },
            {
              extend: 'copy',
              className: 'btn btn-success',
              text: '<i class="fa fa-copy"></i> Copy',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'excel',
              className: 'btn btn-info',
              text: '<i class="fa fa-file-excel-o"></i> Excel',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'print',
              className: 'btn btn-warning',
              text: '<i class="fa fa-print"></i> Print',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            }
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 10,
          'searching': true,
          "processing": true,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        $('#judul_weekly').html('<b>Audit IK <br>Bulan '+result.monthTitle+' <br>dengan Kondisi '+kondisi+'<b>');

        $('#loading').hide();
        $('#modalDetail').modal('show');
      }else{
        $('#loading').hide();
        openErrorGritter('Error!',result.message);
      }
    });
  }

  // function showModal(month,kondisi,department) {
  //   $('#loading').show();
  //   var data = {
  //     month: month,
  //     kondisi: kondisi,
  //   }

  //   $.get('{{ url("fetch/detail_audit_ik_monitoring") }}', data, function(result, status, xhr) {
  //     if(result.status){
  //       $('#data-activity').html('');
  //       var datatable = "";

  //       $('#data_log_sudah').DataTable().clear();
  //       $('#data_log_sudah').DataTable().destroy();

  //       if (kondisi === 'Belum Dikerjakan') {
  //         datatable += '<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">';
  //         datatable += '<thead>'
  //         datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
  //         datatable += '<th>No. Dokumen</th>';
  //         datatable += '<th>Nama Dokumen</th>';
  //         datatable += '<th>Target</th>';
  //         datatable += '<th>Leader</th>';
  //         datatable += '<th>Foreman</th>';
  //         datatable += '</tr>';
  //         datatable += '</thead>';

  //         for(var j = 0; j < result.datas.length;j++){
  //             datatable += '<tbody style="border:1px solid black">';
  //             datatable += '<tr style="border:1px solid black">';
  //             datatable += '<td>'+result.datas[j].no_dokumen+'</td>';
  //             datatable += '<td>'+result.datas[j].nama_dokumen+'</td>';
  //             datatable += '<td>'+result.datas[j].month+'</td>';
  //             datatable += '<td>'+result.datas[j].leader+'</td>';
  //             datatable += '<td>'+result.datas[j].foreman+'</td>';
  //             datatable += '</tr>';
  //             datatable += '</tbody>';
  //         }
  //         datatable += '</table>';
  //         datatable += '<hr style="border:2px solid black">';
  //       }else{
  //         datatable += '<table id="data_log_sudah" class="table table-striped table-bordered" style="width: 100%;">';
  //         datatable += '<thead>'
  //         datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#fcba03">';
  //         datatable += '<th>Dokumen</th>';
  //         datatable += '</tr>';
  //         datatable += '</thead>';
  //         datatable += '<tbody style="border:1px solid black">';
  //         for(var j = 0; j < result.datas.length;j++){
  //           $('#data-log-detail-'+j).DataTable().clear();
  //           $('#data-log-detail-'+j).DataTable().destroy();
  //           datatable += '<tr>';
  //           datatable += '<td>';
  //           datatable += '<div class="box-group" id="accordion">';
  //               datatable += '<div class="panel box box-solid">';
  //                 datatable += '<div class="box-header with-border" style="background-color:#7e5686;text-align:center">';
  //                   datatable += '<h4 class="box-title">';
  //                     datatable += '<a data-toggle="collapse" data-parent="#accordion" href="#collapse'+j+'" aria-expanded="false" class="collapsed" style="color:white;">'+result.datass[j][0].no_dokumen+' - '+result.datass[j][0].nama_dokumen+' ('+result.datass[j][0].leader+')';
  //                     datatable += '</a>';
  //                   datatable += '</h4>';
  //                   var hrefreport = '{{url("index/audit_report_activity/print_audit_report/")}}/'+result.datass[j][0].activity_list_id+'/'+result.datass[j][0].month;
  //                   datatable += '<a  href="'+hrefreport+'" target="_blank" class="btn btn-success btn-sm pull-right">Report <i class="fa fa-arrow-right"></i></a>';
  //                 datatable += '</div>';
  //                 datatable += '<div id="collapse'+j+'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">';
  //                   datatable += '<div class="box-body">';
  //                   datatable += '<table id="data-log-detail-'+j+'" class="table table-bordered table-striped" style="width: 100%;">';
  //                     datatable += '<thead>';
  //                     datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
  //                     datatable += '<th style="width:1%">Target : '+result.datass[j][0].month+'</th>';
  //                     datatable += '<th style="width:3%">Nama Dokumen : '+result.datass[j][0].nama_dokumen+'</th>';
  //                     datatable += '</tr>';
  //                     datatable += '</thead>';

  //                     datatable += '<tbody style="border:1px solid black">';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Dept</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].department+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Section</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].section+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Group</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].subsection+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Audit Date</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].date+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black;background-color:#a6ffc9">Kesesuaian Aktual</td>';
  //                     datatable += '<td style="border:1px solid black;background-color:#a6ffc9">'+result.datass[j][0].kesesuaian_aktual_proses+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black;background-color:#a6d4ff">Kesesuaian QC Kouteihyo</td>';
  //                     datatable += '<td style="border:1px solid black;background-color:#a6d4ff">'+result.datass[j][0].kesesuaian_qc_kouteihyo+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black;background-color:#ffa6a6">Kelengkapan Point Safety</td>';
  //                     datatable += '<td style="border:1px solid black;background-color:#ffa6a6">'+result.datass[j][0].kelengkapan_point_safety+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Tindakan Perbaikan</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].tindakan_perbaikan+'</td>';
  //                     datatable += '</tr>';
  //                     if (result.datass[j][0].tindakan_perbaikan == '-') {
  //                       datatable += '<tr>';
  //                       datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
  //                       datatable += '<td style="border:1px solid black"></td>';
  //                       datatable += '</tr>';
  //                     }else{
  //                       datatable += '<tr>';
  //                       datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
  //                       datatable += '<td style="border:1px solid black">'+result.datass[j][0].target+'</td>';
  //                       datatable += '</tr>';
  //                     }
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Kondisi</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].condition+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Penanganan</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].handling+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">PIC</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].operator+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '<tr>';
  //                     datatable += '<td style="border:1px solid black">Leader</td>';
  //                     datatable += '<td style="border:1px solid black">'+result.datass[j][0].leader+'</td>';
  //                     datatable += '</tr>';
  //                     datatable += '</tbody>';
  //                     datatable += '</table>';
  //                   datatable += '</div>';
  //                 datatable += '</div>';
  //               datatable += '</div>';
  //           datatable += '</div>';
  //           datatable += '</td>';
  //           datatable += '</tr>';
  //         }
  //         datatable += '</tbody>';
  //         datatable += '</table>';
  //       }

  //       $('#data-activity').append(datatable);

  //       $('#data_log_sudah').DataTable({
  //         'dom': 'Bfrtip',
  //         'responsive':true,
  //         'lengthMenu': [
  //         [ 10, 25, 50, -1 ],
  //         [ '10 rows', '25 rows', '50 rows', 'Show all' ]
  //         ],
  //         'buttons': {
  //           buttons:[
  //           {
  //             extend: 'pageLength',
  //             className: 'btn btn-default',
  //           },
  //           {
  //             extend: 'copy',
  //             className: 'btn btn-success',
  //             text: '<i class="fa fa-copy"></i> Copy',
  //             exportOptions: {
  //               columns: ':not(.notexport)'
  //             }
  //           },
  //           {
  //             extend: 'excel',
  //             className: 'btn btn-info',
  //             text: '<i class="fa fa-file-excel-o"></i> Excel',
  //             exportOptions: {
  //               columns: ':not(.notexport)'
  //             }
  //           },
  //           {
  //             extend: 'print',
  //             className: 'btn btn-warning',
  //             text: '<i class="fa fa-print"></i> Print',
  //             exportOptions: {
  //               columns: ':not(.notexport)'
  //             }
  //           }
  //           ]
  //         },
  //         'paging': true,
  //         'lengthChange': true,
  //         'pageLength': 10,
  //         'searching': true,
  //         "processing": true,
  //         'ordering': true,
  //         'order': [],
  //         'info': true,
  //         'autoWidth': true,
  //         "sPaginationType": "full_numbers",
  //         "bJQueryUI": true,
  //         "bAutoWidth": false,
  //         "processing": true
  //       });

  //       $('#judul_weekly').html('<b>Audit IK <br>Bulan '+result.monthTitle+' <br>dengan Kondisi '+kondisi+'<b>');

  //       $('#loading').hide();
  //       $('#modalDetail').modal('show');
  //     }else{
  //       $('#loading').hide();
  //       openErrorGritter('Error!',result.message);
  //     }
  //   });
  // }


  Highcharts.createElement('link', {
    href: '{{ url("fonts/UnicaOne.css")}}',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);

  Highcharts.theme = {
    colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
    '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
        stops: [
        [0, '#2a2a2b']
        ]
      },
      style: {
        fontFamily: 'sans-serif'
      },
      plotBorderColor: '#606063'
    },
    title: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase',
        fontSize: '20px'
      }
    },
    subtitle: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase'
      }
    },
    xAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      title: {
        style: {
          color: '#A0A0A3'

        }
      }
    },
    yAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      tickWidth: 1,
      title: {
        style: {
          color: '#A0A0A3'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.85)',
      style: {
        color: '#F0F0F0'
      }
    },
    plotOptions: {
      series: {
        dataLabels: {
          color: 'white'
        },
        marker: {
          lineColor: '#333'
        }
      },
      boxplot: {
        fillColor: '#505053'
      },
      candlestick: {
        lineColor: 'white'
      },
      errorbar: {
        color: 'white'
      }
    },
    legend: {
    },
    credits: {
      style: {
        color: '#666'
      }
    },
    labels: {
      style: {
        color: '#707073'
      }
    },

    drilldown: {
      activeAxisLabelStyle: {
        color: '#F0F0F3'
      },
      activeDataLabelStyle: {
        color: '#F0F0F3'
      }
    },

    navigation: {
      buttonOptions: {
        symbolStroke: '#DDDDDD',
        theme: {
          fill: '#505053'
        }
      }
    },

    rangeSelector: {
      buttonTheme: {
        fill: '#505053',
        stroke: '#000000',
        style: {
          color: '#CCC'
        },
        states: {
          hover: {
            fill: '#707073',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          },
          select: {
            fill: '#000003',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          }
        }
      },
      inputBoxBorderColor: '#505053',
      inputStyle: {
        backgroundColor: '#333',
        color: 'silver'
      },
      labelStyle: {
        color: 'silver'
      }
    },

    navigator: {
      handles: {
        backgroundColor: '#666',
        borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(255,255,255,0.1)',
      series: {
        color: '#7798BF',
        lineColor: '#A6C7ED'
      },
      xAxis: {
        gridLineColor: '#505053'
      }
    },

    scrollbar: {
      barBackgroundColor: '#808083',
      barBorderColor: '#808083',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: '#606063',
      buttonBorderColor: '#606063',
      rifleColor: '#FFF',
      trackBackgroundColor: '#404043',
      trackBorderColor: '#404043'
    },

    legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
    background2: '#505053',
    dataLabelsColor: '#B0B0B3',
    textColor: '#C0C0C0',
    contrastTextColor: '#F0F0F3',
    maskColor: 'rgba(255,255,255,0.3)'
  };
  Highcharts.setOptions(Highcharts.theme);


function openSuccessGritter(title, message){
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '5000'
  });
}

function openErrorGritter(title, message) {
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-danger',
    image: '{{ url("images/image-stop.png") }}',
    sticky: false,
    time: '5000'
  });
}
</script>