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
            <select class="form-control" id="fq" onchange="drawChart()" style="border-color: #605ca8">
              <option value="FY196">FY196</option>
              <option value="FY195">FY195</option>
            </select>
          </div>
          <br>
        </div>

        <div class="col-md-2 pull-right">
          <div class="input-group date">
            <div class="input-group-addon bg-purple" style="border-color: #605ca8">
              <i class="fa fa-group"></i>
            </div>
            <select class="form-control select2" id="section" style="border-color: #605ca8" onchange="drawChart()">

              @foreach($cost_center as $cc)
              <option value="{{ $cc->cost_center }}" > {{$cc->cost_center_name}}</option>
              @endforeach
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

		var fq = $('#fq').val();
    var section = $('#section').val();

    var data = {
     tahun: fq,
     section: section
   }

   $.get('{{ url("fetch/report/overtime_report_section") }}', data, function(result) {

    // -------------- CHART OVERTIME REPORT CONTROL ----------------------
    var seriesData = [];
    var xCategories = [];
    var seriesDataFirst = [];
    var uniqueNames = [];
    var newData = [];
    var i, cat;
    var title = fq;
    var check = 0;

    for(i = 0; i < result.date.length; i++){
     xCategories.push(result.date[i].bulan);
   }

   $.each(result.datas, function(i, el){
    if($.inArray(el.employee_id+"-"+el.name, uniqueNames) === -1) uniqueNames.push(el.employee_id+"-"+el.name);
  });


   $.each(uniqueNames, function(key, value) {
    $.each(xCategories, function(key2, value2) {
      seriesDataFirst.push({nik:value, date:value2});
    });
  });

   // console.log(seriesDataFirst);

   $.each(seriesDataFirst, function(key, value) {
    $.each(result.datas, function(index, elem) {
      if(value.nik == (elem.employee_id+"-"+elem.name) && value.date == elem.mon) {
        newData.push({nik:value.nik, date:value.date, jam:elem.jam});
        check = 1;
      }
    });

    if (check == 0) {
      newData.push({nik:value.nik, date:value.date, jam:0});
    }

    check = 0;
  });

   


   for(i = 0; i < newData.length; i++){
    if(seriesData){
      var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == newData[i].nik;});
      if(currSeries.length === 0){
        seriesData[seriesData.length] = currSeries = {name: newData[i].nik, data: []};
      }
      else {
        currSeries = currSeries[0];
      }
      var index = currSeries.data.length;
      currSeries.data[index] = newData[i].jam;
    }
    else {
      seriesData[0] = {name: newData[i].jam, data: [intVal(newData[i].jam)]}
    }
  }

  var target = [];

  $.each(result.budgets, function(key, value) {
    target.push(value.budget_mp);
  })


        // Populate series
        seriesData.push({type: 'spline', name: 'Target OT', data: target, color: 'red', dashStyle: 'dash'});

        

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

 $('#tgl').datepicker({
   <?php $tgl_max = date('d-m-Y') ?>
   autoclose: true,
   format: "dd-mm-yyyy",
   endDate: '<?php echo $tgl_max ?>',
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