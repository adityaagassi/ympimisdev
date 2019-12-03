@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	@font-face {
		font-family: JTM;
		src: url("{{ url("fonts/JTM.otf") }}") format("opentype");
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

	.judul {
		font-family: 'JTM';
		color: white;
		font-size: 35pt;
	}
	#kz_top_sum, #kz_top_count {
		font-size: 15pt;
	}

	#kz_top_count > tr {
		color: white;
	}
	#kz_top_sum > tr:first-child, #kz_top_count > tr:first-child {
		color: #363836 !important;
		background-color: #ffbf00 !important;
	}
	#kz_top_sum > tr:nth-child(2), #kz_top_count > tr:nth-child(2) {
		color: #363836 !important;
		background-color: #a9aba9 !important;
	}
	#kz_top_sum > tr:nth-child(3), #kz_top_count > tr:nth-child(3) {
		color: #363836 !important;
		background-color: #cc952f !important;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<div class="col-xs-2 pull-left">
					<p class="judul"><i style="color: #c290d1">e </i> - Kaizen</p>
				</div>
				<div class="col-xs-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="kz_total" style="width: 100%; height: 500px;"></div>
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
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/drilldown.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');
		drawChart();

		// setInterval(drawChart, 3000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {

		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal
		}

		$.get('{{ url("fetch/kaizen/report") }}', data, function(result) {
			var data1 = [];
			var data_total = [];
			var total_tmp = 0;
			var data_tmp = [];

			for (var i = 0; i < result.charts.length; i++) {
				if (typeof result.charts[i+1] === 'undefined') {
					total_tmp += parseInt(result.charts[i].kaizen);
					data_total.push({name:result.charts[i].department, y:total_tmp, drilldown:result.charts[i].department});
				} else {
					if (result.charts[i].department != result.charts[i+1].department) {
						total_tmp += parseInt(result.charts[i].kaizen);
						data_total.push({name:result.charts[i].department, y:total_tmp, drilldown:result.charts[i].department});
						total_tmp = 0;
					} else {
						total_tmp += parseInt(result.charts[i].kaizen);
					}
				}
			}

			// console.table(data_total);

			for (var z = 0; z < data_total.length; z++) {
				for (var x = 0; x < result.charts.length; x++) {
					if (data_total[z].name == result.charts[x].department) {
						data_tmp.push([result.charts[x].section, parseInt(result.charts[x].kaizen)]);
					}
				}
				data1.push({name:data_total[z].name, id:data_total[z].name, data: data_tmp});
				data_tmp = [];
			}

			// console.table(data1);

			Highcharts.chart('kz_total', {
				chart: {
					type: 'column'
				},
				title: {
					text: 'Data Kaizen Teian'
				},
				subtitle: {
					text: 'Click the columns to view detail per Section'
				},
				accessibility: {
					announceNewData: {
						enabled: true
					}
				},
				xAxis: {
					type: 'category'
				},
				yAxis: {
					title: {
						text: 'Total Kaizen'
					}

				},
				legend: {
					enabled: false
				},
				plotOptions: {
					series: {
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							format: '{point.y}'
						}
					},
					column: {
						animation: false
					}
				},

				credits:{
					enabled:false
				},
				tooltip: {
					headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
					pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
				},

				series: [
				{
					name: "Department",
					colorByPoint: true,
					data: data_total
				}
				],
				drilldown: {
					series: data1
				}
			});
		});
	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months"
	});
</script>
@endsection
