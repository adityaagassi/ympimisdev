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
					<div class="col-xs-2" style="color:black;">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="fySelect" data-placeholder="Select Fiscal Year" onchange="changeFy()">
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
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3" style="vertical-align: bottom;">
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
											<div id="chart0" style="width: 99%;"></div>			
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
										<div class="col-xs-3" style="vertical-align: bottom;">
											<table id="table_monthly" class="table table-bordered" style="margin:0">
												<thead id="head_weekly">
													<tr>
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Check</th>
														<th style="padding: 0px;">NG</th>
														<th style="padding: 0px;">NG Rate</th>
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

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart2_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart2_tenor" style="width: 99%;"></div>
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
											<div id="chart3_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart3_tenor" style="width: 99%;"></div>
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
		setInterval(drawChart, 60*60*1000);

	});

	function changeFy() {
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

	function drawChart() {
		var data = {
			bulan:"{{$_GET['bulan']}}",
			fy:"{{$_GET['fy']}}"
		}

		$.get('{{ url("fetch/middle/bff_ng_rate_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					//Chart 1
					$('#body_monthly').append().empty();
					var fy = result.fy;
					
					var month =  [];
					var target = [];
					var ng = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.monthly.length; i++) {
						month.push(result.monthly[i].tgl);
						ng.push(result.monthly[i].ng_rate);
						ng[i] = ng[i] || 0;
						ng_rate_monthly.push(ng[i] * 100);
						target.push(result.target.target);
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
							if(ng_rate_monthly[i] - target[i] > 0){
								body += "<td style='color: red;'>"+(ng_rate_monthly[i] - target[i]).toFixed(2)+"%</td>";
							}else{
								body += "<td>"+Math.abs((ng_rate_monthly[i] - target[i]).toFixed(2))+"%</td>";
							}
						}
						body += "</tr>";
					}
					$('#body_monthly').append(body);


					Highcharts.chart('chart0', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Monthly NG Rate Buffing Sax Key on ' +fy+ '</span>',
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

		$.get('{{ url("fetch/middle/bff_ng_rate_weekly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#body_weekly').append().empty();
					
					var week_name = [];
					var ng = [];
					var g= [];					
					var ng_rate_weekly = [];
					var body = "";

					for (var i = 0; i < result.weekly.length; i++) {
						week_name.push(result.weekly[i].week_name);
						ng_rate_weekly.push((result.weekly[i].ng / result.weekly[i].g) * 100);

						body += "<tr>";
						body += "<td>"+result.weekly[i].week_name+"</td>";
						body += "<td>"+result.weekly[i].g+"</td>";
						body += "<td>"+result.weekly[i].ng+"</td>";
						body += "<td>"+((result.weekly[i].ng / result.weekly[i].g) * 100).toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_weekly').append(body);


					Highcharts.chart('chart1', {
						chart: {
							type: 'line'
						},
						title: {
							text: '<span style="font-size: 18pt;">Weekly NG Rate Buffing Sax Key on '+result.bulanText+'</span>',
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


		$.get('{{ url("fetch/middle/bff_ng_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var ng_rate_alto = [];			
					var ng = [];
					var jml = [];
					var color = [];
					var series = [];

					for (var i = 0; i < result.ng_alto.length; i++) {
						if(result.ng_alto[i].ng_name == 'Buff Tarinai'){
							ng.push(result.ng_alto[i].ng_name);
							ng_rate_alto.push(result.ng_alto[i].ng/result.ng_alto[i].check*100);
							color.push('#00897B');
						}else if(result.ng_alto[i].ng_name == 'NG Soldering'){
							ng.push(result.ng_alto[i].ng_name);
							ng_rate_alto.push(result.ng_alto[i].ng/result.ng_alto[i].check*100);
							color.push('#F9A825');
						}else if(result.ng_alto[i].ng_name == 'Kizu'){
							ng.push(result.ng_alto[i].ng_name);
							ng_rate_alto.push(result.ng_alto[i].ng/result.ng_alto[i].check*100);
							color.push('#aaeeee');
						}else if(result.ng_alto[i].ng_name == 'Buff Others (Aus, Nami, dll)'){
							ng.push(result.ng_alto[i].ng_name);
							ng_rate_alto.push(result.ng_alto[i].ng/result.ng_alto[i].check*100);
							color.push('#BCAAA4');
						}else if(result.ng_alto[i].ng_name == 'Buff Nagare'){
							ng.push(result.ng_alto[i].ng_name);
							ng_rate_alto.push(result.ng_alto[i].ng/result.ng_alto[i].check*100);
							color.push('#7798BF');
						}

						series.push({name : ng[i], data: [ng_rate_alto[i]], color: color[i]});
					}
					

					Highcharts.chart('chart2_alto', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 16pt;">Highest NG Buffing Alto Sax Key on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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




					var ng_rate_tenor = [];			
					var ng = [];
					var jml = [];
					var color = [];
					var series = [];

					for (var i = 0; i < result.ng_tenor.length; i++) {
						if(result.ng_tenor[i].ng_name == 'Buff Tarinai'){
							ng.push(result.ng_tenor[i].ng_name);
							ng_rate_tenor.push(result.ng_tenor[i].ng/result.ng_tenor[i].check*100);
							color.push('#00897B');
						}else if(result.ng_tenor[i].ng_name == 'NG Soldering'){
							ng.push(result.ng_tenor[i].ng_name);
							ng_rate_tenor.push(result.ng_tenor[i].ng/result.ng_tenor[i].check*100);
							color.push('#F9A825');
						}else if(result.ng_tenor[i].ng_name == 'Kizu'){
							ng.push(result.ng_tenor[i].ng_name);
							ng_rate_tenor.push(result.ng_tenor[i].ng/result.ng_tenor[i].check*100);
							color.push('#aaeeee');
						}else if(result.ng_tenor[i].ng_name == 'Buff Others (Aus, Nami, dll)'){
							ng.push(result.ng_tenor[i].ng_name);
							ng_rate_tenor.push(result.ng_tenor[i].ng/result.ng_tenor[i].check*100);
							color.push('#BCAAA4');
						}else if(result.ng_tenor[i].ng_name == 'Buff Nagare'){
							ng.push(result.ng_tenor[i].ng_name);
							ng_rate_tenor.push(result.ng_tenor[i].ng/result.ng_tenor[i].check*100);
							color.push('#7798BF');
						}

						series.push({name : ng[i], data: [ng_rate_tenor[i]], color: color[i]});
					}

					Highcharts.chart('chart2_tenor', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 16pt;">Highest NG Buffing Tenor Sax Key on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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

				}
			}
		});


$.get('{{ url("fetch/middle/bff_ng_key_monthly") }}', data, function(result, status, xhr) {
	if(xhr.status == 200){
		if(result.status){

			var key = [];
			var nagare = [0,0,0,0,0,0,0,0,0,0];
			var other = [0,0,0,0,0,0,0,0,0,0];
			var tarinai = [0,0,0,0,0,0,0,0,0,0];
			var kizu = [0,0,0,0,0,0,0,0,0,0];
			var soldering = [0,0,0,0,0,0,0,0,0,0];

			for (var i = 0; i < result.ngKey_alto.length; i++) {
				key.push(result.ngKey_alto[i].key);
				for (var j = 0; j < result.ngKey_alto_detail.length; j++) {
					if(result.ngKey_alto[i].key == result.ngKey_alto_detail[j].key){
						if(result.ngKey_alto_detail[j].ng_name == 'Buff Nagare'){
							nagare[i] = result.ngKey_alto_detail[j].ng;
						}else if(result.ngKey_alto_detail[j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							other[i] = result.ngKey_alto_detail[j].ng;
						}else if(result.ngKey_alto_detail[j].ng_name == 'Buff Tarinai'){
							tarinai[i] = result.ngKey_alto_detail[j].ng;
						}else if(result.ngKey_alto_detail[j].ng_name == 'Kizu'){
							kizu[i] = result.ngKey_alto_detail[j].ng;
						}else if(result.ngKey_alto_detail[j].ng_name == 'NG Soldering'){
							soldering[i] = result.ngKey_alto_detail[j].ng;
						}
					}
				}
			}

			Highcharts.chart('chart3_alto', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 16pt;">10 Highest Keys NG Buffing Alto Sax on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
					name: 'Buff Nagare',
					data: nagare,
					color: '#7798BF'
				},
				{
					name: 'Buff Others (Aus, Nami, dll)',
					data: other,
					color: '#BCAAA4'
				},
				{
					name: 'Buff Tarinai',
					data: tarinai,
					color: '#00897B'
				},
				{
					name: 'Kizu',
					data: kizu,
					color: '#aaeeee'
				},
				{
					name: 'NG Soldering',
					data: soldering,
					color: '#F9A825'
				}
				]
			});



			var key = [];
			var nagare = [0,0,0,0,0,0,0,0,0,0];
			var other = [0,0,0,0,0,0,0,0,0,0];
			var tarinai = [0,0,0,0,0,0,0,0,0,0];
			var kizu = [0,0,0,0,0,0,0,0,0,0];
			var soldering = [0,0,0,0,0,0,0,0,0,0];

			for (var i = 0; i < result.ngKey_tenor.length; i++) {
				key.push(result.ngKey_tenor[i].key);
				for (var j = 0; j < result.ngKey_tenor_detail.length; j++) {
					if(result.ngKey_tenor[i].key == result.ngKey_tenor_detail[j].key){
						if(result.ngKey_tenor_detail[j].ng_name == 'Buff Nagare'){
							nagare[i] = result.ngKey_tenor_detail[j].ng;
						}else if(result.ngKey_tenor_detail[j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							other[i] = result.ngKey_tenor_detail[j].ng;
						}else if(result.ngKey_tenor_detail[j].ng_name == 'Buff Tarinai'){
							tarinai[i] = result.ngKey_tenor_detail[j].ng;
						}else if(result.ngKey_tenor_detail[j].ng_name == 'Kizu'){
							kizu[i] = result.ngKey_tenor_detail[j].ng;
						}else if(result.ngKey_tenor_detail[j].ng_name == 'NG Soldering'){
							soldering[i] = result.ngKey_tenor_detail[j].ng;
						}
					}
				}
			}

			Highcharts.chart('chart3_tenor', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<span style="font-size: 16pt;">10 Highest Keys NG Buffing Tenor Sax on '+result.bulanText+'</span><br><center><span style="color: rgba(96, 92, 168);"></center></span>',
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
					name: 'Buff Nagare',
					data: nagare,
					color: '#7798BF'
				},
				{
					name: 'Buff Others (Aus, Nami, dll)',
					data: other,
					color: '#BCAAA4'
				},
				{
					name: 'Buff Tarinai',
					data: tarinai,
					color: '#00897B'
				},
				{
					name: 'Kizu',
					data: kizu,
					color: '#aaeeee'
				},
				{
					name: 'NG Soldering',
					data: soldering,
					color: '#F9A825'
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
					text: '<span style="font-size: 18pt;">Daily NG Rate Buffing Sax Key '+bulanText(result.bulan)+'</span>',
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
