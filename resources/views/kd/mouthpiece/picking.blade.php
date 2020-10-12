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
	tbody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
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
		font-size: 1vw;
		padding-top: 0;
		padding-bottom: 0;
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
				<i class="glyphicon glyphicon-qrcode" style="font-size: 3vw; background-color: #00c0ef;"></i>
			</div>
			<input type="text" style="text-align: center; font-size: 3vw; height: 100px;" class="form-control" id="qr_checksheet" placeholder="Scan QR Checksheet">
			
		</div>
		<div class="col-xs-12" id="picking">
			<input id="qr_item" type="text" style="border:0; width: 100%; text-align: center; height: 20px; background-color: #3c3c3c; height: 0px;">
			<table id="pickingTable" class="table table-bordered table-stripped">
				<thead style="background-color: orange;">
					<tr>
						<th colspan="6" style="font-size: 2.5vw;">PICKING LIST <span id="data_op"></span></th>
					</tr>
					<tr>
						<th style="width: 1%; font-size: 2vw;">#</th>
						<th style="width: 1%; font-size: 2vw;">Jenis</th>
						<th style="width: 1%; font-size: 2vw;">Material</th>
						<th style="width: 5%; font-size: 2vw;">Deskripsi</th>
						<th style="width: 1%; font-size: 2vw;">Quantity</th>
						<th style="width: 1%; font-size: 2vw;">Status</th>
					</tr>
				</thead>
				<tbody id="pickingTableBody" style="background-color: white;">
				</tbody>
			</table>
			<button id="finishPicking" onclick="finishPicking()" class="btn btn-danger" style="font-weight: bold; font-size: 3vw; width: 100%;">SELESAI PICKING</button>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<div style="background-color: #00c0ef;">
							<center>
								<h3>MATERIAL PICKING</h3>
							</center>
						</div>
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						<br><br>
						<a href="{{ url("/index/kd_mouthpiece/packing") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-hand-o-right"></i> Ke Halaman Packing</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- <div class="modal fade" id="modalChecksheet">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Checksheet QR</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="checksheet" placeholder="Scan Checksheet" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> --}}

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
		clearAll();
		$('#finishPicking').prop('disabled', true);
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});

	function clearAll(){
		$('#picking').hide();
		$('#employee_id').val('');
		$('#kd_number').val('');
		$('#operator').val('');
		$('#qr_item').val('');
		$('#qr_checksheet').val('');

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#qr_item').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#qr_item').val().length == 7){
				$('#loading').show();
				var material_number = $('#qr_item').val();
				var kd_number = $('#kd_number').val();
				var employee_id = $('#employee_id').val();
				var data = {
					kd_number:kd_number,
					material_number:material_number,
					employee_id:employee_id
				}
				$.post('{{ url("scan/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
					if(result.status){
						$('#qr_item').val("");
						$('#qr_item').focus();
						selectChecksheet(kd_number);
						$('#loading').hide();
						openSuccessGritter('Success', result.message);
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#qr_item').val('');							
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Kode material tidak valid');
				$('#qr_item').val('');			
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
						$('#data_op').text(" ("+result.employee.employee_id+" - "+result.employee.name+")")
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_checksheet').val('');
						$('#qr_item').val('');
						$('#qr_checksheet').focus();
						// $('#modalChecksheet').modal('show');

						// $('#modalChecksheet').modal({
						// 	backdrop: 'static',
						// 	keyboard: false
						// });

						// $('#modalChecksheet').on('shown.bs.modal', function () {
						// 	$('#checksheet').focus();
						// });
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

	function focusTag(){
		$('#qr_item').focus();
	}

	function finishPicking(){
		$('#loading').show();
		var kd_number = $('#kd_number').val();
		var data = {
			kd_number:kd_number
		}
		if(confirm("Apakah anda yakin picking sudah selesai?")){
			$.post('{{ url("create/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
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
			id:id
		}
		$.get('{{ url("fetch/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
			if(result.status){
				$('#checksheet').hide();
				$('#picking').show();
				$('#pickingTableBody').html("");

				$('#kd_number').val(id);

				var pickingData = "";

				var total_quantity = 0;
				var total_actual = 0;

				$.each(result.checksheet_details, function(key, value){
					pickingData += '<tr>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+(key+1)+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.remark.toUpperCase()+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_number+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_description+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.quantity+'</td>';
					if(value.quantity > value.actual_quantity){
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">-</td>';
					}
					else{
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';						
					}
					pickingData += '</tr>';

					total_quantity += value.quantity;
					total_actual += value.actual_quantity;
				});

				if(total_quantity == total_actual){
					$('#finishPicking').prop('disabled', false);
				}

				$('#pickingTableBody').append(pickingData);
				setInterval(focusTag, 1000);
				$('#loading').hide();
			}
			else{
				$('#qr_checksheet').val("");
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$('#qr_checksheet').focus();
			}
		});

	}

	$('#qr_checksheet').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_checksheet").val().length == 10){
				selectChecksheet($("#qr_checksheet").val());
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

