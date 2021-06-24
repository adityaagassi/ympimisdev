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
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="injection">
		<input type="hidden" id="proses" value="injection">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="order_id">

		<div class="col-xs-6 col-md-offset-3" id="field_kanban">
			<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
				<span style="font-size: 3vw; background-color: #00c0ef; padding-top: 6px;">
					&nbsp;
					<i class="glyphicon glyphicon-qrcode"></i>
					&nbsp;
				</span>
			</div>
			<input type="text" style="text-align: center; font-size: 3vw; height: 100px;" class="form-control" id="kanban" placeholder="Scan Kanban">	
		</div>


		<div class="col-xs-12" id="picking">
			{{-- <input id="qr_item" type="text" style="border:0; width: 100%; text-align: center; height: 20px; background-color: #3c3c3c; height: 0px;"> --}}

			<input id="qr_item" type="text" style="border:0; width: 100%; text-align: center; height: 20px; color: white; background-color: #3c3c3c; height: 50px;">

			<table id="pickingTable" class="table table-bordered table-stripped">
				<thead style="background-color: orange;">
					<tr>
						<th colspan="6" style="font-size: 2.5vw;">OPERATOR <span id="data_op"></span></th>
					</tr>
					<tr>
						<th colspan="6" style="font-size: 2.5vw;">PICKING LIST<span id="material"></span></th>
					</tr>
					<tr>
						<th style="width: 1%; font-size: 2vw;">#</th>
						<th style="width: 1%; font-size: 2vw;">Jenis</th>
						<th style="width: 5%; font-size: 2vw;">Deskripsi</th>
						<th style="width: 1%; font-size: 2vw;">Quantity</th>
						<th style="width: 1%; font-size: 2vw;">Actual</th>
						<th style="width: 1%; font-size: 2vw;">Status</th>
					</tr>
				</thead>
				<tbody id="pickingTableBody" style="background-color: white;">
				</tbody>
			</table>

			<table class="table table-bordered table-stripped">
				<thead style="background-color: orange;">
					<tr>
						<th width="50%" style="font-size: 2.5vw; vertical-align: middle;">
							<span id="hours">00</span>:
							<span id="minutes">00</span>:
							<span id="seconds">00</span>
						</th>
						<th width="50%" style="font-size: 2.5vw;">
							<button id="start" onclick="startInj()" class="btn btn-success" style="font-weight: bold; font-size: 3vw; width: 100%;">START</button>
							<button id="finish" onclick="finishInj()" class="btn btn-danger" style="font-weight: bold; font-size: 3vw; width: 100%;">FINISH</button></span>
						</th>
					</tr>
				</thead>
			</table>


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
								<h3>INJECTION VERIFICATION</h3>
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

		setTime();
		setInterval(setTime, 1000);

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearAll(){
		$('#picking').hide();

		$('#employee_id').val('');
		$('#order_id').val('');
		$('#operator').val('');
		$('#qr_item').val('');
		$('#kanban').val('');

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
						$('#data_op').text(" ("+result.employee.employee_id+" - "+result.employee.name+")")
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_item').val('');


						$('#kanban').val('');
						$('#kanban').focus();

					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
				
			}else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});


	$('#kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kanban").val().length >= 11){
				selectChecksheet();
			}
			else{
				openErrorGritter('Error!', 'Kanban tidak valid.');
				audio_error.play();
				$("#kanban").val("");
			}			
		}
	});


	function selectChecksheet(){
		var kanban = $('#kanban').val();
		var location = $('#location').val();
		var proses = $('#proses').val();

		if(kanban == ''){
			return false;
		}

		var data = {
			kanban : kanban, 
			location : location,
			proses : proses 
		}

		$.get('{{ url("fetch/reed/injection_picking_list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#field_kanban').hide();

				$('#picking').show();
				$('#pickingTableBody').html("");

				$('#order_id').val(result.order.id);
				$('#material').text(" ("+result.order.material_number+" - "+result.order.material_description+")")


				var pickingData = "";

				var total_quantity = 0;
				var total_actual = 0;

				$.each(result.data, function(key, value){

					pickingData += '<tr>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+(key+1)+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.picking_list+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.picking_description+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.quantity+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.actual_quantity+'</td>';


					if(value.quantity != value.actual_quantity){
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">-</td>';
					}
					else{
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';						
					}
					pickingData += '</tr>';

					total_quantity += value.quantity;
					total_actual += value.actual_quantity;
				});


				var reload;
				if(total_quantity == total_actual){
					if(result.order.start_injection != null){
						time = new Date(result.order.start_injection);
						document.getElementById("hours").innerHTML = pad(parseInt(diff_seconds(new Date(), time) / 3600));
						document.getElementById("minutes").innerHTML = pad(parseInt((diff_seconds(new Date(), time) % 3600) / 60));
						document.getElementById("seconds").innerHTML = pad(diff_seconds(new Date(), time) % 60);

						$('#start').hide();

						$('#finish').show();
						$('#finish').prop('disabled', false);

						reload = false;
					}else{
						if(result.order.setup_molding == 1){
							$('#start').prop('disabled', false);
							$('#start').show();

							$('#finish').hide();
							$('#finish').prop('disabled', true);

							reload = false;
						}else{
							$('#start').prop('disabled', true);
							$('#start').show();

							$('#finish').hide();
							$('#finish').prop('disabled', true);

							reload = true;
						}
					}
				}else{
					$('#start').prop('disabled', true);
					$('#start').show();

					$('#finish').hide();
					$('#finish').prop('disabled', true);

					reload = true;

				}

				$('#pickingTableBody').append(pickingData);
				setInterval(focusTag, 1000);

				if(reload){
					setTimeout(function() {
						selectChecksheet();
					}, 3000)
				}

			}else{
				$('#kanban').val("");
				$('#kanban').focus();

				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}


	function focusTag(){
		$('#qr_item').focus();
	}


	$('#qr_item').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();

			var qr_item = $('#qr_item').val();
			var order_id = $('#order_id').val();
			var location = $('#location').val();
			var employee_id = $('#employee_id').val();

			var data = {
				qr_item:qr_item,
				order_id:order_id,
				location:location,
				employee_id:employee_id
			}

			$.post('{{ url("scan/reed/injection_picking") }}', data, function(result, status, xhr){
				if(result.status){
					$('#qr_item').val("");
					$('#qr_item').focus();

					selectChecksheet();

					$('#loading').hide();
					audio_ok.play();
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
	});


	function startInj(){
		$('#loading').show();
		var order_id = $('#order_id').val();
		var employee_id = $('#employee_id').val();

		var data = {
			order_id:order_id,
			employee_id:employee_id,
		}

		if(confirm("Apakah anda yakin memulai proses injeksi?")){
			$.post('{{ url("fetch/reed/start_injection") }}', data, function(result, status, xhr){
				if(result.status){

					time = new Date(result.start);

					document.getElementById("hours").innerHTML = pad(parseInt(diff_seconds(new Date(), time) / 3600));
					document.getElementById("minutes").innerHTML = pad(parseInt((diff_seconds(new Date(), time) % 3600) / 60));
					document.getElementById("seconds").innerHTML = pad(diff_seconds(new Date(), time) % 60);

					$('#start').prop('disabled', true);
					$('#start').hide();

					$('#finish').show();
					$('#finish').prop('disabled', false);

					$('#loading').hide();

				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();				
				}
			});
		}else{
			$('#loading').hide();
			return false;
		}
	}


	function finishInj(){
		$('#loading').show();
		var order_id = $('#order_id').val();
		var employee_id = $('#employee_id').val();
		$('#kanban').val('');


		var data = {
			order_id:order_id,
			employee_id:employee_id,
		}

		if(confirm("Apakah anda yakin mengakhiri proses injeksi?")){
			$.post('{{ url("fetch/reed/finish_injection") }}', data, function(result, status, xhr){
				if(result.status){
					time = undefined;
					$('#finish').prop('disabled', true);

					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();				
				}
			});
		}else{
			$('#loading').hide();
			return false;
		}
	}


	var time;
	function setTime() {
		if(time !== undefined){
			var duration = diff_seconds(new Date(), time);

			document.getElementById("hours").innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes").innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds").innerHTML = pad(duration % 60);				
		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
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

