@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
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
		margin:0; 
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		NG Buffing Report <span class="text-purple">??</span>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin-top: 1%;">
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
							<select class="form-control select2" multiple="multiple" id="fySelect" data-placeholder="Select Key" onchange="change()">
								<option value="">ALTO</option>
								<option value="">TENOR</option>
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
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly" class="table table-bordered" style="margin:0">
												<thead id="">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">NG Rate</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">Diff</th>
													</tr>
												</thead>
												<tbody id="">
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
										<div class="col-xs-3">
											<table id="table_monthly" class="table table-bordered" style="margin:0">
												<thead id="">
													<tr>
														<th style="padding: 0px;">OP Name</th>
														<th style="padding: 0px;">Total NG</th>
													</tr>
												</thead>
												<tbody id="">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart2" style="width: 99%;"></div>			
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
											<table id="table_monthly" class="table table-bordered" style="margin:0">
												<thead id="">
													<tr>
														<th style="padding: 0px;">NG Name</th>
														<th style="padding: 0px;">NG Rate</th>
													</tr>
												</thead>
												<tbody id="">
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('m-Y') ?>
		format: "mm-yyyy",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		endDate: '<?php echo $tgl_max ?>'

	});

	function drawChart() {

		//Chart 1
		var month =  ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar"];
		var target = [];
		var ng_rate_monthly = [];

		for (var i = 0; i < 12; i++) {
			target.push(18.23);
			ng_rate_monthly.push(20);
		}


		Highcharts.chart('chart1', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<span style="font-size: 18pt;">Grafik Monthly of % NG</span>',
				useHTML: true
			},
			xAxis: {
				categories: month
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				max: 30,
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



		//Chart 2
		Highcharts.chart('chart2', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<span style="font-size: 18pt;">OP penghasil NG terbesar</span>',
				useHTML: true
			},
			xAxis: {
				categories: ['A','B','C','D','E']
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				max: 50,
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
					},
					dashStyle: 'ShortDash'
				}
			},credits: {
				enabled: false
			},
			series: [
			{
				name: 'NG Rate',
				data: [40,34,29,25,20]
			}
			]
		});

		//Chart 3
		Highcharts.chart('chart3', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<span style="font-size: 18pt;">Grafik Total NG</span>',
				useHTML: true
			},
			xAxis: {
				categories: ['A','B','C','D','E']
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				max: 50,
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
					},
					dashStyle: 'ShortDash'
				}
			},credits: {
				enabled: false
			},
			series: [
			{
				name: 'NG Rate',
				data: [40,34,29,25,20]
			}
			]
		});



		var a = [];
		var b = [];

		for (var i = 1; i < 31; i++) {
			a.push(i);
			b.push(i);
		}

		//Chart 4
		Highcharts.chart('chart4', {
			chart: {
				type: 'line'
			},
			title: {
				text: '<span style="font-size: 18pt;">Daily NG Rate Sax Tenor Key</span>',
				useHTML: true
			},
			xAxis: {
				categories: a
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				max: 30,
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
				data: b
			}
			]
		});
		
	}

</script>
@endsection
