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
		<input type="hidden" id="employee_id">

		<input type="hidden" id="order_id">
		<input type="hidden" id="gmc_kanban">
		<input type="hidden" id="gmc_store">
		<input type="hidden" id="gmc_hako">

		<div id="main">
			<div class="col-xs-3">
				<center>
					<button class="btn btn-warning btn-md" onclick="refreshInput()" style="font-size: 2vw; margin-bottom: 5%;">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</button>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: orange;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">1. SCAN KANBAN</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="kanban" placeholder="Scan QR Code">
					</div>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: #00c0ef;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">2. SCAN STORE</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="store" placeholder="Scan QR Code">
					</div>

					<div class="col-xs-12" style="padding: 0px; margin-bottom: 5%;">
						<div style="background-color: #CE93D8;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">3. SCAN HAKO</span><br>
						</div>
						<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="hako" placeholder="Scan QR Code">
					</div>

				</center>
			</div>
			<div class="col-xs-9">
				<div class="col-xs-6">
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
				<div class="col-xs-6" id="inventory">
					<table width="" id="inventoryTable" class="table table-bordered table-stripped">
						<thead style="background-color: #00c0ef;">
							<tr>
								<th colspan="3" style="width: 1%; font-size: 1.5vw;">Inventory</th>
							</tr>
						</thead>
						<thead id="inventoryBody" style="background-color: #00c0ef;">
							<tr>
								<th style="width: 1%; font-size: 1.5vw;" id="inv_material"></th>
								<th style="width: 1%; font-size: 1.5vw;" id="inv_location"></th>
								<th style="width: 1%; font-size: 1.5vw;" id="inv_qty"></th>
							</tr>
						</thead>
					</table>
				</div>

				<div class="col-xs-12" id="delivery">
					<table id="deliveryTable" class="table table-bordered table-stripped">
						<thead style="background-color: orange;">
							<tr>
								<th style="width: 1%; font-size: 1.2vw;">Kanban</th>
								<th style="width: 1%; font-size: 1.2vw;">Material</th>
								<th style="width: 6%; font-size: 1.2vw;">Deskripsi</th>
								<th style="width: 1%; font-size: 1.2vw;">Quantity</th>
								<th style="width: 1%; font-size: 1.2vw;">Hako</th>
								<th style="width: 1%; font-size: 1.2vw;">Hako Delivered</th>
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
						<div style="background-color: #CE93D8;">
							<center>
								<h3>AFTER INJECTION DELIVERY</h3>
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
		$('#operator').val("");
		$('#gmc_kanban').val("");
		$('#gmc_store').val("");
		$('#gmc_hako').val("");

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
						$('#kanban').val('');
						$('#kanban').focus();
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

	$('#kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kanban").val().length >= 11){
				selectChecksheet($("#kanban").val(), 'store');
			}else{
				openErrorGritter('Error!', 'Kanban tidak valid.');
				audio_error.play();
				$('#kanban').val('');
				$('#kanban').focus();
			}			
		}
	});


	function selectChecksheet(id, condition){
		$('#loading').show();

		var data = {
			kanban : id
		}

		$.get('{{ url("fetch/reed/injection_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				$('#deliveryBody').html("");
				$('#gmc_kanban').val(result.order.material_number);
				$('#order_id').val(result.order.id);

				var deliveryData = '';
				deliveryData += '<tr>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.kanban+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.material_number+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.material_description+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.quantity+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.hako+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.hako_delivered+'</td>';

				if(result.order.hako == result.order.hako_delivered){
					deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';
				}else{
					deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">'+(result.order.hako_delivered - result.order.hako)+'</td>';
				}

				deliveryData += '</tr>';
				$('#deliveryBody').append(deliveryData);

				if(condition == 'store'){
					$('#kanban').prop('disabled', true);

					$('#store').val('');
					$('#store').focus();
				}else if(condition == 'hako'){
					$('#hako').val('');
					$('#hako').focus();
				}


				if(result.inventory){
					$('#inv_material').text(result.inventory.material_number);
					$('#inv_location').text(result.inventory.storage_location);
					$('#inv_qty').text(result.inventory.quantity);
				}else{
					$('#inv_material').text(result.order.material_number);
					$('#inv_location').text(result.storage_location);
					$('#inv_qty').text('0');
				}

				$('#inventory').show();
				$('#delivery').show();


				$('#loading').hide();

			}else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();

				$('#kanban').val('');
				$('#kanban').focus();
			}
		});
	}


	$('#store').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if( ($("#store").val().length >= 13) && (($('#store').val().includes('-'))) ){
				var gmc_kanban = $("#gmc_kanban").val();

				var data = $("#store").val();
				var data = data.split('-');

				var text = data[0];
				var gmc_store = data[1].substring(0, 7);

				if(text.toUpperCase() == 'STORE'){
					if(gmc_kanban == gmc_store){
						$('#store').prop('disabled', true);

						$('#hako').val('');
						$('#hako').focus();

						openSuccessGritter('Success', 'Store Sesuai');
					}else{
						openErrorGritter('Error!', 'Store salah.');
						audio_error.play();
						$('#store').val('');
						$('#store').focus();
					}
				}else{
					openErrorGritter('Error!', 'Store tidak valid.');
					audio_error.play();
					$('#store').val('');
					$('#store').focus();
				}
			}else{
				openErrorGritter('Error!', 'QR Code tidak terdaftar.');
				audio_error.play();
				$('#store').val('');
				$('#store').focus();
			}			
		}
	});

	$('#hako').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if( ($('#hako').val().length >= 12) && ($('#hako').val().includes('-')) ){
				var gmc_kanban = $("#gmc_kanban").val();

				var data = $("#hako").val();
				var data = data.split('-');

				var text = data[0];
				var gmc_hako = data[1].substring(0, 7);

				if(text.toUpperCase() == 'HAKO'){
					if(gmc_kanban == gmc_hako){
						scanDelivery();
					}else{
						openErrorGritter('Error!', 'Hako salah.');
						audio_error.play();
						$('#hako').val('');
						$('#hako').focus();
					}
				}else{
					openErrorGritter('Error!', 'Hako tidak valid.');
					audio_error.play();
					$('#hako').val('');
					$('#hako').focus();
				}
			}else{
				openErrorGritter('Error!', 'QR Code tidak terdaftar.');
				audio_error.play();
				$('#hako').val('');
				$('#hako').focus();			
			}
		}
	});

	function scanDelivery() {
		$('#loading').show();
		var kanban = $("#kanban").val();
		var employee_id = $("#employee_id").val();
		var order_id = $("#order_id").val();

		var data = {
			kanban : kanban,
			employee_id : employee_id,
			order_id : order_id
		}

		$.post('{{ url("scan/reed/injection_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				updateChecksheet(kanban, order_id, 'hako');
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

	function updateChecksheet(kanban, id, condition){
		$('#loading').show();

		var data = {
			kanban : kanban,
			order_id : id
		}

		$.get('{{ url("fetch/reed/update_injection_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				$('#deliveryBody').html("");
				$('#gmc_kanban').val(result.order.material_number);

				var deliveryData = '';
				deliveryData += '<tr>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.kanban+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.material_number+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.material_description+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.quantity+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.hako+'</td>';
				deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.order.hako_delivered+'</td>';

				if(result.order.hako == result.order.hako_delivered){
					deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';
				}else{
					deliveryData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">'+(result.order.hako_delivered - result.order.hako)+'</td>';
				}

				deliveryData += '</tr>';
				$('#deliveryBody').append(deliveryData);

				if(condition == 'store'){
					$('#kanban').prop('disabled', true);

					$('#store').val('');
					$('#store').focus();
				}else if(condition == 'hako'){
					$('#hako').val('');
					$('#hako').focus();
				}


				if(result.inventory){
					$('#inv_material').text(result.inventory.material_number);
					$('#inv_location').text(result.inventory.storage_location);
					$('#inv_qty').text(result.inventory.quantity);
				}else{
					$('#inv_material').text(result.order.material_number);
					$('#inv_location').text(result.storage_location);
					$('#inv_qty').text('0');
				}

				$('#inventory').show();
				$('#delivery').show();


				$('#loading').hide();

			}else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();

				$('#kanban').val('');
				$('#kanban').focus();
			}
		});
	}

	function refreshInput() {
		$('#inventory').hide();
		$('#delivery').hide();

		$('#kanban').val('');
		$('#store').val('');
		$('#hako').val('');

		$('#kanban').prop('disabled', false);
		$('#store').prop('disabled', false);
		$('#hako').prop('disabled', false);

		$('#kanban').focus();
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

