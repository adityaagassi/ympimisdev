@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	/*Start CSS Numpad*/
	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	/*End CSS Numpad*/

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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	#qr_code {
		text-align: center;
		font-weight: bold;
	}
	.input {
		text-align: center;
		font-weight: bold;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row" style="">
		
		<div class="col-xs-12 col-md-6 col-lg-6" style="">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<p id="inputor_name" style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;"></p>

				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="SCAN QR CODE" id="qr_code">
					<span class="input-group-btn">
						<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;Scan QR</button>
					</span>
				</div>
			</div>

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">REVISE PI</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Store</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="store"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Category</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="category"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Material Number</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="material_number"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Location</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="location"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Material Desc.</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="material_description"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Model Key Surface</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="model_key_surface"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Lot</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="lot_uom"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Remark</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="remark"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 2%;">
				<table class="table table-bordered" id="store_table">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="3">QUANTITY</th>
						</tr>
						<tr>
							<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">INPUT PI</th>
							<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">AUDIT 1</th>
							<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">FINAL QTY</th>
						</tr>
					</thead>
					<tbody id="store_body">
					</tbody>
				</table>
			</div>

		</div>

		<div class="col-xs-12 col-lg-6 col-md-6">
			<div class="row" style="margin-bottom: 2%;">
				<table style="width: 100%;">
					<tbody>
						<tr>
							<td style="width:100%; text-align: left; vertical-align: middle; font-size: 18px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<div class="form-group">
									<label class="col-xs-2" style="padding: 0px; color: yellow;">Reason</label>
									<div class="col-xs-8" style="padding: 0px;">
										<select class="form-control select2" name="reason" id='reason' data-placeholder="Select Reason">
											<option value=""></option>
											<option value="Salah input PI">Salah input PI</option>
											<option value="Kesalahan input transaksi return/repair">Kesalahan input transaksi return/repair</option>
											<option value="Salah hitung">Salah hitung</option>
											<option value="Belum terhitung">Belum terhitung</option>
											<option value="Salah identifikasi item single/assy">Salah identifikasi item single/assy</option>
											<option value="Salah input transaksi loc transfer dari maekotei">Salah input transaksi loc transfer dari maekotei</option>
										</select>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<table style="width: 100%;">
					<tbody>
						<tr>
							<td style="width:100%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<button class="btn btn-success pull-right" onclick="addCount()" style="height: 40px; vertical-align: top; margin-right: 1.2%;"><i class="fa fa-plus"></i></button>
							</td>
						</tr>
					</tbody>
				</table>
				<table style="width: 100%;">
					<thead>
						<tr>
							<td style="width:25%; color: yellow; font-weight: bold; font-size: 20px; text-align: center;">QTY</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; text-align: center;">X</td>
							<td style="width:25%; color: yellow; font-weight: bold; font-size: 20px; text-align: center;">LOT</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; text-align: center;">=</td>
							<td style="width:25%; color: yellow; font-weight: bold; font-size: 20px; text-align: center;">TOTAL</td>
							<td style="width:10%; color: yellow; font-weight: bold; font-size: 20px; text-align: center; padding-left: 10px;">#</td>
						</tr>
					</thead>
					<tbody id="count">
						<tr id="count_1">
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_1">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_1">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
							<td style="width:25%; font-weight: bold; font-size: 20px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_1">
							</td>
							<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_1" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>
							</td>
						</tr>
						<tr id="count_2">
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_2">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_2">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
							<td style="width:25%; font-weight: bold; font-size: 20px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_2">
							</td>
							<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_2" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>
							</td>
						</tr>
						<tr id="count_3">
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_3">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_3">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
							<td style="width:25%; font-weight: bold; font-size: 20px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_3">
							</td>
							<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_3" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>
							</td>
						</tr>
						<tr id="count_4">
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_4">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_4">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
							<td style="width:25%; font-weight: bold; font-size: 20px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_4">
							</td>
							<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_4" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>
							</td>
						</tr>
						<tr id="count_5">
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_5">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>
							<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_5">
							</td>
							<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
							<td style="width:25%; font-weight: bold; font-size: 20px;">
								<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_5">
							</td>
							<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
								<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_5" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>
							</td>
						</tr>
					</tbody>
				</table>
				<table style="width: 100%; margin-top: 2%; margin-bottom: 2%;">
					<tr>
						<td style="width: 53%;">
							<label style=" text-align: center; color: yellow; font-size:20px;">Total</label>
						</td>
						<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>
						<td style="width: 25%;">
							<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="form-control input" id="sum_total" readonly="">
						</td>
						<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">
							<span style="width: 100%;"></span>
						</td>
					</tr>
				</table>
				<table style="width: 100%;">
					<tr>
						<td style="width: 50%;">
							<button type="button" style="width: 100%; font-size:20px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="canc()" class="btn btn-danger pull-right">&nbsp;Cancel&nbsp;</button>
						</td>
						<td style="width: 50%;">
							<button id="save_button" type="button" style="width: 100%; font-size:20px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="save()" class="btn btn-success">&nbsp;Save&nbsp;</button>
						</td>
					</tr>
				</table>
			</div>		
		</div>
		
		<div class="modal modal-default fade" id="scanModal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-center"><b>SCAN QR CODE HERE</b></h4>
					</div>
					<div class="modal-body">
						<div id='scanner' class="col-xs-12">
							<div class="col-xs-10 col-xs-offset-1">
								<div id="loadingMessage">
									🎥 Unable to access video stream
									(please make sure you have a webcam enabled)
								</div>
								<canvas style="width: 100%; height: 300px;" id="canvas" hidden></canvas>
								<div id="output" hidden>
									<div id="outputMessage">No QR code detected.</div>
								</div>
							</div>									
						</div>

						<p style="visibility: hidden;">camera</p>
						<input type="hidden" id="code">
					</div>
				</div>
			</div>
		</div>

	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var vdo;
	var lot_uom;

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('#qr_code').focus();

		$('#save_button').prop('disabled', true);	

		$('.select2').select2();

	});


	var count = 5;
	function addCount(){
		++count;

		$add = '';
		$add += '<tr id="count_'+ count +'">';
		$add += '<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">';
		$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_'+count+'">';
		$add += '</td>';
		$add += '<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>';
		$add += '<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">';
		$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_'+count+'">';
		$add += '</td>';
		$add += '<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>';
		$add += '<td style="width:25%; font-weight: bold; font-size: 20px;">';
		$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_'+count+'">';
		$add += '</td>';
		$add += '<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">';
		$add += '<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_'+count+'" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>';
		$add += '</td>';
		$add += '</tr>';

		$('#count').append($add);
		$('#qty_'+count).addClass('numpad');
		$('#koef_'+count).addClass('numpad');

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		console.log(count);
	}

	function removeCount(param){

		var index = param.split('_');
		var id = index[1];

		$("#count_"+id).remove();

		if(count != id){
			var lop = parseInt(id) + 1;		
			for (var i = lop; i <= count; i++) {
				document.getElementById("count_"+ i).id = "count_"+ (i-1);
				document.getElementById("qty_"+ i).id = "qty_"+ (i-1);
				document.getElementById("koef_"+ i).id = "koef_"+ (i-1);
				document.getElementById("total_"+ i).id = "total_"+ (i-1);
				document.getElementById("remove_"+ i).id = "remove_"+ (i-1);
			}		
		}
		count--;
		changeVal();

		console.log(count);
	}

	function resetCount() {
		$('#count').append().empty();

		count = 5;

		for (var i = 1; i <= count; i++) {
			$add = '';
			$add += '<tr id="count_'+ i +'">';
			$add += '<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">';
			$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="qty_'+i+'">';
			$add += '</td>';
			$add += '<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">X</td>';
			$add += '<td style="width:25%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">';
			$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="koef_'+i+'">';
			$add += '</td>';
			$add += '<td style="color: yellow; font-size: 20px; font-weight: bold; padding-bottom:2px; padding-top:2px;">=</td>';
			$add += '<td style="width:25%; font-weight: bold; font-size: 20px;">';
			$add += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" onchange="changeVal()" class="numpad input" id="total_'+i+'">';
			$add += '</td>';
			$add += '<td style="width:10%; font-weight: bold; font-size: 20px; padding-left: 10px; padding-bottom:2px; padding-top:2px;">';
			$add += '<span style="width: 100%;"></span><button class="btn btn-danger" id="remove_'+i+'" onclick="removeCount(id)" style="height: 40px; vertical-align: top;"><i class="fa fa-close"></i></button>';
			$add += '</td>';
			$add += '</tr>';

			$('#count').append($add);
			$('#qty_'+i).addClass('numpad');
			$('#koef_'+i).addClass('numpad');
		}

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	}

	function stopScan() {
		$('#scanModal').modal('hide');
	}

	function videoOff() {
		vdo.pause();
		vdo.src = "";
		vdo.srcObject.getTracks()[0].stop();
	}

	$( "#scanModal" ).on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#scanModal').on('hidden.bs.modal', function () {
		videoOff();
	});

	
	$('#qr_code').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var id = $("#qr_code").val();

			// if(numberValidation(id)){
				var data = {
					id : id
				}

				$.get('{{ url("fetch/stocktaking/material_detail_new") }}', data, function(result, status, xhr){

					if (result.status) {

						// if(result.material[0].process <= 1){
						// 	$('#save_button').prop('disabled', true);
						// 	openErrorGritter('Error', 'Proses sebelumnya belum selesai');
						// 	canc();
						// 	return false;
						// }else{
						// 	$('#save_button').prop('disabled', false);
						// 	openSuccessGritter('Success', 'QR Code Successfully');
						// }					
						$('#save_button').prop('disabled', false);
						openSuccessGritter('Success', 'QR Code Successfully');
						
						$('#qr_code').prop('disabled', true);

						$("#store").text(result.material[0].store);
						$("#category").text(result.material[0].category);
						$("#material_number").text(result.material[0].material_number);
						$("#location").text(result.material[0].location);
						$("#material_description").text(result.material[0].material_description);
						$("#remark").text(result.material[0].remark);
						$("#model_key_surface").text((result.material[0].model || '')+' '+(result.material[0].key || '')+' '+(result.material[0].surface || ''));
						$("#lot_uom").text((result.material[0].lot || '-') + ' ' + result.material[0].bun);
						
						
						$("#store_body").empty();
						var body = '';
						var num = '';
						for (var i = 0; i < result.material.length; i++) {
							var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 25px; font-weight: bold;"';
							body += '<tr>';
							if(result.material[i].quantity != null){
								body += '<td '+css+'>'+result.material[i].quantity+'</td>';
							}else{
								body += '<td '+css+'>'+'-'+'</td>';							
							}

							if(result.material[i].audit1 != null){
								body += '<td '+css+'>'+result.material[i].audit1+'</td>';
							}else{
								body += '<td '+css+'>'+'-'+'</td>';							
							}

							if(result.material[i].final_count != null){
								body += '<td '+css+'>'+result.material[i].final_count+'</td>';
							}else{
								body += '<td '+css+'>'+'-'+'</td>';							
							}
							body += '</tr>';
						}
						$("#store_body").append(body);

					} else {
						openErrorGritter('Error', 'QR Code Not Registered');
						canc();
					}

					$('#scanner').hide();
					$('#scanModal').modal('hide');
					$(".modal-backdrop").remove();
				});
			// }else{
			// 	canc();
			// 	openErrorGritter('Error', 'QR Code Tidak Terdaftar');
			// }	
		}
	});

	function numberValidation(id){
		var number = /^[0-9]+$/;

		if(!id.match(number)){
			return false;
		}else{
			return true;
		}
	}


	function showCheck(kode) {
		$(".modal-backdrop").add();
		$('#scanner').show();

		var video = document.createElement("video");
		vdo = video;
		var canvasElement = document.getElementById("canvas");
		var canvas = canvasElement.getContext("2d");
		var loadingMessage = document.getElementById("loadingMessage");

		var outputContainer = document.getElementById("output");
		var outputMessage = document.getElementById("outputMessage");

		function drawLine(begin, end, color) {
			canvas.beginPath();
			canvas.moveTo(begin.x, begin.y);
			canvas.lineTo(end.x, end.y);
			canvas.lineWidth = 4;
			canvas.strokeStyle = color;
			canvas.stroke();
		}

		navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
			video.srcObject = stream;
			video.setAttribute("playsinline", true);
			video.play();
			requestAnimationFrame(tick);
		});

		function tick() {
			loadingMessage.innerText = "⌛ Loading video..."
			if (video.readyState === video.HAVE_ENOUGH_DATA) {
				loadingMessage.hidden = true;
				canvasElement.hidden = false;

				canvasElement.height = video.videoHeight;
				canvasElement.width = video.videoWidth;
				canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
				var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
				var code = jsQR(imageData.data, imageData.width, imageData.height, {
					inversionAttempts: "dontInvert",
				});

				if (code) {
					drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
					drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
					drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
					drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
					outputMessage.hidden = true;
					videoOff();
					document.getElementById("qr_code").value = code.data;

					checkCode(video, code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}

	}



	function checkCode(video, code) {

		var data = {
			id : code
		}

		$.get('{{ url("fetch/stocktaking/material_detail_new") }}', data, function(result, status, xhr){

			if (result.status) {

				// if(result.material[0].process <= 1){					
				// 	$('#save_button').prop('disabled', true);
				// 	openErrorGritter('Error', 'Proses sebelumnya belum selesai');
				// 	canc();
				// 	return false;
				// }else{
				// 	$('#save_button').prop('disabled', false);
				// 	openSuccessGritter('Success', 'QR Code Successfully');
				// }						

				$('#save_button').prop('disabled', false);
				openSuccessGritter('Success', 'QR Code Successfully');
				$('#qr_code').prop('disabled', true);

				$("#store").text(result.material[0].store);
				$("#category").text(result.material[0].category);
				$("#material_number").text(result.material[0].material_number);
				$("#location").text(result.material[0].location);
				$("#material_description").text(result.material[0].material_description);
				$("#remark").text(result.material[0].remark);
				$("#model_key_surface").text((result.material[0].model || '')+' '+(result.material[0].key || '')+' '+(result.material[0].surface || ''));
				$("#lot_uom").text((result.material[0].lot || '-') + ' ' + result.material[0].bun);


				$("#store_body").empty();
				var body = '';
				var num = '';
				for (var i = 0; i < result.material.length; i++) {
					var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 25px; font-weight: bold;"';
					body += '<tr>';
					if(result.material[i].quantity != null){
						body += '<td '+css+'>'+result.material[i].quantity+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					if(result.material[i].audit1 != null){
						body += '<td '+css+'>'+result.material[i].audit1+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					if(result.material[i].final_count != null){
						body += '<td '+css+'>'+result.material[i].final_count+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					body += '</tr>';
				}
				$("#store_body").append(body);

			} else {
				openErrorGritter('Error', 'QR Code Not Registered');
			}

			$('#scanner').hide();
			$('#scanModal').modal('hide');
			$(".modal-backdrop").remove();
		});

	}

	function reloadQty(id){
		var data = {
			id : id
		}

		$.get('{{ url("fetch/stocktaking/material_detail_new") }}', data, function(result, status, xhr){

			if (result.status) {
				$("#store_body").empty();
				var body = '';
				var num = '';
				for (var i = 0; i < result.material.length; i++) {
					var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 25px; font-weight: bold;"';
					body += '<tr>';
					if(result.material[i].quantity != null){
						body += '<td '+css+'>'+result.material[i].quantity+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					if(result.material[i].audit1 != null){
						body += '<td '+css+'>'+result.material[i].audit1+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					if(result.material[i].final_count != null){
						body += '<td '+css+'>'+result.material[i].final_count+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}
					body += '</tr>';
				}
				$("#store_body").append(body);

			}
		});
	}

	function canc(){
		$("#store_body").empty();

		$('#save_button').prop('disabled', true);

		$('#store').html("");
		$('#category').html("");
		$('#material_number').html("");
		$('#location').html("");
		$('#material_description').html("");
		$('#remark').html("");
		$('#model_key_surface').html("");
		$('#lot_uom').html("");
		$('#text_lot').html("");
		$('#lot').prop('disabled', false);

		$('#qr_code').val("");
		$('#qr_code').prop('disabled', false);
		$('#qr_code').focus();

		$("#reason").prop('selectedIndex', 0).change();

		resetCount();
		document.getElementById("sum_total").value = '';
	}

	function changeVal(){
		var sum_total = 0;

		for (var i = 1; i <= count; i++) {
			var qty = $("#qty_"+i).val();
			var koef = $("#koef_"+i).val();

			if(qty == '' || koef == ''){
				continue;
			}

			var total = parseFloat(qty) * parseFloat(koef);
			document.getElementById("total_"+i).value =  total;

			sum_total += total;
		}

		document.getElementById("sum_total").value = sum_total;
	}

	function save(){
		var id = $("#qr_code").val();
		var quantity = $("#sum_total").val();
		var reason = $("#reason").val();

		var data = {
			id : id,
			quantity : quantity,
			reason : reason
		}

		if(reason == '' || quantity == ''){
			openErrorGritter('Error', 'Reason harus di isi');
			return false;
		}
		
		$.post('{{ url("fetch/stocktaking/update_revise_new") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', result.message);

				canc();
				// reloadQty(id);
			}else{
				openErrorGritter('Error', result.message);
			}
		});
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