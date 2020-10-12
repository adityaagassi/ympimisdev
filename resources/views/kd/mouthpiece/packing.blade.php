@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 2vw;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: RGB(252, 248, 227);
		font-weight: bold;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="kd_number">
		<div class="col-xs-6 col-md-offset-3" id="checksheet">
			<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
				<i class="glyphicon glyphicon-qrcode" style="font-size: 3vw; background-color: orange;"></i>
			</div>
			<input type="text" style="text-align: center; font-size: 3vw; height: 100px;" class="form-control" id="qr_checksheet" placeholder="Scan QR Checksheet">
		</div>
		<div id="packing">
			<div class="col-xs-3">
				<table id="operatorTable" class="table table-bordered table-stripped" style="margin-bottom: 26px;">
					<thead style="background-color: #00c0ef;">
						<tr>
							<th style="width: 1%; font-size: 1.5vw;" id="emp_id"></th>
						</tr>
						<tr>
							<th style="width: 1%; font-size: 1.5vw;" id="emp_name"></th>
						</tr>
					</thead>
				</table>
				<center>
					<div style="background-color: #00c0ef;">
						<span style="font-weight: bold; color: white; font-size: 2vw;">1. SCAN INNER</span><br>
					</div>
					<input type="text" style="text-align: center; width: 100%; font-size: 2vw; margin-bottom: 10px;" id="barcode_inner" placeholder="Scan Barcode">
					<div style="background-color: orange;">
						<span style="font-weight: bold; color: white; font-size: 2vw;">2. SCAN OUTER</span><br>
					</div>
					<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="barcode_outer" placeholder="Scan Barcode">
				</center>
			</div>
			<div class="col-xs-9">
				<table id="checksheetTable" class="table table-bordered table-stripped">
					<thead style="background-color: #00c0ef;">
						<tr>
							<th style="width: 1%; font-size: 1.5vw;">Nomor Checksheet</th>
							<th style="width: 1%; font-size: 1.5vw;">Destnasi</th>
							<th style="width: 1%; font-size: 1.5vw;">Tanggal Ekspor</th>
						</tr>
					</thead>
					<thead id="checksheetTableBody" style="background-color: #00c0ef;">
					</thead>
				</table>
				<table id="packingTable" class="table table-bordered table-stripped">
					<thead style="background-color: orange;">
						<tr>
							<th style="width: 1%; font-size: 1.2vw;">#</th>
							<th style="width: 1%; font-size: 1.2vw;">Material</th>
							<th style="width: 6%; font-size: 1.2vw;">Deskripsi</th>
							<th style="width: 1%; font-size: 1.2vw;">Quantity</th>
							<th style="width: 1%; font-size: 1.2vw;">Packed</th>
							<th style="width: 1%; font-size: 1.2vw;">Diff</th>
							<th style="width: 1%; font-size: 1.2vw;">Print</th>
							<th style="width: 1%; font-size: 1.2vw;">Print</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
				<button id="finishPacking" onclick="finishPacking()" class="btn btn-danger" style="font-weight: bold; font-size: 3vw; width: 100%; margin-top: 20px;">SELESAI PACKING</button>
			</div>
		</div>		
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<div style="background-color: orange;">
							<center>
								<h3>MOUTHPIECE PACKING</h3>
							</center>
						</div>
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						<br><br>
						<a href="{{ url("/index/kd_mouthpiece/picking") }}" class="btn btn-info" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-hand-o-right"></i> Ke Halaman Picking</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
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
		$('#packing').hide();
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});

	function clearAll(){
		$('#operator').val("");
		$('#qr_checksheet').val("");
		$('#barcode_inner').val("");
		$('#barcode_outer').prop('disabled', false);
		$('#barcode_outer').val("");
		$('#barcode_outer').prop('disabled', true);
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#barcode_inner').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#barcode_inner').val().length == 7){
				$('#barcode_inner').prop('disabled', true);
				$('#barcode_outer').prop('disabled', false);
				$('#barcode_outer').val("");
				$('#barcode_outer').focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Barcode tidak valid');
				$('#barcode_inner').val('');			
			}
		}
	});

	$('#barcode_outer').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#barcode_outer').val().length == 7){
				if($('#barcode_outer').val() != $('#barcode_inner').val()){
					audio_error.play();
					openErrorGritter('Error', 'Barcode INNER & OUTER tidak sama.');
					$('#barcode_inner').val('');
					$('#barcode_outer').val('');
					$('#barcode_inner').prop('disabled', false);
					$('#barcode_outer').prop('disabled', true);
					$('#barcode_inner').focus();
					return false;
				}

				$('#loading').show();

				var material_number = $('#barcode_outer').val();
				var kd_number = $('#kd_number').val();
				var employee_id = $('#employee_id').val();
				
				var data = {
					kd_number:kd_number,
					material_number:material_number,
					employee_id:employee_id
				}
				$.post('{{ url("scan/kd_mouthpiece/packing") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						$('#packingTable').DataTable().ajax.reload();
						openSuccessGritter('Success', result.message);
						$('#barcode_inner').val('');
						$('#barcode_outer').val('');
						$('#barcode_inner').prop('disabled', false);
						$('#barcode_outer').prop('disabled', true);
						$('#barcode_inner').focus();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#barcode_inner').val('');
						$('#barcode_outer').val('');
						$('#barcode_inner').prop('disabled', false);
						$('#barcode_outer').prop('disabled', true);
						$('#barcode_inner').focus();
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Barcode tidak valid');
				$('#barcode_outer').val('');			
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/kd_mouthpiece/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#emp_id').text(result.employee.employee_id);
						$('#emp_name').text(result.employee.name);
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_checksheet').val('');
						$('#qr_checksheet').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});

	function finishPacking(){
		$('#loading').show();
		var kd_number = $('#kd_number').val();
		var data = {
			kd_number:kd_number
		}

		if(confirm("Apakah anda yakin proses packing sudah selesai?")){
			$.post('{{ url("create/kd_mouthpiece/packing") }}', data, function(result, status, xhr){
				if(result.status){
					location.reload(true);
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			});
		}
		else{
			return false;
		}
	}

	function selectChecksheet(id){
		$('#loading').show();
		var data = {
			id:id,
			remark:1
		}
		$.get('{{ url("check/kd_mouthpiece/checksheet") }}', data, function(result, status, xhr){
			if(result.status){
				$('#checksheet').hide();
				$('#packing').show();
				$('#kd_number').val(id);

				var tableChecksheet = "";
				$('#checksheetTableBody').html("");

				tableChecksheet += '<tr>';
				tableChecksheet += '<th style="font-size: 1.5vw;">'+result.checksheet.kd_number+'</th>';
				tableChecksheet += '<th style="font-size: 1.5vw;">'+result.checksheet.destination_shortname+'</th>';
				tableChecksheet += '<th style="font-size: 1.5vw;">'+result.checksheet.st_date+'</th>';
				tableChecksheet += '</tr>';

				$('#checksheetTableBody').append(tableChecksheet);

				var table = $('#packingTable').DataTable({
					'responsive': true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'paging': false,
					'lengthChange': true,
					'searching': false,
					'ordering': false,
					'order': [],
					'info': false,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": false,
					"bAutoWidth": false,
					"processing": true,
					"columnDefs": [{
						"targets": 5,
						"createdCell": function (td, cellData, rowData, row, col) {
							if ( cellData <  0 ) {
								$(td).css('background-color', 'RGB(255,204,255)')
							}
							else
							{
								$(td).css('background-color', 'RGB(204,255,255)')
							}
						}
					}],
					"ajax": {
						"type" : "get",
						"url" : "{{ url("fetch/kd_mouthpiece/packing") }}",
						"data" : data
					},
					"columns": [
					{
						"data": null, "render": function (data, type, full, meta) {
							return meta.row + 1;
						}
					},
					{ "data": "material_number"},
					{ "data": "material_description" },
					{ "data": "quantity"},
					{ "data": "actual_quantity"},
					{ "data": "diff"},
					{ "data": "outer" },
					{ "data": "inner" }]
				});

				clearAll();
				$('#barcode_inner').focus();
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#kd_number").val("");
			}

		});
	}

	$('#qr_checksheet').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_checksheet").val().length == 10){
				selectChecksheet($("#qr_checksheet").val());
				clearAll();
			}
			else{
				openErrorGritter('Error!', 'QR Checksheet tidak valid.');
				audio_error.play();
				$("#qr_checksheet").val("");
			}			
		}
	});

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

