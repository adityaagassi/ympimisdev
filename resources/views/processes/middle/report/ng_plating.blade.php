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
				<form method="GET" action="{{ url('index/middle/report_plt_ng/'.$id) }}">
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
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">NG Rate</th>
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
											<table id="table_ic_weekly" class="table table-bordered" style="margin:0">
												<thead id="head_ic_weekly">
													<tr style="background-color: rgba(126,86,134,.7);">
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">%NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_ic_weekly">
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





					<div class="nav-tabs-custom" id="tab_1">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly_kensa" class="table table-bordered" style="margin:0">
												<thead id="head_monthly_kensa">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">NG Rate</th>
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
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_kensa_weekly" class="table table-bordered" style="margin:0">
												<thead id="head_kensa_weekly">
													<tr style="background-color: rgba(126,86,134,.7);">
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">%NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_kensa_weekly">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_kensa_2" style="width: 99%;"></div>			
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
					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_4_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_4_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div id="chart_kensa_5" style="width: 99%;"></div>
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
		setInterval(drawChart, 60*60*1000);

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

		$.get('{{ url("fetch/middle/plt_ng_rate_monthly/".$id) }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#body_monthly_ic').append().empty();
					var fy = result.fy;
					
					var month =  [];
					var target = [];
					var ng = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly_ic.length; i++) {
						target.push(8.37);						
						month.push(result.monthly_ic[i].tgl);
						ng.push(result.monthly_ic[i].ng_rate);
						ng[i] = ng[i] || 0;
						ng_rate_monthly.push(ng[i] * 100);
					}

					var body = "";
					for (var i = 0; i < result.monthly_ic.length; i++) {
						body += "<tr>";
						body += "<td>"+month[i]+"</td>";
						body += "<td>"+result.monthly_ic[i].ng+"</td>";
						body += "<td>"+result.monthly_ic[i].g+"</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_monthly_ic').append(body);

					Highcharts.chart('chart_ic_1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate I.C. Plating Sax Key on '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: month
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
					var month =  [];
					var target = [];
					var ng = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly_kensa.length; i++) {
						target.push(4.45);
						month.push(result.monthly_kensa[i].tgl);
						ng.push(result.monthly_kensa[i].ng_rate);
						ng[i] = ng[i] || 0;
						ng_rate_monthly.push(ng[i] * 100);
					}

					var body = "";
					for (var i = 0; i < result.monthly_kensa.length; i++) {
						body += "<tr>";
						body += "<td>"+month[i]+"</td>";
						body += "<td>"+result.monthly_kensa[i].ng+"</td>";
						body += "<td>"+result.monthly_kensa[i].g+"</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_monthly_kensa').append(body);

					Highcharts.chart('chart_kensa_1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate Kensa Plating Sax Key on '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: month
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


$.get('{{ url("fetch/middle/plt_ng_rate_weekly/".$id) }}', data, function(result, status, xhr) {
	if(xhr.status == 200){
		if(result.status){
			$('#body_ic_weekly').append().empty();
			var week_name = [];
			var ng = [];
			var g= [];					
			var ng_rate_weekly = [];
			var body = "";

			for (var i = 0; i < result.weekly_ic.length; i++) {
				week_name.push(result.weekly_ic[i].week_name);
				ng_rate_weekly.push((result.weekly_ic[i].ng / result.weekly_ic[i].g) * 100);

				body += "<tr>";
				body += "<td>"+result.weekly_ic[i].week_name+"</td>";
				body += "<td>"+result.weekly_ic[i].g+"</td>";
				body += "<td>"+result.weekly_ic[i].ng+"</td>";
				body += "<td>"+(((result.weekly_ic[i].ng / result.weekly_ic[i].g) * 100) || 0).toFixed(2)+"%</td>";
				body += "</tr>";
			}
			$('#body_ic_weekly').append(body);	

			Highcharts.chart('chart_ic_2', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Weekly NG Rate I.C. Plating Sax Key on '+result.bulanText+'</span>',
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




			$('#body_kensa_weekly').append().empty();
			var week_name = [];
			var ng = [];
			var g= [];					
			var ng_rate_weekly = [];
			var body = "";

			for (var i = 0; i < result.weekly_kensa.length; i++) {
				week_name.push(result.weekly_kensa[i].week_name);
				ng_rate_weekly.push((result.weekly_kensa[i].ng / result.weekly_kensa[i].g) * 100);

				body += "<tr>";
				body += "<td>"+result.weekly_kensa[i].week_name+"</td>";
				body += "<td>"+result.weekly_kensa[i].g+"</td>";
				body += "<td>"+result.weekly_kensa[i].ng+"</td>";
				body += "<td>"+(((result.weekly_kensa[i].ng / result.weekly_kensa[i].g) * 100) || 0).toFixed(2)+"%</td>";
				body += "</tr>";
			}
			$('#body_kensa_weekly').append(body);	

			Highcharts.chart('chart_kensa_2', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Weekly NG Rate Kensa Plating Sax Key on '+result.bulanText+'</span>',
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


$.get('{{ url("fetch/middle/plt_ng/".$id) }}', data, function(result, status, xhr) {
	if(xhr.status == 200){
		if(result.status){

			//IC Alto
			var ng_rate_alto = [];			
			var ng = [];
			var jml = [];
			var color = [];
			var series = [];

			for (var i = 0; i < result.ic_ng_alto.length; i++) {
				if(result.ic_ng_alto[i].ng_name == 'Kizu'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#2b908f');
				}else if(result.ic_ng_alto[i].ng_name == 'Nami'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#90ee7e');
				}else if(result.ic_ng_alto[i].ng_name == 'Deko'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#f45b5b');
				}else if(result.ic_ng_alto[i].ng_name == 'Aus'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#7798BF');
				}else if(result.ic_ng_alto[i].ng_name == 'Buff kurang'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#aaeeee');
				}else if(result.ic_ng_alto[i].ng_name == 'Buff nagare'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#ff0066');
				}else if(result.ic_ng_alto[i].ng_name == 'Rho tsuki'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#eeaaee');
				}else if(result.ic_ng_alto[i].ng_name == 'Rho tare'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#FFEB3B');
				}else if(result.ic_ng_alto[i].ng_name == 'Lain-lain'){
					ng.push(result.ic_ng_alto[i].ng_name);
					ng_rate_alto.push(result.ic_ng_alto[i].ng/result.ic_ng_alto[i].check*100);
					color.push('#9C27B0');
				}

				series.push({name : ng[i], data: [ng_rate_alto[i]], color: color[i]});
			}

			Highcharts.chart('chart_ic_3_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">Highest NG I.C. Plating Alto Sax Key<br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
								return Highcharts.numberFormat(this.y,3)+'%';
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



			//IC Tenor
			var ng_rate_tenor = [];			
			var ng = [];
			var jml = [];
			var color = [];
			var series = [];

			for (var i = 0; i < result.ic_ng_tenor.length; i++) {
				if(result.ic_ng_tenor[i].ng_name == 'Kizu'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#2b908f');
				}else if(result.ic_ng_tenor[i].ng_name == 'Nami'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#90ee7e');
				}else if(result.ic_ng_tenor[i].ng_name == 'Deko'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#f45b5b');
				}else if(result.ic_ng_tenor[i].ng_name == 'Aus'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#7798BF');
				}else if(result.ic_ng_tenor[i].ng_name == 'Buff kurang'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#aaeeee');
				}else if(result.ic_ng_tenor[i].ng_name == 'Buff nagare'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#ff0066');
				}else if(result.ic_ng_tenor[i].ng_name == 'Rho tsuki'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#eeaaee');
				}else if(result.ic_ng_tenor[i].ng_name == 'Rho tare'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#FFEB3B');
				}else if(result.ic_ng_tenor[i].ng_name == 'Lain-lain'){
					ng.push(result.ic_ng_tenor[i].ng_name);
					ng_rate_tenor.push(result.ic_ng_tenor[i].ng/result.ic_ng_tenor[i].check*100);
					color.push('#9C27B0');
				}

				series.push({name : ng[i], data: [ng_rate_tenor[i]], color: color[i]});

			}


			Highcharts.chart('chart_ic_3_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 18pt;">Highest NG I.C. Plating Tenor Sax Key<br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
								return Highcharts.numberFormat(this.y,3)+'%';
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


			//Kensa Alto
			var ng_name;
			var ng_rate;
			var color;
			var series = [];

			var other_ng = 0;
			var other_check = 0;

			for (var i = 0; i < result.kensa_ng_alto.length; i++) {
				if(result.kensa_ng_alto[i].ng_name == 'Kizu before'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#2b908f';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Kizu after'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#9c4dcc';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Yogore'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#90ee7e';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Buff tarinai'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#f45b5b';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Nami'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#7798BF';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Mekki nai'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#aaeeee';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Kumori'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#ff0066';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Hakuri nokoru'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#FF8F00';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Mekki warui'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#cfd8dc';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Kake'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#a1887f';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Handa oi'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#212121';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_alto[i].ng_name == 'Yogore plt'){
					ng_name = result.kensa_ng_alto[i].ng_name;
					ng_rate = [result.kensa_ng_alto[i].ng];
					color = '#FFEB3B';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else{
					other_ng += result.kensa_ng_alto[i].ng;
					other_check = result.kensa_ng_alto[i].check;

				}
			}

			// series.push({name : 'Others', data: [other_ng/other_check], color: '#455dff'});
			series.push({name : 'Others', data: [other_ng], color: '#455dff'});
			series.sort(function(a, b){return b.data[0] - a.data[0]});

			Highcharts.chart('chart_kensa_3_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<center><span style="font-size: 16pt;">Highest NG Kensa Plating Alto Sax Key</center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: ng_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					reversed: true,
					labels: {
						enabled: false
					},
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'Total Not Good'
					}
				},
				legend : {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					headerFormat: '<span>Alto Key Not Good</span><br/>',
					pointFormat: '<span style="color:{point.color}">{series.name}</span>: <b>{point.y}</b> <br/>'
				},
				plotOptions: {
					series: {
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer',
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}
				},credits: {
					enabled: false
				},
				series: series
			});



			//Kensa Tenor

			var ng_name;
			var ng_rate;
			var color;
			var series = [];

			var other_ng = 0;
			var other_check = 0;

			for (var i = 0; i < result.kensa_ng_tenor.length; i++) {
				if(result.kensa_ng_tenor[i].ng_name == 'Kizu before'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#2b908f';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Kizu after'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#9c4dcc';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Yogore'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#90ee7e';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Buff tarinai'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#f45b5b';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Nami'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#7798BF';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Mekki nai'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#aaeeee';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Kumori'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#ff0066';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Hakuri nokoru'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#FF8F00';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Mekki warui'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#cfd8dc';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Kake'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#a1887f';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Handa oi'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#212121';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else if(result.kensa_ng_tenor[i].ng_name == 'Yogore plt'){
					ng_name = result.kensa_ng_tenor[i].ng_name;
					ng_rate = [result.kensa_ng_tenor[i].ng];
					color = '#FFEB3B';
					series.push({name : ng_name, data: ng_rate, color: color});

				}else{
					other_ng += result.kensa_ng_tenor[i].ng;
					other_check = result.kensa_ng_tenor[i].check;

				}
			}

			series.push({name : 'Others', data: [other_ng], color: '#455dff'});
			series.sort(function(a, b){return b.data[0] - a.data[0]});



			Highcharts.chart('chart_kensa_3_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<center><span style="font-size: 16pt;">Highest NG Kensa Plating Tenor Sax Key</center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				xAxis: {
					categories: ng_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					reversed: true,
					labels: {
						enabled: false
					},
				},
				yAxis: {
					type: 'logarithmic',
					title: {
						text: 'Total Not Good'
					}
				},
				legend : {
					enabled: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
					shadow: true
				},
				tooltip: {
					headerFormat: '<span>Tenor Key Not Good</span><br/>',
					pointFormat: '<span style="color:{point.color}">{series.name}</span>: <b>{point.y}</b> <br/>'
				},
				plotOptions: {
					series: {
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer',
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}
				},credits: {
					enabled: false
				},
				series: series
			});



		//IC Alto Key
		var key = [];

		var kizu = [0,0,0,0,0,0,0,0,0,0];
		var nami = [0,0,0,0,0,0,0,0,0,0];
		var deko = [0,0,0,0,0,0,0,0,0,0];
		var aus = [0,0,0,0,0,0,0,0,0,0];
		var buff_kurang = [0,0,0,0,0,0,0,0,0,0];
		var buff_nagare = [0,0,0,0,0,0,0,0,0,0];
		var rho_tsuki = [0,0,0,0,0,0,0,0,0,0];
		var rho_tare = [0,0,0,0,0,0,0,0,0,0];
		var lain = [0,0,0,0,0,0,0,0,0,0];
		

		for (var i = 0; i < result.ic_ng_key_alto.length; i++) {
			key.push(result.ic_ng_key_alto[i].key);

			for (var j = 0; j < result.ic_ng_key_alto_detail.length; j++) {
				if(result.ic_ng_key_alto[i].key == result.ic_ng_key_alto_detail[j].key){

					if(result.ic_ng_key_alto_detail[j].ng_name == 'Kizu'){
						kizu[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Nami'){
						nami[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Deko'){
						deko[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Aus'){
						aus[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Buff kurang'){
						buff_kurang[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Buff nagare'){
						buff_nagare[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Rho tsuki'){
						rho_tsuki[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Rho tare'){
						rho_tare[i] = result.ic_ng_key_alto_detail[j].ng;									
					}else if(result.ic_ng_key_alto_detail[j].ng_name == 'Lain-lain'){
						lain[i] = result.ic_ng_key_alto_detail[j].ng;									
					}
				}
			}
		}

		Highcharts.chart('chart_ic_4_alto', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<span style="font-size: 16pt;">10 Highest Keys NG I.C. Plating Alto Sax</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
				useHTML: true
			},
			subtitle: {
				text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
				name: 'Kizu',
				data: kizu,
				color: '#2b908f'
			},
			{
				name: 'Nami',
				data: nami,
				color: '#90ee7e'
			},
			{
				name: 'Deko',
				data: deko,
				color: '#f45b5b'
			},
			{
				name: 'Aus',
				data: aus,
				color: '#7798BF'
			},
			{
				name: 'Buff Kurang',
				data: buff_kurang,
				color: '#aaeeee'
			},
			{
				name: 'Buff Nagare',
				data: buff_nagare,
				color: '#ff0066'
			},
			{
				name: 'Rho Tsuki',
				data: rho_tsuki,
				color: '#eeaaee'
			},
			{
				name: 'Rho Tare',
				data: rho_tare,
				color: '#FFEB3B'
			},
			{
				name: 'Lain-lain',
				data: lain,
				color: '#9C27B0'
			}
			]
		});



		//IC Tenor Key
		var kizu = [0,0,0,0,0,0,0,0,0,0];
		var nami = [0,0,0,0,0,0,0,0,0,0];
		var deko = [0,0,0,0,0,0,0,0,0,0];
		var aus = [0,0,0,0,0,0,0,0,0,0];
		var buff_kurang = [0,0,0,0,0,0,0,0,0,0];
		var buff_nagare = [0,0,0,0,0,0,0,0,0,0];
		var rho_tsuki = [0,0,0,0,0,0,0,0,0,0];
		var rho_tare = [0,0,0,0,0,0,0,0,0,0];
		var lain = [0,0,0,0,0,0,0,0,0,0];
		

		for (var i = 0; i < result.ic_ng_key_tenor.length; i++) {
			key.push(result.ic_ng_key_tenor[i].key);

			for (var j = 0; j < result.ic_ng_key_tenor_detail.length; j++) {
				if(result.ic_ng_key_tenor[i].key == result.ic_ng_key_tenor_detail[j].key){

					if(result.ic_ng_key_tenor_detail[j].ng_name == 'Kizu'){
						kizu[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Nami'){
						nami[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Deko'){
						deko[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Aus'){
						aus[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Buff kurang'){
						buff_kurang[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Buff nagare'){
						buff_nagare[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Rho tsuki'){
						rho_tsuki[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Rho tare'){
						rho_tare[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}else if(result.ic_ng_key_tenor_detail[j].ng_name == 'Lain-lain'){
						lain[i] = result.ic_ng_key_tenor_detail[j].ng;									
					}
				}
			}
		}

		Highcharts.chart('chart_ic_4_tenor', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<span style="font-size: 16pt;">10 Highest Keys NG I.C. Plating Tenor Sax</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
				useHTML: true
			},
			subtitle: {
				text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
				name: 'Kizu',
				data: kizu,
				color: '#2b908f'
			},
			{
				name: 'Nami',
				data: nami,
				color: '#90ee7e'
			},
			{
				name: 'Deko',
				data: deko,
				color: '#f45b5b'
			},
			{
				name: 'Aus',
				data: aus,
				color: '#7798BF'
			},
			{
				name: 'Buff Kurang',
				data: buff_kurang,
				color: '#aaeeee'
			},
			{
				name: 'Buff Nagare',
				data: buff_nagare,
				color: '#ff0066'
			},
			{
				name: 'Rho Tsuki',
				data: rho_tsuki,
				color: '#eeaaee'
			},
			{
				name: 'Rho Tare',
				data: rho_tare,
				color: '#FFEB3B'
			},
			{
				name: 'Lain-lain',
				data: lain,
				color: '#9C27B0'
			}
			]
		});



			//Kensa Alto Key
			var key = [];

			var kizu_before = [0,0,0,0,0,0,0,0,0,0];
			var kizu_after = [0,0,0,0,0,0,0,0,0,0];
			var yogore = [0,0,0,0,0,0,0,0,0,0];
			var buff_tarinai = [0,0,0,0,0,0,0,0,0,0];
			var nami = [0,0,0,0,0,0,0,0,0,0];
			var mekki_nai = [0,0,0,0,0,0,0,0,0,0];
			var kumori = [0,0,0,0,0,0,0,0,0,0];			
			var hakuri_nokoru = [0,0,0,0,0,0,0,0,0,0];
			var mekki_warui = [0,0,0,0,0,0,0,0,0,0];
			var kake = [0,0,0,0,0,0,0,0,0,0];
			var handa_oi = [0,0,0,0,0,0,0,0,0,0];
			var yogore_plt = [0,0,0,0,0,0,0,0,0,0];
			var other = [0,0,0,0,0,0,0,0,0,0];

			for (var i = 0; i < result.kensa_ng_key_alto.length; i++) {
				key.push(result.kensa_ng_key_alto[i].key);

				for (var j = 0; j < result.kensa_ng_key_alto_detail.length; j++) {
					if(result.kensa_ng_key_alto[i].key == result.kensa_ng_key_alto_detail[j].key){

						if(result.kensa_ng_key_alto_detail[j].ng_name == 'Kizu before'){
							kizu_before[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Kizu after'){
							kizu_after[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Yogore'){
							yogore[i] = result.kensa_ng_key_alto_detail[j].ng;									
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Buff tarinai'){
							buff_tarinai[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Nami'){
							nami[i] = result.kensa_ng_key_alto_detail[j].ng;									
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Mekki nai'){
							mekki_nai[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Kumori'){
							kumori[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Hakuri nokoru'){
							hakuri_nokoru[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Mekki warui'){
							mekki_warui[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Kake'){
							kake[i] = result.kensa_ng_key_alto_detail[j].ng;									
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Handa oi'){
							handa_oi[i] = result.kensa_ng_key_alto_detail[j].ng;									
						}else if(result.kensa_ng_key_alto_detail[j].ng_name == 'Yogore plt'){
							yogore_plt[i] = result.kensa_ng_key_alto_detail[j].ng;
						}else{
							if(typeof other[i] == 'undefined'){
								other.push(result.kensa_ng_key_alto_detail[j].ng);
							}else{
								other[i] += result.kensa_ng_key_alto_detail[j].ng;
							}
						}
					}
				}
			}

			Highcharts.chart('chart_kensa_4_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 16pt;">10 Highest Keys NG Kensa Plating Alto Sax</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
					data: kizu_before,
					color: '#2b908f'
				},
				{
					name: 'Kizu After',
					data: kizu_after,
					color: '#9c4dcc'
				},
				{
					name: 'Yogore',
					data: yogore,
					color: '#90ee7e'
				},				
				{
					name: 'Buff Taranai',
					data: buff_tarinai,
					color: '#f45b5b'
				},
				{
					name: 'Nami',
					data: nami,
					color: '#7798BF'
				},
				{
					name: 'Mekki Nai',
					data: mekki_nai,
					color: '#aaeeee'
				},
				{
					name: 'Kumori',
					data: kumori,
					color: '#ff0066'
				},
				{
					name: 'Hakuri Nokoru',
					data: hakuri_nokoru,
					color: '#FF8F00'
				},
				{
					name: 'Mekki Warui',
					data: mekki_warui,
					color: '#cfd8dc'
				},
				{
					name: 'Kake',
					data: kake,
					color: '#a1887f'
				},
				{
					name: 'Handa oi',
					data: handa_oi,
					color: '#212121'
				},
				{
					name: 'Yogore Plt',
					data: yogore_plt,
					color: '#FFEB3B'
				},
				{
					name: 'Others',
					data: other,
					color: '#455dff'
				}
				]
			});


			//Kensa Tenor Key
			var key = [];

			var kizu_before = [0,0,0,0,0,0,0,0,0,0];
			var kizu_after = [0,0,0,0,0,0,0,0,0,0];
			var yogore = [0,0,0,0,0,0,0,0,0,0];
			var buff_tarinai = [0,0,0,0,0,0,0,0,0,0];
			var nami = [0,0,0,0,0,0,0,0,0,0];
			var mekki_nai = [0,0,0,0,0,0,0,0,0,0];
			var kumori = [0,0,0,0,0,0,0,0,0,0];			
			var hakuri_nokoru = [0,0,0,0,0,0,0,0,0,0];
			var mekki_warui = [0,0,0,0,0,0,0,0,0,0];
			var kake = [0,0,0,0,0,0,0,0,0,0];
			var handa_oi = [0,0,0,0,0,0,0,0,0,0];
			var yogore_plt = [0,0,0,0,0,0,0,0,0,0];
			var other = [0,0,0,0,0,0,0,0,0,0];

			for (var i = 0; i < result.kensa_ng_key_tenor.length; i++) {
				key.push(result.kensa_ng_key_tenor[i].key);

				for (var j = 0; j < result.kensa_ng_key_tenor_detail.length; j++) {
					if(result.kensa_ng_key_tenor[i].key == result.kensa_ng_key_tenor_detail[j].key){

						if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Kizu before'){
							kizu_before[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Kizu after'){
							kizu_after[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Yogore'){
							yogore[i] = result.kensa_ng_key_tenor_detail[j].ng;									
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Buff tarinai'){
							buff_tarinai[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Nami'){
							nami[i] = result.kensa_ng_key_tenor_detail[j].ng;									
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Mekki nai'){
							mekki_nai[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Kumori'){
							kumori[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Hakuri nokoru'){
							hakuri_nokoru[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Mekki warui'){
							mekki_warui[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Kake'){
							kake[i] = result.kensa_ng_key_tenor_detail[j].ng;									
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Handa oi'){
							handa_oi[i] = result.kensa_ng_key_tenor_detail[j].ng;									
						}else if(result.kensa_ng_key_tenor_detail[j].ng_name == 'Yogore plt'){
							yogore_plt[i] = result.kensa_ng_key_tenor_detail[j].ng;
						}else{
							if(typeof other[i] == 'undefined'){
								other.push(result.kensa_ng_key_tenor_detail[j].ng);
							}else{
								other[i] += result.kensa_ng_key_tenor_detail[j].ng;
							}
						}
					}
				}
			}

			Highcharts.chart('chart_kensa_4_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 16pt;">10 Highest Keys NG Kensa Plating Tenor Sax</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
					useHTML: true
				},
				subtitle: {
					text: '<span style="font-size: 15pt; color: rgb(60, 60, 60);">on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
					data: kizu_before,
					color: '#2b908f'
				},
				{
					name: 'Kizu After',
					data: kizu_after,
					color: '#9c4dcc'
				},
				{
					name: 'Yogore',
					data: yogore,
					color: '#90ee7e'
				},				
				{
					name: 'Buff Taranai',
					data: buff_tarinai,
					color: '#f45b5b'
				},
				{
					name: 'Nami',
					data: nami,
					color: '#7798BF'
				},
				{
					name: 'Mekki Nai',
					data: mekki_nai,
					color: '#aaeeee'
				},
				{
					name: 'Kumori',
					data: kumori,
					color: '#ff0066'
				},
				{
					name: 'Hakuri Nokoru',
					data: hakuri_nokoru,
					color: '#FF8F00'
				},
				{
					name: 'Mekki Warui',
					data: mekki_warui,
					color: '#cfd8dc'
				},
				{
					name: 'Kake',
					data: kake,
					color: '#a1887f'
				},
				{
					name: 'Handa oi',
					data: handa_oi,
					color: '#212121'
				},
				{
					name: 'Yogore Plt',
					data: yogore_plt,
					color: '#FFEB3B'
				},
				{
					name: 'Others',
					data: other,
					color: '#455dff'
				}
				]
			});



		}
	}
});


$.get('{{ url("fetch/middle/plt_ng_rate/".$id) }}', data, function(result, status, xhr) {
	if(xhr.status == 200){
		if(result.status){
			var tgl = [];
			var alto = [];
			var tenor = [];

			for (var i = 0; i < result.ic.length; i++) {
				if(result.ic[i].hpl == 'ASKEY'){
					tgl.push(result.ic[i].week_date);
					alto.push(result.ic[i].ng_rate);
				}
				if(result.ic[i].hpl == 'TSKEY'){
					tenor.push(result.ic[i].ng_rate);
				}
			}
			var bulan = result.bulan;

			Highcharts.chart('chart_ic_5', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Daily NG Rate I.C. Plating Sax Key on '+bulanText(bulan)+'</span>',
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
					data: alto,
					color: '#f5ff0d',
					lineWidth: 3,
				},
				{
					name: 'Tenor',
					data: tenor,
					color: '#00FF00',
					lineWidth: 3,
				}
				]
			});

			var tgl = [];
			var alto = [];
			var tenor = [];

			for (var i = 0; i < result.kensa.length; i++) {
				if(result.kensa[i].hpl == 'ASKEY'){
					tgl.push(result.kensa[i].week_date);
					alto.push(result.kensa[i].ng_rate);
				}
				if(result.kensa[i].hpl == 'TSKEY'){
					tenor.push(result.kensa[i].ng_rate);
				}
			}


			Highcharts.chart('chart_kensa_5', {
				chart: {
					type: 'line'
				},
				title: {
					text: '<span style="font-size: 18pt;">Daily NG Rate Kensa Plating Sax Key on '+bulanText(bulan)+'</span>',
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
					data: alto,
					color: '#f5ff0d',
					lineWidth: 3,


				},
				{
					name: 'Tenor',
					data: tenor,
					color: '#00FF00',
					lineWidth: 3,

				}
				]
			});
		}
	}
});





}

</script>


@stop