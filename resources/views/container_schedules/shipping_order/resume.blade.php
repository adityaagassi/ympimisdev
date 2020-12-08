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
	<div class="col-xs-2 input-group date" style="text-align: center;">
		<div class="input-group-addon bg-green">
			<i class="fa fa-calendar"></i>
		</div>
		<input type="text" class="form-control monthpicker" name="period" id="period" onchange="fillTable()" placeholder="Select Period">	
	</div>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">		
		<div class="col-xs-8 col-xs-offset-2">
			<h2 style="text-align: center; text-transform: uppercase;" id="table_header">{{ $title }}</h2>
			<div class="row" style="margin-top: 3%;">
				<div class="col-xs-4">
					<div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-ship"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">PLAN</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_plan"></span>
						</div>
					</div>
				</div>

				<div class="col-xs-4">
					<div class="info-box">
						<span class="info-box-icon bg-green"><i class="glyphicon glyphicon-ok"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">CONFIRMED</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_confirmed"></span>
						</div>
					</div>
				</div>

				<div class="col-xs-4">
					<div class="info-box">
						<span class="info-box-icon bg-red"><i class="glyphicon glyphicon-remove"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">NOT CONFIRMED</span>
							<span class="info-box-number" style="font-size: 30px;" id="total_not_confirmed"></span>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12" style="padding: 0px;">
				<table id="tableList" class="table table-bordered" style="width: 100%; font-size: 16px;">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 1%;">DESTINATION</th>
							<th style="width: 1%;">PLAN</th>
							<th style="width: 1%;">CONFIRMED</th>
							<th style="width: 1%;">NOT CONFIRMED</th>
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

	$('#uploadReservation').on('submit', function(event){
		event.preventDefault();
		var formdata = new FormData(this);

		$("#loading").show();

		$.ajax({
			url:"{{ url('fetch/shipping_order/upload_ship_reservation') }}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					$('#upload_period').val('');
					$('#upload_file').val('');
					$('#modalUpload').modal('hide');
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', 'Fatal Error');
			}
		});
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

					total_plan += result.data[i].plan;
					total_confirmed += result.data[i].confirmed;
					total_not_confirmed += result.data[i].not_confirmed;

					tableData += '</tr>';
				}

				var percen_confirmed = (total_confirmed/total_plan * 100).toFixed(2) + '%';
				var percen_not_confirmed = (total_not_confirmed/total_plan * 100).toFixed(2) + '%';

				$('#total_plan').text(total_plan);
				$('#total_confirmed').html(total_confirmed + ' <small style="font-size: 20px; text-style: italic;">('+ percen_confirmed +')</small>');
				$('#total_not_confirmed').html(total_not_confirmed + ' <small style="font-size: 20px; text-style: italic;">('+ percen_not_confirmed +')</small>');
				$('#table_header').text('YMPI Booking Management List ' + result.month);

				$('#tableBodyList').append(tableData);

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