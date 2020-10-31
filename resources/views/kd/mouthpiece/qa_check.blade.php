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
			<table id="operatorTable" class="table table-bordered table-stripped" style="margin-bottom: 26px;">
				<thead style="background-color: #ccff90;">
					<tr>
						<th style="width: 1%; font-size: 1.5vw;" id="emp_id"></th>
					</tr>
					<tr>
						<th style="width: 1%; font-size: 1.5vw;" id="emp_name"></th>
					</tr>
				</thead>
			</table>
			<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
				<i class="glyphicon glyphicon-qrcode" style="font-size: 3vw; background-color: #ccff90;"></i>
			</div>
			<input type="text" style="text-align: center; font-size: 3vw; height: 100px;" class="form-control" id="qr_checksheet" placeholder="Scan QR Checksheet">
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<div style="background-color: #ccff90;">
							<center>
								<h3>QA Check</h3>
							</center>
						</div>
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						<br><br>
						<a href="{{ url("/home") }}" class="btn btn-success" style="width: 100%; font-size: 1vw; font-weight: bold; margin-top: 10px;"><i class="fa fa-hand-o-right"></i> Ke Halaman Dashboard</a>
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
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});

	function clearAll(){
		$('#employee_id').val('');
		$('#kd_number').val('');
		$('#operator').val('');
		$('#qr_checksheet').val('');
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	$('#qr_checksheet').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_checksheet").val().length == 10){
				if(confirm("Apakah anda yakin material ini sudah OK QA Check?")){
					var kd_number = $('#qr_checksheet').val();
					var employee_id = $('#employee_id').val();
					var data = {
						kd_number:kd_number,
						employee_id:employee_id
					}
					$.post('{{ url("scan/kd_mouthpiece/qa_check") }}', data, function(result, status, xhr){
						if(result.status){
							$('#loading').hide();
							openSuccessGritter('Success', result.message);
							$('#qr_checksheet').val("");
							$('#qr_checksheet').focus();
						}
						else{
							$('#loading').hide();
							audio_error.play();
							openErrorGritter('Error', result.message);
							$('#qr_checksheet').val("");
							$('#qr_checksheet').focus();
						}
					});
				}
				else{
					return false;
				}
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

