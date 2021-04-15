@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
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
		<input type="hidden" id="location" value="packing">
		<input type="hidden" id="proses" value="packing">
		<input type="hidden" id="employee_id">

		<div id="main">
			<div class="col-xs-3">
				<center>
					<div class="col-xs-12">
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
					</div>
					
					<button class="btn btn-warning btn-md" onclick="refreshInput()" style="font-size: 2vw; margin-bottom: 5%;">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</button>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: orange;">
							<span style="font-weight: bold; color: rgb(60,60,60); font-size: 2vw;">1. SCAN PACKING ORDER</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="order_id" placeholder="Scan QR Code">
					</div>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: #00c0ef;">
							<span style="font-weight: bold; color: rgb(60,60,60); font-size: 2vw;">2. SCAN OUTER BOX</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="outer_box" placeholder="Scan QR Code">
					</div>

					<div class="col-xs-12" style="padding: 0px;">
						<div style="background-color: #CE93D8;">
							<span style="font-weight: bold; color: rgb(60,60,60); font-size: 2vw;">3. SCAN BLISTER PACK</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="blister" placeholder="Scan QR Code">
					</div>


				</center>
			</div>
			<div class="col-xs-9" style="margin-top: 5%;">
				<div class="col-xs-12" id="delivery">
					<table id="deliveryTable" class="table table-bordered table-stripped">
						<thead style="background-color: orange;">
							<tr>
								<tr>
									<th style="width: 1%; font-size: 1.2vw;" id="order_material" colspan="6"></th>
								</tr>
							</tr>
							<thead style="background-color: orange;">
								<tr>
									<th style="width: 1%; font-size: 1.2vw;">Order ID</th>
									<th style="width: 1%; font-size: 1.2vw;">Material</th>
									<th style="width: 5%; font-size: 1.2vw;">Deskripsi</th>
									<th style="width: 2%; font-size: 1.2vw;">Quantity</th>
									<th style="width: 2%; font-size: 1.2vw;">Act. Quantity</th>
									<th style="width: 1%; font-size: 1.2vw;">Diff</th>
								</tr>
							</thead>
							<tbody id="deliveryBody">
							</tbody>
						</table>
					</div>

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
							<div style="background-color: #7eca9c;">
								<center>
									<h3>PACKING VERIFICATION</h3>
								</center>
							</div>
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
							<br><br>
							<a href="{{ url("index/final/reed_synthetic") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-arrow-left"></i> Ke Halaman Packing</a>
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
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
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
			$('#order_id').val("");

			$('#kanban').val("");
			$('#store').val("");
			$('#hako').val("");

			$('#operator').focus();

			$('#main').hide();
			$('#inventory').hide();
			$('#delivery').hide();


		}

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


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

							$('#main').show();
							$('#order_id').val('');
							$('#order_id').focus();
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

		$('#order_id').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#order_id").val().length == 9){
					selectChecksheet();
				}else{
					openErrorGritter('Error!', 'Packing order tidak valid.');
					audio_error.play();
					$('#order_id').val('');
					$('#order_id').focus();
				}			
			}
		});


		function selectChecksheet(id){
			$('#loading').show();

			var location = $("#location").val();
			var proses = $("#proses").val();
			var order_id = $("#order_id").val();

			var data = {
				order_id : order_id,
				location : location,
				proses : proses
			}

			$.get('{{ url("fetch/reed/packing_picking_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#deliveryBody').html("");
					$('#order_material').html("");
					$('#order_material').html(result.order.material_number+" - "+result.order.material_description);
					var deliveryData = '';

					$.each(result.data, function(key, value){
						deliveryData += '<tr>';
						deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.order_id+'</td>';
						deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_number+'</td>';
						deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.picking_description+'</td>';
						deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.quantity+'</td>';
						deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.actual_quantity+'</td>';

						if(value.quantity == value.actual_quantity){
							deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';
						}else{
							deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">'+(value.actual_quantity - value.quantity)+'</td>';
						}

						deliveryData += '</tr>';
					});
					$('#deliveryBody').append(deliveryData);
					$('#delivery').show();

					$('#order_id').prop('readonly', true);

					if($('#outer_box').val() == ''){
						$('#outer_box').val('');
						$('#outer_box').focus();
					}

					$('#loading').hide();

				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();

					$('#order_id').val('');
					$('#order_id').focus();
				}
			});
		}

		$('#outer_box').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#outer_box").val().length == 7){
					$('#blister').val('');
					$('#blister').focus();

					$('#outer_box').prop('readonly', true);

				}else{
					openErrorGritter('Error!', 'Outer tidak valid.');
					audio_error.play();
					$('#outer_box').val('');
					$('#outer_box').focus();
				}	
			}
		});

		$('#blister').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#blister").val().length == 7){
					$('#blister').prop('readonly', true);

					scanPacking();

					$('#blister').val('');
					$('#blister').prop('readonly', false);
				}else{
					openErrorGritter('Error!', 'Blister pack tidak valid.');
					audio_error.play();
					$('#blister').val('');
					$('#blister').focus();
				}	
			}
		});

		function scanPacking() {
			$('#loading').show();
			var order_id = $("#order_id").val();
			var outer_box = $("#outer_box").val();
			var blister = $("#blister").val();

			if(order_id == '' || outer_box == '' || blister == ''){
				openErrorGritter('Error!', 'Proses salah');
				audio_error.play();
				return false;
			}

			var data = {
				order_id : order_id,
				outer_box : outer_box,
				blister : blister
			}

			$.post('{{ url("scan/reed/packing_box") }}', data, function(result, status, xhr){
				if(result.status){

					selectChecksheet();
					$('#loading').hide();
					audio_ok.play();

					openSuccessGritter('Success', result.message);

				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			});
		}

		function refreshInput() {
			$('#inventory').hide();
			$('#delivery').hide();

			$('#order_id').val('');
			$('#outer_box').val('');
			$('#blister').val('');

			$('#order_id').prop('readonly', false);
			$('#outer_box').prop('readonly', false);
			$('#blister').prop('readonly', false);

			$('#order_id').focus();
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '1000'
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

