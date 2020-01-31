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

	.kedip {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: pulse 1s infinite;  /* Safari 4+ */
		-moz-animation: pulse 1s infinite;  /* Fx 5+ */
		-o-animation: pulse 1s infinite;  /* Opera 12+ */
		animation: pulse 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes pulse {
		0%, 49% {
			background-color: #00a65a;
			color: white;
		}
		50%, 100% {
			background-color: #ffffff;
			color: #444;
		}
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="machine_code">
	<input type="hidden" id="tag_input">
	<input type="hidden" id="order_no">
	<input type="hidden" id="operator_id" value="{{ $employee_id }}">
	<input type="hidden" id="started_at">
	<input type="hidden" id="item_number">
	<input type="hidden" id="sequence_process">
	<input type="hidden" id="green">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-12" style="padding: 0px; margin-bottom: 0.5%;">
			<div class="progress-group" id="progress_div">
				<div class="progress" style="height: 30px; border: 1px solid; padding: 0px; margin: 0px;">
					<div class="progress-bar progress-bar-striped" id="progress_bar" style="font-size: 20px; padding-top: 0.25%;"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0.25%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:25px; width: 30%;" id="op">{{ $employee_id }}</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 25px;" id="op2">{{ $name }}</td>
					</tr>
				</tbody>
			</table>

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
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 25%;">PIC</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_pic"></td>
						<td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:20px; width: 20%;">Lampiran</td>
						<td style="padding-left: 2%; text-align: left; color: white; background-color: rgb(50, 50, 50); font-size: 20px; width: 30%;" id="text_attach"></td>
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
					
				</tbody>
			</table>

			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black; font-size: 20px;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black; height: 40px; font-size: 25px;" class="form-control" id="machine" placeholder="Tap Machine Tag..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black; font-size: 20px;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
			</div>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px;" id="machine_table">
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size: 25px; width: 30%;" id="machine_information"></td>
					</tr>
				</tbody>
			</table>

			<table class="table table-bordered" style="width: 100%; margin-bottom: 3%;">
				{{-- <thead id='process_progress_bar'>
					<tr>
						<th style="background-color: rgb(50, 50, 50); padding: 0px;" colspan="2">
							<div class="col-xs-12" style="padding: 0px;">
								<div class="progress-group" id="progress_div">
									<div class="progress" style="height: 30px; border: 1px solid; padding: 0px; margin: 0px;">
										<div class="progress-bar progress-bar-success progress-bar-striped" id="process_bar" style="font-size: 20px; padding-top: 0.5%;"></div>
									</div>
								</div>
							</div>
						</th>
					</tr>
				</thead> --}}
				<tbody>
					<tr>
						<td style="width: 75%; color: white; font-size: 2vw; background-color: rgb(50, 50, 50);">
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
		<div class="col-xs-5">
			<div class="nav-tabs-custom tab-default" style="background-color: rgb(60,60,60)">
				<ul class="nav nav-tabs">
					<li id="vendor-tab-1" class="vendor-tab active" style="font-weight: bold; font-size: 1vw; text-align: center; width: 50%; margin: 0px; color: rgb(60,60,60);"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Process Plan</a></li>
					<li id="vendor-tab-2" class="vendor-tab pull-right" style="font-weight: bold; font-size: 1vw; text-align: center; width: 50%; margin: 0px; color: rgb(60,60,60);"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Actual Process</a></li>
				</ul>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1">
					<div id="step"></div>
				</div>
				<div class="tab-pane" id="tab_2">
					<div id="actual"></div>
				</div>
			</div>

		</div>
	</section>

	
	<div class="modal fade" id="modalTag">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">WJO Card</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="tag" placeholder="Tap WJO Card" required>
							<br>
							<div class="col-xs-4 pull-right">
								<a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
									Logout
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
							</div>
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
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="leader" placeholder="Tap ID" required>
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

			$('#modalTag').modal({
				backdrop: 'static',
				keyboard: false
			});

			$('#process_progress_bar').hide();
			$('#machine_table').hide();



			setInterval(setTime, 1000);
		});

		var count_time = false;
		var count_progress_bar = false;
		var count_process_bar = false;
		var started_at;
		var std_time;
		var listed_time;
		var target_date;
		function setTime() {
			if(count_time){
				document.getElementById("hours").innerHTML = pad(parseInt(diff_seconds(new Date(), started_at) / 3600));
				document.getElementById("minutes").innerHTML = pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60));
				document.getElementById("seconds").innerHTML = pad(diff_seconds(new Date(), started_at) % 60);
			}

			if(count_process_bar){
				var actual = diff_seconds(new Date(), started_at);
				var percent = (actual / std_time) * 100;
				
				$('#process_bar').append().empty();
				if(percent <= 100){
					$('#process_bar').addClass('active');
					$('#process_bar').removeClass('progress-bar-danger');
					$('#process_bar').addClass('progress-bar-success');
					$('#process_bar').html(Math.round(percent)+'%');
					$('#process_bar').css('width', percent+'%');
					$('#process_bar').css('color', 'white');
					$('#process_bar').css('font-weight', 'bold');
				}else{
					$('#process_bar').addClass('active');
					$('#process_bar').addClass('progress-bar-danger');
					$('#process_bar').html('100%');
					$('#process_bar').css('width', '100%');
					$('#process_bar').css('color', 'white');
					$('#process_bar').css('font-weight', 'bold');

				}	
			}

			if(count_progress_bar){
				var actual = diff_seconds(new Date(), listed_time);
				var target = diff_seconds(target_date, listed_time);
				var percent = (actual / target) * 100;

				$('#progress_bar').append().empty();
				if(percent <= 100){
					$('#progress_bar').addClass('active');
					$('#progress_bar').addClass('progress-bar-success');
					$('#progress_bar').html(Math.round(percent)+'%');
					$('#progress_bar').css('width', percent+'%');
					$('#progress_bar').css('color', 'white');
					$('#progress_bar').css('font-weight', 'bold');
				}else{
					$('#progress_bar').addClass('active');
					$('#progress_bar').addClass('progress-bar-danger');
					$('#progress_bar').html('100%');
					$('#progress_bar').css('width', '100%');
					$('#progress_bar').css('color', 'white');
					$('#progress_bar').css('font-weight', 'bold');

				}	
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

		$('#modalLeader').on('shown.bs.modal', function () {
			$('#leader').focus();
		});

		$('#modalTag').on('shown.bs.modal', function () {
			$('#tag').focus();
		});

		$('#tag').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#tag").val().length >= 10){
					var tag = $("#tag").val();

					var data = {
						tag : tag
					}

					$.get('{{ url("scan/workshop/tag/rfid") }}', data, function(result, status, xhr){
						if(result.status){
							
							count_progress_bar = true;
							
							listed_time = new Date(result.listed_time.date);
							var date = result.wjo.target_date.split("-");
							target_date = new Date(date[0], (parseInt(date[1]) - 1), date[2], 23, 59, 59, 0);

							$('#tag').val('');
							$('#machine').focus();

							$('#modalTag').modal('hide');
							openSuccessGritter('Success!', result.message);
							document.getElementById("tag_input").value = result.wjo.tag;
							document.getElementById("order_no").value = result.wjo.order_no;

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
							$('#text_order_no').html(result.wjo.order_no +' ('+result.wjo.tag_remark+')');
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
								$('#text_drawing').append('<button style="padding: 2%;" class="btn btn-sm btn-primary" onClick="downloadDrw(\''+result.wjo.file_name+'\')">'+ result.wjo.file_name +'&nbsp;&nbsp;&nbsp;<i class="fa fa-external-link"></i></button>');
							}else{
								$('#text_drawing').append('-');
							}

							$('#text_attach').append().empty();
							if(result.wjo.attachment){
								$('#text_attach').css('padding-top', '0.25%');
								$('#text_attach').css('padding-bottom', '0.75%');
								$('#text_attach').append('<button style="padding: 2%;" class="btn btn-sm btn-primary" onClick="downloadAtt(\''+result.wjo.attachment+'\')">'+ result.wjo.attachment +'&nbsp;&nbsp;&nbsp;<i class="fa fa-external-link"></i></button>');
							}else{
								$('#text_attach').append('-');
							}

							$("#step").append().empty();
							$("#actual").append().empty();
							var step = '';
							var actual = '';
							var green = '';
							
							if(result.flow_process.length > 0){
								$('#process_progress_bar').show();
								if(result.wjo_log.length == 0){
									green = 0;
								}else{
									green = result.wjo_log.length;
								}
								step += '<ul class="timeline">';
								step += '<li class="time-label">';
								step += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;</span>';
								step += '</li>';
								for (var i = 0; i < result.flow_process.length; i++) {
									step += '<li>';
									step += '<i class="fa fa-stack-1x">'+ result.flow_process[i].sequence_process +'</i>';
									step += '<div class="timeline-item" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
									step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.flow_process[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ (result.flow_process[i].std_time / 60) +'s<span></p>';
									step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.flow_process[i].machine_name +'</p>';
									step += '</div>';
									step += '</li>';
								}
								step += '<li>';
								step += '<i class="fa fa-check-square-o bg-blue"></i>';
								step += '</li>';
								step += '</ul>';
								
							}

							if(result.wjo_log.length > 0){
								$('#process_progress_bar').show();

								actual += '<ul class="timeline">';
								actual += '<li class="time-label">';
								actual += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;</span>';
								actual += '</li>';
								for (var i = 0; i < result.wjo_log.length; i++) {
									actual += '<li>';
									actual += '<i class="fa fa-stack-1x bg-green">'+ result.wjo_log[i].sequence_process +'</i>';
									actual += '<div class="timeline-item bg-green" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
									actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.wjo_log[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ Math.ceil(result.wjo_log[i].actual / 60) +'s<span></p>';
									actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.wjo_log[i].machine_name +'</p>';
									actual += '</div>';
									actual += '</li>';
								}
							}

							$("#step").append(step);
							$("#actual").append(actual);

							document.getElementById("green").value = green;

							for (var i = 0; i < green; i++) {
								$("#timeline_number_" + i).addClass('bg-green');
								$("#timeline_box_" + i).addClass('bg-green');						
							}
							
						}
						else{
							audio_error.play();
							openErrorGritter('Error', result.message);
							$('#tag').val('');
						}
						
					});
}
}
});

$('#machine').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($("#machine").val().length >= 10){
			var machine_code = $("#machine").val();
			var	order_no = $("#order_no").val();
			var	operator_id = $("#operator_id").val();

			var data = {
				machine_tag : machine_code,
				order_no : order_no,
				operator_id : operator_id
			}

			$.get('{{ url("fetch/workshop/machine") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					count_time = true;
					count_process_bar = true;

					$('#machine').val('');
					$('#machine_table').show();
					document.getElementById("machine_code").value = result.current_machine.machine_code;
					document.getElementById("machine_information").innerHTML = result.current_machine.process_name + '  (' + result.current_machine.machine_name + ')';					
					document.getElementById("started_at").value = result.started_at;
					started_at = new Date(result.started_at);

					$("#actual").append().empty();

					var actual = '';
					var green = '';

					actual += '<ul class="timeline">';
					actual += '<li class="time-label">';
					actual += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;</span>';
					actual += '</li>';
					if(result.wjo_log.length > 0){
						$('#process_progress_bar').show();
						
						green = result.wjo_log.length;

						for (var i = 0; i < result.wjo_log.length; i++) {
							actual += '<li>';
							actual += '<i class="fa fa-stack-1x" id="timeline_number_'+ i +'">'+ result.wjo_log[i].sequence_process +'</i>';
							actual += '<div class="timeline-item" id="timeline_box_'+ i +'" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
							actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.wjo_log[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ Math.ceil(result.wjo_log[i].actual / 60) +'s<span></p>';
							actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.wjo_log[i].machine_name +'</p>';
							actual += '</div>';
							actual += '</li>';
						}

						actual += '<li>';
						actual += '<i class="fa fa-stack-1x" id="timeline_number_'+ green +'">'+ (green+1) +'</i>';
						actual += '<div class="timeline-item" id="timeline_box_'+ green +'" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
						actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.current_machine.process_name +'</p>';
						actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.current_machine.machine_name +'</p>';
						actual += '</div>';
						actual += '</li>';

					}else{
						green = 0;

						actual += '<li>';
						actual += '<i class="fa fa-stack-1x" id="timeline_number_'+ green +'">1</i>';
						actual += '<div class="timeline-item" id="timeline_box_'+ green +'" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
						actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.current_machine.process_name +'</p>';
						actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.current_machine.machine_name +'</p>';
						actual += '</div>';
						actual += '</li>';
					}


					$("#actual").append(actual);
					document.getElementById("green").value = green;

					for (var i = 0; i < green; i++) {
						$("#timeline_number_" + i).addClass('bg-green');
						$("#timeline_box_" + i).addClass('bg-green');						
					}

					$("#timeline_number_" + green).addClass('kedip');
					$("#timeline_box_" + green).addClass('kedip');

				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#machine').val('');

					if(result.message == 'Proses tidak sama dengan sebelumnya'){
						// alert('Alur proses tidak sesuai');
						if(confirm("Alur proses tidak sama dengan sebelumnya\nUntuk mengubah alur yang sudah ada perlu persetujuan Leader\nApakah anda ingin mengubah alur proses yang telah disimpan?")){
							$('#modalLeader').modal('show');
							document.getElementById("order_no").value = result.order_no;
							document.getElementById("item_number").value = result.item_number;
							document.getElementById("sequence_process").value = result.sequence_process;
						}
					}
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
				order_no : $("#order_no").val(),
				item_number : $("#item_number").val(),
				sequence_process : $("#sequence_process").val(),
			}
			$.get('{{ url("scan/workshop/leader/rfid") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#modalLeader').modal('hide');

					$('#leader').val('');
					$('#machine').focus();
					count_process_bar = false;

					$("#step").append().empty();
					$("#actual").append().empty();
					var step = '';
					var actual = '';
					var green = '';

					if(result.flow_process.length > 0){
						$('#process_progress_bar').show();
						if(result.wjo_log.length == 0){
							green = 0;
						}else{
							green = result.wjo_log.length;
						}
						step += '<ul class="timeline">';
						step += '<li class="time-label">';
						step += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;</span>';
						step += '</li>';
						for (var i = 0; i < result.flow_process.length; i++) {
							step += '<li>';
							step += '<i class="fa fa-stack-1x">'+ result.flow_process[i].sequence_process +'</i>';
							step += '<div class="timeline-item" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
							step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.flow_process[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ (result.flow_process[i].std_time / 60) +'s<span></p>';
							step += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.flow_process[i].machine_name +'</p>';
							step += '</div>';
							step += '</li>';
						}
						step += '<li>';
						step += '<i class="fa fa-check-square-o bg-blue"></i>';
						step += '</li>';
						step += '</ul>';
					}

					if(result.wjo_log.length > 0){
						$('#process_progress_bar').show();

						actual += '<ul class="timeline">';
						actual += '<li class="time-label">';
						actual += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Start&nbsp;&nbsp;&nbsp;&nbsp;</span>';
						actual += '</li>';
						for (var i = 0; i < result.wjo_log.length; i++) {
							actual += '<li>';
							actual += '<i class="fa fa-stack-1x bg-green">'+ result.wjo_log[i].sequence_process +'</i>';
							actual += '<div class="timeline-item bg-green" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
							actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.wjo_log[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ Math.ceil(result.wjo_log[i].actual / 60) +'s<span></p>';
							actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.wjo_log[i].machine_name +'</p>';
							actual += '</div>';
							actual += '</li>';
						}
					}

					$("#step").append(step);
					$("#actual").append(actual);

					document.getElementById("green").value = green;

					for (var i = 0; i < green; i++) {
						$("#timeline_number_" + i).addClass('bg-green');
						$("#timeline_box_" + i).addClass('bg-green');						
					}
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

function downloadDrw(attachment) {
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

function downloadAtt(attachment) {
	var data = {
		file:attachment
	}
	$.get('{{ url("download/workshop/attachment") }}', data, function(result, status, xhr){
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
	var order_no = $("#order_no").val();
	var tag = $("#tag_input").val();
	var machine_code = $("#machine_code").val();
	var operator_id = $("#operator_id").val();
	var started_at = $("#started_at").val();
	var green = $("#green").val();

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
				$("#timeline_number_" + green).removeClass('kedip');
				$("#timeline_box_" + green).removeClass('kedip');

				$("#timeline_number_" + green).addClass('bg-green');
				$("#timeline_box_" + green).addClass('bg-green');

				var actual = '';
				actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 23px;">'+ result.wjo_log.process_name +'<span class="pull-right" style="margin-right: 3%;">'+ Math.ceil(result.wjo_log.actual / 60) +'s<span></p>';
				actual += '<p style="padding: 0px; font-size: 18px; font-weight: bold;">'+ result.wjo_log.machine_name +'</p>';

				$("#timeline_box_" + green).html("");
				$("#timeline_box_" + green).append(actual);


				count_time = false;
				count_process_bar = false;
				$('#process_bar').removeClass('active');

				$('#machine').focus();

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
		time: '4000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '4000'
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