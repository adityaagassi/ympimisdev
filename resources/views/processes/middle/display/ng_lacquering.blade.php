@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		background-color: rgba(126,86,134,.7);
		text-align: center;
		vertical-align: middle;
		color: black;
		font-size: 1vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 1vw;
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
		color: black;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('MiddleProcessController@indexReportLcqNg') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="bulan" placeholder="Select Month">
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="fySelect" data-placeholder="Select Fiscal Year" onchange="change()">
								@foreach($fys as $fy)
								<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
								@endforeach
							</select>
							<input type="text" name="fy" id="fy" hidden>
						</div>
					</div>
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="submit">Search</button>
						</div>
					</div>
				</form>
				<!-- <ol class="breadcrumb pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 12px;"></ol> -->
			</div>
			
			<div class="col-xs-12" style="padding: 0px">
				<div class="col-xs-12" style="padding: 0px">
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly" class="table table-bordered" style="margin:0">
												<thead id="head_monthly">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">NG Rate</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
													</tr>
												</thead>
												<tbody id="body_monthly">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_weekly" class="table table-bordered" style="margin:0">
												<thead id="head_weekly">
													<tr style="background-color: rgba(126,86,134,.7);">
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">%NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_weekly">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart1" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div id="chart2" style="width: 99%;"></div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div id="chart3" style="width: 99%;"></div>
							</div>
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
		// setInterval(drawChart, 18000);
	});

	function change() {
		$("#fy").val($("#fySelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('m-Y') ?>
		format: "mm-yyyy",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		endDate: '<?php echo $tgl_max ?>'

	});

	function bulanText(param){
		var bulan = parseInt(param.slice(0, 2));
		var tahun = param.slice(3, 8);
		var bulanText = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

		return bulanText[bulan-1]+" "+tahun;
	}


	function drawChart(){
		var data = {
			bulan:"{{$_GET['bulan']}}",
			fy:"{{$_GET['fy']}}"
		}

		$.get('{{ url("fetch/middle/lcq_ng_rate_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var fy = result.fy;
					var tgl = [];
					var target = [];
					var ng = [];
					var total = [];		
					var ng_rate = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly.length; i++) {
						tgl.push(result.monthly[i].tgl);
						target.push(8.37);
						ng.push(result.monthly[i].ng);
						total.push(result.monthly[i].total);
						total[i] = total[i] || 0;

						ng_rate[i] = (ng[i] / total[i]) * 100;
						ng_rate[i] = ng_rate[i] || 0;
						ng_rate_monthly.push(ng_rate[i]);
					}

					var body = "";
					for (var i = 0; i < result.monthly.length; i++) {
						body += "<tr>";
						body += "<td>"+tgl[i]+"</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng[i]+"</td>";
						body += "<td>"+total[i]+"</td>";
						body += "</tr>";
					}
					$('#body_monthly').append(body);

					Highcharts.chart('chart', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate %IC Sax Key LCQ in '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: tgl
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							max: 100,
							min: 0
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',

						},
						plotOptions: {
							column: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2);
									}
								}
							},
							line: {
								marker: {
									enabled: false
								}
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'NG Rate',
							data: ng_rate_monthly
						},
						{
							name: 'Target',
							type: 'line',
							data: target,
							color: '#FF0000',
						}
						]
					});

				}
			}
		});

		$.get('{{ url("fetch/middle/lcq_ng_rate_weekly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var bulan = result.bulan;
					var week_name = [];
					var ng = [];
					var g = [];
					var perolehan = []
					var ng_rate = [];
					var ng_rate_weekly = [];

					for (var i = 0; i < result.weekly.length; i++) {
						week_name.push(result.weekly[i].week_name);

						ng.push(parseInt(result.weekly[i].ng));
						g.push(parseInt(result.weekly[i].g));
						ng[i] = ng[i] || 0;
						g[i] = g[i] || 0;
						perolehan[i] = ng[i] + g[i];
						ng_rate[i] = (ng[i] / perolehan[i]) * 100;
						ng_rate[i] = ng_rate[i] || 0;
						ng_rate_weekly.push(ng_rate[i]);
					}

					console.log(ng);
					console.log(perolehan);
					console.log(ng_rate);

					var body = "";
					for (var i = 0; i < result.weekly.length; i++) {
						body += "<tr>";
						body += "<td>"+week_name[i]+"</td>";
						body += "<td>"+perolehan[i]+"</td>";
						body += "<td>"+ng[i]+"</td>";
						body += "<td>"+ng_rate_weekly[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_weekly').append(body);

					console.log(body);		

					Highcharts.chart('chart1', {
						chart: {
							type: 'line'
						},
						title: {
							text: '<span style="font-size: 18pt;">%NG Rate Weekly in '+bulanText(bulan)+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: week_name
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							max: 100,
							min: 0
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">NG Rate </span>: <b>{point.y:.2f}%</b> <br/>',

						},
						plotOptions: {
							line: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2);
									}
								}
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							data: ng_rate_weekly
						}
						]
					});

				}
			}
		});

		$.get('{{ url("fetch/middle/lcq_ng") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var ng_name = [];
					var jml = [];			
					for (var i = 0; i < result.ng.length; i++) {
						ng_name.push(result.ng[i].ng_name);
						jml.push(parseInt(result.ng[i].jml));
					}
					var bulan = result.bulan;

					Highcharts.chart('chart2', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Terbesar IC Sax Key LCQ in '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
							useHTML: true
						},
						xAxis: {
							categories: ng_name,
							labels: {
								style: {
									textOverflow: 'ellipsis'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Total Not Good'
							}
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '',
							pointFormat: '<span style="color:{point.color}">Not Good {point.category}</span>: <b>{point.y}</b> <br/>'
						},
						plotOptions: {
							series: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									format: '{point.y}'
								}
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							"colorByPoint": true,
							name: 'NG',
							data: jml,
						}
						]
					});
				}
			}


		});

		$.get('{{ url("fetch/middle/lcq_ng_rate") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var tgl = [];
					var ng = [];
					var g = [];
					var ng_rate = [];
					var ng_rate_daily = [];

					for (var i = 0; i < result.daily.length; i++) {
						tgl.push(result.daily[i].tgl);

						ng.push(result.daily[i].ng);
						g.push(result.daily[i].g);
						ng[i] = ng[i] || 0;
						g[i] = g[i] || 0;
						ng_rate[i] = (ng[i] / (ng[i]+g[i])) * 100;
						ng_rate[i] = ng_rate[i] || 0;
						ng_rate_daily.push(ng_rate[i]);
					}
					var bulan = result.bulan;


					Highcharts.chart('chart3', {
						chart: {
							type: 'line'
						},
						title: {
							text: '<span style="font-size: 18pt;">Daily %IC Sax Key LCQ in '+bulanText(bulan)+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: tgl
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							max: 100,
							min: 0
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">NG Rate </span>: <b>{point.y:.2f}%</b> <br/>',

						},
						plotOptions: {
							line: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2);
									}
								}
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							data: ng_rate_daily
						}
						]
					});
				}
			}
		});

	}

</script>


@stop