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
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="label_verification">
		<input type="hidden" id="proses" value="label_verification">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="material_number">
		
		<div class="col-xs-12">
			<div class="col-xs-2 pull-left" style="padding: 0px;margin-left: 20px">
				<div class="input-group date">
					<div class="input-group-addon bg-green">
						<i class="fa fa-calendar"></i>
					</div>

					<input type="text" name="date_receive" id="date_receive" class="form-control datepicker" placeholder="Choose Receive Date" onchange="selectDate()"  style="width: 100%;">
				</div>
			</div>
		</div>
		
		<div class="col-xs-12" style="margin-top: 1%;">
			<div class="col-xs-3">
				<center>
					<table id="operatorTable" class="table table-bordered table-stripped" style="margin-bottom: 5%;">
						<thead style="background-color: #00c0ef;">
							<tr>
								<th style="width: 1%; font-size: 1.5vw;" id="emp_id"></th>
							</tr>
							<tr>
								<th style="width: 1%; font-size: 1.5vw;" id="emp_name"></th>
							</tr>
						</thead>
					</table>

					<button class="btn btn-warning btn-md" onclick="refreshInput()" style="font-size: 2vw; margin-bottom: 5%;">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</button>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: orange;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">1. SCAN STORE</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_store" placeholder="Scan QR Code">
					</div>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: #00c0ef;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">2. SCAN LABEL</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_label" placeholder="Scan QR Code">
					</div>

				</center>
			</div>
			<div class="col-xs-9">

				<div class="col-xs-12" id="label">
					<table id="labelTable" class="table table-bordered table-stripped">
						<thead style="background-color: orange;">
							<tr>
								<th style="width: 1%; font-size: 1.2vw;">Receive Date</th>
								<th style="width: 1%; font-size: 1.2vw;">Material</th>
								<th style="width: 6%; font-size: 1.2vw;">Deskripsi</th>
								<th style="width: 1%; font-size: 1.2vw;">Quantity</th>
								<th style="width: 1%; font-size: 1.2vw;">Quantity Bag</th>
								<th style="width: 1%; font-size: 1.2vw;">Actual Bag</th>
								<th style="width: 1%; font-size: 1.2vw;">Diff</th>
							</tr>
						</thead>
						<tbody id="labelBody" style="background-color: white;">
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>		
</div>
</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive">
					<div class="form-group">
						<div style="background-color: #9c27b0;color: white;">
							<center>
								<h3>STORE VERIFICATION</h3>
							</center>
						</div>
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						<br><br>
						<a href="{{ url("/index/reed") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-arrow-left"></i> Ke Halaman Reed</a>
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
		clearAll();
		$('#startInj').prop('disabled', true);

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearAll(){
		$('#label').hide();

		$('#employee_id').val('');
		$('#operator').val('');
	}

	function refreshInput() {
		$('#qr_store').val('');
		$('#qr_label').val('');
		$('#date_receive').val('');

		$('#qr_store').focus();
		$('#labelBody').html("");

	}


	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length == 9){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/reed/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#emp_id').text(result.employee.employee_id);
						$('#emp_name').text(result.employee.name);
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();

						$('#store').val('');
						$('#store').focus();
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

	function selectDate(){
		var date_receive = $('#date_receive').val();

		if(date_receive == ''){
			return false;
		}

		var data = {
			date_receive : date_receive
		}

		$.get('{{ url("fetch/reed/label_verification") }}', data, function(result, status, xhr){
			if(result.status){
				$('#label').show();
				$('#labelBody').html("");

				var pickingData = "";

				$.each(result.order, function(key, value){

					$("#material_number").val(value.material_number);

					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.receive_date+'</td>';
					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.material_number+'</td>';
					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.material_description+'</td>';
					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.quantity+'</td>';
					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.bag_quantity+'</td>';
					pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px;">'+value.bag_delivered+'</td>';

					if(value.bag_quantity == value.bag_delivered){
						pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px; background-color: rgb(204,255,255);">OK</td>';
					}else{
						pickingData += '<td style="font-size: 1vw; height:2%; vertical-align:middle; text-align: center; height:40px; background-color: rgb(255,204,255);">'+(value.bag_quantity - value.bag_delivered)+'</td>';
					}

					

				});

				$('#labelBody').append(pickingData);


			}else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	$('#qr_store').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_store").val() == $("#material_number").val()){

				$('#qr_label').val('');
				$('#qr_label').focus();
			}else{
				openErrorGritter('Error!', 'Store Salah');
				audio_error.play();
				$('#qr_store').val('');
				$('#qr_store').focus();
			}			
		}
	});

	$('#qr_label').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_label").val() == $("#material_number").val()){
				updateDiff();
			}else{
				openErrorGritter('Error!', 'Label Salah');
				audio_error.play();
				$('#qr_label').val('');
				$('#qr_label').focus();
			}	
		}
	});

	function updateDiff() {
		$('#loading').show();
		var receive_date = $("#date_receive").val();
		var material_number = $("#material_number").val();
		var employee_id = $("#employee_id").val();

		var data = {
			receive_date : receive_date,
			material_number : material_number,
			employee_id : employee_id
		}

		$.post('{{ url("scan/reed/store_verification") }}', data, function(result, status, xhr){
			if(result.status){
				selectDate();
				$('#loading').hide();
				$("#material_number").val('');
				$("#qr_store").val('');
				$("#qr_label").val('');


				audio_ok.play();
				openSuccessGritter('Success', result.message);

			}else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
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

