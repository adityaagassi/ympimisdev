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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="tag_input">
	<input type="hidden" id="order_no">
	<input type="hidden" id="exe" value="{{ $exe }}">
	<input type="hidden" id="operator_id">
	<input type="hidden" id="started_at">
	<input type="hidden" id="item_number">
	<input type="hidden" id="sequence_process">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px;">
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size: 25px; width: 30%;" id="machine_information">{{ $process_name }} ({{ $machine_name }})</td>
					</tr>
				</tbody>
			</table>

			<table class="table table-bordered" style="width: 100%; margin-bottom: 3%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:25px; width: 30%;" id="op">-</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 25px;" id="op2">-</td>
					</tr>
				</tbody>
			</table>

			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black; font-size: 2vw;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black; height: 45px; font-size: 2vw;" class="form-control" id="tag" name="tag" placeholder="Tap WJO Tag..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black; font-size: 2vw;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
			</div>	
			<table class="table table-bordered" style="width: 100%; margin-bottom: 3%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(50, 50, 50); color: white; text-align: center; padding:0;font-size: 35px;" colspan="4" id="text_order_no">Order No.</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">Prioritas</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_priority"></td>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 20%;">Drawing</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_drawing"></td>
					</tr>
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">Target Selesai</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_target_date"></td>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 20%;">Kategori</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_category"></td>
					</tr>
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">Material</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_material"></td>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 20%;">Jumlah</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_quantity"></td>
					</tr>	
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">Nama Barang</td>
						<td colspan="3" style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px;" id="text_item_name"></td>
					</tr>
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">Uraian Permintaan</td>
						<td colspan="3" style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px;" id="text_problem_description"></td>
					</tr>		
					<tr>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">PIC</td>
						<td colspan="3" style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px;" id="text_pic"></td>
					</tr>
					<tr>
						
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 3%;">
				<thead>
					<tr>
						<th style="background-color: rgb(50, 50, 50); padding: 0px;" colspan="2">
							<div class="col-md-12" style="padding: 0px;">
								<div class="progress-group" id="progress_div">
									<div class="progress" style="height: 30px; border: 1px solid; padding: 0px; margin: 0px;">
										<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar" style="font-size: 20px; padding-top: 0.5%;"></div>
									</div>
								</div>
							</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 75%; color: white; font-size: 2vw;">
							<p style="margin: 0px;"><label id='hours'>00</label>:<label id='minutes'>00</label>:<label id='seconds'>00</label></p>
						</td>
						<td style="width: 25%;">
							<div class="col-md-12" style="padding: 0px;">
								<button class="btn btn-success" onclick="finish()" style="width: 100%; font-size: 20px;"><i class="fa fa-check"></i> Finish</button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-5" style="padding-right: 0;" id="step"></div>
	</section>

	<div class="modal fade" id="modalMachine">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Mesin</label>
							<select class="form-control select2" data-placeholder="Pilih Mesin" id="machine" onChange="focusOperator()" style="width: 100% height: 35px; font-size: 15px;" required>
								<option value=""></option>
								@foreach($machines as $machine)
								<option value="{{ $machine->machine_code }}">{{ $machine->machine_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Tag Karyawan</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalLeader">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label style="text-align: center;" for="exampleInputEmail1">Tap ID Leader/Foreman untuk mengubah alur proses yang telah tersimpan</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="leader" placeholder="Scan ID" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

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
			$('.select2').select2();

			if($('#exe').val() == 'exe'){
				$('#modalMachine').modal({
					backdrop: 'static',
					keyboard: false
				});
			}else{
				$('#modalOperator').modal({
					backdrop: 'static',
					keyboard: false
				});
			}

			setInterval(setTime, 1000);
		});

		var duration = 0;
		var count = false;
		var started_at;
		function setTime() {
			if(count){
				document.getElementById("hours").innerHTML = pad(parseInt(diff_seconds(new Date(), started_at) / 3600));
				document.getElementById("minutes").innerHTML = pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60));
				document.getElementById("seconds").innerHTML = pad(diff_seconds(new Date(), started_at) % 60);
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

		function focusOperator(){
			var machine_code = $("#machine").val();
			document.getElementById("exe").value = machine_code;

			var data = {
				machine_code : machine_code
			}

			$.get('{{ url("fetch/workshop/machine") }}', data, function(result, status, xhr){
				if(result.status){
					document.getElementById("machine_information").innerHTML = result.process_name + '  (' + result.machine_name + ')';

					$('#modalMachine').modal('hide');
					$('#modalOperator').modal({
						backdrop: 'static',
						keyboard: false
					});
				}
			});

		}

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('#modalLeader').on('shown.bs.modal', function () {
			$('#leader').focus();
		});

		$('#operator').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#operator").val().length >= 10){
					var data = {
						employee_id : $("#operator").val()
					}
					$.get('{{ url("scan/workshop/operator/rfid") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							document.getElementById("operator_id").value = result.employee.employee_id;
							$('#modalOperator').modal('hide');
							$('#tag').focus();
							$('#op').html(result.employee.employee_id);
							$('#op2').html(result.employee.name);
						}
						else{
							audio_error.play();
							openErrorGritter('Error', result.message);
							$('#operator').val('');
						}
					});

				}
			}
		});

		$('#leader').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#leader").val().length >= 10){
					var data = {
						employee_id : $("#leader").val(),
						item_number : $("#item_number").val(),
						sequence_process : $("#sequence_process").val(),
					}
					$.get('{{ url("scan/workshop/leader/rfid") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$('#modalLeader').modal('hide');
						}
						else{
							audio_error.play();
							openErrorGritter('Error', result.message);
							$('#leader').val('');
						}
					});

				}
			}
		});

		$('#tag').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#tag").val().length >= 10){
					var data = {
						tag : $("#tag").val(),
						machine_code : $("#exe").val()
					}

					$.get('{{ url("scan/workshop/tag/rfid") }}', data, function(result, status, xhr){
						if(result.status){
							duration = 0;
							count = true;
							started_at = new Date(result.started_at);

							$('#progress_bar').append().empty();
							$('#progress_bar').addClass('active');
							$('#progress_bar').html('45%');
							$('#progress_bar').css('width', '45%');
							$('#progress_bar').css('color', 'white');
							$('#progress_bar').css('font-weight', 'bold');


							$('#tag').val('');
							openSuccessGritter('Success!', result.message);
							document.getElementById("tag_input").value = result.wjo.tag;
							document.getElementById("order_no").value = result.wjo.order_no;
							document.getElementById("started_at").value = result.started_at;

							$('#text_priority').append().empty();
							if(result.wjo.priority == 'Urgent'){
								$('#text_order_no').css('color', 'red');
								$('#text_priority').css('padding-bottom', '1%');
								$('#text_priority').append('<span class="label label-danger">Urgent</span>');
							}else{
								$('#text_order_no').css('color', 'white');	
								$('#text_priority').css('padding-bottom', '1%');
								$('#text_priority').append('<span class="label label-default">Normal</span>');
							}
							$('#text_order_no').html(result.wjo.order_no);
							$('#text_item_name').html(result.wjo.item_name);
							$('#text_category').html(result.wjo.category);
							$('#text_quantity').html(result.wjo.quantity);						
							$('#text_material').html(result.wjo.material);						
							$('#text_problem_description').html(result.wjo.problem_description);					
							$('#text_target_date').html(result.wjo.target_date);
							$('#text_pic').html(result.wjo.name);


							$('#text_drawing').append().empty();
							if(result.wjo.file_name){
								$('#text_drawing').css('padding-top', '0.25%');
								$('#text_drawing').css('padding-bottom', '0.75%');
								$('#text_drawing').append('<button style="padding: 2%;" class="btn btn-sm btn-primary" onClick="downloadAtt(\''+result.wjo.file_name+'\')">'+ result.wjo.file_name +'&nbsp;&nbsp;&nbsp;<i class="fa fa-external-link"></i></button>');
							}else{
								$('#text_drawing').append('-');
							}



							$("#step").append().empty();
							var step = '';
							var green = ''
							step += '<ul class="timeline">';
							step += '<li class="time-label">';
							step += '<span class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
							step += '</li>';

							if(result.flow_process.length > 0){
								if(result.wjo_log.length == 0){
									green = 0;
								}else{
									green = result.wjo_log.length;
								}
								for (var i = 0; i < result.flow_process.length; i++) {
									step += '<li>';
									step += '<i class="fa fa-stack-1x" id="timeline_number_'+ i +'">'+ result.flow_process[i].sequence_process +'</i>';
									step += '<div class="timeline-item" id="timeline_box_'+ i +'" style="padding: 1%; padding-left: 2%;">';
									step += '<p style="padding-bottom: 0px; margin-bottom: 0px; font-size: 1vw; font-weight: bold;">Langkah '+ result.flow_process[i].sequence_process +'</p>';
									step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.flow_process[i].process_name +'</p>';
									step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.flow_process[i].machine_name +'</p>';
									step += '</div>';
									step += '</li>';

								}
								step += '<li>';
								step += '<i class="fa fa-check-square-o bg-blue"></i>';
								step += '</li>';
								step += '</ul>';
							}else{
								if(result.wjo_log.length > 0){
									for (var i = 0; i < result.wjo_log.length; i++) {
										step += '<li>';
										step += '<i class="fa fa-stack-1x">'+ result.wjo_log[i].sequence_process +'</i>';
										step += '<div class="timeline-item" style="padding: 1%; padding-left: 2%;">';
										step += '<p style="padding-bottom: 0px; margin-bottom: 0px; font-size: 1vw; font-weight: bold;">Langkah '+ result.wjo_log[i].sequence_process +'</p>';
										step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.wjo_log[i].process_name +'</p>';
										step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.wjo_log[i].machine_name +'</p>';
										step += '</div>';
										step += '</li>';
									}
									step += '<li>';
									step += '<i class="fa fa-stack-1x bg-green">'+ (result.wjo_log.length + 1) +'</i>';
									step += '<div class="timeline-item bg-green" style="padding: 1%; padding-left: 2%;">';
									step += '<p style="padding-bottom: 0px; margin-bottom: 0px; font-size: 1vw; font-weight: bold; color: white;">Langkah '+ (result.wjo_log.length + 1) +'</p>';
									step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.current_machine.process_name +'</p>';
									step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.current_machine.machine_name +'</p>';
									step += '</div>';
									step += '</li>';

								}else{
									step += '<li>';
									step += '<i class="fa fa-stack-1x bg-green">1</i>';
									step += '<div class="timeline-item bg-green" style="padding: 1%; padding-left: 2%;">';
									step += '<p style="padding-bottom: 0px; margin-bottom: 0px; font-size: 1vw; font-weight: bold; color: white;">Langkah 1</p>';
									step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.current_machine.process_name +'</p>';
									step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.current_machine.machine_name +'</p>';
									step += '</div>';
									step += '</li>';
								}	
							}

							$("#step").append(step);
							$("#timeline_number_" + green).addClass('bg-green');
							$("#timeline_box_" + green).addClass('bg-green');



						}
						else{
							if(result.message == 'Proses tidak sama dengan sebelumnya'){
								audio_error.play();
								openErrorGritter('Error', result.message);
								$('#tag').val('');
								if(confirm("Proses tidak sama dengan sebelumnya\nApakah anda ingin mengubah alur proses yang telah disimpan?")){
									$('#modalLeader').modal('show');
									document.getElementById("order_no").value = result.order_no;
									document.getElementById("item_number").value = result.item_number;
									document.getElementById("sequence_process").value = result.sequence_process;

								}

							}else{
								audio_error.play();
								openErrorGritter('Error', result.message);
								$('#tag').val('');
							}
						}
					});

}
}
});

function downloadAtt(attachment) {
	var data = {
		file:attachment
	}
	$.get('{{ url("download/workshop/drawing") }}', data, function(result, status, xhr){
		if(xhr.status == 200){
			if(result.status){
				window.open(result.file_path);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});

}

function finish(){
	count = false;

	var order_no = $("#order_no").val();
	var tag = $("#tag_input").val();
	var machine_code = $("#exe").val();
	var operator_id = $("#operator_id").val();
	var started_at = $("#started_at").val();

	var data = {
		order_no : order_no,
		tag : tag,
		machine_code : machine_code,
		operator_id : operator_id,
		started_at : started_at,
	}

	if(confirm("Apakah anda yakin untuk mengakhiri proses ini?\nData tidak dapat dikembalikan.")){
		$.post('{{ url("create/workshop/tag/process_log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tag').val('');
				$('#tag').focus();
				$('#progress_bar').removeClass('active');

				openSuccessGritter('Success!', result.message);			
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);

			}
		});				
	}else{
		$("#loading").hide();
	}


}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate() {
	var d = new Date();
	var day = addZero(d.getDate());
	var month = addZero(d.getMonth()+1);
	var year = addZero(d.getFullYear());
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
}
</script>
@endsection