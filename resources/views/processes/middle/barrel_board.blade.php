@extends('layouts.display')
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
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> ??</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<input type="hidden" id="mrpc" value="{{ $mrpc }}">
		<input type="hidden" id="hpl" value="{{ $hpl }}">
		<div class="col-xs-8" style="padding-right: 0;">
			<table id="queueTable" class="table table-bordered">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 1%; padding: 0;">No</th>
						<th style="width: 3%; padding: 0;">Model</th>
						<th style="width: 1%; padding: 0;">Qty</th>
						<th style="width: 2%; padding: 0;">Key C</th>
						<th style="width: 2%; padding: 0;">Key D</th>
						<th style="width: 2%; padding: 0;">Key E</th>
						<th style="width: 2%; padding: 0;">Key F</th>
						<th style="width: 2%; padding: 0;">Key G</th>
						<th style="width: 2%; padding: 0;">Key H</th>
						<th style="width: 2%; padding: 0;">Key J</th>
						<th style="width: 6%; padding: 0;">Created At</th>
					</tr>
				</thead>
				<tbody id="queueTableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
		<div class="col-xs-4">
			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center;" class="form-control" id="qr" placeholder="Scan QR Here...">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<center><h4 class="text-yellow">Lacquering (LCQ)</h4></center>
			<table id="lcq" class="table table-bordered" width="100%" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 10%; padding:0;">Model</th>
						<th style="width: 10%; padding:0;">Key</th>
						<th style="width: 10%; padding:0;">Set</th>
						<th style="width: 10%; padding:0;">Reset</th>
					</tr>
				</thead>
				<tbody id="tb_lcq">
				</tbody>
				<tfoot style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th colspan="2">Total</th>
						<th id="total_set">0</th>
						<th id="total_reset">0</th>
					</tr>
				</tfoot>
			</table>
			<center><h4 class="text-yellow">Plating (PLT)</h4></center>
			<table id="plt" class="table table-bordered" width="100%">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 10%; padding:0;">Model</th>
						<th style="width: 10%; padding:0;">Key</th>
						<th style="width: 10%; padding:0;">Qty</th>
					</tr>
				</thead>
				<tbody id="tb_plt">
				</tbody>
				<tfoot style="background-color: rgb(126,86,134); color: #FFD700;">
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
		var hpl = $('#hpl').val().split(',');
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : hpl,
		}
		$.get('{{ url("fetch/middle/barrel_board") }}', data, function(result, status, xhr){
			$('#tb_plt').empty();
			$('#tb_lcq').empty();

			$('#queueTable').DataTable().clear();
			$('#queueTable').DataTable().destroy();
			var set = 0, reset = 0, plt = 0;
			var tb_plt = "";
			var tb_lcq = "";
			var no1 = 1, no2 = 1;
			$.each(result.barrel_board, function(index, value) {
				var color = "";

				if (no1 % 2 === 0 ) {
					color1 = 'style="background-color: #fffcb7"';
				} else {
					color1 = 'style="background-color: #ffd8b7"';
				}

				if (no2 % 2 === 0 ) {
					color2 = 'style="background-color: #fffcb7"';
				} else {
					color2 = 'style="background-color: #ffd8b7"';
				}

				if (value.plt != 0 ) {
					tb_plt += "<tr "+color2+"><td>"+value.model+"</td><td>"+value.key+"</td><td>"+value.plt+"</td></tr>";

					plt += parseInt(value.plt);
					no2++;
				} else {
					tb_lcq += "<tr "+color1+"><td>"+value.model+"</td><td>"+value.key+"</td><td>"+value.set+"</td><td>"+value.reset+"</td></tr>";

					set += parseInt(value.set);
					reset += parseInt(value.reset);
					no1++;
				}

				$("#total_set").text(set);
				$("#total_reset").text(reset);
				$("#total_plt").text(plt);
			});

			$("#tb_plt").append(tb_plt);
			$("#tb_lcq").append(tb_lcq);

			$('#queueTableBody').html("");
			var queueTableBody = "";
			var no = 1
			$.each(result.barrel_queues, function(index, value){
				if (no % 2 === 0 ) {
					color = 'style="background-color: #fffcb7"';
				} else {
					color = 'style="background-color: #ffd8b7"';
				}

				var k = value.key;
				var key = k.substr(0,1);
				queueTableBody += "<tr "+color+">";
				queueTableBody += "<td>"+no+"</td>";
				queueTableBody += "<td>"+value.model+" "+value.surface+"</td>";
				queueTableBody += "<td>"+value.quantity+"</td>";
				if(key == 'C'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}
				if(key == 'D'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}
				if(key == 'E'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}
				if(key == 'F'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}
				if(key == 'G'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}

				if(key == 'H'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}

				if(key == 'J'){
					queueTableBody += "<td>"+value.key+"</td>";					
				}
				else{
					queueTableBody += "<td>-</td>";					
				}
				queueTableBody += "<td>"+value.created_at+"</td>";	
				queueTableBody += "</tr>";
				no += 1;
			});
			$("#queueTableBody").append(queueTableBody);

			$('#queueTable').DataTable({
				'responsive':true,
				"pageLength": 30,
				'paging': true,
				'lengthChange': false,
				'searching': false,
				'ordering': false,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
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