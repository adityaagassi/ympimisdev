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
		</div>
		<div class="col-xs-8" style="margin-top: 5px;">
			<input type="hidden" id="mrpc" value="{{ $mrpc }}">
			<input type="hidden" id="hpl" value="{{ $hpl }}">
			<table id="ququeTable" class="table table-bordered table-striped" width="100%">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">No</th>
						<th style="width: 10%;">Key C</th>
						<th style="width: 10%;">Key D</th>
						<th style="width: 10%;">Key E</th>
						<th style="width: 10%;">Key F</th>
						<th style="width: 10%;">Key G</th>
						<th style="width: 10%;">Key H</th>
						<th style="width: 10%;">Key J</th>
						<th style="width: 20%;">Created At</th>
					</tr>
				</thead>
				<tbody id="tableBody" style="font-size: 18px;  font-weight: bold; padding:0;">
				</tbody>
			</table>
		</div>

		<div class="col-xs-4">
			asd
			
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