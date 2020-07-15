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
	#ngList {
		height:120px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:420px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<thead>
					<!-- <tr>
						<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Total Shot Counter <span style="color: red" id="counter"></span></th>
					</tr> -->
					<tr>
						<th style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Employee ID</th>
						<th style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Name</th>
						<th style=" background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Mesin</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:1.5vw; width: 30%;" id="op">-</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;" id="op2">-</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:1.5vw;" id="mesin">-</td>
					</tr>
					<!-- <tr>
						<td style="width: 10px; background-color: rgb(220,220,220); padding:0;font-size: 20px;" id="gaugechart"></td>
					</tr>
					<tr>
						<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black"><b id="statusLog">Running</b> - <b id="statusMesin">Mesin</b></td>
					</tr>
					<tr>							
						<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"> <b id="colorpart"> - </b> </td>
					</tr>
					<tr>							
						<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"><b id="modelpart"> - </b> </td>
					</tr>
					<tr>							
						<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"><b id="moldingpart"> - </b> </td>
					</tr>
					<tr>							
						<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(255,255,102);"><div class="timerrunning">
				            <span class="hourrunning" id="hourrunning">00</span> h : <span class="minuterunning" id="minuterunning">00</span> m : <span class="secondrunning" id="secondrunning">00</span> s
				            <input type="hidden" id="running" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
				        	</div>
				    	</td>
					</tr> -->
					
				</tbody>
			</table>
			<div class="col-xs-6" style="padding: 0px;margin-bottom: 20px;">
				<div class="input-group" style="padding-top: 10px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag_molding" name="tag_molding" placeholder="Scan Tag Molding" required disabled>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px">
				<div class="input-group" style="padding-top: 10px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag_product" name="tag_product" placeholder="Scan Tag Product ..." required disabled>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
			</div>
			<table class="table table-bordered" style="padding-top: 20px;padding-bottom: 0px">
				<tr>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Molding
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Part Type
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Part Name
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Color
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Cavity
					</td>
				</tr>
				<tr>
					<td id="molding" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<td id="part_type" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<td id="part_name" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<td id="color" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<td id="cavity" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
				</tr>
			</table>

			<div class="col-xs-12" style="padding: 0px;">
				<div style="text-align: center;" id="timer">
					<div class="timerinjection" style="color:#000;font-size: 80px;background-color: #85ffa7">
			            <span class="hourinjection">00</span>:<span class="minuteinjection">00</span>:<span class="secondinjection">00</span>
			        </div>
			        <div class="timeout" style="color:red;font-size: 80px;display: none">
			        </div>
				</div>
			</div>

			<div class="col-xs-12" style="padding: 0px;padding-top: 10px">
				<input type="hidden" id="start_time">
				<input type="hidden" id="molding_part_type">
				<input type="hidden" id="material_number">
				<button class="btn btn-success" id="btn_mulai" style="font-size: 30px;font-weight: bold;width: 100%" onclick="mulaiProses()">
					MULAI PROSES
				</button>
			</div>
			<div style="padding-top: 20px;" id="perolehan">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
					<tbody>
						<tr>
							<td colspan="2" style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Perolehan
							</td>
						</tr>
						<tr>
							<td>
								<input type="number" class="pull-right" name="total_shot" style="height: 4.5vw;font-size: 1.5vw;width: 100%;text-align: center;vertical-align: middle;" id="total_shot" placeholder="Total Shot" disabled>
							</td>
							<td>
								<input type="number" class="pull-right" name="running_shot" style="height: 4.5vw;font-size: 1.5vw;width: 100%;text-align: center;vertical-align: middle;" id="running_shot" placeholder="Running Shot">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-12" style="padding: 0px;padding-top: 10px">
				<button class="btn btn-danger" id="btn_selesai" style="font-size: 30px;font-weight: bold;width: 100%" onclick="selesaiProses()">
					SELESAI PROSES
				</button>
			</div>
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			

			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loop" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
							<td id="ng{{$nomor+1}}" style="font-size: 20px;">{{ $ng_list->ng_name }}</td>
							<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="padding: 0px;padding-top: 10px">
				<button class="btn btn-warning" id="btn_ganti" onclick="changeMesin()" style="font-size: 30px;font-weight: bold;width: 100%">
					GANTI MESIN
				</button>
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
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMesin">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="mesin_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Pilih mesin</span></center>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12" id="mesin_btn">
									@foreach($mesin as $mesin)
									<div class="col-xs-3" style="padding-top: 5px">
										<center>
											<button class="btn btn-primary" id="{{$mesin}}" style="width: 200px;font-size: 15px" onclick="getMesin(this.id)">{{$mesin}}</button>
										</center>
									</div>
									@endforeach
								</div>
						    </div>
						</div>
					</div>
					<div class="col-xs-12" id="mesin_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin</span></center>
									</div>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-primary" id="mesin_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesin2()">
									MESIN
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<button onclick="saveMesin()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalProduct">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="product_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Mesin</span></center>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12" id="product_btn">
								</div>
						    </div>
						</div>
					</div>
					<div class="col-xs-12" id="product_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Tipe Produk</span></center>
									</div>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-primary" id="product_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeProduct()">
									YRS
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="cavity_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Cavity</span></center>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12" id="cavity_btn">
								</div>
						    </div>
						</div>
					</div>
					<div class="col-xs-12" id="cavity_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Cavity</span></center>
									</div>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-info" id="cavity_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeCavity()">
									CAVITY
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer">
							<button onclick="saveProduct()" class="btn btn-success pull-right">
								CONFIRM
							</button>
							<button class="btn btn-warning pull-left" onclick="cancelProcess()">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Reason</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="Reason" placeholder="Reason" required><br>
						<button class="btn btn-warning pull-left" data-dismiss="modal">Cancel</button>
						<button class="btn btn-success pull-right" onclick="saveStatus()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var hour;
    var minute;
    var second;
    var intervalTime;
    var intervalUpdate;
	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag_product').val('');
		$('#tag_molding').val('');
		$('#tag_product').prop('disabled', true);
		$('#tag_molding').prop('disabled', true);
		$('#running_shot').val('');
		$('#total_shot').val('');
		var mesin = "{{substr($name,10)}}";
		$('#cavity_fix').hide();
		$('#product_fix').hide();
		$('#mesin_fix').hide();
		$('#perolehan').hide();
		$('#btn_selesai').hide();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#modalMesin').modal('show');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$("#tag_molding").removeAttr('disabled');
						$('#tag_molding').focus();
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

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_product").val().length >= 7){
				var data = {
					tag : $("#tag_product").val(),
					part_type : $("#molding_part_type").val()
				}
				
				$.get('{{ url("scan/new_tag_injeksi") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#tag_product').prop('disabled', true);
						var btn_product = "";
						$('#product_btn').empty();
						$.each(result.product, function(key, value) {
							btn_product += '<div class="col-xs-3" style="padding-top: 5px">';
							btn_product += '<center><button class="btn btn-primary" id="'+value.product+'" style="width: 200px;font-size: 15px" onclick="getProduct(this.id)">'+value.product+'';
							btn_product += '</button></center>';
							btn_product += '</div>';
						});
						$('#product_btn').append(btn_product);

						var btn_cavity = "";
						$('#cavity_btn').empty();
						$.each(result.cavity, function(key, value) {
							btn_cavity += '<div class="col-xs-3" style="padding-top: 5px">';
							btn_cavity += '<center><button class="btn btn-info" id="'+value.no_cavity+'" style="width: 200px;font-size: 15px" onclick="getCavity(this.id)">'+value.no_cavity+'';
							btn_cavity += '</button></center>';
							btn_cavity += '</div>';
						});
						$('#cavity_btn').append(btn_cavity);
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_product").val("");
						$("#tag_product").focus();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_product").val("");
				$("#tag_product").focus();
			}			
		}
	});

	$('#tag_molding').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_molding").val().length >= 7){
				var data = {
					tag : $("#tag_molding").val(),
					// part : $("#part_type").text(),
				}
				
				$.get('{{ url("scan/part_molding") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#tag_molding').prop('disabled', true);
						$('#molding').html(result.part.part);
						$('#molding_part_type').val(result.part.product);
						$('#tag_product').removeAttr('disabled');
						$('#tag_product').focus();
					}
					else{
						openErrorGritter('Error!', 'Molding Invalid');
						audio_error.play();
						$("#tag_molding").val("");
						$("#tag_molding").focus();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_product").val("");
				$("#tag_product").focus();
			}			
		}
	});

	function getProduct(value) {
		$('#product_fix').show();
		$('#product_choice').hide();
		$('#product_fix2').html(value);
	}

	function changeProduct() {
		$('#product_fix').hide();
		$('#product_choice').show();
		$('#product_fix2').html("YRS");
	}

	function getMesin(value) {
		$('#mesin_fix').show();
		$('#mesin_choice').hide();
		$('#mesin_fix2').html(value);
	}

	function changeMesin2() {
		$('#mesin_fix').hide();
		$('#mesin_choice').show();
		$('#mesin_fix2').html("MESIN");
	}

	function changeMesin() {
		$('#tag_product').val("");
		$('#tag_molding').val("");
		$('#tag_product').prop('disabled',true);
		$('#tag_molding').prop('disabled',true);
		$('#start_time').val("");
		$('#molding').html("-");
		$('#part_name').html("-");
		$('#part_type').html("-");
		$('#color').html("-");
		$('#cavity').html("-");
		$('#total_shot').val("");
		$('#material_number').val("");
		$('#btn_mulai').show();
		$('#btn_selesai').hide();
		$('#perolehan').hide();
		$('#modalProduct').modal('hide');
		$('#modalMesin').modal('show');
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			// for (var j = 0; j < ng_name.length; j++ ) {
				$('#count'+i).html(0);
			// }
		}
		clearTimeout(intervalTime);
		clearInterval(intervalUpdate);
		$('div.timerinjection span.secondinjection').html("00");
		$('div.timerinjection span.minuteinjection').html("00");
		$('div.timerinjection span.hourinjection').html("00");
	}

	function getCavity(value) {
		$('#cavity_fix').show();
		$('#cavity_choice').hide();
		$('#cavity_fix2').html(value);
	}

	function changeCavity() {
		$('#cavity_fix').hide();
		$('#cavity_choice').show();
		$('#cavity_fix2').html("CAVITY");
	}

	function changeMolding() {
		$('#molding').html('-');
		$('#tag_molding').removeAttr('disabled');
		$('#tag_molding').val('');
		$('#tag_molding').focus();
	}

	function saveProduct() {
		var product = $('#product_fix2').text();
		var productSplit = product.split("-");
		$('#part_type').html(productSplit[1]);
		$('#part_name').html(productSplit[0]);
		$('#color').html(productSplit[2]);
		$('#material_number').val(productSplit[3]);
		$('#cavity').html($('#cavity_fix2').text());
		$('#modalProduct').modal('hide');
		intervalUpdate = setInterval(update_temp,10000);
		create_temp();
		update_tag();
	}

	function saveMesin() {
		$('#modalMesin').modal('hide');
		$('#mesin').html($('#mesin_fix2').text());
		$('#tag_molding').focus();
		get_temp();
	}

	function changeProductFix() {
		$('#modalProduct').modal('show');
		$('#part_name').html('-');
		$('#part_type').html("-");
		// $('#start_time').html("-");
	}

	function cancelProcess(){
		$('#modalProduct').modal('hide');
		// $('#start_time').html('-');
		$('#part_type').html('-');
		$('#part_name').html('-');
		$('#tag_product').val('');
		$('#tag_product').removeAttr('disabled');
		$('#tag_product').focus();
		$('#tag_molding').prop('disabled',true);
	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			$('#count'+id).text(parseInt(count)+1);
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
	}

	function mulaiProses() {
		$('#modalProduct').modal('show');
		countUpFromTime(getActualFullDate());
		$('#start_time').val(getActualFullDate());
		// get_temp();
		$('#btn_mulai').hide();
		$('#btn_selesai').show();
		$('#perolehan').show();
		$('#btn_ganti').show();
	}

	function create_temp() {
		var start_time = $('#start_time').val();
		var data = {
			tag_product:$('#tag_product').val(),
			tag_molding:$('#tag_molding').val(),
			operator_id:$('#op').text(),
			start_time:start_time,
			mesin:$('#mesin').text(),
			part_name:$('#part_name').text(),
			part_type:$('#part_type').text(),
			color:$('#color').text(),
			molding:$('#molding').text(),
			cavity:$('#cavity').text(),
			material_number:$('#material_number').val()
		}
		$.post('{{ url("index/injeksi/create_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function update_tag() {
		var data = {
			tag:$('#tag_product').val(),
			operator_id:$('#op').text(),
			part_name:$('#part_name').text(),
			part_type:$('#part_type').text(),
			color:$('#color').text(),
			cavity:$('#cavity').text(),
			location:$('#mesin').text(),
			material_number:$('#material_number').val()
		}
		$.post('{{ url("index/injeksi/update_tag") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function get_temp() {
		var data = {
			// tag_product:$('#tag_product').val(),
			// tag_molding:$('#tag_molding').val(),
			mesin:$('#mesin').html()
		}
		$.get('{{ url("index/injeksi/get_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				var start_time = result.datas.start_time;
				$('#tag_product').val(result.datas.tag_product);
				$('#tag_molding').val(result.datas.tag_molding);
				$('#tag_product').prop('disabled',true);
				$('#tag_molding').prop('disabled',true);
				$('#start_time').val(start_time);
				$('#molding').html(result.datas.molding);
				$('#part_name').html(result.datas.part_name);
				$('#part_type').html(result.datas.part_type);
				$('#color').html(result.datas.color);
				$('#cavity').html(result.datas.cavity);
				$('#total_shot').val(result.datas.shot);
				$('#material_number').val(result.datas.material_number);
				countUpFromTime(new Date(start_time));
				$('#btn_mulai').hide();
				$('#btn_selesai').show();
				$('#perolehan').show();
				$('#modalProduct').modal('hide');
				if (result.datas.ng_name != null) {
					var ng_name = result.datas.ng_name.split(',');
					var ng_count = result.datas.ng_count.split(',');
					var jumlah_ng = '{{$nomor+1}}';
					for (var i = 1; i <= jumlah_ng; i++ ) {
						for (var j = 0; j < ng_name.length; j++ ) {
							if($('#ng'+i).text() == ng_name[j]){
								$('#count'+i).html(ng_count[j]);
							}
						}
					}
				}
				intervalUpdate = setInterval(update_temp,10000);
				$('#btn_ganti').show();
			}
			else{
				$('#tag_molding').removeAttr('disabled');
				$('#tag_molding').focus();
			}
		});
	}

	function update_temp() {
		if ($('#running_shot').val() == "") {
			var shot =0;
		}else{
			var shot = parseInt($('#running_shot').val());
		}
		var ng_name = [];
		var ng_count = [];
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if($('#count'+i).text() != 0){
				ng_name.push($('#ng'+i).text());
				ng_count.push($('#count'+i).text());
			}
		}
		var data = {
			tag_product:$('#tag_product').val(),
			tag_molding:$('#tag_molding').val(),
			shot:shot,
			ng_name:ng_name.join(),
			ng_count:ng_count.join(),
		}
		$.post('{{ url("index/injeksi/update_temp") }}', data, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success!', result.message);
				$('#total_shot').val(result.total_shot);
				$('#running_shot').val("");
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function selesaiProses() {
		$('#selesai_button').prop('disabled', true);
		$('#loading').show();
		var ng_name = [];
		var ng_count = [];
		var ng_counting = 0;
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if($('#count'+i).text() != 0){
				ng_name.push($('#ng'+i).text());
				ng_count.push($('#count'+i).text());
				ng_counting = ng_counting + parseInt($('#count'+i).text());
			}
		}
		var data = {
			tag_product:$('#tag_product').val(),
			tag_molding:$('#tag_molding').val(),
			operator_id:$('#op').text(),
			start_time:$('#start_time').val(),
			mesin:$('#mesin').text(),
			part_name:$('#part_name').text(),
			part_type:$('#part_type').text(),
			color:$('#color').text(),
			molding:$('#molding').text(),
			cavity:$('#cavity').text(),
			shot:parseInt($('#total_shot').val()),
			material_number:$('#material_number').val(),
			ng_name:ng_name.join(),
			ng_count:ng_count.join(),
			ng_counting:ng_counting
		}
		$.post('{{ url("index/injeksi/create_log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				location.reload();
			}
			else{
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

	function countUpFromTime(countFrom) {
	  countFrom = new Date(countFrom).getTime();
	  var now = new Date(),
	      countFrom = new Date(countFrom),
	      timeDifference = (now - countFrom);
	    
	  var secondsInADay = 60 * 60 * 1000 * 24,
	      secondsInAHour = 60 * 60 * 1000;
	    
	  days = Math.floor(timeDifference / (secondsInADay) * 1);
	  years = Math.floor(days / 365);
	  if (years > 1){
	  	days = days - (years * 365) 
	  }
	  hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
	  mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
	  secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

	  $('div.timerinjection span.secondinjection').html(addZero(secs));
	  $('div.timerinjection span.minuteinjection').html(addZero(mins));
	  $('div.timerinjection span.hourinjection').html(addZero(hours));

	  clearTimeout(intervalTime);
	  intervalTime = setTimeout(function(){ countUpFromTime(countFrom); }, 1000);
	}
</script>
@endsection