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
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="loc" value="{{ $loc }}">
	<div class="row" style="margin-right: 15px;">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ url('index/welding/resume/'.$loc) }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="bulan" placeholder="Select Month">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="form-group">
							<select class="form-control select2" id="fySelect" data-placeholder="Select Fiscal Year" onchange="changeFy()">
								<option value=""></option>
								@foreach($fys as $fy)
								<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
								@endforeach
							</select>
							<input type="text" name="fy" id="fy" hidden>
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="form-group">
							<select class="form-control select2" id="keySelect" data-placeholder="Select Key" onchange="changeKey()">
								<option value=""></option>
								<option value="askey">ASKEY</option>
								<option value="tskey">TSKEY</option>
								<option value="all">All</option>
							</select>
							<input type="text" name="key" id="key" hidden>
						</div>
					</div>
					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
				</form>
			</div>
		</div>

		<div class="col-xs-12" style="padding: 0px; margin-left: 15px; margin-right: 15px;">
			<div class="col-xs-12" style="padding: 0px;">
				<div class="nav-tabs-custom">
					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-3">
										<table id="table_monthly" class="table table-bordered" style="margin:0">
											<thead id="head_monthly">
												<tr>
													<th style="padding: 0px;">Month</th>
													<th style="padding: 0px;">Total NG</th>
													<th style="padding: 0px;">Total Check</th>
													<th style="padding: 0px;">Target</th>
													<th style="padding: 0px;">NG Rate</th>
												</tr>
											</thead>
											<tbody id="body_monthly">
											</tbody>
										</table>
									</div>
									<div class="col-xs-9">
										<div id="chart_monthly" style="width: 99%;"></div>			
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12" style="padding: 0px;">
				<div class="nav-tabs-custom">
					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-3">
										<table id="table_weekly" class="table table-bordered" style="margin:0">
											<thead id="head_weekly">
												<tr>
													<th style="padding: 0px;">Week</th>
													<th style="padding: 0px;">Total NG</th>
													<th style="padding: 0px;">Total Check</th>
													<th style="padding: 0px;">NG Rate</th>
												</tr>
											</thead>
											<tbody id="body_weekly">
											</tbody>
										</table>
									</div>
									<div class="col-xs-9">
										<div id="chart_weekly" style="width: 99%;"></div>			
									</div>
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
									<div id="chart_ng_as" style="width: 99%;"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0.5%;">
						<div class="nav-tabs-custom">
							<div class="tab-content">
								<div class="tab-pane active">
									<div id="chart_ng_ts" style="width: 99%;"></div>
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
									<div id="chart_ng_askey" style="width: 99%;"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0.5%;">
						<div class="nav-tabs-custom">
							<div class="tab-content">
								<div class="tab-pane active">
									<div id="chart_ng_tskey" style="width: 99%;"></div>
								</div>
							</div>
						</div>	
					</div>
				</div>
			</div>

			<div class="col-xs-12" style="padding: 0px;">
				<div class="nav-tabs-custom">
					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-xs-12">
									<div id="chart_daily" style="width: 99%;"></div>			
								</div>
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
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();

		drawChart();
		setInterval(drawChart, 300000);

	});

	function drawChart() {
		var data = {
			loc:"{{ $loc }}",
			bulan:"{{ $_GET['bulan'] }}",
			fy:"{{$_GET['fy']}}",
			key:"{{$_GET['key']}}"
		}

		var loc = "{{ $loc }}";

		$.get('{{ url("fetch/welding/resume") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var key = "{{$_GET['key']}}";
					if(key == 'askey'){
						key = 'ASKEY';
					}else if(key == 'tskey'){
						key = 'TSKEY';
					}else{
						key = 'Sax Key';
					}

					$('#body_monthly').append().empty();
					var bulan = [];
					var ng_rate = [];
					var target = [];
					var body = "";

					for (var i = 0; i < result.monthly.length; i++) {
						bulan.push(result.monthly[i].bulan);
						ng_rate.push(result.monthly[i].ng_rate);
						target.push(1);


						body += "<tr>";
						body += "<td>"+result.monthly[i].bulan+"</td>";
						body += "<td>"+(result.monthly[i].ng || 'NaN')+"</td>";
						body += "<td>"+(result.monthly[i].cek || 'NaN')+"</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+(result.monthly[i].ng_rate || 'NaN')+"%</td>";
						body += "</tr>";
					}				
					$('#body_monthly').append(body);

					Highcharts.chart('chart_monthly', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG Rate '+ key +' on '+ result.fy,
							style: {
								textTransform: 'uppercase',
								fontSize: '18px'
							}
						},
						xAxis: {
							categories: bulan
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
							data: ng_rate
						},
						{
							name: 'Target',
							type: 'line',
							data: target,
							color: '#FF0000',
						}
						]
					});









					$('#body_weekly').append().empty();
					var week = [];
					var ng_rate = [];
					var body = "";

					for (var i = 0; i < result.weekly.length; i++) {
						week.push(result.weekly[i].week_name);
						ng_rate.push(result.weekly[i].ng_rate);
						
						body += "<tr>";
						body += "<td>"+result.weekly[i].week_name+"</td>";
						body += "<td>"+(result.weekly[i].ng || 'NaN')+"</td>";
						body += "<td>"+(result.weekly[i].cek || 'NaN')+"</td>";
						body += "<td>"+(result.weekly[i].ng_rate || 'NaN')+"%</td>";
						body += "</tr>";
					}				
					$('#body_weekly').append(body);

					Highcharts.chart('chart_weekly', {
						chart: {
							type: 'line'
						},
						title: {
							text: 'Weekly NG Rate '+ key +' on '+ bulanText(result.bulan),
							style: {
								textTransform: 'uppercase',
								fontSize: '18px'
							}
						},
						xAxis: {
							categories: week
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
							name: 'NG Rate',
							data: ng_rate
						}
						]
					});









					var bulan = [];
					var ng_rate_alto = [];
					var ng_rate_tenor = [];

					for (var i = 0; i < result.daily_alto.length; i++) {
						bulan.push(result.daily_alto[i].week_date);
						ng_rate_alto.push(result.daily_alto[i].ng_rate);
					}

					for (var i = 0; i < result.daily_tenor.length; i++) {
						ng_rate_tenor.push(result.daily_tenor[i].ng_rate);
					}

					Highcharts.chart('chart_daily', {
						chart: {
							type: 'line'
						},
						title: {
							text: 'Daily NG Rate on '+ bulanText(result.bulan),
							style: {
								textTransform: 'uppercase',
								fontSize: '18px'
							}
						},
						xAxis: {
							categories: bulan
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
						series: [{
							name: 'Alto',
							data: ng_rate_alto,
							color: '#f5ff0d',
							lineWidth: 3,
						},{
							name: 'Tenor',
							data: ng_rate_tenor,
							color: '#00FF00',
							lineWidth: 3,
						}]

					});


				}
			}
		});


$.get('{{ url("fetch/welding/key_resume") }}', data, function(result, status, xhr) {

	var key_title = "{{$_GET['key']}}";
	if(key_title == 'askey'){
		key_title = 'ASKEY';
	}else if(key_title == 'tskey'){
		key_title = 'TSKEY';
	}else{
		key_title = 'Sax Key';
	}

	if(loc == 'hsa-visual-sx'){
		
		var key = [];
		var rotare = [];
		var rotsuki = [];
		var gosong = [];
		var dimensi = [];
		var toke = [];
		var bari = [];
		var rooi = [];
		var kizu = [];
		var other = [];	


		for (var i = 0; i < result.askey.length; i++) {

			key.push(result.askey[i].key);

			for (var j = 0; j < result.askey_detail.length; j++) {
				if(result.askey[i].key == result.askey_detail[j].key){

					if(result.askey_detail[j].ng_name == 'Ro Tare'){
						rotare.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Ro Tsuki'){
						rotsuki.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Gosong'){
						gosong.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Dimensi'){
						dimensi.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Toke'){
						toke.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Bari'){
						bari.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Ro Oi'){
						rooi.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Kizu'){
						kizu.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Other'){
						other.push(result.askey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_askey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Askey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Ro Tare',
				data: rotare,
				color: '#2b908f'
			},
			{
				name: 'Ro Tsuki',
				data: rotsuki,
				color: '#90ee7e'
			},
			{
				name: 'Gosong',
				data: gosong,
				color: '#f45b5b'
			},
			{
				name: 'Dimensi',
				data: dimensi,
				color: '#7798BF'
			},
			{
				name: 'Toke',
				data: toke,
				color: '#aaeeee'
			},
			{
				name: 'Bari',
				data: bari,
				color: '#ff0066'
			},
			{
				name: 'Ro Oi',
				data: rooi,
				color: '#FF8F00'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#9C27B0'
			},
			{
				name: 'Other',
				data: other,
				color: '#FFEB3B'
			}
			]
		});






		var key = [];
		var rotare = [];
		var rotsuki = [];
		var gosong = [];
		var dimensi = [];
		var toke = [];
		var bari = [];
		var rooi = [];
		var kizu = [];
		var other = [];	


		for (var i = 0; i < result.tskey.length; i++) {

			key.push(result.tskey[i].key);

			for (var j = 0; j < result.tskey_detail.length; j++) {
				if(result.tskey[i].key == result.tskey_detail[j].key){

					if(result.tskey_detail[j].ng_name == 'Ro Tare'){
						rotare.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Ro Tsuki'){
						rotsuki.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Gosong'){
						gosong.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Dimensi'){
						dimensi.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Toke'){
						toke.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Bari'){
						bari.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Ro Oi'){
						rooi.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Kizu'){
						kizu.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Other'){
						other.push(result.tskey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_tskey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Tskey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Ro Tare',
				data: rotare,
				color: '#2b908f'
			},
			{
				name: 'Ro Tsuki',
				data: rotsuki,
				color: '#90ee7e'
			},
			{
				name: 'Gosong',
				data: gosong,
				color: '#f45b5b'
			},
			{
				name: 'Dimensi',
				data: dimensi,
				color: '#7798BF'
			},
			{
				name: 'Toke',
				data: toke,
				color: '#aaeeee'
			},
			{
				name: 'Bari',
				data: bari,
				color: '#ff0066'
			},
			{
				name: 'Ro Oi',
				data: rooi,
				color: '#FF8F00'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#9C27B0'
			},
			{
				name: 'Other',
				data: other,
				color: '#FFEB3B'
			}
			]
		});


	}else if(loc == 'phs-visual-sx'){
		var key = [];
		var rotare = [];
		var rotsuki = [];
		var gosong = [];
		var dimensi = [];
		var toke = [];
		var bari = [];
		var rooi = [];
		var kizu = [];
		var other = [];	


		for (var i = 0; i < result.askey.length; i++) {

			key.push(result.askey[i].key);

			for (var j = 0; j < result.askey_detail.length; j++) {
				if(result.askey[i].key == result.askey_detail[j].key){

					if(result.askey_detail[j].ng_name == 'Ro Tare'){
						rotare.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Ro Tsuki'){
						rotsuki.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Gosong'){
						gosong.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Dimensi'){
						dimensi.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Toke'){
						toke.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Bari'){
						bari.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Ro Oi'){
						rooi.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Kizu'){
						kizu.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Other'){
						other.push(result.askey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_askey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Askey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Ro Tare',
				data: rotare,
				color: '#2b908f'
			},
			{
				name: 'Ro Tsuki',
				data: rotsuki,
				color: '#90ee7e'
			},
			{
				name: 'Gosong',
				data: gosong,
				color: '#f45b5b'
			},
			{
				name: 'Dimensi',
				data: dimensi,
				color: '#7798BF'
			},
			{
				name: 'Toke',
				data: toke,
				color: '#aaeeee'
			},
			{
				name: 'Bari',
				data: bari,
				color: '#ff0066'
			},
			{
				name: 'Ro Oi',
				data: rooi,
				color: '#FF8F00'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#9C27B0'
			},
			{
				name: 'Other',
				data: other,
				color: '#FFEB3B'
			}
			]
		});






		var key = [];
		var rotare = [];
		var rotsuki = [];
		var gosong = [];
		var dimensi = [];
		var toke = [];
		var bari = [];
		var rooi = [];
		var kizu = [];
		var other = [];	


		for (var i = 0; i < result.tskey.length; i++) {

			key.push(result.tskey[i].key);

			for (var j = 0; j < result.tskey_detail.length; j++) {
				if(result.tskey[i].key == result.tskey_detail[j].key){

					if(result.tskey_detail[j].ng_name == 'Ro Tare'){
						rotare.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Ro Tsuki'){
						rotsuki.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Gosong'){
						gosong.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Dimensi'){
						dimensi.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Toke'){
						toke.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Bari'){
						bari.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Ro Oi'){
						rooi.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Kizu'){
						kizu.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Other'){
						other.push(result.tskey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_tskey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Tskey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Ro Tare',
				data: rotare,
				color: '#2b908f'
			},
			{
				name: 'Ro Tsuki',
				data: rotsuki,
				color: '#90ee7e'
			},
			{
				name: 'Gosong',
				data: gosong,
				color: '#f45b5b'
			},
			{
				name: 'Dimensi',
				data: dimensi,
				color: '#7798BF'
			},
			{
				name: 'Toke',
				data: toke,
				color: '#aaeeee'
			},
			{
				name: 'Bari',
				data: bari,
				color: '#ff0066'
			},
			{
				name: 'Ro Oi',
				data: rooi,
				color: '#FF8F00'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#9C27B0'
			},
			{
				name: 'Other',
				data: other,
				color: '#FFEB3B'
			}
			]
		});
		

	}else if(loc == 'hsa-dimensi-sx'){
		var key = [];
		var celah = [];
		var geser = [];
		var gata = [];
		var sudutng = [];
		var salahspec = [];
		var step2 = [];
		var lain = [];


		for (var i = 0; i < result.askey.length; i++) {

			key.push(result.askey[i].key);

			for (var j = 0; j < result.askey_detail.length; j++) {
				if(result.askey[i].key == result.askey_detail[j].key){

					if(result.askey_detail[j].ng_name == 'Celah'){
						celah.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Geser'){
						geser.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Gata'){
						gata.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Sudut NG'){
						sudutng.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Salah Spec'){
						salahspec.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Step 2'){
						step2.push(result.askey_detail[j].jml);
					}else if(result.askey_detail[j].ng_name == 'Lain-lain'){
						lain.push(result.askey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_askey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Askey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Celah',
				data: celah,
				color: '#2b908f'
			},
			{
				name: 'Geser',
				data: geser,
				color: '#90ee7e'
			},
			{
				name: 'Gata',
				data: gata,
				color: '#f45b5b'
			},
			{
				name: 'Sudut NG',
				data: sudutng,
				color: '#7798BF'
			},
			{
				name: 'Salah Spec',
				data: salahspec,
				color: '#aaeeee'
			},
			{
				name: 'Step 2',
				data: step2,
				color: '#ff0066'
			},
			{
				name: 'Lain-lain',
				data: lain,
				color: '#FF8F00'
			}
			]
		});


		var key = [];
		var celah = [];
		var geser = [];
		var gata = [];
		var sudutng = [];
		var salahspec = [];
		var step2 = [];
		var lain = [];


		for (var i = 0; i < result.tskey.length; i++) {

			key.push(result.tskey[i].key);

			for (var j = 0; j < result.tskey_detail.length; j++) {
				if(result.tskey[i].key == result.tskey_detail[j].key){

					if(result.tskey_detail[j].ng_name == 'Celah'){
						celah.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Geser'){
						geser.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Gata'){
						gata.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Sudut NG'){
						sudutng.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Salah Spec'){
						salahspec.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Step 2'){
						step2.push(result.tskey_detail[j].jml);
					}else if(result.tskey_detail[j].ng_name == 'Lain-lain'){
						lain.push(result.tskey_detail[j].jml);
					}
				}
			}
		}

		Highcharts.chart('chart_ng_tskey', {
			chart: {
				type: 'column'
			},
			title: {
				text: '10 Highest Tskey NG on '+bulanText(result.bulan),
				style: {
					textTransform: 'uppercase',
					fontSize: '18px'
				}
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
				name: 'Celah',
				data: celah,
				color: '#2b908f'
			},
			{
				name: 'Geser',
				data: geser,
				color: '#90ee7e'
			},
			{
				name: 'Gata',
				data: gata,
				color: '#f45b5b'
			},
			{
				name: 'Sudut NG',
				data: sudutng,
				color: '#7798BF'
			},
			{
				name: 'Salah Spec',
				data: salahspec,
				color: '#aaeeee'
			},
			{
				name: 'Step 2',
				data: step2,
				color: '#ff0066'
			},
			{
				name: 'Lain-lain',
				data: lain,
				color: '#FF8F00'
			}
			]
		});

	}


});


$.get('{{ url("fetch/welding/ng_resume") }}', data, function(result, status, xhr) {

});



}

function changeFy() {
	$("#fy").val($("#fySelect").val());
}

function changeKey() {
	$("#key").val($("#keySelect").val());
}

function bulanText(param){
	var bulan = parseInt(param.slice(0, 2));
	var tahun = param.slice(3, 8);
	var bulanText = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

	return bulanText[bulan-1]+" "+tahun;
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

</script>
@endsection