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
	.dataTables_info,
	.dataTables_length {
		color: white;
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
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
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
						<th style="width: 1%; padding: 0;">#</th>
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
			<table id="lcq" class="table table-bordered" width="100%" style="margin-top: 5px; margin-bottom: 5px;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 1%; padding:0;">Shift</th>
						<th style="width: 3%; padding:0;">Key</th>
						<th style="width: 2%; padding:0;">Set</th>
						<th style="width: 2%; padding:0;">Reset</th>
						<th style="width: 1%; padding:0;">#</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color: #fffcb7;">1</td>
						<td style="background-color: #fffcb7;">AS LCQ</td>
						<td style="background-color: #fffcb7;" id="aslcqset1">0</td>
						<td style="background-color: #fffcb7;" id="aslcqreset1">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(1,'ASKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">1</td>
						<td style="background-color: #fffcb7;">TS LCQ</td>
						<td style="background-color: #fffcb7;" id="tslcqset1">0</td>
						<td style="background-color: #fffcb7;" id="tslcqreset1">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(1,'TSKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">1</td>
						<td style="background-color: #fffcb7;">AS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #fffcb7;" id="aspltreset1">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(1,'ASKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">1</td>
						<td style="background-color: #fffcb7;">TS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #fffcb7;" id="tspltreset1">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(1,'TSKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #f39c12; font-weight: bold;" colspan="2">Total</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totset1">0</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totreset1">0</td>
						<td style="background-color: #f39c12; font-weight: bold;"></td>
					</tr>
					<tr>
						<td style="background-color: #ffd8b7;">2</td>
						<td style="background-color: #ffd8b7;">AS LCQ</td>
						<td style="background-color: #ffd8b7;" id="aslcqset2">0</td>
						<td style="background-color: #ffd8b7;" id="aslcqreset2">0</td>
						<td style="background-color: #ffd8b7;"><button class="btn btn-info btn-xs" onclick="detailResult(2,'ASKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #ffd8b7;">2</td>
						<td style="background-color: #ffd8b7;">TS LCQ</td>
						<td style="background-color: #ffd8b7;" id="tslcqset2">0</td>
						<td style="background-color: #ffd8b7;" id="tslcqreset2">0</td>
						<td style="background-color: #ffd8b7;"><button class="btn btn-info btn-xs" onclick="detailResult(2,'TSKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #ffd8b7;">2</td>
						<td style="background-color: #ffd8b7;">AS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #ffd8b7;" id="aspltreset2">0</td>
						<td style="background-color: #ffd8b7;"><button class="btn btn-info btn-xs" onclick="detailResult(2,'ASKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #ffd8b7;">2</td>
						<td style="background-color: #ffd8b7;">TS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #ffd8b7;" id="tspltreset2">0</td>
						<td style="background-color: #ffd8b7;"><button class="btn btn-info btn-xs" onclick="detailResult(2,'TSKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #f39c12; font-weight: bold;" colspan="2">Total</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totset2">0</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totreset2">0</td>
						<td style="background-color: #f39c12; font-weight: bold;"></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">3</td>
						<td style="background-color: #fffcb7;">AS LCQ</td>
						<td style="background-color: #fffcb7;" id="aslcqset3">0</td>
						<td style="background-color: #fffcb7;" id="aslcqreset3">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(3,'ASKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">3</td>
						<td style="background-color: #fffcb7;">TS LCQ</td>
						<td style="background-color: #fffcb7;" id="tslcqset3">0</td>
						<td style="background-color: #fffcb7;" id="tslcqreset3">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(3,'TSKEY','LCQ')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">3</td>
						<td style="background-color: #fffcb7;">AS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #fffcb7;" id="aspltreset3">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(3,'ASKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #fffcb7;">3</td>
						<td style="background-color: #fffcb7;">TS PLT</td>
						<td style="background-color: black;">0</td>
						<td style="background-color: #fffcb7;" id="tspltreset3">0</td>
						<td style="background-color: #fffcb7;"><button class="btn btn-info btn-xs" onclick="detailResult(3,'TSKEY','PLT')">Detail</button></td>
					</tr>
					<tr>
						<td style="background-color: #f39c12; font-weight: bold;" colspan="2">Total</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totset3">0</td>
						<td style="background-color: #f39c12; font-weight: bold;" id="totreset3">0</td>
						<td style="background-color: #f39c12; font-weight: bold;"></td>
					</tr>
				</tbody>
			</table>
			<table id="tableFlanel" class="table table-bordered" width="100%">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th colspan="5" style="padding: 0;">Flanel Process</th>
					</tr>
					<tr>
						<th style="width: 2%; padding:0;">Tag</th>
						<th style="width: 2%; padding:0;">Model</th>
						<th style="width: 3%; padding:0;">Created</th>
					</tr>
				</thead>
				<tbody id="tableBodyFlanel">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="detailModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><b id="headModal"></b></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered" width="100%">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Model</th>
										<th>Key</th>
										<th>Set</th>
										<th>Reset</th>
									</tr>
								</thead>
								<tbody id="detailBody">
								</tbody>
								<tfoot>
									<tr style="background-color: #f39c12; font-weight: bold;">
										<th colspan="2" >Total</th>
										<th id="total1">0</th>
										<th id="total2">0</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
				</div>
			</div>
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

			$('#queueTable').DataTable().clear();
			$('#queueTable').DataTable().destroy();
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
				var r = value.remark
				queueTableBody += "<td>"+r.split('+')[0]+"</td>";	
				queueTableBody += "</tr>";
				no += 1;
			});
			$("#queueTableBody").append(queueTableBody);

			$('#queueTable').DataTable({
				'responsive':true,
				"pageLength": 40,
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

			var aslcqset1 = 0;
			var tslcqset1 = 0;

			var aslcqset2 = 0;
			var tslcqset2 = 0;

			var aslcqset3 = 0;
			var tslcqset3 = 0;

			var aslcqreset1 = 0;
			var tslcqreset1 = 0;
			var aspltreset1 = 0;
			var tspltreset1 = 0;

			var aslcqreset2 = 0;
			var tslcqreset2 = 0;
			var aspltreset2 = 0;
			var tspltreset2 = 0;

			var aslcqreset3 = 0;
			var tslcqreset3 = 0;
			var aspltreset3 = 0;
			var tspltreset3 = 0;

			var totset1 = 0;
			var totreset1 = 0;
			var totset2 = 0;
			var totreset2 = 0;
			var totset3 = 0;
			var totreset3 = 0;

			$('#aslcqset1').html("");
			$('#tslcqset1').html("");
			$('#aslcqset2').html("");
			$('#tslcqset2').html("");
			$('#aslcqset3').html("");
			$('#tslcqset3').html("");

			$('#aslcqreset1').html("");
			$('#tslcqreset1').html("");
			$('#aspltreset1').html("");
			$('#tspltreset1').html("");

			$('#aslcqreset2').html("");
			$('#tslcqreset2').html("");
			$('#aspltreset2').html("");
			$('#tspltreset2').html("");

			$('#aslcqreset3').html("");
			$('#tslcqreset3').html("");
			$('#aspltreset3').html("");
			$('#tspltreset3').html("");

			$('#totset1').html("");
			$('#totreset1').html("");
			$('#totset2').html("");
			$('#totreset2').html("");
			$('#totset3').html("");
			$('#totreset3').html("");

			$.each(result.barrel_board, function(index, value){
				if(value.hpl == 'ASKEY' && value.status == 'set' && value.shift == 1){
					aslcqset1 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'set' && value.shift == 1){
					tslcqset1 += parseInt(value.qty);
				}
				if(value.hpl == 'ASKEY' && value.status == 'set' && value.shift == 2){
					aslcqset2 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'set' && value.shift == 2){
					tslcqset2 += parseInt(value.qty);
				}
				if(value.hpl == 'ASKEY' && value.status == 'set' && value.shift == 3){
					aslcqset3 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'set' && value.shift == 3){
					tslcqset3 += parseInt(value.qty);
				}

				if(value.hpl == 'ASKEY' && value.status == 'reset' && value.shift == 1){
					aslcqreset1 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'reset' && value.shift == 1){
					tslcqreset1 += parseInt(value.qty);
				}
				if(value.hpl == 'ASKEY' && value.status.toLowerCase() == 'plt' && value.shift == 1){
					aspltreset1 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status.toLowerCase() == 'plt' && value.shift == 1){
					tspltreset1 += parseInt(value.qty);
				}

				if(value.hpl == 'ASKEY' && value.status == 'reset' && value.shift == 2){
					aslcqreset2 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'reset' && value.shift == 2){
					tslcqreset2 += parseInt(value.qty);
				}
				if(value.hpl == 'ASKEY' && value.status.toLowerCase() == 'plt' && value.shift == 2){
					aspltreset2 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status.toLowerCase() == 'plt' && value.shift == 2){
					tspltreset2 += parseInt(value.qty);
				}

				if(value.hpl == 'ASKEY' && value.status == 'reset' && value.shift == 3){
					aslcqreset3 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status == 'reset' && value.shift == 3){
					tslcqreset3 += parseInt(value.qty);
				}
				if(value.hpl == 'ASKEY' && value.status.toLowerCase() == 'plt' && value.shift == 3){
					aspltreset3 += parseInt(value.qty);
				}
				if(value.hpl == 'TSKEY' && value.status.toLowerCase() == 'plt' && value.shift == 3){
					tspltreset3 += parseInt(value.qty);
				}
			});

			$('#aslcqset1').html(aslcqset1);
			$('#tslcqset1').html(tslcqset1);
			$('#aslcqset2').html(aslcqset2);
			$('#tslcqset2').html(tslcqset2);
			$('#aslcqset3').html(aslcqset3);
			$('#tslcqset3').html(tslcqset3);

			$('#aslcqreset1').html(aslcqreset1);
			$('#tslcqreset1').html(tslcqreset1);
			$('#aspltreset1').html(aspltreset1);
			$('#tspltreset1').html(tspltreset1);

			$('#aslcqreset2').html(aslcqreset2);
			$('#tslcqreset2').html(tslcqreset2);
			$('#aspltreset2').html(aspltreset2);
			$('#tspltreset2').html(tspltreset2);

			$('#aslcqreset3').html(aslcqreset3);
			$('#tslcqreset3').html(tslcqreset3);
			$('#aspltreset3').html(aspltreset3);
			$('#tspltreset3').html(tspltreset3);

			totset1 = aslcqset1+tslcqset1;
			totreset1 = aslcqreset1+tslcqreset1+aspltreset1+tspltreset1;
			totset2 = aslcqset2+tslcqset2;
			totreset2 = aspltreset2+tspltreset2+aslcqreset2+tslcqreset2;
			totset3 = aslcqset3+tslcqset3;
			totreset3 = aslcqreset3+tslcqreset3+aspltreset3+tspltreset3;

			$('#totset1').html(totset1);
			$('#totreset1').html(totreset1);
			$('#totset2').html(totset2);
			$('#totreset2').html(totreset2);
			$('#totset3').html(totset3);
			$('#totreset3').html(totreset3);

			$('#tableFlanel').DataTable().clear();
			$('#tableFlanel').DataTable().destroy();
			$('#tableBodyFlanel').html("");
			var tableBodyFlanel = "";

			no2 = 1;

			$.each(result.flanels, function(index, value){

				if (no2 % 2 === 0 ) {
					color2 = 'style="background-color: #fffcb7"';
				} else {
					color2 = 'style="background-color: #ffd8b7"';
				}

				tableBodyFlanel += "<tr "+color2+">";
				tableBodyFlanel += "<td>"+value.tag+"</td>";
				tableBodyFlanel += "<td>"+value.model+" "+value.key+"</td>";
				tableBodyFlanel += "<td>"+value.created_at+"</td>";	
				tableBodyFlanel += "</tr>";
				no2 += 1;
			});
			$('#tableBodyFlanel').append(tableBodyFlanel);

			$('#tableFlanel').DataTable({
				'responsive':true,
				"pageLength": 19,
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

function detailResult(shift, key, surface) {
	var hpl = $('#hpl').val().split(',');
	var data = {
		shift:shift,
		key:key,
		surface:surface,
		mrpc : $('#mrpc').val(),
		hpl : hpl
	}

	$.get('{{ url("fetch/middle/barrel_result") }}', data, function(result, status, xhr){
		if (key == 'ASKEY')
			var longName = "Alto Saxophone";
		else if  (key == 'TSKEY')
			var longName = "Tenor Saxophone";

		$("#headModal").text(longName+" Shift "+shift);
		$("#detailBody").empty();
		var body = "", total1 = 0, total2 = 0, no = 1;
		$.each(result.datas, function(index, value){

			if (no % 2 === 0 ) {
				color = 'style="background-color: #fffcb7"';
			} else {
				color = 'style="background-color: #ffd8b7"';
			}
			body += "<tr "+color+">";
			body += "<td>"+value.model+"</td>";
			body += "<td>"+value.key+"</td>";
			body += "<td>"+value.set+"</td>";
			total1 += parseInt(value.set);
			if (value.plt == "0") {
				body += "<td>"+value.reset+"</td>";
				total2 += parseInt(value.reset);
			}
			else {
				body += "<td>"+value.plt+"</td>";
				total2 += parseInt(value.plt);
			}
			body += "</tr>";
			no++;
		});

		$("#detailBody").append(body);
		$("#total1").text(total1);
		$("#total2").text(total2);
	});

	
	$("#detailModal").modal("show");
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