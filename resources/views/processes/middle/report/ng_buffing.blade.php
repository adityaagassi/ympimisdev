@extends('layouts.display')
@section('stylesheets')
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
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('MiddleProcessController@indexReportBuffingNg') }}">
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
							<select class="form-control select2" multiple="multiple" id="fySelect" data-placeholder="Select Fiscal Year" onchange="changeFy()">
								@foreach($fys as $fy)
								<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
								@endforeach
							</select>
							<input type="text" name="fy" id="fy" hidden>
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="hplSelect" data-placeholder="Select HPL" onchange="changeHpl()">
								<option value="ASKEY">Alto Key</option>
								<option value="TSKEY">Tenor Key</option>
							</select>
							<input type="text" name="hpl" id="hpl" hidden>
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
														<th style="padding: 0px;">Diff</th>
													</tr>
												</thead>
												<tbody id="body_monthly">
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
								<div class="row">
									<div class="col-xs-12">
										<div id="chart2" style="width: 100%;"></div>			
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
											<table id="table_ng" class="table table-bordered" style="margin:0">
												<thead id="head_ng">
													<tr>
														<th style="padding: 0px;">NG Name</th>
														<th style="padding: 0px;">NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_ng">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart3" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">		
								<div id="chart4" style="width: 100%;"></div>			
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>


</section>
@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
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

	function changeFy() {
		$("#fy").val($("#fySelect").val());
	}

	function changeHpl() {
		$("#hpl").val($("#hplSelect").val());
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

	function drawChart() {
		var data = {
			bulan:"{{$_GET['bulan']}}",
			fy:"{{$_GET['fy']}}",
			hpl:"{{$_GET['hpl']}}"
		}

		$.get('{{ url("fetch/middle/bff_ng_rate_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					//Chart 1
					$('#body_monthly').append().empty();
					
					var month =  [];
					var target = [];
					var ng = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly.length; i++) {
						month.push(result.monthly[i].tgl);
						ng.push(result.monthly[i].ng_rate);
						ng[i] = ng[i] || 0;
						ng_rate_monthly.push(ng[i] * 100);
						target.push(18.23);
					}
					
					var body = "";
					for (var i = 0; i < result.monthly.length; i++) {
						body += "<tr>";
						body += "<td>"+month[i]+"</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "<td>"+target[i]+"%</td>";
						if(ng_rate_monthly[i] == 0){
							body += "<td></td>";
						}else{
							body += "<td>"+(ng_rate_monthly[i] - target[i]).toFixed(2)+"%</td>";
						}
						body += "</tr>";
					}
					$('#body_monthly').append(body);


					Highcharts.chart('chart1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Grafik Monthly of % NG '+ result.hpl +'</span>',
							useHTML: true
						},
						xAxis: {
							categories: month
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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


		$.get('{{ url("fetch/middle/bff_op_ng_monthly/resume") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var name = [];
					var ng_rate = [];

					for (var i = 0; i < result.op_ng.length; i++) {
						name.push(result.op_ng[i].name);
						ng_rate.push(result.op_ng[i].ng_rate * 100);
					}

					var body = "";
					for (var i = 0; i < result.op_ng.length; i++) {
						body += "<tr>";
						body += "<td>"+name[i]+"</td>";
						body += "<td>"+ng_rate[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_op_ng').append(body);

					Highcharts.chart('chart2', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Highest NG Rate by OP on '+ bulanText(result.bulan) +'</span>',
							useHTML: true
						},
						xAxis: {
							categories: name
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'NG Rate',
							data: ng_rate
						}
						]
					});
				}
			}
		});


		$.get('{{ url("fetch/middle/bff_ng_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var ng_name = [];
					var ng_rate = [];

					for (var i = 0; i < result.ng.length; i++) {
						ng_name.push(result.ng[i].ng_name);
						ng_rate.push(result.ng[i].ng_rate*100);
					}

					var body = "";
					for (var i = 0; i < result.ng.length; i++) {
						body += "<tr>";
						body += "<td>"+ng_name[i]+"</td>";
						body += "<td>"+ng_rate[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_ng').append(body);

					Highcharts.chart('chart3', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Highest '+result.hpl+' NG on '+bulanText(result.bulan)+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: ng_name
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'NG Rate',
							data: ng_rate
						}
						]
					});

				}
			}
		});


		$.get('{{ url("fetch/middle/bff_ng_rate_daily") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var tgl = [];
					var alto = [];
					var tenor = [];

					for (var i = 0; i < result.daily.length; i++) {
						if(result.daily[i].hpl == 'ASKEY'){
							tgl.push(result.daily[i].week_date);
							alto.push(result.daily[i].ng_rate);
						}
						if(result.daily[i].hpl == 'TSKEY'){
							tenor.push(result.daily[i].ng_rate);
						}
					}

					Highcharts.chart('chart4', {
						chart: {
							type: 'line'
						},
						title: {
							text: '<span style="font-size: 18pt;">Daily NG Rate Sax Key '+bulanText(result.bulan)+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: tgl
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
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
							color: '#f5ff0d',
							data: alto,
							lineWidth: 3,

						},
						{
							name: 'Tenor',
							color: '#00FF00',
							data: tenor,
							lineWidth: 3,


						}
						]
					});


				}
			}
		});

	}

</script>
@endsection
