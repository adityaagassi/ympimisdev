@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>

		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="input-group">
				<input type="text" style="text-align: center; font-size: 22px; height: 40px;" class="form-control" id="qr" placeholder="Scan QR Here...">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<input type="hidden" id="mrpc" value="{{ $mrpc }}">
			<input type="hidden" id="hpl" value="{{ $hpl }}">
			<center><p style="margin: 10px 0 0 0; font-size: 20px">20 Juni 2019</p></center>
		</div>
		<div class="col-xs-4" style="margin-top: 5px;">
			<center style="font-size: 20px; margin: 2px">Laquering (LCQ)</center>
			<table id="lcq" class="table table-bordered table-striped" width="100%">
				<thead style="background-color: rgba(126,86,134,.7); font-size: 18px">
					<tr>
						<th style="width: 10%;">Model</th>
						<th style="width: 10%;">Key</th>
						<th style="width: 10%;">Set</th>
						<th style="width: 10%;">Reset</th>
					</tr>
				</thead>
				<tbody id="tb_lcq" style="font-size: 16px;  font-weight: bold; padding:0;">
				</tbody>
				<tfoot style="font-size: 18px; background-color: #ddd">
					<tr>
						<th colspan="2">Total</th>
						<th id="total_set">0</th>
						<th id="total_reset">0</th>
					</tr>
				</tfoot>
			</table>
		</div>
		`
		<div class="col-xs-4" style="margin-top: 5px;">
			<center style="font-size: 20px; margin: 2px">Plating (PLT)</center>
			<table id="plt" class="table table-bordered table-striped" width="100%">
				<thead style="background-color: rgba(126,86,134,.7); font-size: 18px">
					<tr>
						<th style="width: 10%;">Model</th>
						<th style="width: 10%;">Key</th>
						<th style="width: 10%;">Qty</th>
					</tr>
				</thead>
				<tbody id="tb_plt" style="font-size: 16px;  font-weight: bold; padding:0;">
				</tbody>
				<tfoot style="font-size: 18px; background-color: #ddd">
					<tr>
						<th colspan="2">Total</th>
						<th id="total_plt">0</th>
					</tr>
				</tfoot>
			</table>
			
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#qr').focus();

		$('#qr').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#qr").val().length > 7){
					scanQr($("#qr").val());
					return false;
				}
				else{
					openErrorGritter('Error!', 'QR code invalid.');
					audio_error.play();
					$("#qr").val("");
					$('#qr').focus();
				}
			}
		});
		get_barrel_board();
		setInterval(get_barrel_board, 10000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanQr(qr){
		data = {
			qr:qr
		}
		
		$.post('{{ url("scan/middle/barrel") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$("#qr").val("");
					$("#qr").focus();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$("#qr").val("");
					$("#qr").focus();
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
				$("#qr").val("");
				$("#qr").focus();
			}
		});
	}

	function get_barrel_board() {
		$('#tb_plt').empty();
		$('#tb_lcq').empty();
		$.get('{{ url("fetch/middle/barrel_board") }}', function(result, status, xhr){
			var set = 0, reset = 0, plt = 0;
			var tb_plt = "";
			var tb_lcq = "";
			$.each(result.barrel_board, function(index, value) {
				if (value.plt != 0 ) {
					tb_plt += "<tr><td>"+value.model+"</td><td>"+value.key+"</td><td>"+value.plt+"</td></tr>";

					plt += parseInt(value.plt);
				} else {
					tb_lcq += "<tr><td>"+value.model+"</td><td>"+value.key+"</td><td>"+value.set+"</td><td>"+value.reset+"</td></tr>";

					set += parseInt(value.set);
					reset += parseInt(value.reset);
				}

				$("#total_set").text(set);
				$("#total_reset").text(reset);
				$("#total_plt").text(plt);
			});
			$("#tb_plt").append(tb_plt);
			$("#tb_lcq").append(tb_lcq);
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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};
</script>
@endsection