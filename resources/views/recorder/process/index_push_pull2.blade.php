@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	body{
		padding-right: 0px !important;
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
	td{
		text-overflow: ellipsis;
	}
	table {
		/*table-layout:fixed;*/
		text-align: center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
		/*margin-top:20px;*/
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<input type="hidden" id="data" value="data">
	<div class="row" style="padding-top: 0px">
		<div class="col-md-12">
			<div class="box box-solid" style="background-color: #f0f0f0">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<table class="table table-striped">
										<tr>
											<td colspan="3" style="font-size: 20px; font-weight: bold;background-color: #4db6ac;border: 1px solid black">
												PUSH PULL CHECK
											</td>
										</tr>
										<tr>
											<td style="width:2%;background-color: #26a69a;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #26a69a;border: 1px solid black" id="op3">
												-
											</td>
											<td style="width:30%;background-color: #80cbc4;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #26a69a;border: 1px solid black" id="op4">
												-
											</td>
										</tr>
									</table>
								</div>
								<div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Last Check (Kgf):</center></span>
									<input type="text" name="last_check" id="last_check" class="form-control" value="3.0" required="required" pattern="" title="" style="width: 100%;height: 250px;background-color: #ffdd71;font-size: 10vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" disabled>
								</div>
								<div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Judgement :</center></span>
									<input type="text" name="judgement_push_pull" id="judgement_push_pull" class="form-control" value="NG" required="required" pattern="" title="" style="width: 100%;height: 250px;font-size: 10vw;text-align: center;font-weight:bold;background-color: #ff6363;color: #163756;border: 1px solid black" disabled>
								</div>
							</div>
							<span style="font-size: 20px; font-weight: bold;"><center>COLOR :</center></span>
							<input type="text" name="color_push_pull" id="color_push_pull" class="form-control" value="YRS" required="required" pattern="" title="" style="width: 100%;height: 140px;background-color: #ffd0b0;font-size: 8vw;text-align: center;font-weight:bold;color: #163756;border: 1px solid black" disabled>
							<!-- <span style="font-size: 20px; font-weight: bold;"><center>Jenis Middle:</center></span>
							<input type="text" name="middle_type3" id="middle_type3" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 80px;background-color: #e7ff8c;font-size: 5vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly>
							<input type="text" name="middle_type_desc3" id="middle_type_desc3" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 40px;background-color: #e7ff8c;font-size: 2vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<!-- <div class="col-xs-12">
							<center><label for="exampleInputEmail1">ID Card Operator Kango</label></center>
							<div class="input-group">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
								<input class="form-control" style="text-align: center;width: 100%;border:1px solid black" type="text" id="operator" placeholder="Scan ID Card" required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
							</div>
						</div> -->
						<div class="col-xs-12">
							<center><label for="exampleInputEmail1">ID Card Operator Push Pull</label></center>
							<div class="input-group">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
								<input class="form-control" style="text-align: center;width: 100%;border:1px solid black" type="text" id="operator2" placeholder="Scan ID Card" required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
							</div>
						</div>
						<center><label for="exampleInputEmail1">Color</label></center>
						<div class="col-xs-12" id="color_choice">
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #fffac4" class="btn btn-default" id="IVORY" onclick="getModel(this.id)">IVORY</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-success" id="GREEN" onclick="getModel(this.id)">GREEN</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-primary" id="BLUE" onclick="getModel(this.id)">BLUE</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #ff40cf;color: white" class="btn btn-default" id="PINK" onclick="getModel(this.id)">PINK</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-danger" id="RED" onclick="getModel(this.id)">RED</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #96631b;color: white" class="btn btn-default" id="BROWN" onclick="getModel(this.id)">BROWN</button>
							</div>
						</div>
						<div class="col-xs-12" id="choice_color"><button style="width: 100%;font-size: 20px;color: black;font-weight: bold;background-color: #ffdd71" class="btn btn-default" id="color_fix" onclick="changeColor()">YRS</button>
						</div>
						<!-- <center><label  style="padding-top: 20px" for="exampleInputEmail1">Jenis Middle</label></center>
						<div class="col-xs-12" id="middle_choice">
							<div class="col-xs-6">
								<button style="width: 100%;font-size: 20px;background-color: #ffdd71" class="btn btn-default" id="B" onclick="getMiddle(this.id,this.value)" value="Baroque">Baroque
								</button>
							</div>
							<div class="col-xs-6">
								<button style="width: 100%;font-size: 20px;background-color: #ffab40" class="btn btn-default" id="G" onclick="getMiddle(this.id,this.value)" value="German">German
								</button>
							</div>
						</div>
						<div class="col-xs-12" id="middle">
							<button style="width: 100%;font-size: 20px;background-color: #ffdd71" class="btn btn-default" id="middle_type_desc2" onclick="changeMiddle()">
							</button>
						</div> -->
						<div class="col-xs-12" style="padding-top: 60px">
							<button class="btn btn-success" style="width: 100%;font-size: 40px;font-weight: bold;" onclick="submit()">MULAI PROSES</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');
	$('#injection_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#operator2').val('');
		// $('#middle').hide();
		$('#choice_color').hide();
	});

	// function changeMiddle() {
	// 	$('#middle').hide();
	// 	$('#middle_choice').show();
	// }

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator2').focus();
	});

	$('#operator2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator2").val().length >= 8){
				var data = {
					employee_id : $("#operator2").val()
				}

				$.get('{{ url("scan/push_pull/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#op3').html(result.employee.employee_id);
						$('#op4').html(result.employee.name);
						$('#operator2').prop('disabled', true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', 'Employee ID Invalid.');
						$('#operator2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator2").val("");
			}
		}
	});

	function getModel(model) {
		// $('#color_camera').val(model);
		$('#color_push_pull').val(model);
		$('#color_choice').hide();
		$('#color_fix').html(model);
		$('#choice_color').show();
	}

	function changeColor(){
		$('#color_fix').html('YRS');
		$('#choice_color').hide();
		$('#color_choice').show();
	}

	function submit() {
		if ($('#operator2').val() == '') {
			alert('Isi Semua Data!');
		}
		else if($('#color_fix').text() == 'YRS'){
			alert('Pilih Warna!');
		}
		else{
			setInterval(push_pull,1000);
			// setInterval(camera_kango,1000);
			// setInterval(camera_kango2,1000);
			$('#modalOperator').modal('hide');
		}
	}

	function push_pull() {
			var data = {
				model : $('#color_push_pull').val(),
				check_date : getActualFullDate(),
				pic_check : $('#op4').text(),
			}

			$.post('{{ url("push_pull/store_push_pull") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.judgement.length > 0) {
						$('#last_check').val(result.value);
						$('#judgement_push_pull').val(result.judgement);
						if (result.judgement == 'OK') {
							document.getElementById('judgement_push_pull').style.backgroundColor = "#57ff86";
							document.getElementById('judgement_push_pull').style.color = "#163756";
						}
						else if(result.judgement == 'NG'){
							audio_error.play();
							document.getElementById('judgement_push_pull').style.backgroundColor = "#ff6363";
							document.getElementById('judgement_push_pull').style.color = "#163756";
						}
					}
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
	}

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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

</script>
@endsection
