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
	#moldingLog > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#moldingLogPasang > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:150px;
		overflow-y: scroll;
	}

	#molding_pasang {
		height:150px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:480px;
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
	<input type="hidden" id="molding_code" value="">
	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 5px; padding-left: 0">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">LEPAS MOLDING<span style="color: red" id="counter"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size:1vw; width: 1%;" id="op_0">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;width: 1%" id="op_1">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;width: 1%" id="op_2">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 15px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;"><b>Molding List</b></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								PIC Pasang
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Shot
							</th>
						</tr>
					</thead>
					<tbody id="moldingLog">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="color_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Total Shot</td>
							<td id="total_shot_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="lepastime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerlepas">
					            <span class="hourlepas" id="hourlepas">00</span> h : <span class="minutelepas" id="minutelepas">00</span> m : <span class="secondlepas" id="secondlepas">00</span> s
					            <input type="hidden" id="lepas" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_lepas" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="reasonlepas">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);" id="reason">-
					    	</td>
						</tr>
						<!-- <tr>
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"> KEPUTUSAN
					    	</td>
						</tr> -->
						<tr>
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px" id="div_keputusan">
									KEPUTUSAN
								</div>
								<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px" id="div_maintenance">
									<button id="btn_maintenance" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeDecision('MAINTENANCE')" class="btn btn-warning">MAINTENANCE</button>
								</div>
								<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px" id="div_ok">
									<button id="btn_ok" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeDecision('MASIH OK')" class="btn btn-success">MASIH OK</button>
								</div>
								<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;display: none;" id="div_decision">
									<button id="btn_decision" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelDecision()" class="btn btn-info">TIDAK TAHU</button>
								</div>
							</td>
						</tr>
						<tr id="lepasnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="hourlepas" id="hourlepas">Note</span>
					    	</td>
						</tr>
						<tr id="lepasnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notelepas" id="notelepas" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="startLepas()" class="btn btn-success">MULAI LEPAS</button>
			</div>
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px">
				<button id="pause_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause('LEPAS','PAUSE')" class="btn btn-warning">PAUSE</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelLepas()" class="btn btn-danger">BATAL</button>	
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px">
				<button id="finish_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishLepas()" class="btn btn-success">SELESAI LEPAS</button>
			</div>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 5px">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">PASANG MOLDING <span style="color: red" id="counter"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<!-- <td id="mesin_pasang_pilihan" style="padding:0;background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;">
								<button class="btn btn-danger" onclick="getDataMesin(1);" id="#1">#1</button>
								<button class="btn btn-danger" onclick="getDataMesin(2);" id="#2">#2</button>
								<button class="btn btn-danger" onclick="getDataMesin(3);" id="#3">#3</button>
								<button class="btn btn-danger" onclick="getDataMesin(4);" id="#4">#4</button>
								<button class="btn btn-danger" onclick="getDataMesin(5);" id="#5">#5</button>
								<button class="btn btn-danger" onclick="getDataMesin(6);" id="#6">#6</button>
								<button class="btn btn-danger" onclick="getDataMesin(7);" id="#7">#7</button>
								<button class="btn btn-danger" onclick="getDataMesin(8);" id="#8">#8</button>
								<button class="btn btn-danger" onclick="getDataMesin(9);" id="#9">#9</button>
								<button class="btn btn-danger" onclick="getDataMesin(11);" id="#11">#11</button>
							</td> -->
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="mesin_pasang">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;">
							<span style="color: red"><i id="pesan_pasang" ></i></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding_pasang">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Product
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Last Counter
							</th>
						</tr>
					</thead>
					<tbody id="moldingLogPasang">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="product_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_pasang_list" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Last Counter</td>
							<td id="last_counter_pasang" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="pasangtime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerpasang">
					            <span class="hourpasang" id="hourpasang">00</span> h : <span class="minutepasang" id="minutepasang">00</span> m : <span class="secondpasang" id="secondpasang">00</span> s
					            <input type="hidden" id="pasang" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_pasang" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="pasangnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="hourpasang" id="hourpasang">Note</span>
					    	</td>
						</tr>
						<tr id="pasangnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notepasang" id="notepasang" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPasang()" class="btn btn-success">MULAI PASANG</button>
				<!-- <input type="hidden" id="start_time_pasang"> -->
			</div>
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px">
				<button id="pause_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause('PASANG','PAUSE')" class="btn btn-warning">PAUSE</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="cek_visual_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="pause('PASANG','CEK VISUAL & DIMENSI')" class="btn btn-default">CEK VISUAL & DIMENSI</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="approval_pasang" style="width: 100%; padding-top: 10px;padding-bottom: 12px; margin-top: 10px; font-size: 23px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="pause('PASANG','APPROVAL QA')" class="btn btn-default">FIRST INJECT + APPROVAL QA</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelPasang()" class="btn btn-danger">BATAL</button>
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 5px">
				<button id="finish_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPasang()" class="btn btn-success">SELESAI PASANG</button>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6" style="padding-right: 5px;padding-left: 0px">
					<!-- <div class="row"> -->
						<button id="change_operator" style="width: 100%;  font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeOperator()" class="btn btn-info">GANTI OPERATOR</button>
					<!-- </div> -->
				</div>
				<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px">
					<!-- <div class="row"> -->
						<button id="change_mesin" style="width: 100%;  font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeMesin()" class="btn btn-primary">GANTI MESIN</button>
					<!-- </div> -->
				</div>
			</div>
		</div>
		<div class="col-xs-6">
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID 1</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_0" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_0" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 2</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_1" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_1" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 3</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_2" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_2" placeholder="Scan ID Card">

					</div>
					<div class="col-xs-12">
						<div class="row">
							<button id="btn_operator" onclick="saveOperator()" class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px">
								CONFIRM
							</button>
						</div>
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
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin</span></center>
							</div>
							<div class="col-xs-12" id="mesin_btn">
								@foreach($mesin as $mesin)
								<div class="col-xs-3" style="padding-top: 5px">
									<center>
										<button class="btn btn-primary" id="{{$mesin}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMesin(this.id)">{{$mesin}}</button>
									</center>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="mesin_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-primary" id="mesin_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesin2()">
									MESIN
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="row">
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
</div>

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center> <b style="font-size: 2vw" id="statusReason">PAUSE</b> </center>
				<input type="hidden" id="typePause">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">Reason</label></center>
						<!-- <input class="form-control" style="width: 100%; text-align: center;" type="text" id="reasonPause" placeholder="Reason" required><br> -->
						<select class="form-control select2" id="reasonPause" data-placeholder="Pilih Reason" style="width: 100%;text-align: center;">
							<option value="-">Pilih Reason</option>
							<option value="Istirahat">Istirahat</option>
							<option value="Ganti Shift">Ganti Shift</option>
							<option value="Approval Tunggu QA">Approval Tunggu QA</option>
							<option value="Trouble">Trouble</option>
							<option value="No Production">No Production</option>
							<option value="Cek Visual & Dimensi">Cek Visual & Dimensi</option>
							<option value="Approval QA">Approval QA</option>
						</select>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<button class="btn btn-danger btn-block" style="font-weight: bold;font-size: 20px" data-dismiss="modal">Cancel</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="saveStatus()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var intervalUpdate;

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#reasonlepas').hide();
		$('#lepasnote').hide();
		$('#lepasnote2').hide();
		$('#lepastime').hide();
		$('#finish_lepas').hide();
		$('#div_decision').hide();
		$('#div_ok').hide();
		$('#div_maintenance').hide();
		$('#div_keputusan').hide();

		// $('#mesin_pasang').hide();
		$('#pasangnote').hide();
		$('#pasangnote2').hide();
		$('#pasangtime').hide();
		$('#finish_pasang').hide();
		$('#batal_pasang').hide();
		$('#batal_lepas').hide();

		$('#mesin_fix').hide();

		$('#operator_0').val('');
		$('#operator_1').val('');
		$('#operator_2').val('');

		$('#molding_code').val('');
		setInterval(setTime, 1000);
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator_0').focus();
	});

	$('#operator_0').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_0").val().length >= 8){
				var data = {
					employee_id : $("#operator_0").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator').modal('hide');
						// $('#op').html(result.employee.employee_id);
						// $('#op2').html(result.employee.name);
						// $('#employee_id').val(result.employee.employee_id);
						// $('#modalMesin').modal('show');
						$('#operator_0').val(result.employee.name);
						$('#op_0').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_0').val(result.employee.employee_id);
						$('#operator_0').prop('disabled',true);
						$('#operator_1').focus();
						// getMoldingLog();
						// get_history_temp(result.employee.name);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_0').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_0").val("");
			}			
		}
	});

	$('#operator_1').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_1").val().length >= 8){
				var data = {
					employee_id : $("#operator_1").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator').modal('hide');
						// $('#op').html(result.employee.employee_id);
						// $('#op2').html(result.employee.name);
						// $('#employee_id').val(result.employee.employee_id);
						// $('#modalMesin').modal('show');
						$('#operator_1').val(result.employee.name);
						$('#op_1').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_1').val(result.employee.employee_id);
						$('#operator_1').prop('disabled',true);
						$('#operator_2').focus();
						// getMoldingLog();
						// get_history_temp(result.employee.name);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_1').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_1").val("");
			}			
		}
	});

	$('#operator_2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_2").val().length >= 8){
				var data = {
					employee_id : $("#operator_2").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator').modal('hide');
						// $('#op').html(result.employee.employee_id);
						// $('#op2').html(result.employee.name);
						// $('#employee_id').val(result.employee.employee_id);
						// $('#modalMesin').modal('show');
						$('#operator_2').val(result.employee.name);
						$('#op_2').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_2').val(result.employee.employee_id);
						$('#operator_2').prop('disabled',true);
						// getMoldingLog();
						// get_history_temp(result.employee.name);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_2").val("");
			}			
		}
	});

	// $('#operator_3').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#operator").val().length >= 8){
	// 			var data = {
	// 				employee_id : $("#operator").val()
	// 			}
				
	// 			$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', result.message);
	// 					$('#modalOperator').modal('hide');
	// 					$('#op').html(result.employee.employee_id);
	// 					$('#op2').html(result.employee.name);
	// 					$('#employee_id').val(result.employee.employee_id);
	// 					$('#modalMesin').modal('show');
	// 					// getMoldingLog();
	// 					// get_history_temp(result.employee.name);
	// 				}
	// 				else{
	// 					audio_error.play();
	// 					openErrorGritter('Error', result.message);
	// 					$('#operator').val('');
	// 				}
	// 			});
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', 'Employee ID Invalid.');
	// 			audio_error.play();
	// 			$("#operator").val("");
	// 		}			
	// 	}
	// });

	function saveOperator() {
		$('#modalOperator').modal('hide');
		// $('#op').html(result.employee.employee_id);
		// $('#op2').html(result.employee.name);
		// $('#employee_id').val(result.employee.employee_id);
		$('#modalMesin').modal('show');
	}

	function saveMesin() {
		if ($('#mesin_fix2').text() == 'MESIN') {
			alert('Pilih Mesin');
		}else{
			$('#mesin_pasang').html($('#mesin_fix2').text());
			getMoldingLogPasang($('#mesin_fix2').text());
			$('#modalMesin').modal('hide');
			getMoldingLog($('#mesin_fix2').text());
			get_history_temp($('#mesin_fix2').text());
		}
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

	function changeDecision(value) {
		$('#div_maintenance').hide();
		$('#div_ok').hide();
		$('#div_decision').show();
		$('#btn_decision').html(value);
	}

	function cancelDecision() {
		$('#div_maintenance').show();
		$('#div_ok').show();
		$('#div_decision').hide();
		$('#btn_decision').html("TIDAK TAHU");
	}

	function getDataMesin(nomor_mesin) {
		$('#mesin_pasang').html('Mesin ' + nomor_mesin);
		$('#mesin_pasang_pilihan').hide();
		$('#mesin_pasang').show();
		getMoldingLogPasang('Mesin ' + nomor_mesin);
	}

	function changeMesin() {
		$('#mesin_pasang').html('-');
		// $('#mesin_pasang').hide();
		// $('#moldingLogPasang').html("");
		$('#modalMesin').modal("show");
		$('#mesin_choice').show();
		$('#mesin_fix').hide();
		cancelAll();
	}

	function changeOperator() {
		location.reload();
	}

	function getMoldingLog(mesin){
		var data = {
			mesin:mesin
		}
		$.get('{{ url("get/injeksi/get_molding") }}', data, function(result, status, xhr){
			if(result.status){

				var moldingLog = '';
				$('#moldingLog').html("");
				var no = 1;
				var color ="";
				$.each(result.datas, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 20px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 20px;"';
						}
					if (value.shot >= 15000) {
						color = 'style="background-color: #ff3030;font-size: 20px;color:white"';
					}
					moldingLog += '<tr onclick="fetchCount(\''+value.mesin+'\',\''+value.part+'\',\''+value.product+'\',\''+value.shot+'\')" style="padding-top:5px;padding-bottom:5px;">';
					moldingLog += '<td '+color+'>'+value.pic+'</td>';
					moldingLog += '<td '+color+'>'+value.mesin+'</td>';
					moldingLog += '<td '+color+'>'+value.part+'</td>';
					moldingLog += '<td '+color+'>'+value.shot+'</td>';
					// moldingLog += '<td '+color+'>'+value.end_time+'</td>';
					
					moldingLog += '</tr>';				
				no++;
				});
				$('#moldingLog').append(moldingLog);

				// $('#statusLog').text(result.log[0].status);
				

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function getMoldingLogPasang(mesin){
		var data = {
			mesin : mesin,
		}
		$.get('{{ url("get/injeksi/get_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#pesan_pasang').html(result.pesan);

				if (result.pesan.length == 0) {
					var moldingLogPasang = '';
					// $('#moldingLogPasang').html("");
					var no = 1;
					var color ="";
					$.each(result.datas, function(key, value) {
						if (no % 2 === 0 ) {
								color = 'style="background-color: #fffcb7;font-size: 25px;padding-top:5px;padding-bottom:5px;"';
							} else {
								color = 'style="background-color: #ffd8b7;font-size: 25px;padding-top:5px;padding-bottom:5px;"';
							}
						moldingLogPasang += '<tr onclick="fetchCountPasang(\''+value.id+'\')">';
						moldingLogPasang += '<td '+color+'>'+value.product+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.part+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.mesin+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.last_counter+'</td>';
						// moldingLogPasang += '<td '+color+'>'+value.end_time+'</td>';
						
						moldingLogPasang += '</tr>';				
					no++;
					});
					$('#moldingLogPasang').append(moldingLogPasang);
				}

				// $('#statusLog').text(result.log[0].status);
				

				// openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function fetchCount(mesin,part,product,shot){
		// var data = {
		// 	id : id,
		// }
		// $.get('{{ url("fetch/injeksi/fetch_molding") }}', data, function(result, status, xhr){
		// 	if(result.status){
				$('#mesin_lepas').html(mesin);
				$('#part_lepas').html(part);
				$('#color_lepas').html(product);
				$('#total_shot_lepas').html(shot);
		// 	}
		// 	else{
		// 		alert('Attempt to retrieve data failed');
		// 	}
		// });
	}

	function fetchCountPasang(id){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/injeksi/fetch_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#product_pasang').html(result.datas.product);
				$('#part_pasang').html(result.datas.part);
				$('#mesin_pasang_list').html(result.datas.mesin);
				$('#last_counter_pasang').html(result.datas.last_counter);

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function startLepas() {
		if ($('#mesin_lepas').text() == '') {
			alert('Pilih Data Molding Yang Akan Dilepas.');
		}else{
			// $('#reasonlepas').show();
			$('#lepasnote').show();
			$('#lepasnote2').show();
			$('#lepastime').show();
			$('#finish_lepas').show();
			$('#batal_lepas').show();
			$('#start_lepas').hide();
			$('#div_ok').show();
			$('#div_maintenance').show();
			$('#div_keputusan').show();
			intervalUpdate = setInterval(update_history_temp,60000);
			store_history_temp('LEPAS');
		}
	}

	function cancelAll() {
		$('#finish_lepas').hide();
		$('#lepasnote').hide();
		$('#lepasnote2').hide();
		$('#batal_lepas').hide();
		$('#lepastime').hide();
		$('#start_lepas').show();
		$('#secondlepas').html("00");
        $('#minutelepas').html("00");
        $('#hourlepas').html("00");
        $('#part_lepas').html("");
        $('#total_shot_lepas').html("");
        $('#color_lepas').html("");
        $('#mesin_lepas').html("");
        $('#reasonlepas').hide();
        $('#div_ok').hide();
        $('#div_maintenance').hide();
        $('#div_keputusan').hide();
        $('#div_decision').hide();

        $('#finish_pasang').hide();
		$('#pasangnote').hide();
		$('#pasangnote2').hide();
		$('#batal_pasang').hide();
		$('#pasangtime').hide();
		$('#start_pasang').show();
		$('#secondpasang').html("00");
        $('#minutepasang').html("00");
        $('#hourpasang').html("00");
        $('#product_pasang').html("");
        $('#part_pasang').html("");
        $('#mesin_pasang_list').html("");
        $('#last_counter_pasang').html("");
        $('#moldingLogPasang').html("");
        $('#mesin_pasang_pilihan').show();

        $('#molding_code').val('');
        // $('#mesin_pasang').hide();
	}

	function cancelLepas() {
		var pic = $('#op2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var data = {
			pic : pic,
			mesin : mesin,
			part : part,
			type : 'LEPAS',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				getMoldingLog($('#mesin_fix2').text());
				$('#finish_lepas').hide();
				$('#lepasnote').hide();
				$('#lepasnote2').hide();
				$('#batal_lepas').hide();
				$('#lepastime').hide();
				$('#start_lepas').show();
				$('#secondlepas').html("00");
		        $('#minutelepas').html("00");
		        $('#hourlepas').html("00");
		        $('#part_lepas').html("");
		        $('#total_shot_lepas').html("");
		        $('#color_lepas').html("");
		        $('#mesin_lepas').html("");
		        $('#reasonlepas').hide();
		        $('#div_ok').hide();
		        $('#div_maintenance').hide();
		        $('#div_keputusan').hide();
		        $('#div_decision').hide();
			} else {
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishLepas() {
		$('#loading').show();
		clearInterval(intervalUpdate);
		count = false;
		var detik = $('div.timerlepas span.secondlepas').text();
        var menit = $('div.timerlepas span.minutelepas').text();
        var jam = $('div.timerlepas span.hourlepas').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#lepas').val(waktu);

		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var color = $('#color_lepas').text();
		var total_shot = $('#total_shot_lepas').text();
		var start_time = $('#start_time_lepas').val();
		var end_time = getActualFullDate();
		var running_time = $('#lepas').val();
		var notelepas = $('#notelepas').val();
		var reason = $('#reason').text();
		var decision = $('#btn_decision').text();
		var molding_code = $('#molding_code').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		if (reason == '-' || decision == 'TIDAK TAHU') {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
			var data = {
				mesin : mesin,
				type : 'LEPAS',
				pic_1 : pic_1,
				pic_2 : pic_2,
				pic_3 : pic_3,
				reason : reason,
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time,
				end_time : end_time,
				running_time : running_time,
				notelepas : notelepas,
				decision : decision,
				molding_code : molding_code,
			}

			$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','History Molding has been created');
					// reset();
					$('#finish_lepas').hide();
					$('#lepastime').hide();
					$('#lepasnote').hide();
					$('#lepasnote2').hide();
					$('#batal_lepas').hide();
					$('#start_lepas').show();
					$('#secondlepas').html("00");
			        $('#minutelepas').html("00");
			        $('#hourlepas').html("00");
			        $('#part_lepas').html("");
			        $('#total_shot_lepas').html("");
			        $('#color_lepas').html("");
			        $('#mesin_lepas').html("");
			        $('#reasonlepas').hide();
			        $('#div_ok').hide();
			        $('#div_maintenance').hide();
			        $('#div_keputusan').hide();
			        $('#div_decision').hide();
					getMoldingLog($('#mesin_fix2').text());
					getMoldingLogPasang($('#mesin_fix2').text());
					$('#loading').hide();
					location.reload();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
					$('#loading').hide();
				}
			});
		}
	}

	function startPasang() {
		if ($('#mesin_pasang_list').text() == '' || $('#mesin_pasang').text() == '-') {
			alert('Pilih Data Molding Yang Akan Dipasang.');
		}else if ($('#pesan_pasang').text() != '') {
			alert('Mesin Sudah Terpasang Molding. Silahkan Pilih Mesin Lain.');
		}
		else{
			$('#pasangnote').show();
			$('#pasangnote2').show();
			$('#pasangtime').show();
			$('#finish_pasang').show();
			$('#start_pasang').hide();
			$('#batal_pasang').show();
			intervalUpdate = setInterval(update_history_temp,60000);
			store_history_temp('PASANG');
		}
	}

	function cancelPasang() {
		var pic = $('#op2').text();
		var mesin = $('#mesin_pasang').text();
		var data = {
			pic : pic,
			mesin : mesin,
			type : 'PASANG',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        // $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        // $('#mesin_pasang').hide();
			} else {
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishPasang() {
		$('#loading').show();
		clearInterval(intervalUpdate);
		count_pasang = false;
		var detik = $('div.timerpasang span.secondpasang').text();
        var menit = $('div.timerpasang span.minutepasang').text();
        var jam = $('div.timerpasang span.hourpasang').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#pasang').val(waktu);

		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();
		var mesin = $('#mesin_pasang').text();
		var part = $('#part_pasang').text();
		var color = $('#product_pasang').text();
		var total_shot = $('#last_counter_pasang').text();
		var start_time = $('#start_time_pasang').val();
		var end_time = getActualFullDate();
		var running_time = $('#pasang').val();
		var notepasang = $('#notepasang').val();
		var molding_code = $('#molding_code').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			type : 'PASANG',
			pic_1 : pic_1,
			pic_2 : pic_2,
			pic_3 : pic_3,
			part : part,
			color : color,
			total_shot : total_shot,
			start_time : start_time,
			end_time : end_time,
			running_time : running_time,
			notelepas : notepasang,
			molding_code : molding_code,
		}

		$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','History Molding has been created');
				// reset();
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        // $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        // $('#mesin_pasang').hide();
		        getMoldingLog($('#mesin_fix2').text());
		        getMoldingLogPasang($('#mesin_fix2').text());
		        $('#loading').hide();
		        location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create History Molding Failed');
				$('#loading').hide();
			}
		});
	}

	function store_history_temp(type) {
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var start_time = getActualFullDate();
		if (type === 'LEPAS') {
			var mesin = $('#mesin_lepas').text();
			var part = $('#part_lepas').text();
			var color = $('#color_lepas').text();
			var total_shot = $('#total_shot_lepas').text();
			if (parseInt(total_shot) < 15000) {
				$('#reason').html('LEPAS');
			}else if (parseInt(total_shot) >= 15000){
				$('#reason').html('MAINTENANCE');
			}
			$('#start_time_lepas').val(start_time);
			duration = 0;
			count = true;
			started_at = new Date(start_time);
			getMoldingLog($('#mesin_fix2').text());
		}
		else if (type === 'PASANG') {
			var mesin = $('#mesin_pasang').text();
			var color = $('#part_pasang').text();
			var part = $('#product_pasang').text();
			var total_shot = $('#last_counter_pasang').text();
			$('#start_time_pasang').val(start_time);
			duration = 0;
			count_pasang = true;
			started_at = new Date(start_time);
			getMoldingLogPasang(mesin);
		}

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}

		if (mesin == '-' || mesin == null) {
			alert('Semua Data Harus Diisi');
		}else{
			var data = {
				molding_code:type+'_'+pic+'_'+mesin+'_'+color+'_'+getActualFullDate(),
				mesin : mesin,
				type : type,
				pic : pic.join(', '),
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time
			}

			$.post('{{ url("index/injeksi/store_history_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','History Molding Temp has been created');
					// reset();
					getMoldingLogPasang($('#mesin_fix2').text());
					$('#molding_code').val(result.molding_code);
				} else {
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
				}
			});
		}
	}

	function get_history_temp(mesin) {
		var data = {
			mesin : mesin
		}
		$.get('{{ url("index/injeksi/get_history_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {
						var pic = value.pic.split(', ');

						console.log(pic.length);

						if (pic.length == 1) {
							$('#op_0').html(pic[0]);
						}else if(pic.length == 2){
							$('#op_0').html(pic[0]);
							$('#op_1').html(pic[1]);
						}else{
							$('#op_0').html(pic[0]);
							$('#op_1').html(pic[1]);
							$('#op_2').html(pic[2]);
						}
						if (value.remark != null) {
							if (confirm('Pekerjaan dalam proses '+value.remark+'. Apakah Anda ingin melanjutkan?')) {
								$('#molding_code').val(value.molding_code);
								changeStatus(value.molding_code);
								if (value.type == "LEPAS") {
									$('#mesin_lepas').html(value.mesin);
									$('#part_lepas').html(value.part);
									$('#color_lepas').html(value.product);
									$('#total_shot_lepas').html(value.total_shot);
									$('#notelepas').val(value.note);
									$('#start_time_lepas').val(value.start_time);
									if (parseInt(value.total_shot) < 15000) {
										$('#reason').html('LEPAS');
									}else if (parseInt(value.total_shot) >= 15000){
										$('#reason').html('MAINTENANCE');
									}
									duration = 0;
									count = true;
									started_at = new Date(value.start_time);
									$('#start_lepas').hide();
									$('#finish_lepas').show();
									$('#lepastime').show();
									$('#lepasnote').show();
									// $('#reasonlepas').show();
									$('#lepasnote2').show();
									$('#batal_lepas').show();
									$('#div_ok').show();
									$('#div_maintenance').show();
									$('#div_keputusan').show();
								}
								else if(value.type == 'PASANG'){
									$('#mesin_pasang_pilihan').hide();
									$('#mesin_pasang').show();
									$('#mesin_pasang').html(value.mesin);
									$('#mesin_pasang_list').html(value.mesin);
									$('#part_pasang').html(value.color);
									$('#product_pasang').html(value.part);
									$('#last_counter_pasang').html(value.total_shot);
									$('#notepasang').val(value.note);
									$('#start_time_pasang').val(value.start_time);
									duration = 0;
									count_pasang = true;
									started_at = new Date(value.start_time);
									$('#start_pasang').hide();
									$('#finish_pasang').show();
									$('#pasangtime').show();
									$('#pasangnote').show();
									$('#pasangnote2').show();
									$('#batal_pasang').show();
								}								
								intervalUpdate = setInterval(update_history_temp,60000);
							}else{
								changeMesin();
							}
						}else{
							$('#molding_code').val(value.molding_code);
							if (value.type == "LEPAS") {
								$('#mesin_lepas').html(value.mesin);
								$('#part_lepas').html(value.part);
								$('#color_lepas').html(value.product);
								$('#total_shot_lepas').html(value.total_shot);
								$('#notelepas').val(value.note);
								$('#start_time_lepas').val(value.start_time);
								if (parseInt(value.total_shot) < 15000) {
									$('#reason').html('LEPAS');
								}else if (parseInt(value.total_shot) >= 15000){
									$('#reason').html('MAINTENANCE');
								}
								duration = 0;
								count = true;
								started_at = new Date(value.start_time);
								$('#start_lepas').hide();
								$('#finish_lepas').show();
								$('#lepastime').show();
								$('#lepasnote').show();
								// $('#reasonlepas').show();
								$('#lepasnote2').show();
								$('#batal_lepas').show();
								$('#div_ok').show();
								$('#div_maintenance').show();
								$('#div_keputusan').show();
							}
							else if(value.type == 'PASANG'){
								$('#mesin_pasang_pilihan').hide();
								$('#mesin_pasang').show();
								$('#mesin_pasang').html(value.mesin);
								$('#mesin_pasang_list').html(value.mesin);
								$('#part_pasang').html(value.color);
								$('#product_pasang').html(value.part);
								$('#last_counter_pasang').html(value.total_shot);
								$('#notepasang').val(value.note);
								$('#start_time_pasang').val(value.start_time);
								duration = 0;
								count_pasang = true;
								started_at = new Date(value.start_time);
								$('#start_pasang').hide();
								$('#finish_pasang').show();
								$('#pasangtime').show();
								$('#pasangnote').show();
								$('#pasangnote2').show();
								$('#batal_pasang').show();
							}								
							intervalUpdate = setInterval(update_history_temp,60000);
						}
					});
				}
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function update_history_temp() {
		var mesin = $('#mesin_fix2').text();
		var notelepas = $('#notelepas').val();
		var notepasang = $('#notepasang').val();

		var data = {
			mesin : mesin,
			note : notelepas,
			type : 'LEPAS'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});

		var data2 = {
			mesin : mesin,
			note : notepasang,
			type : 'PASANG'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data2, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});
	}

	function pause(type,status) {
		$('#modalStatus').modal('show');
		$('#typePause').val(type);
		$('#statusReason').html(status);
		if (status === 'CEK VISUAL & DIMENSI') {
			$('#reasonPause').val('Cek Visual & Dimensi').trigger('change');
		}else if(status === 'APPROVAL QA') {
			$('#reasonPause').val('Approval QA').trigger('change');
		}else{
			$('#reasonPause').val('-').trigger('change');
		}
	}

	function saveStatus() {
		var reason = $('#reasonPause').val();

		if (reason == '-') {
			alert('Pilih Reason');
		}else{
			var pic_1 = $('#op_0').text();
			var pic_2 = $('#op_1').text();
			var pic_3 = $('#op_2').text();

			var pic = [];

			if (pic_1 != "-") {
				pic.push(pic_1);
			}

			if (pic_2 != "-") {
				pic.push(pic_2);
			}

			if (pic_3 != "-") {
				pic.push(pic_3);
			}

			var type=$('#typePause').val();

			if (type == 'PASANG') {
				var mesin = $('#mesin_pasang').text();
				var part = $('#part_pasang').text();
			}else{
				var mesin = $('#mesin_lepas').text();
				var part = $('#part_lepas').text();
			}

			var data = {
				type:$('#typePause').val(),
				molding_code:$('#molding_code').val(),
				status:$('#statusReason').text(),
				pic:pic.join(),
				mesin:mesin,
				part:part,
				reason:reason,
				start_time:getActualFullDate(),
			}

			$.get('{{ url("input/reason_pause") }}', data, function(result, status, xhr){
				if(result.status){
					alert('Pemasangan / Pelepasan Molding Dalam Proses '+$('#statusReason').text());
					location.reload();
					$('#reasonPause').val('-').trigger('change');
				}else{
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function changeStatus(molding_code) {
		var data = {
			molding_code:molding_code
		}
		$.get('{{ url("change/reason_pause") }}', data, function(result, status, xhr){
			if(result.status){
				alert('Pemasangan / Pelepasan Molding Dilanjutkan.');
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}


	var duration = 0;
	var count = false;
	var count_pasang = false;
	var started_at;
	function setTime() {
		if(count){
			$('#secondlepas').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutelepas').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourlepas').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
		}
		if(count_pasang){
			$('#secondpasang').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutepasang').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourpasang').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
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

