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
  }
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }
  #queueTable.dataTable {
    margin-top: 0px!important;
  }
  #loading, #error { display: none; }
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
            <select class="form-control" id="fy" onchange="drawChart()" style="border-color: #605ca8">
              <option value="FY196">FY196</option>
              <option value="FY195">FY195</option>
            </select>
          </div>
          <br>
        </div>
      </div>

      <div class="col-md-12">
        <div id="over_control" style="width: 100%; height: 550px;"></div>
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

	function drawChart() {
    var fy = $("#fy").val();

    var data = {
      fy: fy
    }

    $.get('{{ url("fetch/report/overtime_report_outsource") }}', data, function(result) {

    // -------------- CHART OVERTIME REPORT CONTROL ----------------------
    var seriesData = [];
    var xCategories = [];
    var seriesDataFirst = [];
    var uniqueNames = [];
    var newData = [];
    var i, cat;
    var title = fy;
    var check = 0;

    for(var z = 0; z < result.datas.length; z++){
      cat = result.datas[z].bulan;
      
      if(xCategories.indexOf(cat) === -1){
        xCategories[xCategories.length] = cat;
      }
    }
    console.log(xCategories);

    for(i = 0; i < result.datas.length; i++){
      if(seriesData){
        var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == result.datas[i].nik+"-"+result.datas[i].namaKaryawan;});
        if(currSeries.length === 0){
          seriesData[seriesData.length] = currSeries = {name: result.datas[i].nik+"-"+result.datas[i].namaKaryawan, data: []};
        }
        else {
          currSeries = currSeries[0];
        }
        var index = currSeries.data.length;
        currSeries.data[index] = result.datas[i].jam;
      }
      else {
        seriesData[0] = {name: result.datas[i].jam, data: [intVal(result.datas[i].jam)]}
      }
    }       

    $('#over_control').highcharts({
      chart: {
        type: 'spline'
      },
      title: {
        text: 'YEAR '+title
      },
      xAxis: {
        categories: xCategories
      },
      yAxis: {
        title: {
          text: 'Total Jam'
        }
      },
      legend: {
        enabled: false
      },
      tooltip: {
        formatter: function () {
          return this.series.name +
          ' : ' + this.y + 'hour(s)';
        }
      },
      plotOptions: {
        line: {
          dataLabels: {
            enabled: false
          },
          enableMouseTracking: true
        },
        series: {
          marker: {
            enabled: false
          },
          lineWidth: 1
        }
      },
      credits:{
        enabled:false
      },
      series: seriesData
    });
  });
  }

  $('#bulan').datepicker({
   autoclose: true,
   format: "yyyy-mm",
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