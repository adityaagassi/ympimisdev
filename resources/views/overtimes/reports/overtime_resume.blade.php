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

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-2">
				<div class="form-group">
					<select class="form-control select2" data-placeholder="Select Fiscal Year" id="fiscal_year" name="fiscal_year"> 
						<option value=""></option>
						@foreach($fiscal_years as $fiscal_year)
						<option value="{{ $fiscal_year->fiscal_year }}">{{ $fiscal_year->fiscal_year }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" data-placeholder="Select Cost Center" name="costcenter" id="costcenter">
						@foreach($costcenters as $costcenter)
						<option value="{{ $costcenter->cost_center }}">{{ $costcenter->cost_center_name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-1">
				<div class="form-group">
					<button class="btn btn-success" type="submit" onClick="drawChart()">Search</button>
				</div>
			</div>
			<div class="col-md-12">
				<div id="wait" class="loading">
					<div>
						<center>
							<i class="fa fa-spinner fa-5x fa-spin" style="color: white;"></i><br>
							<h2 style="margin: 0px; color: white;">Loading . . .</h2>
						</center>
					</div>
				</div>
				<div class="nav-tabs-custom" id="tab-ot">
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div id="ot" style="width: 99%;"></div>
						</div>
					</div>
				</div>
				<div class="nav-tabs-custom" id="tab-mp">
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div id="mp" style="width: 99%;"></div>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>

</section>


@stop

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

		$('.select2').select2();

		drawChart();
	});

	function drawChart(){
		$("#wait").show();
		$("#tab-ot").hide();
		$("#tab-mp").hide();

		var fy = $('#fiscal_year').val();
		var cc = $('#costcenter').val();
		var data = {
			fy:fy,
			cc:cc
		}
		
		$.get('{{ url("fetch/report/overtime_resume") }}', data, function(result, status, xhr) {
			$("#wait").hide();
			if(result.status){
				$("#tab-ot").show();
				$("#tab-mp").show();
				// ----------------  OVERTIME ----------------------
				if(result.ot_actual.length > 0){
					var period = [];
					var avg_ot_actual = [];
					var avg_ot_forecast = [];
					var avg_ot_budget = [];

					var total_ot_actual = [];
					var total_ot_forecast = [];
					var total_ot_budget = [];

					var mov_ot_actual = [];
					var mov_ot_forecast = [];
					var mov_ot_budget = [];

					for (var i = 0; i < result.ot_actual.length; i++) {
						period.push(result.ot_actual[i].period);
						avg_ot_actual.push(result.ot_actual[i].total/result.mp_actual[i].emp);
						avg_ot_budget.push(result.ot_budget[i].total_budget/result.mp_actual[i].emp);
						avg_ot_forecast.push(result.ot_forecast[i].total_forecast/result.mp_actual[i].emp);

						avg_ot_actual[i] = avg_ot_actual[i] || 0; 
						avg_ot_forecast[i] = avg_ot_forecast[i] || 0; 
						avg_ot_budget[i] = avg_ot_budget[i] || 0; 

						if(i == 0){
							total_ot_actual.push(avg_ot_actual[i]);
							total_ot_forecast.push(avg_ot_forecast[i]); 
							total_ot_budget.push(avg_ot_budget[i]);  
						}
						else{
							total_ot_actual.push(total_ot_actual[i-1] + avg_ot_actual[i]);
							total_ot_forecast.push(total_ot_forecast[i-1] + avg_ot_forecast[i]);
							total_ot_budget.push(total_ot_budget[i-1] + avg_ot_budget[i]);
						}

						mov_ot_actual.push(total_ot_actual[i] / (i+1));
						mov_ot_forecast.push(total_ot_forecast[i] / (i+1));	
						mov_ot_budget.push(total_ot_budget[i] / (i+1));

					}

					Highcharts.chart('ot', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Overtime Monthly Resume in '+result.fy
						},
						xAxis: {
							categories: period,
							gridLineWidth: 3,
							gridLineColor: 'RGB(204,255,255)',
							crosshair: true
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							series: {
								pointPadding: 0.93,
								groupPadding: 0.93,
								shadow: false,
								cursor: 'pointer',
								borderWidth: 0
							},
							column: {
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								},
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2);
									}
								}
							},
							spline: {
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2);
									},
									color: (Highcharts.theme && Highcharts.theme.dataLabelsColor)
									|| 'white',
									style: {
										textShadow: '0 0 3px black'
									}
								}

							}
						},
						credits: {
							enabled: false
						},
						legend : {
							align: 'center',
							verticalAlign: 'bottom',
							x: 0,
							y: 0,

							backgroundColor: (
								Highcharts.theme && Highcharts.theme.background2) || 'white',
							borderColor: '#CCC',
							borderWidth: 1,
							shadow: false
						},
						series: [
						{
							name: 'Actual',
							data: avg_ot_actual,
						},
						{
							name: 'Forecast',
							data: avg_ot_forecast,
						},
						{
							name: 'Budget',
							data: avg_ot_budget,
						},
						{
							color: '#90ed7d',
							name: 'Mov. Budget',
							type: 'spline',
							data: mov_ot_budget,
							marker: {
								enabled: true
							}
						},
						{
							color: '#434348',
							name: 'Mov. Forecast',
							type: 'spline',
							data: mov_ot_forecast,
							marker: {
								enabled: true
							}
						},
						{
							color: '#7cb5ec',
							name: 'Mov. Actual',
							type: 'spline',
							data: mov_ot_actual,
							marker: {
								enabled: true
							}
						}
						]

					});

				}

			// ----------------  MANPOWER ----------------------

			if(result.mp_actual.length > 0){
				var period = [];
				var mp_actual = [];
				var mp_forecast = [];
				var mp_budget = [];

				for (var i = 0; i < result.mp_actual.length; i++) {
					period.push(result.ot_actual[i].period);

					mp_actual.push(parseInt(result.mp_actual[i].emp));
					mp_budget.push(parseInt(result.mp_budget[i].total_budget_mp));
					mp_forecast.push(parseInt(result.mp_forecast[i].total_forecast_mp));
				}

				Highcharts.chart('mp', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Manpower Monthly Resume in '+result.fy
					},
					xAxis: {
						categories: period,
						gridLineWidth: 3,
						gridLineColor: 'RGB(204,255,255)',
						crosshair: true
					},
					yAxis: {
						tickPositioner: function () {

							var Dev6 = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin))*1.05);
							var Dev5 = Math.floor(Math.min(Math.abs(this.dataMax), Math.abs(this.dataMin))*1);
							var Dev4 = Math.floor(Math.min(Math.abs(this.dataMax), Math.abs(this.dataMin))*0.95);
							var Dev3 = Math.floor(Math.min(Math.abs(this.dataMax), Math.abs(this.dataMin))*0.9);
							var Dev2 = Math.floor(Math.min(Math.abs(this.dataMax), Math.abs(this.dataMin))*0.85);
							var Dev1 = Math.floor(Math.min(Math.abs(this.dataMax), Math.abs(this.dataMin))*0.8);

							return [Dev1, Dev2, Dev3, Dev4, Dev5, Dev6];
						},
						title: {
							text: 'Total'
						}
					},
					legend : {
						enabled: false
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						series: {
							pointPadding: 0.93,
							groupPadding: 0.93,
							shadow: false,
							cursor: 'pointer',
							borderWidth: 0
						},
						column: {
							events: {
								legendItemClick: function () {
									return false; 
								}
							},
							animation:{
								duration:0
							},
							dataLabels: {	
								enabled: true,
								rotation: -65,
								formatter: function () {
									return Highcharts.numberFormat(this.y,0);
								}
							}
						}
					},
					credits: {
						enabled: false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					series: [
					{
						name: 'Actual',
						data: mp_actual,
					},
					{
						name: 'Forecast',
						data: mp_forecast,
					},
					{
						name: 'Budget',
						data: mp_budget,
					}
					]
					
				});

			}

		}
	});

}

</script>


@stop