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
					<div class="col-xs-2" style="color:black;">
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
			</div>
			
			<div class="col-xs-12" style="padding: 0px">
				<div class="col-xs-12" style="padding: 0px">
					<div class="nav-tabs-custom" id="tab_1">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly_ic" class="table table-bordered" style="margin:0">
												<thead id="head_monthly_ic">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">NG Rate</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
													</tr>
												</thead>
												<tbody id="body_monthly_ic">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_ic_1" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
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
											<div id="chart_ic_2" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_3_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_3_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_4_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_4_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div id="chart_ic_5" style="width: 99%;"></div>
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly_kensa" class="table table-bordered" style="margin:0">
												<thead id="head_monthly_kensa">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">NG Rate</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
													</tr>
												</thead>
												<tbody id="body_monthly_kensa">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_kensa_1" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_2_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_2_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_3_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_3_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div id="chart_kensa_4" style="width: 99%;"></div>
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
		setInterval(drawChart, 60000);
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
					$('#body_monthly_ic').append().empty();
					var fy = result.fy;
					var tgl = [];
					var target = [];
					var ng = [];
					var total = [];		
					var ng_rate = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly_ic.length; i++) {
						tgl.push(result.monthly_ic[i].tgl);
						target.push(8.37);
						ng.push(result.monthly_ic[i].ng);
						total.push(result.monthly_ic[i].total);
						total[i] = total[i] || 0;

						ng_rate[i] = (ng[i] / total[i]) * 100;
						ng_rate[i] = ng_rate[i] || 0;
						ng_rate_monthly.push(ng_rate[i]);
					}

					var body = "";
					for (var i = 0; i < result.monthly_ic.length; i++) {
						body += "<tr>";
						body += "<td>"+tgl[i]+"</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng[i]+"</td>";
						body += "<td>"+total[i]+"</td>";
						body += "</tr>";
					}
					$('#body_monthly_ic').append(body);

					Highcharts.chart('chart_ic_1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate IC Sax Key on '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: tgl
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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
										return Highcharts.numberFormat(this.y,2)+'%';
									}
								}
							},
							line: {
								marker: {
									enabled: false
								},
								dashStyle: 'ShortDash'
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

					$('#body_monthly_kensa').append().empty();
					var tgl = [];
					var target = [];
					var ng = [];
					var total = [];		
					var ng_rate = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly_kensa.length; i++) {
						tgl.push(result.monthly_kensa[i].tgl);
						target.push(4.45);
						ng.push(result.monthly_kensa[i].ng);
						total.push(result.monthly_kensa[i].total);
						total[i] = total[i] || 0;

						ng_rate[i] = (ng[i] / total[i]) * 100;
						ng_rate[i] = ng_rate[i] || 0;
						ng_rate_monthly.push(ng_rate[i]);
					}

					var body = "";
					for (var i = 0; i < result.monthly_kensa.length; i++) {
						body += "<tr>";
						body += "<td>"+tgl[i]+"</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng[i]+"</td>";
						body += "<td>"+total[i]+"</td>";
						body += "</tr>";
					}
					$('#body_monthly_kensa').append(body);

					Highcharts.chart('chart_kensa_1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate Kensa Sax Key on '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: tgl
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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
										return Highcharts.numberFormat(this.y,2)+'%';
									}
								}
							},
							line: {
								marker: {
									enabled: false
								},
								dashStyle: 'ShortDash'
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
			$('#body_weekly').append().empty();
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
				perolehan[i] = g[i];
				ng_rate[i] = (ng[i] / perolehan[i]) * 100;
				// ng_rate[i] = ng_rate[i] || 0;
				ng_rate_weekly.push(ng_rate[i]);
			}

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

			Highcharts.chart('chart_ic_2', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Weekly NG Rate IC Sax Key on '+bulanText(bulan)+'</span>',
					useHTML: true
				},
				xAxis: {
					categories: week_name
				},
				yAxis: {
					title: {
						text: 'NG Rate (%)'
					},
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
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						}
					},
					series: {
						connectNulls: true
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
			var bulan = result.bulan;
			var ng_rate_alto = [];			
			var ng = [];
			var jml = [];
			var color = [];
			var series = [];

			for (var i = 0; i < result.ngIC_alto.length; i++) {
				if(result.ngIC_alto[i].ng_name == 'Kizu Beret, Scrath, Butsu'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#2b908f');
				}else if(result.ngIC_alto[i].ng_name == 'Aus, Nami, Buff Torinai, Buff tdk rata'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#90ee7e');
				}else if(result.ngIC_alto[i].ng_name == 'Kizu'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#f45b5b');
				}else if(result.ngIC_alto[i].ng_name == 'Sisa Lusterlime'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#7798BF');
				}else if(result.ngIC_alto[i].ng_name == 'Toke, Rohtare, gosong, Handatsuki'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#aaeeee');
				}else if(result.ngIC_alto[i].ng_name == 'Pesok,Kake,Bengkok'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#ff0066');
				}else if(result.ngIC_alto[i].ng_name == 'Lain-lain (Hakuri nokoru,material salah,bekas)'){
					ng.push(result.ngIC_alto[i].ng_name);
					jml.push([result.ngIC_alto[i].jml]);
					ng_rate_alto.push((jml[i]/result.totalCekIC_alto[0].total)*100);
					color.push('#eeaaee');
				}

				series.push({name : ng[i], data: [ng_rate_alto[i]], color: color[i]});
			}

			Highcharts.chart('chart_ic_3_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">Highest NG IC Alto Sax Key on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					reversed: true,
					labels: {
						enabled: false
					},
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'NG Rate (%)'
					}
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					headerFormat: '<span>NG Name</span><br/>',
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
				},
				plotOptions: {
					series:{
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							},
							style:{
								textOutline: false,
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: series
			});




			var bulan = result.bulan;
			var ng_rate_tenor = [];			
			var ng = [];
			var jml = [];
			var color = [];
			var series = [];

			for (var i = 0; i < result.ngIC_tenor.length; i++) {
				if(result.ngIC_tenor[i].ng_name == 'Kizu Beret, Scrath, Butsu'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#2b908f');
				}else if(result.ngIC_tenor[i].ng_name == 'Aus, Nami, Buff Torinai, Buff tdk rata'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#90ee7e');
				}else if(result.ngIC_tenor[i].ng_name == 'Kizu'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#f45b5b');
				}else if(result.ngIC_tenor[i].ng_name == 'Sisa Lusterlime'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#7798BF');
				}else if(result.ngIC_tenor[i].ng_name == 'Toke, Rohtare, gosong, Handatsuki'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#aaeeee');
				}else if(result.ngIC_tenor[i].ng_name == 'Pesok,Kake,Bengkok'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#ff0066');
				}else if(result.ngIC_tenor[i].ng_name == 'Lain-lain (Hakuri nokoru,material salah,bekas)'){
					ng.push(result.ngIC_tenor[i].ng_name);
					jml.push([result.ngIC_tenor[i].jml]);
					ng_rate_tenor.push((jml[i]/result.totalCekIC_tenor[0].total)*100);
					color.push('#eeaaee');
				}

				series.push({name : ng[i], data: [ng_rate_tenor[i]], color: color[i]});
			}

			Highcharts.chart('chart_ic_3_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">Highest NG IC Tenor Sax Key on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					reversed: true,
					labels: {
						enabled: false
					},
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'NG Rate (%)'
					}
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					headerFormat: '<span>NG Name</span><br/>',
					pointFormat: '<span style="color:{point.color}">{point.category}</span>: <b>{point.y:.2f}%</b> <br/>'
				},
				plotOptions: {
					series:{
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							},
							style:{
								textOutline: false,
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: series
			});

			


			var key = [];
			var kizu_beret = [];
			var aus = [];
			var kizu = [];
			var sisa = [];
			var toke = [];
			var pesok = [];
			var lain = [];



			for (var i = 0; i < result.ngICKey_alto.length; i++) {
				key.push(result.ngICKey_alto[i].key);
				for (var j = 0; j < result.ngICKey_alto_detail.length; j++) {
					if(result.ngICKey_alto[i].key == result.ngICKey_alto_detail[j].key){
						if(result.ngICKey_alto_detail[j].ng_name == 'Kizu Beret, Scrath, Butsu'){
							kizu_beret.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Aus, Nami, Buff Torinai, Buff tdk rata'){
							aus.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Kizu'){
							kizu.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Sisa Lusterlime'){
							sisa.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Toke, Rohtare, gosong, Handatsuki'){
							toke.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Pesok,Kake,Bengkok'){
							pesok.push(result.ngICKey_alto_detail[j].jml);
						}else if(result.ngICKey_alto_detail[j].ng_name == 'Lain-lain (Hakuri nokoru,material salah,bekas)'){
							lain.push(result.ngICKey_alto_detail[j].jml);
						}
					}
				}
			}

			Highcharts.chart('chart_ic_4_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">10 Highest Keys NG IC Alto Sax on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: key
				},
				yAxis: {
					title: {
						text: 'Total Not Good'
					},
					stackLabels: {
						enabled: true,
						style: {
							color: 'black',
						}
					},
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name}</span> : <b>{point.y}</b> <br/>',
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					},
					series: {
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Kizu Beret, Scrath, Butsu',
					data: kizu_beret,
					color: '#2b908f'
				},
				{
					name: 'Aus, Nami, Buff Torinai, Buff tdk rata',
					data: aus,
					color: '#90ee7e'
				},
				{
					name: 'Kizu',
					data: kizu,
					color: '#f45b5b'
				},
				{
					name: 'Sisa Lusterlime',
					data: sisa,
					color: '#7798BF'
				},
				{
					name: 'Toke, Rohtare, gosong, Handatsuki',
					data: toke,
					color: '#aaeeee'
				},
				{
					name: 'Pesok,Kake,Bengkok',
					data: pesok,
					color: '#ff0066'
				},
				{
					name: 'Lain-lain (Hakuri nokoru,material salah,bekas)',
					data: lain,
					color: '#eeaaee'
				}
				]
			});



			var key = [];
			var kizu_beret = [];
			var aus = [];
			var kizu = [];
			var sisa = [];
			var toke = [];
			var pesok = [];
			var lain = [];

			for (var i = 0; i < result.ngICKey_tenor.length; i++) {
				key.push(result.ngICKey_tenor[i].key);
				for (var j = 0; j < result.ngICKey_tenor_detail.length; j++) {
					if(result.ngICKey_tenor[i].key == result.ngICKey_tenor_detail[j].key){
						if(result.ngICKey_tenor_detail[j].ng_name == 'Kizu Beret, Scrath, Butsu'){
							kizu_beret.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Aus, Nami, Buff Torinai, Buff tdk rata'){
							aus.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Kizu'){
							kizu.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Sisa Lusterlime'){
							sisa.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Toke, Rohtare, gosong, Handatsuki'){
							toke.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Pesok,Kake,Bengkok'){
							pesok.push(result.ngICKey_tenor_detail[j].jml);
						}else if(result.ngICKey_tenor_detail[j].ng_name == 'Lain-lain (Hakuri nokoru,material salah,bekas)'){
							lain.push(result.ngICKey_tenor_detail[j].jml);
						}
					}
				}
			}

			Highcharts.chart('chart_ic_4_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">10 Highest Keys NG IC Tenor Sax on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: key
				},
				yAxis: {
					title: {
						text: 'Total Not Good'
					},
					stackLabels: {
						enabled: true,
						style: {
							color: 'black',
						}
					},
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name}</span> : <b>{point.y}</b> <br/>',
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					},
					series: {
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Kizu Beret, Scrath, Butsu',
					data: kizu_beret,
					color: '#2b908f'
				},
				{
					name: 'Aus, Nami, Buff Torinai, Buff tdk rata',
					data: aus,
					color: '#90ee7e'
				},
				{
					name: 'Kizu',
					data: kizu,
					color: '#f45b5b'
				},
				{
					name: 'Sisa Lusterlime',
					data: sisa,
					color: '#7798BF'
				},
				{
					name: 'Toke, Rohtare, gosong, Handatsuki',
					data: toke,
					color: '#aaeeee'
				},
				{
					name: 'Pesok,Kake,Bengkok',
					data: pesok,
					color: '#ff0066'
				},
				{
					name: 'Lain-lain (Hakuri nokoru,material salah,bekas)',
					data: lain,
					color: '#eeaaee'
				}
				]
			});

			var ng_name_alto = [];
			var jml_alto = [];			
			var ng_rate_alto = [];			
			for (var i = 0; i < result.ngKensa_alto.length; i++) {
				ng_name_alto.push(result.ngKensa_alto[i].ng_name);
				jml_alto.push(parseInt(result.ngKensa_alto[i].jml));
				ng_rate_alto.push((jml_alto[i]/result.totalCekKensa_alto[0].total)*100);
			}
			var bulan = result.bulan;

			Highcharts.chart('chart_kensa_2_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<center><span style="font-size: 18pt;">10 Highest NG Kensa Alto Sax Key on '+bulanText(bulan)+'</center></span>',
					useHTML: true
				},
				xAxis: {
					categories: ng_name_alto,
					labels: {
						// padding: 50,
						useHTML: true,
						style: {
							maxWidth: '90px',
							textOverflow: 'ellipsis',
						},
					}
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'NG Rate (%)'
					}
				},
				legend : {
					enabled: false
				},
				tooltip: {
					headerFormat: '<span>Alto Key Not Good</span><br/>',
					pointFormat: '<span style="color:{point.color}">{point.category}</span>: <b>{point.y:.2f}%</b> <br/>'
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						}
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					"colorByPoint": true,
					name: 'NG',
					data: ng_rate_alto,
				}
				]
			});

			var ng_name_tenor = [];
			var jml_tenor = [];			
			var ng_rate_tenor = [];			
			for (var i = 0; i < result.ngKensa_tenor.length; i++) {
				ng_name_tenor.push(result.ngKensa_tenor[i].ng_name);
				jml_tenor.push(parseInt(result.ngKensa_tenor[i].jml));
				ng_rate_tenor.push((jml_tenor[i]/result.totalCekKensa_tenor[0].total)*100);
			}
			var bulan = result.bulan;

			Highcharts.chart('chart_kensa_2_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<center><span style="font-size: 18pt;">10 Highest NG Kensa Tenor Sax Key on '+bulanText(bulan)+'</center></span>',
					useHTML: true
				},
				xAxis: {
					categories: ng_name_tenor,
					labels: {
						// padding: 30,
						useHTML: true,
						style: {
							maxWidth: '90px',
							textOverflow: 'ellipsis',
						},
					}
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'NG Rate (%)'
					}
				},
				legend : {
					enabled: false
				},
				tooltip: {
					headerFormat: '<span>Tenor Key Not Good</span><br/>',
					pointFormat: '<span style="color:{point.color}">{point.category}</span>: <b>{point.y:.2f}%</b> <br/>'
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						}
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					"colorByPoint": true,
					name: 'NG',
					data: ng_rate_tenor,
				}
				]
			});





			var key = [];
			var	kizu = [];
			var	hokori = [];
			var	kizu_after = [];
			var	scrath = [];
			var	buff_tarinai = [];
			var	toso_usui = [];
			var	tare = [];
			var	yogore = [];
			var	black_shimi = [];
			var	buff_tidak_rata = [];
			var lain = [];	

			for (var i = 0; i < result.ngKensaKey_alto.length; i++) {
				key.push(result.ngKensaKey_alto[i].key);
				for (var j = 0; j < result.ngKensaKey_alto_detail.length; j++) {
					if(result.ngKensaKey_alto[i].key == result.ngKensaKey_alto_detail[j].key){
						if(result.ngKensaKey_alto_detail[j].ng_name == 'Kizu before'){
							kizu.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Hokori benang'){
							hokori.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Kizu after'){
							kizu_after.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Scrath'){
							scrath.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Buff tarinai'){
							buff_tarinai.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Toso usui'){
							toso_usui.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Tare'){
							tare.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Yogore'){
							yogore.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Black shimi'){
							black_shimi.push(result.ngKensaKey_alto_detail[j].jml);
						}else if(result.ngKensaKey_alto_detail[j].ng_name == 'Buff tidak rata'){
							buff_tidak_rata.push(result.ngKensaKey_alto_detail[j].jml);
						}else{
							if(typeof lain[i] == 'undefined'){
								lain.push(result.ngKensaKey_alto_detail[j].jml);
							}else{
								lain[i] += result.ngKensaKey_alto_detail[j].jml;
							}

						}
					}
				}
			}

			Highcharts.chart('chart_kensa_3_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">10 Highest Keys NG Kensa Alto Sax on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: key
				},
				yAxis: {
					title: {
						text: 'Total Not Good'
					},
					stackLabels: {
						enabled: true,
						style: {
							color: 'black',
						}
					},
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name}</span> : <b>{point.y}</b> <br/>',
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					},
					series: {
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Kizu Before',
					data: kizu,
					color: '#2b908f'
				},
				{
					name: 'Hokori Benang',
					data: hokori,
					color: '#90ee7e'
				},
				{
					name: 'Kizu After',
					data: kizu_after,
					color: '#f45b5b'
				},
				{
					name: 'Scrath',
					data: scrath,
					color: '#7798BF'
				},
				{
					name: 'Buff Taranai',
					data: buff_tarinai,
					color: '#aaeeee'
				},
				{
					name: 'Toso Usui',
					data: toso_usui,
					color: '#ff0066'
				},
				{
					name: 'Tare',
					data: tare,
					color: '#FF8F00'
				},
				{
					name: 'Yogore',
					data: yogore,
					color: '#9C27B0'
				},
				{
					name: 'Black Shimi',
					data: black_shimi,
					color: '#212121'
				},
				{
					name: 'Buff Tidak Rata',
					data: buff_tidak_rata,
					color: '#FFEB3B'
				},
				{
					name: 'Lain-lain',
					data: lain,
					color: '#BCAAA4'
				}
				]
			});


			var key = [];
			var	kizu = [];
			var	hokori = [];
			var	kizu_after = [];
			var	scrath = [];
			var	buff_tarinai = [];
			var	toso_usui = [];
			var	tare = [];
			var	yogore = [];
			var	black_shimi = [];
			var	buff_tidak_rata = [];
			var lain = [];	

			for (var i = 0; i < result.ngKensaKey_tenor.length; i++) {
				key.push(result.ngKensaKey_tenor[i].key);
				for (var j = 0; j < result.ngKensaKey_tenor_detail.length; j++) {
					if(result.ngKensaKey_tenor[i].key == result.ngKensaKey_tenor_detail[j].key){
						if(result.ngKensaKey_tenor_detail[j].ng_name == 'Kizu before'){
							kizu.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Hokori benang'){
							hokori.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Kizu after'){
							kizu_after.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Scrath'){
							scrath.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Buff tarinai'){
							buff_tarinai.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Toso usui'){
							toso_usui.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Tare'){
							tare.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Yogore'){
							yogore.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Black shimi'){
							black_shimi.push(result.ngKensaKey_tenor_detail[j].jml);
						}else if(result.ngKensaKey_tenor_detail[j].ng_name == 'Buff tidak rata'){
							buff_tidak_rata.push(result.ngKensaKey_tenor_detail[j].jml);
						}else{
							if(typeof lain[i] == 'undefined'){
								lain.push(result.ngKensaKey_tenor_detail[j].jml);
							}else{
								lain[i] += result.ngKensaKey_tenor_detail[j].jml;
							}

						}
					}
				}
			}

			Highcharts.chart('chart_kensa_3_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">10 Highest Keys NG Kensa Tenor Sax on '+bulanText(bulan)+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: key
				},
				yAxis: {
					title: {
						text: 'Total Not Good'
					},
					stackLabels: {
						enabled: true,
						style: {
							color: 'black',
						}
					},
				},
				legend: {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name}</span> : <b>{point.y}</b> <br/>',
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					},
					series: {
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Kizu Before',
					data: kizu,
					color: '#2b908f'
				},
				{
					name: 'Hokori Benang',
					data: hokori,
					color: '#90ee7e'
				},
				{
					name: 'Kizu After',
					data: kizu_after,
					color: '#f45b5b'
				},
				{
					name: 'Scrath',
					data: scrath,
					color: '#7798BF'
				},
				{
					name: 'Buff Taranai',
					data: buff_tarinai,
					color: '#aaeeee'
				},
				{
					name: 'Toso Usui',
					data: toso_usui,
					color: '#ff0066'
				},
				{
					name: 'Tare',
					data: tare,
					color: '#FF8F00'
				},
				{
					name: 'Yogore',
					data: yogore,
					color: '#9C27B0'
				},
				{
					name: 'Black Shimi',
					data: black_shimi,
					color: '#212121'
				},
				{
					name: 'Buff Tidak Rata',
					data: buff_tidak_rata,
					color: '#FFEB3B'
				},
				{
					name: 'Lain-lain',
					data: lain,
					color: '#BCAAA4'
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
			var ng_alto = [];
			var total_alto = [];
			var ng_rate_alto = [];
			var push = 0;
			for (var i = 0; i < result.dailyICAlto.length; i++) {
				tgl.push(result.dailyICAlto[i].tgl);

				ng_alto.push(result.dailyICAlto[i].ng);
				total_alto.push(result.dailyICAlto[i].total);
				push = (ng_alto[i] / total_alto[i]) * 100;
				if(push == 0){
					push = NaN;
				}
				ng_rate_alto.push(push);
			}

			var tgl = [];
			var ng_tenor = [];
			var total_tenor = [];
			var ng_rate_tenor = [];
			var push = [];
			for (var i = 0; i < result.dailyICTenor.length; i++) {
				tgl.push(result.dailyICTenor[i].tgl);

				ng_tenor.push(result.dailyICTenor[i].ng);
				total_tenor.push(result.dailyICTenor[i].total);
				push = (ng_tenor[i] / total_tenor[i]) * 100;
				if(push == 0){
					push = NaN;
				}
				ng_rate_tenor.push(push);
			}
			var bulan = result.bulan;

			Highcharts.chart('chart_ic_5', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Daily NG Rate IC Sax Key on '+bulanText(bulan)+'</span>',
					useHTML: true
				},
				xAxis: {
					categories: tgl
				},
				yAxis: {
					title: {
						text: 'NG Rate (%)'
					},
					min: 0
				},
				legend : {
					enabled: true
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
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						}
					},
					series: {
						connectNulls: true
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Alto',
					data: ng_rate_alto
				},
				{
					name: 'Tenor',
					data: ng_rate_tenor
				}
				]
			});

			var tgl = [];
			var ng_alto = [];
			var total_alto = [];
			var ng_rate_alto = [];
			var push = 0;
			for (var i = 0; i < result.dailyKensaAlto.length; i++) {
				tgl.push(result.dailyKensaAlto[i].tgl);

				ng_alto.push(result.dailyKensaAlto[i].ng);
				total_alto.push(result.dailyKensaAlto[i].total);
				push = (ng_alto[i] / total_alto[i]) * 100;
				if(push == 0){
					push = NaN;
				}

				ng_rate_alto.push(push);
			}

			var tgl = [];
			var ng_tenor = [];
			var total_tenor = [];
			var ng_rate_tenor = [];

			for (var i = 0; i < result.dailyKensaTenor.length; i++) {
				tgl.push(result.dailyKensaTenor[i].tgl);

				ng_tenor.push(result.dailyKensaTenor[i].ng);
				total_tenor.push(result.dailyKensaTenor[i].total);
				push = (ng_tenor[i] / total_tenor[i]) * 100;
				if(push == 0){
					push = NaN;
				}
				ng_rate_tenor.push(push);
			}
			var bulan = result.bulan;


			Highcharts.chart('chart_kensa_4', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Daily NG Rate Kensa Sax Key on '+bulanText(bulan)+'</span>',
					useHTML: true
				},
				xAxis: {
					categories: tgl
				},
				yAxis: {
					title: {
						text: 'NG Rate (%)'
					},
					min: 0
				},
				legend : {
					enabled: true
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
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						}
					},
					series: {
						connectNulls: true
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Alto',
					data: ng_rate_alto
				},
				{
					name: 'Tenor',
					data: ng_rate_tenor
				}
				]
			});
		}
	}
});


}

</script>


@stop