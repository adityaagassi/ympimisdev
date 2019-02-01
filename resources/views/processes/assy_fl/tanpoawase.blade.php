@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
	padding: 1px;
}
table.table-bordered{
	border:1px solid black;
	margin-top:20px;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
}
.outer .chart-container {
	width: 100%;
	float: right;
	height: 250px;
}
.highcharts-yaxis-grid .highcharts-grid-line {
	display: none;
}

@media (max-width: 100%) {
	.outer {
		width: 100%;
		height: 400px;
	}
	.outer .chart-container {
		width: 100%;
		float: right;
		/*margin: 0 auto;*/
	}
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Tanpo Awase Flute<span class="text-purple"> FLタンポ合わせ</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<div class="col-xs-3">
						<input type="number" style="text-align: center; font-size: 22" class="form-control" id="manPower" name="manPower" placeholder="Manpower" required>
					</div>
					<div class="col-xs-1">
						<input id="toggle_lock" data-toggle="toggle" data-on="Lock" data-off="Open" type="checkbox">
					</div>
					<div class="input-group col-xs-8">
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
							<i class="glyphicon glyphicon-barcode"></i>
						</div>
						<input type="text" style="text-align: center; font-size: 22" class="form-control" id="serialNumber" name="serialNumber" placeholder="Scan Serial Number Here..." required>
						<div class="input-group-addon" id="icon-serial">
							<i class="glyphicon glyphicon-ok"></i>
						</div>
					</div>
					<br>
				</div>
				<div class="col-xs-8">
					<div id="container" style="width:100%; height:550px;"></div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box box-widget">
								<div class="box-footer">
									<div class="row" id="resume"></div>
								</div>
							</div>
						</div>
					</div>					
				</div>
				<div class="col-xs-4 outer">
					<div id="container2" class="chart-container" style="width:100%;"></div>
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Last Output Time</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="lastOutput">-</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Target Efficiency</th>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Manpower</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="targetEff">118%</td>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="actManpower">-</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Average STD Time Per Set</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="stdTime">00:00:00</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Average ACT Time Per Set</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="actTime">00:00:00</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#serialNumber').val("");
		$('#serialNumber').prop("disabled", true);
		$('#manPower').val("");
		$('#manPower').focus();
		fillChartActual();
		$('#toggle_lock').change(function(){				
			if(this.checked){
				$('#manPower').prop('disabled', true);
				$('#serialNumber').prop('disabled', false);
				$('#serialNumber').focus();
			}
			else{
				$('#manPower').prop('disabled', false);
				$('#serialNumber').prop('disabled', true);
				$('#manPower').focus();
			}
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	// $("#serialNumber").on("input", function() {
	// 	delay(function(){
	// 		if ($("#serialNumber").val().length < 8) {
	// 			$("#serialNumber").val("");
	// 		}
	// 	}, 100 );
	// });

	$('#serialNumber').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#serialNumber").val().length == 8){
				scanSerialNumber();
				return false;
			}
			else{
				$("#serialNumber").val("");
				alert('Error!', 'Serial number invalid.');
				audio_error.play();
			}
		}
	});

	function scanSerialNumber(){
		if($('#manPower').val() > 0){
			var manPower = $('#manPower').val();
			var serialNumber = $('#serialNumber').val();
			var data = {
				serialNumber : serialNumber,
				processCode : 2,
				originGroupCode : '041',
				manPower : manPower
			}
			$.post('{{ url("input/process_assy_fl") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success', result.message);
						$('#serialNumber').val('');
						$('#serialNumber').focus();
					}
					else{
						audio_error.play();
						alert(result.message);
						$('#serialNumber').val('');
						$('#serialNumber').focus();
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from server');
				}
			});
		}
		else{
			audio_error.play();
			$("#serialNumber").val("");
			$('#toggle_lock').prop('checked', false).change();
			alert("Please fill number of manpower");
		}
	}

	function fillChartActual(){
		$.get('{{ url("fetch/process_assy_fl_2/actualChart") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var data = result.chartData;
					var xAxis = []
					, planCount = []
					, actualCount = []

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].model);
						planCount.push(data[i].plan);
						actualCount.push(data[i].actual);
					}

					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Month to Date of Production Result<br><span style="color:rgba(96,92,168);">過去一ヶ月間の生産結果</span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: 'Set(s)'
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						plotOptions: {
							series:{
								minPointLength: 10,
								pointPadding: 0,
								groupPadding: 0,
								animation:{
									duration:0
								}
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0,
							}
						},
						series: [{
							name: 'Plan',
							data: planCount,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: actualCount,
							pointPadding: 0.2
						}]
					});

					var totalPlan = 0;
					var totalActual = 0;
					$.each(result.chartData, function(key, value) {
						totalPlan += value.plan;
						totalActual += value.actual;
					});

					if(totalActual-totalPlan < 0){
						totalCaret = '<span class="text-red"><i class="fa fa-caret-down"></i>';
					}
					if(totalActual-totalPlan > 0){
						totalCaret = '<span class="text-yellow"><i class="fa fa-caret-up"></i>';
					}
					if(totalActual-totalPlan == 0){
						totalCaret = '<span class="text-green">&#9679;';
					}

					$('#resume').html("");
					var resumeData = '';
					resumeData += '<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-blue">'+ totalPlan.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Plan<br><span class="text-purple">計画の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-purple">'+ totalActual.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Actual<br><span class="text-purple">実績の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ totalCaret + '' +Math.abs(totalActual-totalPlan).toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Difference<br><span class="text-purple">差異</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					$('#resume').append(resumeData);

					var gaugeOptions = {

						chart: {
							type: 'solidgauge',
							backgroundColor: null,
							spacingTop: 0,
							spacingLeft: 0,
							spacingRight: 0,
							spacingBottom: 0
						},

						title: 'Efficiency',
						exporting: { enabled: false },
						pane: {
							center: ['50%', '85%'],
							size: '140%',
							startAngle: -90,
							endAngle: 90,
							background: {
								backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
								innerRadius: '60%',
								outerRadius: '100%',
								shape: 'arc'
							}
						},

						tooltip: {
							enabled: false
						},

						yAxis: {
							stops: [
							[0, '#FF0000'],
							[113/200, '#FF0000'],
							[114/200, '#FFD700'],
							[115/200, '#FFD700'],
							[116/200, '#FFD700'],
							[117/200, '#FFD700'],
							[118/200, '#55BF3B']
							],
							lineWidth: 0,
							minorTickInterval: null,
							tickAmount: 2,
							title: {
								y: -70
							},
							labels: {
								y: 16
							}
						},

						plotOptions: {
							solidgauge: {
								dataLabels: {
									y: 5,
									borderWidth: 0,
									useHTML: true
								}
							}
						}
					};

					var act_time = 0;
					var std_time = 0;
					var qty = 0;
					var actmanpower = 0;
					var lastInput = "";
					$.each(result.effData, function(key, value) {
						act_time += value.act_time;
						std_time += value.std_time;
						qty += value.quantity;
						actmanpower += value.manpower;
						lastInput += value.last_input;
					});

					if(qty == 0){
						qty = qty+1;
					}
					if(lastInput == ""){
						lastInput = "00-00-00 00:00:00";
					}

					var eff = 0;
					var act_time_set = 0;
					var std_time_set = 0;
					eff = Math.round((std_time/act_time)*100);
					act_time_set = Math.round(act_time/qty);
					std_time_set = Math.round(std_time/qty);

					$('#actTime').html("");
					$('#stdTime').html("");
					$('#actManpower').html("");
					$('#lastOutput').html("");
					$('#lastOutput').html("<center>"+lastInput+"</center>");
					$('#actTime').html("<center>"+secondsTimeSpanToHMS(act_time_set)+"</center>");
					$('#stdTime').html("<center>"+secondsTimeSpanToHMS(std_time_set)+"</center>");
					$('#actManpower').html("<center>"+Math.round(actmanpower,2)+"</center>");

					Highcharts.chart('container2', Highcharts.merge(gaugeOptions, {
						yAxis: {
							min: 0,
							max: 200,
							title: {
								text: 'Efficiency',
								y:-100
								// enabled:false
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'Efficiency',
							data: [eff],
							dataLabels: {
								format: '<span style="font-size:5vw;">{y}%</span>',
								// x: 0,
								y: 30
							}
						}]
					}));
					setTimeout(fillChartActual, 5000);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
	});
}

function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}

</script>
@endsection