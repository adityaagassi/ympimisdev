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
		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div class="row">
				<div class="col-xs-3" style="padding-right: 10px;">
					<div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-ship"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">PLAN</span>
							<span class="info-box-number" style="font-size: 2vw;" id="total_plan"></span>
						</div>
					</div>
					<div class="info-box">
						<span class="info-box-icon bg-green"><i class="glyphicon glyphicon-ok"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">CONFIRMED</span>
							<span class="info-box-number" style="font-size: 2vw;" id="total_confirmed"></span>
						</div>
					</div>
					<div class="info-box">
						<span class="info-box-icon bg-red"><i class="glyphicon glyphicon-remove"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">NOT CONFIRMED</span>
							<span class="info-box-number" style="font-size: 2vw;" id="total_not_confirmed"></span>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px; padding-right: 5px;">
						<a href="{{ url("/index/shipping_agency") }}" class="btn btn-primary" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Shipping Line</a>
					</div>
					<div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
						<a href="{{ url("/index/shipping_order") }}" class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Booking List</a>
					</div>
				</div>
				<div class="col-xs-9" style="padding-left: 0;">
					<div id="container1" style="height: 400px;"></div>				
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
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
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="title_modal" style="font-weight: bold; font-size: 1.5vw;"></span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-8 col-xs-offset-2" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%; vertical-align: middle;" colspan="8">RESUME</th>
								</tr>
								<tr>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">YCJ Ref No.</th>
									<th style="width: 1%; vertical-align: middle;" rowspan="2">Shipper</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Port Loading</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Port of Delivery</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Country</th>
									<th style="width: 2%; vertical-align: middle;" colspan="3">Container Size</th>
								</tr>
								<tr>
									<th style="width: 1%;">40HC</th>
									<th style="width: 1%;">40'</th>
									<th style="width: 1%;">20'</th>									
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
						</table>
					</div>
					<div class="col-xs-12" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetailRef">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%; vertical-align: middle;" colspan="13">BOOKING DETAILS</th>
								</tr>
								<tr>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">YCJ Ref No.</th>
									<th style="width: 1%; vertical-align: middle;" rowspan="2">Shipper</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Port Loading</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Port of Delivery</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Country</th>
									<th style="width: 2%; vertical-align: middle;" colspan="3">Container Size</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Booking No. or B/L No.</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Carier</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Nomination</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Application Rate</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Status</th>
								</tr>
								<tr>
									<th style="width: 1%;">40HC</th>
									<th style="width: 1%;">40'</th>
									<th style="width: 1%;">20'</th>									
								</tr>
							</thead>
							<tbody id="tableDetailRefBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


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
		setInterval(fillTable, 60*60*1000);


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

					if(result.data[i].confirmed == result.data[i].plan){
						tableData += '<td style="background-color: rgb(204, 255, 255);">'+ result.data[i].confirmed +'</td>';
					}else{
						tableData += '<td>'+ result.data[i].confirmed +'</td>';
					}

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

				Highcharts.chart('container1', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Shipping Booking Management List '
					},
					subtitle: {
						text: result.month
					},
					xAxis: {
						categories: date
					},
					yAxis: {
						enabled: true,
						title: {
							enabled: true,
							text: "Qty Container"
						},
						tickInterval: 1
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
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 1,
							borderColor: '#212121',
							dataLabels: {
								enabled: true,
								style:{
									textOutline: false
								},
								formatter: function() {
									if (this.y != 0) {
										return this.y;
									} else {
										return null;
									}
								}
							},
						}, 
						series: {
							cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(event.point.category);

									}
								}
							},
						},
					},
					series: [{
						name: 'Not Confirmed',
						data: plan,
						color: '#dd4b39'
					}, {
						name: 'Confirmed',
						data: confirmed,
						color: '#00a65a'
					}]
				});

			}
		});
	}

	function showDetail(category) {
		var period = $('#period').val();
		var date = category;

		var data = {
			period : period,
			date : date
		}

		$.get('{{ url("fetch/resume_shipping_order_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableDetailBody').html("");
				$('#tableDetailRefBody').html("");

				$('#title_modal').text('Shipping Booking Management Booking Details on ' + result.st_date);

				var detail = '';
				$.each(result.resume, function(key, value){
					var color = '';
					if(value.status){
						color = 'style="background-color: rgb(204, 255, 255);"';
					}else{
						color = 'style="background-color: rgb(255, 204, 255);"';
					}

					detail += '<tr>';
					detail += '<td '+color+'>'+value.ycj_ref_number+'</td>';
					detail += '<td '+color+'>'+value.shipper+'</td>';
					detail += '<td '+color+'>'+value.port_loading+'</td>';
					detail += '<td '+color+'>'+value.port_of_delivery+'</td>';
					detail += '<td '+color+'>'+value.country+'</td>';
					detail += '<td '+color+'>'+(value.fortyhc || '' )+'</td>';
					detail += '<td '+color+'>'+(value.fourty || '' )+'</td>';
					detail += '<td '+color+'>'+(value.twenty || '' )+'</td>';
					detail += '</tr>';
				});
				$('#tableDetailBody').append(detail);


				var detail = '';
				$.each(result.detail, function(key, value){
					var color = '';
					console.log(value.status);
					if(value.status == 'BOOKING CONFIRMED'){
						color = 'style="background-color: rgb(204, 255, 255);"';
					}


					detail += '<tr>';
					detail += '<td '+color+'>'+value.ycj_ref_number+'</td>';
					detail += '<td '+color+'>'+value.shipper+'</td>';
					detail += '<td '+color+'>'+value.port_loading+'</td>';
					detail += '<td '+color+'>'+value.port_of_delivery+'</td>';
					detail += '<td '+color+'>'+value.country+'</td>';
					detail += '<td '+color+'>'+(value.fortyhc || '' )+'</td>';
					detail += '<td '+color+'>'+(value.fourty || '' )+'</td>';
					detail += '<td '+color+'>'+(value.twenty || '' )+'</td>';
					detail += '<td '+color+'>'+value.booking_number+'</td>';
					detail += '<td '+color+'>'+value.carier+'</td>';
					detail += '<td '+color+'>'+value.nomination+'</td>';
					detail += '<td '+color+'>'+value.application_rate+'</td>';
					detail += '<td '+color+'>'+value.status+'</td>';
					detail += '</tr>';
				});
				$('#tableDetailRefBody').append(detail);


				$('#modalDetail').modal('show');
			}
			else{
				openErrorGritter('Error!', result.message);
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