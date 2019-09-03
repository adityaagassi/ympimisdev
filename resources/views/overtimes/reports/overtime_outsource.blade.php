@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
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
    border:1px solid rgb(211,211,211);
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="col-md-2 pull-right">
          <div class="input-group date">
            <div class="input-group-addon bg-purple" style="border-color: #605ca8">
              <i class="fa fa-calendar"></i>
            </div>

            <input type="text" id="bulan" onchange="drawChart()" style="border-color: #605ca8" class="form-control datepicker" placeholder="select Month">
          </div>
          <br>
        </div>
      </div>

      <div class="col-md-12">
        <div class="nav-tabs-custom"> 
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
              <div id="tidak_ada_data"></div>
              <div id="over_control" style="width: 99%;"></div>
            </div>
            <!-- /.tab-pane -->
          </div>
          <!-- /.tab-content -->
        </div>
      </div>
    </div>
  </div>

  <!-- start modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tabel_detail" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 8%;">Date</th>
                    <th style="width: 10%;">Employee ID</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 3%;">Overtime (hour)</th>
                    <th style="width: 20%;">Reason</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal -->

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

    drawChart();
  });

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


	function drawChart() {
    var bulan = $("#bulan").val();

    var data = {
      bulan: bulan
    }

    $.get('{{ url("fetch/report/overtime_report_outsource") }}', data, function(result) {

      if(result.datas.length > 0){
        $('#tidak_ada_data').append().empty();

        var nama = [];
        var jam = [];
        var titleChart = result.bulan;

        
        for (var i = 0; i < result.datas.length; i++) {
          nama.push(result.datas[i].namaKaryawan);
          jam.push(result.datas[i].jam);
        }

        Highcharts.chart('over_control', {
          chart: {
            type: 'column'
          },
          title: {
            text: '<span style="font-size: 18pt;">Overtime Outsource</span><br><center><span style="color: rgba(96, 92, 168);">'+bulanText[parseInt(titleChart.slice(0,2))-1]+' '+titleChart.slice(3,7)+'</center></span>',
            useHTML: true
          },
          xAxis: {
            categories: nama
          },
          yAxis: {
            title: {
              text: 'Total Overtime'
            }
          },
          legend : {
            enabled: false
          },
          tooltip: {
            headerFormat: '',
            pointFormat: '<span style="color:{point.color}">Overtime {point.category}</span>: <b>{point.y}</b> <br/>'
          },
          plotOptions: {
            series: {
              cursor: 'pointer',
              point: {
                events: {
                  click: function (event) {
                    showDetail(event.point.category, result.bulan);
                  }
                }
              },
              borderWidth: 0,
              dataLabels: {
                enabled:true,
                formatter:function() {
                  var pcnt = this.y;
                  return Highcharts.numberFormat(pcnt);
                }
              }
            }
          },credits: {
            enabled: false
          },
          series: [
          {
            colorByPoint : true,
            data: jam,
          }
          ]
        });
      }else{
        $('#over_control').append().empty();
        $('#tidak_ada_data').append().empty();
        $('#tidak_ada_data').append("<br><div class='alert alert-warning alert-dismissible' data-dismiss='alert' aria-hidden='true' style='margin-right: 3.3%;margin-left: 2%'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><h4><i class='icon fa fa-warning'></i> This month's overtime is empty!</h4></div>");
      }

    });
  }

  function showDetail(nama,period) {
    tabel = $('#tabel_detail').DataTable();
    tabel.destroy();

    $('#myModal').modal('show');

    var table = $('#tabel_detail').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
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
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("fetch/report/overtime_detail_outsource") }}",
        "data" : {
          nama : nama,
          period : period
        }
      },
      "columns": [
      { "data": "tanggal", "width": "15%" },
      { "data": "nik", "width": "15%" },
      { "data": "namaKaryawan", "width": "20%" },
      { "data": "ot", "width": "5%" },
      { "data": "reason", "width": "45%" }
      ]
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center>Overtime '+nama+' in '+bulanText[parseInt(period.slice(0,2))-1]+' '+period.slice(3,7)+'<center>');


  }

  $('#bulan').datepicker({
    autoclose: true,
    format: "mm-yyyy",
    startView: "months", 
    minViewMode: "months"
  });

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