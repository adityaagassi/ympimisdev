@extends('layouts.display')
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
		
	</h1>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px; padding-left: 0px;">
			<div class="col-xs-2">
				<div class="input-group date pull-right" style="text-align: center;">
					<div class="input-group-addon bg-green">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control monthpicker" name="period" id="period" placeholder="Select Period">	
				</div>
			</div>

			<div class="col-xs-2" style="padding: 0px;">
				<button id="search" onclick="fillTable()" class="btn btn-primary">Search</button>
			</div>

			<div class="col-xs-4 pull-right" style="padding-right: 0px;">
				<div class="col-xs-4 pull-right" style="padding: 0px;">
					<a href="{{ url("/index/shipping_agency") }}" class="btn btn-info" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Shipping Line</a>
				</div>
				<div class="col-xs-4 pull-right" style="margin-right: 10px; padding: 0px;">
					<a href="{{ url("/index/shipping_order") }}" class="btn btn-info" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Booking List</a>
				</div>
			</div>
			


		</div>

		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div class="row">
				<div class="col-xs-12" style="">
					<div class="col-xs-3" style="padding-left: 0px;">
						<div class="info-box" style="min-height: 75px;">
							<span class="info-box-icon" style="background-color: #605ca8; color: white; height: 75px;"><i class="glyphicon glyphicon-tasks"></i></span>

							<div class="info-box-content">
								<span class="info-box-text">PLAN <span style="color: rgba(96, 92, 168);">計画</span></span>
								<span class="info-box-number" style="font-size: 2vw;" id="total_plan"></span>
							</div>
						</div>
					</div>

					<div class="col-xs-3" style="">
						<div class="info-box" style="min-height: 75px;">
							<span class="info-box-icon bg-green" style="height: 75px;"><i class="fa fa-ship"></i></span>

							<div class="info-box-content">
								<span class="info-box-text">ETD SUB <span style="color: rgba(96, 92, 168);"> ?? </span></span>
								<span class="info-box-number" style="font-size: 2vw;" id="total_etd"></span>
							</div>
						</div>
					</div>

					<div class="col-xs-3" style="">
						<div class="info-box" style="min-height: 75px;">
							<span class="info-box-icon" style="background-color: #455DFF; color: white; height: 75px;"><i class="fa fa-truck"></i></span>

							<div class="info-box-content">
								<span class="info-box-text">ON BOARD <span style="color: rgba(96, 92, 168);"> ?? </span></span>
								<span class="info-box-number" style="font-size: 2vw;" id="total_on_board"></span>
							</div>
						</div>
					</div>

					<div class="col-xs-3" style="padding-right: 0px;">
						<div class="info-box" style="min-height: 75px;">
							<span class="info-box-icon" style="background-color: #CCFFFF; color: #212121; height: 75px;"><i class="glyphicon glyphicon-ok"></i></span>

							<div class="info-box-content">
								<span class="info-box-text">CONFIRMED <span style="color: rgba(96, 92, 168);">確保済み</span></span>
								<span class="info-box-number" style="font-size: 2vw;" id="total_confirmed"></span>
							</div>
						</div>
					</div>
					
					
				</div>
				<div class="col-xs-12" style="">
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
								<th style="width: 3%">DESTINATION<br><span style="color: purple;">仕向け地</span></th>
								<th style="width: 1%">PLAN<br><span style="color: purple;">計画</span></th>
								<th style="width: 1%">ETD SUB<br><span style="color: purple;">??</span></th>
								<th style="width: 1%">ON BOARD<br><span style="color: purple;">??</span></th>
								<th style="width: 1%">CONFIRMED<br><span style="color: purple;">確保済み</span></th>
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
									<th style="width: 2%; vertical-align: middle;" colspan="6">RESUME</th>
								</tr>
								<tr>
									<th style="width: 2%; vertical-align: middle;">YCJ Ref No.</th>
									<th style="width: 1%; vertical-align: middle;">Shipper</th>
									<th style="width: 2%; vertical-align: middle;">Port Loading</th>
									<th style="width: 4%; vertical-align: middle;">Port of Delivery</th>
									<th style="width: 4%; vertical-align: middle;">Country</th>
									<th style="width: 4%; vertical-align: middle;">Plan (in TEUs)</th>
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
									<th style="width: 2%; vertical-align: middle;" colspan="14">BOOKING DETAILS</th>
								</tr>
								<tr>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">YCJ Ref No.</th>
									<th style="width: 1%; vertical-align: middle;" rowspan="2">Shipper</th>
									<th style="width: 2%; vertical-align: middle;" rowspan="2">Port Loading</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Port of Delivery</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Country</th>
									<th style="width: 4%; vertical-align: middle;" rowspan="2">Plan<br>(in TEUs)</th>
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
				var total_etd = 0;
				var total_on_board = 0;
				var total_confirmed = 0;

				for (var i = 0; i < result.data.length; i++) {

					tableData += '<tr>';

					tableData += '<td>'+ result.data[i].port_of_delivery +'</td>';
					tableData += '<td>'+ result.data[i].plan +'</td>';

					if(result.data[i].departed == result.data[i].plan){
						tableData += '<td style="background-color: rgb(204, 255, 255);">'+ result.data[i].departed +'</td>';
					}else{
						tableData += '<td style="background-color: rgb(255, 204, 255);">'+ result.data[i].departed +'</td>';
					}

					
					tableData += '<td>'+ result.data[i].on_board +'</td>';
					
					tableData += '<td>'+ result.data[i].confirmed +'</td>';
					
					tableData += '</tr>';
				}

				

				
				$('#tableBodyList').append(tableData);


				var date = [];
				var plan = [];
				var departed = [];
				var on_board = [];
				var stuffing = [];
				var confirmed = [];


				var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];


				for (var i = 0; i < result.ship_by_dates.length; i++) {
					var d = new Date(result.ship_by_dates[i].week_date)
					date.push(d.getDate()+'-'+monthNames[d.getMonth()]);

					plan.push(parseInt(result.ship_by_dates[i].plan));
					departed.push(parseInt(result.ship_by_dates[i].departed));
					on_board.push(parseInt(result.ship_by_dates[i].on_board));
					stuffing.push(parseInt(result.ship_by_dates[i].stuffing));
					confirmed.push(parseInt(result.ship_by_dates[i].confirmed));

					total_plan += parseInt(result.ship_by_dates[i].plan);
					total_etd += parseInt(result.ship_by_dates[i].departed);
					total_on_board += (parseInt(result.ship_by_dates[i].on_board) + parseInt(result.ship_by_dates[i].stuffing));
					total_confirmed += parseInt(result.ship_by_dates[i].confirmed);

				}
				
				var percen_etd = (total_etd/total_plan * 100).toFixed(2) + '%';
				var percen_on_board = (total_on_board/total_plan * 100).toFixed(2) + '%';
				var percen_confirmed = (total_confirmed/total_plan * 100).toFixed(2) + '%';


				$('#total_plan').html(total_plan + ' <small style="font-size: 20px; text-style: italic;">TEUs</small>');
				$('#total_etd').html(total_etd + ' <small style="font-size: 20px; text-style: italic;">TEUs ('+ percen_etd +')</small>');
				$('#total_on_board').html(total_on_board + ' <small style="font-size: 20px; text-style: italic;">TEUs ('+ percen_on_board +')</small>');
				$('#total_confirmed').html(total_confirmed + ' <small style="font-size: 20px; text-style: italic;">TEUs ('+ percen_confirmed +')</small>');


				Highcharts.chart('container1', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Shipping Booking Management List ('+result.month+')<br><span style="color: rgba(96, 92, 168);">船便予約管理リスト 「'+result.year+'年 '+result.mon+'月」</span>'
					},
					xAxis: {
						categories: date
					},
					yAxis: {
						enabled: true,
						title: {
							enabled: true,
							text: "Quantity Container in TEUs<br>(コンテナ台数)"
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
						pointFormat: '{series.name}: {point.y} TEUs<br/>Total: {point.stackTotal} TEUs'
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
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
					series: [
					{
						name: 'Confirmed (確保済み)',
						data: confirmed,
						stack: 'actual',
						color: '#CCFFFF'
					},{
						name: 'Stuffing ()',
						data: stuffing,
						stack: 'actual',
						color: '#FFFF54'
					},{
						name: 'On Board ()',
						data: on_board,
						stack: 'actual',
						color: '#455DFF'
					},{
						name: 'ETD SUB ()',
						data: departed,
						stack: 'actual',
						color: '#00a65a'
					},{
						name: 'Plan (計画)',
						data: plan,
						stack: 'plan',
						color: '#605ca8'
					}
					]
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
			var concat = '';
			$.each(result.detail, function(key, value){
				var color = '';
				var check = 'BOOKING CONFIRMED';
				var status = value.status;

				if(status.includes(check)){
					concat += value.ycj_ref_number;
					color = 'style="background-color: rgb(204, 255, 255);"';
				}

				detail += '<tr>';
				detail += '<td '+color+'>'+value.ycj_ref_number+'</td>';
				detail += '<td '+color+'>'+value.shipper+'</td>';
				detail += '<td '+color+'>'+value.port_loading+'</td>';
				detail += '<td '+color+'>'+value.port_of_delivery+'</td>';
				detail += '<td '+color+'>'+value.country+'</td>';
				detail += '<td '+color+'>'+value.plan+'</td>';
				detail += '<td '+color+'>'+(value.fortyhc || '' )+'</td>';
				detail += '<td '+color+'>'+(value.fourty || '' )+'</td>';
				detail += '<td '+color+'>'+(value.twenty || '' )+'</td>';
				detail += '<td '+color+'>'+(value.booking_number || '')+'</td>';
				detail += '<td '+color+'>'+value.carier+'</td>';
				detail += '<td '+color+'>'+value.nomination+'</td>';
				detail += '<td '+color+'>'+value.application_rate+'</td>';
				detail += '<td '+color+'>'+value.status+'</td>';
				detail += '</tr>';
			});
			$('#tableDetailRefBody').append(detail);


			var detail = '';
			$.each(result.resume, function(key, value){
				var color = '';
				if(concat.includes(value.ycj_ref_number)){
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
				detail += '<td '+color+'>'+value.plan+'</td>';
				detail += '</tr>';
			});
			$('#tableDetailBody').append(detail);


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