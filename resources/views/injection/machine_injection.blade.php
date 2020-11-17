@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
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
			<!-- <div class="col-xs-6" style="padding: 0px;margin-bottom: 20px;">
				<div class="input-group" style="padding-top: 10px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;font-size: 20px" class="form-control" id="tag_molding" name="tag_molding" placeholder="Scan Tag Molding" required disabled>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
			</div> -->
			<div class="col-xs-8" style="padding: 0px;padding-bottom: 15.6px">
				<input type="hidden" id="tag_molding" name="tag_molding" placeholder="Scan Tag Molding" >
				<div class="input-group" style="padding-top: 10px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;font-size: 20px" class="form-control" id="tag_product" name="tag_product" placeholder="Scan Tag Product ..." required disabled>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
			</div>
			<div class="col-xs-4" style="padding: 0px;padding-bottom: 15.6px">
				<button class="btn btn-warning" id="btnInputTag" style="width: 97%;margin-top: 10px;font-weight: bold;font-size: 15px" onclick="inputTag()">
					Input Tag
				</button>
				<div class="col-xs-6">
					<div class="row">
						<button class="btn btn-danger" id="btnCancelTag" style="width: 97%;margin-top: 10px;font-weight: bold;font-size: 15px" onclick="cancelTag()">
							Batal
						</button>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="row">
						<button class="btn btn-success" id="btnSaveTag" style="width: 97%;margin-top: 10px;font-weight: bold;font-size: 15px" onclick="saveTag()">
							Simpan
						</button>
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
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Dryer
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Lot Number
					</td>
					<!-- <td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Dryer Color
					</td> -->
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
					<td id="dryer" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<td id="dryer_lot_number" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td>
					<!-- <td id="dryer_color" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 1.5vw;">-
					</td> -->
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
				<input type="hidden" id="start_time_product">
				<input type="hidden" id="molding_part_type">
				<input type="hidden" id="material_number">
				<button class="btn btn-success" id="btn_mulai" style="font-size: 30px;font-weight: bold;width: 100%" onclick="mulaiProses()">
					MULAI PROSES INJEKSI
				</button>
			</div>
			<div style="padding-top: 30px;" id="perolehan">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
					<tbody>
						<tr>
							<td colspan="2" style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;font-weight: bold;">
								PEROLEHAN
							</td>
						</tr>
						<tr>
							<td>
								<input type="number" class="pull-right numpad2" name="total_shot" style="height: 4.5vw;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;" id="total_shot" placeholder="Total Qty (Pcs)">
							</td>
							<td>
								<input type="number" class="pull-right numpad" name="running_shot" style="height: 4.5vw;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;" id="running_shot" placeholder="Running Qty (Pcs)">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-12" style="padding: 0px;padding-top: 10px">
				<button class="btn btn-danger" id="btn_selesai" style="font-size: 30px;font-weight: bold;width: 100%" onclick="selesaiProses()">
					SELESAI PROSES MESIN INJEKSI
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

			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
				<button class="btn btn-primary" id="btn_ganti" onclick="changeMesin()" style="font-size: 25px;font-weight: bold;width: 100%">
					PILIH MESIN
				</button>
			</div>
			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-left: 5px">
				<button class="btn btn-info" id="btn_ganti_op" onclick="location.reload()" style="font-size: 25px;font-weight: bold;width: 100%">
					GANTI OPERATOR
				</button>
			</div>
			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
				<button class="btn btn-warning" id="btn_idle" onclick="idle_trouble('IDLE')" style="font-size: 25px;font-weight: bold;width: 100%">
					IDLE
				</button>
			</div>
			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-left: 5px">
				<button class="btn btn-danger" id="btn_trouble" onclick="idle_trouble('TROUBLE')" style="font-size: 25px;font-weight: bold;width: 100%">
					TROUBLE
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
					<div class="col-xs-12">
						<div class="row">
							<a href="{{url('index/injection/molding')}}" target="_blank" class="btn btn-primary btn-sm btn-block">Ganti Molding</a>
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
					<div class="col-xs-12">
						<div class="row">
							<a href="{{url('index/injection/molding')}}" target="_blank" class="btn btn-primary btn-sm pull-right">Ganti Molding</a>
						</div>
					</div>
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
					<div class="col-xs-12" id="dryer_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Pilih Dryer</span></center>
							</div>
							<div class="col-xs-12" id="dryer_btn">
								@foreach($dryer as $dryer)
								<div class="col-xs-3" style="padding-top: 5px">
									<center>
										<button class="btn btn-warning" id="{{$dryer}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getDryer(this.id)">{{$dryer}}</button>
									</center>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="dryer_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">
										<center><span style="font-weight: bold; font-size: 18px;">Pilih Dryer</span></center>
									</div>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-warning" id="dryer_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeDryer2()">
									DRYER
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
										<center><span style="font-weight: bold; font-size: 18px;">Product</span></center>
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
						<div class="row">
							<div class="modal-footer">
								<div class="col-xs-6">
									<div class="row">
										<button class="btn btn-danger" style="font-weight: bold;font-size: 20px;width: 98%" onclick="cancelProcess()">CANCEL</button>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="row">
										<button onclick="saveProduct()" class="btn btn-success" style="font-weight: bold;font-size: 20px;width: 98%">
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
	</div>
</div>

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="status_idle_trouble" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">Reason</label></center>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="reason" placeholder="Reason" required><br>
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>

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

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('#start_time').val("");
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
		$('#dryer_fix').hide();
		$('#perolehan').hide();
		// $('#btn_selesai').hide();
		$('#btnSaveTag').hide();
		$('#btnCancelTag').hide();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	function inputTag() {
		$('#tag_product').removeAttr('disabled');
		$('#tag_product').focus();
		$('#btnInputTag').hide();
		$('#btnSaveTag').show();
		$('#btnCancelTag').show();
	}

	function cancelTag() {
		$('#tag_product').prop('disabled',true);
		$('#tag_product').val("");
		$('#btnInputTag').show();
		$('#btnSaveTag').hide();
		$('#btnCancelTag').hide();
	}

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
				}
				
				$.get('{{ url("scan/new_tag_injeksi") }}', data, function(result, status, xhr){
					if(result.status){
						if (result.tag.material_number == $('#material_number').val()) {
							openSuccessGritter('Success!', result.message);
							$('#tag_product').prop('disabled', true);
						}else{
							openErrorGritter('Error!', 'Tag Invalid');
							audio_error.play();
							$("#tag_product").val("");
							$("#tag_product").focus();
						}
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

	// $('#tag_molding').keyup(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#tag_molding").val().length >= 7){
	// 			var data = {
	// 				tag : $("#tag_molding").val(),
	// 				mesin : $("#mesin_fix2").text(),
	// 				// part : $("#part_type").text(),
	// 			}
				
	// 			$.get('{{ url("scan/part_molding") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', result.message);
	// 					$('#tag_molding').prop('disabled', true);
	// 					$('#molding').html(result.part.part);
	// 					$('#molding_part_type').val(result.part.product);
	// 					$('#tag_product').removeAttr('disabled');
	// 					$('#tag_product').focus();
	// 				}
	// 				else{
	// 					openErrorGritter('Error!', 'Molding Invalid');
	// 					audio_error.play();
	// 					$("#tag_molding").val("");
	// 					$("#tag_molding").focus();
	// 				}
	// 			});
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', 'Tag Invalid');
	// 			audio_error.play();
	// 			$("#tag_product").val("");
	// 			$("#tag_product").focus();
	// 		}			
	// 	}
	// });

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

	function getDryer(value) {
		$('#dryer_fix').show();
		$('#dryer_choice').hide();
		$('#dryer_fix2').html(value);
	}

	function changeDryer2() {
		$('#dryer_fix').hide();
		$('#dryer_choice').show();
		$('#dryer_fix2').html("DRYER");
	}

	function idle_trouble(status) {
		$('#modalStatus').modal('show');
		$('#status_idle_trouble').html(status);
		$('#reason').val('');
	}

	function changeMesin() {
		changeMesin2();
		changeDryer2();
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
		$('#dryer').html("-");
		$('#dryer_lot_number').html("-");
		// $('#dryer_color').html("-");
		$('#material_number').val("");
		$('#total_shot').val("");
		$('#btn_mulai').show();
		// $('#btn_selesai').hide();
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
		// update_tag();
	}

	function saveStatus() {
		var statuses = $('#status_idle_trouble').text();
		var reason = $('#reason').val();


		var data = {
			status:statuses,
			reason:reason,
			mesin:$('#mesin').text(),
			color:$('#color').text(),
			molding:$('#molding').text(),
			cavity:$('#cavity').text(),
			material_number:$('#material_number').val(),
			dryer:$('#dryer').text(),
			dryer_lot_number:$('#dryer_lot_number').text(),
		}

		$.get('{{ url("input/reason_idle_trouble") }}', data, function(result, status, xhr){
			if(result.status){
				if (statuses == 'IDLE') {
					alert('Mesin Dalam Kondisi Idle.');
					location.reload();
				}else{
					alert('Mesin Dalam Kondisi Trouble. Silahkan hubungi bagian terkait.');
					location.reload();
				}
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function changeStatus(mesin) {
		var data = {
			mesin:mesin,
		}

		$.get('{{ url("change/reason_idle_trouble") }}', data, function(result, status, xhr){
			if(result.status){
				alert('Mesin Kembali Bekerja.');
				get_temp();
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function saveMesin() {
		if ($('#mesin_fix2').text() == 'MESIN' || $('#dryer_fix2').text() == 'DRYER') {
			alert('Mesin & Dryer Harus Diisi.');
		}else{
			get_temp();
		}
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
		// $('#tag_product').val('');
		// $('#tag_product').removeAttr('disabled');
		// $('#tag_product').focus();
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
		// if ($('#tag_product').val() == "") {
		// 	openErrorGritter('Error!', "Scan Tag Product.");
		// }else{
			var data = {
				color : $("#color").text()
			}
			$.get('{{ url("fetch/new_product") }}', data, function(result, status, xhr){
				if(result.status){
					var btn_product = "";
					$('#product_btn').empty();
					$.each(result.product, function(key, value) {
						btn_product += '<div class="col-xs-4" style="padding-top: 5px">';
						btn_product += '<center><button class="btn btn-primary" id="'+value.product+'" style="width: 250px;font-size: 17px" onclick="getProduct(this.id)">'+value.product+'';
						btn_product += '</button></center>';
						btn_product += '</div>';
					});
					$('#product_btn').append(btn_product);

					var btn_cavity = "";
					$('#cavity_btn').empty();
					$.each(result.cavity, function(key, value) {
						btn_cavity += '<div class="col-xs-3" style="padding-top: 5px">';
						btn_cavity += '<center><button class="btn btn-info" id="'+value.no_cavity+'" style="width: 200px;font-size: 15px;font-weight:bold" onclick="getCavity(this.id)">'+value.type.toUpperCase()+'-'+value.no_cavity+'';
						btn_cavity += '</button></center>';
						btn_cavity += '</div>';
					});
					$('#cavity_btn').append(btn_cavity);
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			});
			
			$('#modalProduct').modal('show');
			if ($('#start_time').val() == "") {
				countUpFromTime(getActualFullDate());
				$('#start_time').val(getActualFullDate());
				$('#start_time_product').val(getActualFullDate());
			}
			// get_temp();
			$('#btn_mulai').hide();
			// $('#btn_selesai').show();
			$('#perolehan').show();
			$('#btn_ganti').show();
		// }
	}

	function create_temp() {
		var start_time = $('#start_time').val();
		var data = {
			// tag_product:$('#tag_product').val(),
			tag_molding:$('#tag_molding').val(),
			operator_id:$('#op').text(),
			start_time:start_time,
			mesin:$('#mesin').text(),
			part_name:$('#part_name').text(),
			part_type:$('#part_type').text(),
			color:$('#color').text(),
			molding:$('#molding').text(),
			cavity:$('#cavity').text(),
			material_number:$('#material_number').val(),
			dryer:$('#dryer').text(),
			dryer_lot_number:$('#dryer_lot_number').text(),
			dryer_color:$('#color').text(),
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
			mesin:$('#mesin_fix2').text()
		}
		$.get('{{ url("index/injeksi/get_temp") }}', data, function(result, status, xhr){
			if(result.status){
				if (result.data_mesin.status == 'Working') {
					openSuccessGritter('Success!', result.message);
					var start_time = result.data_mesin.start_time;
					// $('#tag_product').val(result.datas.tag_product);
					$('#mesin').html($('#mesin_fix2').text());
					$('#tag_molding').val(result.data_mesin.tag_molding);
					$('#tag_product').prop('disabled',true);
					$('#tag_molding').prop('disabled',true);
					$('#start_time').val(start_time);
					$('#molding').html(result.data_mesin.molding);
					$('#part_name').html(result.data_mesin.part_name);
					$('#part_type').html(result.data_mesin.part_type);
					$('#color').html(result.data_mesin.color);
					$('#cavity').html(result.data_mesin.cavity);
					$('#material_number').val(result.data_mesin.material_number);
					$('#dryer').html(result.data_mesin.dryer);
					$('#dryer_lot_number').html(result.data_mesin.dryer_lot_number);
					// $('#dryer_color').html(result.data_mesin.dryer_color);
					countUpFromTime(new Date(start_time));
					if (result.datas != null) {
						$('#btn_mulai').hide();
						// $('#btn_selesai').show();
						$('#perolehan').show();
						$('#modalProduct').modal('hide');
						$('#total_shot').val(result.datas.shot);
						$('#start_time_product').val(result.datas.start_time);
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
						$('#modalMesin').modal('hide');
					}else{
						$('#btn_mulai').show();
						// $('#btn_selesai').hide();
						$('#perolehan').hide();
						// $('#modalProduct').modal('show');
						$('#modalMesin').modal('hide');
					}
				}else{
					openErrorGritter('Error!','Mesin Dalam Status Idle / Trouble.');
					if (confirm('Mesin Dalam Status Idle / Trouble. Apakah akan melanjutkan kerja mesin?')) {
						changeStatus($('#mesin_fix2').text());
					}
				}
			}
			else{
				$('#loading').show();
				resulttrue = 0;
				var data2 = {
					dryer:$('#dryer_fix2').text(),
				}
				$.get('{{ url("index/injection/fetch_dryer") }}', data2, function(result, status, xhr){
					if(result.status){
						resulttrue++;
						$('#mesin').html($('#mesin_fix2').text());
						$('#dryer').html(result.dryer.dryer);
						$('#dryer_lot_number').html(result.dryer.lot_number);
						// $('#dryer_color').html(result.dryer.color);
					}else{
						$('#loading').hide();
						openErrorGritter('Error!','Dryer Belum Terisi');
					}
				});

				var data3 = {
					mesin:$('#mesin_fix2').text(),
				}
				
				$.get('{{ url("scan/part_molding") }}', data3, function(result, status, xhr){
					if(result.status){
						// openSuccessGritter('Success!', result.message);
						$('#tag_molding').val(result.part.tag);
						$('#tag_molding').prop('disabled', true);
						$('#molding').html(result.part.part);
						$('#molding_part_type').val(result.part.product);
						// $('#tag_product').removeAttr('disabled');
						// $('#tag_product').focus();
						resulttrue++;
						if (resulttrue == 2) {
							$('#loading').hide();
							$('#modalMesin').modal('hide');
						}
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!','Molding Belum Dipasang');
						audio_error.play();
					}
				});
			}
		});
	}

	function update_temp() {
		if ($('#running_shot').val() == "") {
			if ($('#total_shot').val() == "") {
				var shot = 0;
			}else{
				var shot = parseInt($('#total_shot').val());
			}
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
			// tag_product:$('#tag_product').val(),
			tag_molding:$('#tag_molding').val(),
			shot:shot,
			running_shot:$('#running_shot').val(),
			mesin:$('#mesin_fix2').text(),
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

	function saveTag() {
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
			start_time:$('#start_time_product').val(),
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
			ng_counting:ng_counting,
			dryer:$('#dryer').text(),
			dryer_lot_number:$('#dryer_lot_number').text(),
			dryer_color:$('#color').text(),
		}
		$.post('{{ url("index/injeksi/create_log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				cancelTag();
				clearInterval(intervalUpdate);
				$('#total_shot').val("");
				$('#btn_mulai').show();
				// $('#btn_selesai').hide();
				$('#perolehan').hide();
				for (var i = 1; i <= jumlah_ng; i++ ) {
					$('#count'+i).html('0');
				}
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
			material_number:$('#material_number').val(),
			dryer:$('#dryer').text(),
			dryer_lot_number:$('#dryer_lot_number').text(),
			dryer_color:$('#color').text(),
		}
		$.post('{{ url("input/injeksi/mesin_log") }}', data, function(result, status, xhr){
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