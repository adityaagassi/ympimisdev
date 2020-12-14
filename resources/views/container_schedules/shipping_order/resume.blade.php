@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
		<div class="col-xs-2 input-group date pull-right" style="text-align: center;">
			<div class="input-group-addon bg-green">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control monthpicker" name="period" id="period" onchange="fillTable()" placeholder="Select Period">	
		</div>
	</h1>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">		
		<div class="col-xs-12">
			<div class="col-xs-3" style="padding: 0px;">
				<div class="col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-ship"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">PLAN</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_plan"></span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-green"><i class="glyphicon glyphicon-ok"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">CONFIRMED</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_confirmed"></span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-red"><i class="glyphicon glyphicon-remove"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">NOT CONFIRMED</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_not_confirmed"></span>
						</div>
					</div>
					
				</div>
			</div>
			<div class="col-xs-9" style="padding: 0px;">
				<div id="container1"></div>				
			</div>

			<div class="col-xs-12" style="padding-right: 0px;">
				<table id="tableList" class="table table-bordered" style="width: 100%; font-size: 16px;">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 40%">DESTINATION</th>
							<th style="width: 20%">PLAN</th>
							<th style="width: 20%">CONFIRMED</th>
							<th style="width: 20%">NOT CONFIRMED</th>
						</tr>
					</thead>
					<tbody id="tableBodyList">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>


@endsection
@section('scripts')
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>

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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		
		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});
		fillTable();

	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){

		var period = $('#period').val();

		var data = {
			period : period,
		}


		$.get('{{ url("fetch/resume_shipping_order") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableBodyList').html("");

				var tableData = "";
				var total_plan = 0;
				var total_confirmed = 0;
				var total_not_confirmed = 0;

				for (var i = 0; i < result.data.length; i++) {

					tableData += '<tr>';

					tableData += '<td>'+ result.data[i].port_of_delivery +'</td>';
					tableData += '<td>'+ result.data[i].plan +'</td>';
					tableData += '<td>'+ result.data[i].confirmed +'</td>';

					if(result.data[i].not_confirmed > 0){
						tableData += '<td style="background-color: rgb(255, 204, 255);">'+ result.data[i].not_confirmed +'</td>';
					}else{
						tableData += '<td>'+ result.data[i].not_confirmed +'</td>';
					}

					total_plan += parseInt(result.data[i].plan);
					total_confirmed += parseInt(result.data[i].confirmed);
					total_not_confirmed += parseInt(result.data[i].not_confirmed);

					tableData += '</tr>';
				}

				var percen_confirmed = (total_confirmed/total_plan * 100).toFixed(2) + '%';
				var percen_not_confirmed = (total_not_confirmed/total_plan * 100).toFixed(2) + '%';

				$('#total_plan').text(total_plan);
				$('#total_confirmed').html(total_confirmed + ' <small style="font-size: 20px; text-style: italic;">('+ percen_confirmed +')</small>');
				$('#total_not_confirmed').html(total_not_confirmed + ' <small style="font-size: 20px; text-style: italic;">('+ percen_not_confirmed +')</small>');

				$('#tableBodyList').append(tableData);


				var date = [];
				var plan = [];
				var confirmed = [];
				var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

				for (var i = 0; i < result.ship_by_dates.length; i++) {
					var d = new Date(result.ship_by_dates[i].week_date)

					date.push(d.getDate()+'-'+monthNames[d.getMonth()]);
					plan.push(parseInt(result.ship_by_dates[i].plan) - parseInt(result.ship_by_dates[i].confirmed));
					confirmed.push(parseInt(result.ship_by_dates[i].confirmed));
				}

				console.log(date);
				console.log(plan);
				console.log(confirmed);


				Highcharts.chart('container1', {
					chart: {
						height: 300,
						type: 'column'
					},
					title: {
						text: 'Shipping Booking Management List ' + result.month,
						style: {
							textTransform: 'uppercase',
							fontSize: '20px'
						}
					},
					xAxis: {
						categories: date,
						labels: {
							rotation: -60
						}
						// plotLines: [{
						// 	color: '#FF0000',
						// 	width: 2,
						// 	value: 6
						// }]
					},
					yAxis: {
						min: 0,
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
							}
						},
						visible: false
					},
					legend: {
						enabled: true
					},
					exporting: {
						enabled: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								formatter: function() {
									if (this.y != 0) {
										return this.y;
									} else {
										return null;
									}
								}
							}
						}
					},
					series: [{
						name: 'NOT CONFIRMED',
						data: plan,
						color: '#dd4b39'
					}, {
						name: 'CONFIRMED',
						data: confirmed,
						color: '#00a65a'
					}]
				});

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
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}


</script>
@endsection