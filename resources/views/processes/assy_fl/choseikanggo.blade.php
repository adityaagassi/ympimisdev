@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
	padding: 2px;
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Chosei Kanggo Flute<span class="text-purple"> ??? </span>
		<small>Serial Number <span class="text-purple"> ??? </span></small>
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
					<div id="chartActual" style="width: 100%; height: 300px"></div>
					<div class="box box-solid">
						<div class="box-body">
							<table id="tableActual" class="table" style="width: 100%;">
								<thead id="tableHeadActual">
								</thead>
								<tbody id="tableBodyActual">
								</tbody>
								<tfoot id="tableFootActual">
								</tfoot>
							</table>
						</div>
					</div>
					<div id="chartEfficiency" style="width: 100%; height: 200px"></div>
					<div class="box box-solid">
						<div class="box-body">
							<table id="tableEfficiency" class="table" style="width: 100%;">
								<thead id="tableHeadEfficiency">
								</thead>
								<tbody id="tableBodyEfficiency">
								</tbody>
								<tfoot id="tableFootEfficiency">
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-4">
					<center>
						<div id="chartStock" style="width: 100%"></div>
					</center>
					<div class="box box-solid bg-light-blue-gradient">
						<div class="box-body">
							<table id="tableStock" class="table table-bordered" style="width: 100%;">
								<thead>
									<th style="text-align: center; width: 30%;">Model</th>
									<th style="text-align: center; width: 30%;">Plan</th>
									<th style="text-align: center; width: 40%;">Stock WIP</th>
								</thead>
								<tbody id="tableBodyStock">
								</tbody>
								<tfoot id="tableFootStock">
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-4">
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
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
		fillChartStock();
		fillChartEfficiency();
		// setInterval(fillChartActual,10000);
		// setInterval(fillChartStock,10000);
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
				processCode : 4,
				originGroupCode : '041',
				manPower : manPower
			}
			$.post('{{ url("input/process_assy_fl_4") }}', data, function(result, status, xhr){
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

	function fillChartEfficiency(){

		$.get('{{ url("fetch/process_assy_fl_4/efficiencyChart") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var data = result.efficiencyData;
					var effCat = [];
					var effSer = [];

					for (i = 0; i < data.length; i++) {
						effCat.push(data[i].due_date);
						effSer.push(data[i].efficiency*100);
					}

					var headEfficiencyData = '';
					headEfficiencyData += '<tr >';
					headEfficiencyData += '<th colspan="3" style="width:8%; color:purple">Daily Average Efficiency / Model</th>';
					headEfficiencyData += '</tr>';
					headEfficiencyData += '<tr>';
					headEfficiencyData += '<th style="width:8%;">Remark</th>';
					$.each(result.efficiencyTable, function(key, value) {
						headEfficiencyData += '<th>'+ value.model +'</th>';
					});
					$('#tableHeadEfficiency').append(headEfficiencyData);
					headEfficiencyData += '</tr>';
					
					var bodyEfficiencyData = '';
					bodyEfficiencyData += '<tr><td>Std Time</td>';
					$.each(result.efficiencyTable, function(key, value) {
						bodyEfficiencyData += '<td>'+ value.std_time +'</td>';
					});
					bodyEfficiencyData += '</tr>';
					bodyEfficiencyData += '<tr><td>Act Time</td>';
					$.each(result.efficiencyTable, function(key, value) {
						bodyEfficiencyData += '<td>'+ value.actual_time +'</td>';
					});
					bodyEfficiencyData += '</tr>';

					bodyEfficiencyData += '<tr><td>Eff</td>';
					$.each(result.efficiencyTable, function(key, value) {
						bodyEfficiencyData += '<td>'+ value.efficiency*100 +'%</td>';
					});
					bodyEfficiencyData += '</tr>';
					$('#tableBodyEfficiency').append(bodyEfficiencyData);

					var yAxisLabels = [90,95,100,105,110];
					var chart = Highcharts.chart('chartEfficiency', {
						chart: {
							type: 'column',
							backgroundColor:'rgba(255, 255, 255, 0.0)'
						},
						title:{
							text: 'Daily Average Efficiency'
						},
						legend:{
							enabled:false
						},
						credits:{
							enabled:false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									format: '{point.y:.1f}%'
								}
							}
						},
						xAxis: {
							categories: effCat
						},
						yAxis:{
							labels: {
								enabled:false
							},
							type: 'logarithmic',
							title: {
								text: 'Percentage'
							},
							plotLines: [{
								value: 100,
								color: 'red',
								dashStyle: 'shortdash',
								width: 2
							}]
						},
						series: [{
							name: 'Eff',
							type: 'line',
							data: effSer
						}]
					});

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

	function fillChartActual(){
		$.get('{{ url("fetch/process_assy_fl_4/actualChart") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var data = result.planData;
					var actualPlan = [];
					var actualResult = [];
					var tableActualData = '';
					var aCategory = [];

					for (i = 0; i < data.length; i++) {
						actualResult.push(data[i].actual);
						actualPlan.push(data[i].plan);
						aCategory.push(data[i].due_date);
					}

					var headActualData = '';
					headActualData += '<th style="width:8%;">Remark</th>';
					$.each(result.planTable, function(key, value) {
						headActualData += '<th>'+ value.model +'</th>';
					});
					$('#tableHeadActual').append(headActualData);

					var bodyActualData = '';
					bodyActualData += '<tr><td>Plan</td>';
					$.each(result.planTable, function(key, value) {
						bodyActualData += '<td>'+ value.plan +'</td>';
					});
					bodyActualData += '</tr>';
					bodyActualData += '<tr><td>Actual</td>';
					$.each(result.planTable, function(key, value) {
						bodyActualData += '<td>'+ value.actual +'</td>';
					});
					bodyActualData += '</tr>';

					bodyActualData += '<tr><td>Diff</td>';
					$.each(result.planTable, function(key, value) {
						bodyActualData += '<td>'+ (value.actual-value.plan) +'</td>';
					});
					bodyActualData += '</tr>';
					$('#tableBodyActual').append(bodyActualData);


					var chart = Highcharts.chart('chartActual', {
						chart: {
							type: 'column',
							backgroundColor:'rgba(255, 255, 255, 0.0)'
						},
						title:{
							text: 'Actual production'
						},
						credits:{
							enabled:false
						},
						xAxis: {
							categories: aCategory
						},
						yAxis:{
							type: 'logarithmic'
						},
						series: [{
							name: 'Actual',
							data: actualResult
						}, {
							name: 'Plan',
							color: 'red',
							type: 'line',
							data: actualPlan
						}]
					});

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

	function fillChartStock(){
		$.get('{{ url("fetch/process_assy_fl_4/stockChart") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status==200){
				if(result.status){
					var data = result.stockData;
					var stockSeries = [];
					var tableStockData = '';

					for (i = 0; i < data.length; i++) {
						stockSeries.push({name: data[i].model, y: data[i].stock});
					}

					$.each(result.stockTable, function(key, value) {
						tableStockData += '<tr>';
						tableStockData += '<td style="width: 40%">'+ value.model +'</td>';
						tableStockData += '<td style="width: 15%">'+ value.plan +'</td>';
						tableStockData += '<td style="width: 15%">'+ value.quantity +'</td>';
						tableStockData += '</tr>';
					});
					$('#tableBodyStock').append(tableStockData);

					// alert(JSON.stringify(stockSeries));
					
					$('#tableStock').DataTable({
						'paging'      : true,
						'lengthChange': false,
						'searching'   : false,
						'ordering'    : false,
						'info'        : true,
						'autoWidth'   : false,
						'pageLength'  : 10,
						'infoCallback': function( settings, start, end, max, total, pre ) {
							return "<b>Total "+ total +" set(s)</b>";
						},
					})

					Highcharts.chart('chartStock', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
							backgroundColor:'rgba(255, 255, 255, 0.0)'
						},
						title: {
							text: 'Actual Stock'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.y}</b>',
							enabled:false
						},
						credits:{
							enabled:false
						},
						exporting:{
							enabled:false,
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									distance:-50,
									format: '<b>{point.name}</b><br>{point.y} set(s)'
								},
								showInLegend: false
							}
						},
						series: [{
							name: 'Quantity',
							colorByPoint: true,
							data: stockSeries
						}]
					});
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

</script>
@endsection