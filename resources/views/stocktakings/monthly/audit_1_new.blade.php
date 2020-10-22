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
	#progress-text {
		text-align: center;
		font-weight: bold;
		font-size: 20px;
		color: #fff;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
	
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
	<p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
		<span style="font-size: 50px;">Please wait ... </span><br>
		<span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>

<section class="content" style="padding-top: 0;">
	<div class="row" style="margin-left: 1%; margin-right: 1%;" id="main">
		<div class="col-xs-12" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<p id="auditor_name" style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;"></p>
				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="SCAN STORE" id="qr_code">
					<span class="input-group-btn">
						<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;&nbsp;Scan</button>
					</span>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;overflow-x: scroll;">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="9" id='store_title'>STORE</th>
					</tr>
					<tr>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">SUB STORE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">CATEGORY</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL NUMBER</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL DESCRIPTION</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">UOM</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">COUNT PI</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">AUDIT 1</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">#ID</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">ACTION</th>
					</tr>
				</thead>
				<tbody id="store_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="progress-confirm">
			<div class="col-xs-9" style="padding: 0px;">
				<div class="progress-group">
					<div class="progress" style="height: 40px; margin-bottom: 10px;">
						<span id="progress-text" style="padding-top: 0.8%;">0% Complete</span>
						<div class="progress-bar progress-bar-success progress-bar-striped active" id="progress-bar" style="padding-top: 0.8%;"></div>
					</div>
				</div>
			</div>
			<div class="col-xs-3">
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; margin-right: 1%; padding: 9.5%; padding-top: 0px; padding-bottom: 0px;" onclick="canc()" id="cancel" class="btn btn-danger">&nbsp;CANCEL&nbsp;</button>
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 9.5%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" id="confirm" class="btn btn-success" disabled>CONFIRM</button>
			</div>
		</div>
	</div>

	<div class="row" style="margin-left: 1%; margin-right: 1%; margin-top: 3%;" id="input">
		<div class="col-xs-12 col-md-7 col-lg-7" style="padding-left: 0px; margin-top: 3.5%;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<input type="hidden" id="id">

				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">MATERIAL DETAILS</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Store</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="store"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Sub Store</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="sub_store"></td>
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
					</tbody>
				</table>
			</div>
		</div>

		<div class="col-xs-12 col-md-5 col-lg-5">
			<div class="col-xs-12">
				<div class="form-group row" align="right">
					<div class="col-xs-1 col-xs-offset-11" align="right">
						<button class="btn btn-success" onclick="addCount()"><i class="fa fa-plus"></i></button>
					</div>
				</div>
			</div>

			<div id="count">
				{{-- 1 --}}
				<div class="col-xs-12" id="count_1">
					<div class="form-group row" style="margin-bottom: 2%;">
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="qty_1">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="koef_1">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>
						<div class="col-xs-2" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_1" readonly="">
						</div>
						<div class="col-xs-2" align="right" style="padding: 0">
							<button class="btn btn-danger" id="remove_1" onclick="removeCount(id)"><i class="fa fa-close"></i></button>
						</div>
					</div>
				</div>
				{{-- 2 --}}
				<div class="col-xs-12" id="count_2">
					<div class="form-group row" style="margin-bottom: 2%;">
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="qty_2">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="koef_2">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>
						<div class="col-xs-2" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_2" readonly="">
						</div>
						<div class="col-xs-2" align="right" style="padding: 0">
							<button class="btn btn-danger" id="remove_2" onclick="removeCount(id)"><i class="fa fa-close"></i></button>
						</div>
					</div>
				</div>
				{{-- 3 --}}
				<div class="col-xs-12" id="count_3">
					<div class="form-group row" style="margin-bottom: 2%;">
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="qty_3">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="koef_3">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>
						<div class="col-xs-2" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_3" readonly="">
						</div>
						<div class="col-xs-2" align="right" style="padding: 0">
							<button class="btn btn-danger" id="remove_3" onclick="removeCount(id)"><i class="fa fa-close"></i></button>
						</div>
					</div>
				</div>
				{{-- 4 --}}
				<div class="col-xs-12" id="count_4">
					<div class="form-group row" style="margin-bottom: 2%;">
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="qty_4">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="koef_4">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>
						<div class="col-xs-2" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_4" readonly="">
						</div>
						<div class="col-xs-2" align="right" style="padding: 0">
							<button class="btn btn-danger" id="remove_4" onclick="removeCount(id)"><i class="fa fa-close"></i></button>
						</div>
					</div>
				</div>
				{{-- 5 --}}
				<div class="col-xs-12" id="count_5">
					<div class="form-group row" style="margin-bottom: 2%;">
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="qty_5">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>
						<div class="col-xs-3" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control numpad input" id="koef_5">
						</div>
						<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>
						<div class="col-xs-2" align="right" style="padding: 0">
							<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_5" readonly="">
						</div>
						<div class="col-xs-2" align="right" style="padding: 0">
							<button class="btn btn-danger" id="remove_5" onclick="removeCount(id)"><i class="fa fa-close"></i></button>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12">	
				<div class="form-group row" align="right">					
					<label class="col-xs-1 col-xs-offset-5" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">Total</label>
					<div class="col-xs-3 col-xs-offset-2" align="right">
						<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="sum_total" readonly="">
					</div>
				</div>
			</div>

			<div class="col-xs-11" align="right">
				<div class="input-group input-group-lg">
					<button type="button" style="font-size:25px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="cancInput()" class="btn btn-danger">&nbsp;Cancel&nbsp;</button>

					<button type="button" style="font-size:25px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="save()" class="btn btn-success">&nbsp;<i class="fa fa-save"></i> &nbsp;Save&nbsp;</button>
				</div>
			</div>
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

	<div class="modal fade" id="modalAuditor">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Auditor</label>
							<select class="form-control select2" name="auditor" id='auditor' data-placeholder="Select Auditor" style="width: 100%;">
								<option value="">Select Auditor</option>
								@foreach($auditors as $auditor)
								<option value="{{ $auditor->employee_id }} - {{ $auditor->name }}">{{ $auditor->employee_id }} - {{ $auditor->name }}</option>
								@endforeach
							</select>
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
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var vdo;
	var lot_uom;

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('#modalAuditor').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('.select2').select2();

		$('#qr_code').blur();

		$('#progress-confirm').hide();

		$('#input').hide();

	});

	$("#auditor").change(function(){
		$('#modalAuditor').modal('hide');

		var auditor = $('#auditor').val(); 
		$('#auditor_name').text('');
		$('#auditor_name').text('Auditor : ' + auditor);

	});

	var count = 5;
	function addCount(){
		++count;

		$add = '';
		$add += '<div class="col-xs-12" id="count_'+ count +'">';
		$add += '<div class="form-group row" style="margin-bottom: 2%;">';
		$add += '<div class="col-xs-3" align="right">';
		$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="qty_'+ count +'">';
		$add += '</div>';
		$add += '<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>';
		$add += '<div class="col-xs-3" align="right">';
		$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="koef_'+ count +'">';
		$add += '</div>';
		$add += '<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>';
		$add += '<div class="col-xs-3" align="right">';
		$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_'+ count +'" readonly="">';
		$add += '</div>';
		$add += '<div class="col-xs-1" align="right">';
		$add += '<button class="btn btn-danger" id="remove_'+count+'" onclick="removeCount(id)"><i class="fa fa-close"></i></button>';
		$add += '</div>';
		$add += '</div>';
		$add += '</div>';

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
			$add += '<div class="col-xs-12" id="count_'+ i +'">';
			$add += '<div class="form-group row" style="margin-bottom: 2%;">';
			$add += '<div class="col-xs-3" align="right">';
			$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="qty_'+ i +'">';
			$add += '</div>';
			$add += '<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">X</label>';
			$add += '<div class="col-xs-3" align="right">';
			$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="koef_'+ i +'">';
			$add += '</div>';
			$add += '<label class="col-xs-1" style="padding: 0px; text-align: center; color: yellow; font-size:20px;">=</label>';
			$add += '<div class="col-xs-3" align="right">';
			$add += '<input type="text" style="font-size:20px; height: 30px;" onchange="changeVal()" class="form-control input" id="total_'+ i +'" readonly="">';
			$add += '</div>';
			$add += '<div class="col-xs-1" align="right">';
			$add += '<button class="btn btn-danger" id="remove_'+count+'" onclick="removeCount(id)"><i class="fa fa-close"></i></button>';
			$add += '</div>';
			$add += '</div>';
			$add += '</div>';

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
			fillStore(id);
		}
	});

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

					fillStore(code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}
	}


	function fillStore(store){
		var data = {
			store : store,
			process : 1
		}

		$.get('{{ url("fetch/stocktaking/audit_store_list_new") }}', data, function(result, status, xhr){
			if (result.status) {
				if(result.store.length <= 0){
					openErrorGritter('Error', 'Store Not Found');
					return false;
				}

				$('#qr_code').prop('disabled', true);
				$('#scanner').hide();
				$('#scanModal').modal('hide');
				$(".modal-backdrop").remove();

				$("#store_body").empty();
				$("#store_title").text("");
				$("#store_title").text("STORE : " + store.toUpperCase());

				$('#progress-confirm').show();
				$('#confirm').hide();
				$('#progress-bar').removeClass('active');						

				var body = '';
				var num = '';
				for (var i = 0; i < result.store.length; i++) {
					if(result.store[i].category == 'SINGLE'){
						var css = 'style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 15px;"';
					}else{
						var css = 'style="padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 15px;"';
					}

					num++;
					body += '<tr>';
					body += '<td '+css+'>'+result.store[i].sub_store+'</td>';
					body += '<td '+css+'>'+result.store[i].category+'</td>';
					body += '<td '+css+'>'+result.store[i].material_number+'</td>';
					body += '<td '+css+'>'+result.store[i].material_description+'</td>';
					body += '<td '+css+'>'+result.store[i].bun+'</td>';
					if(result.store[i].quantity != null){
						body += '<td '+css+'>'+result.store[i].quantity+'</td>';
					}else{
						body += '<td '+css+'>'+'-'+'</td>';							
					}

					if(result.store[i].audit1 != null){
						body += '<td '+css+'>'+result.store[i].audit1+'</td>';
					}else{
						body += '<td '+css+'></td>';							
					}
					
					body += '<td '+css+'>'+result.store[i].id+'</td>';

					if(result.store[i].process == 1){
						if (result.store[i].quantity != 0) {
							if(result.store[i].audit1){
								body += '<td '+css+'><button style="width: 50%; height: 100%;" onclick="cancAudit(\''+result.store[i].id+'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button></td>';
							}else{
								body += '<td '+css+'><button style="width: 50%; height: 100%;" onclick="showAudit(\''+result.store[i].id+'\')" class="btn btn-xs btn-success form-control"><span><i class="fa fa-check-square-o"></i></span></button></td>';
							}
						}
						else{
							body += '<td '+css+'></td>';
						}

						$('#confirm').show();
						$('#progress-bar').addClass('active');

					}else{
						body += '<td '+css+'>-</td>';
					}

					body += '</tr>';

				}
				$("#store_body").append(body);

				checkConf(store);

			}else {
				$('#scanner').hide();
				$('#scanModal').modal('hide');
				$(".modal-backdrop").remove();
				canc();

				if(result.message){
					openErrorGritter('Error', result.message);
				}else{
					openErrorGritter('Error', 'Store tidak ditemukan');					
					$('#qr_code').focus();
				}
				$('#qr_code').focus();
			}
		});
	}

	function canc(){
		$('#qr_code').val("");
		$('#qr_code').prop('disabled', false);
		$('#qr_code').focus();
		$('#qr_code').blur();

		$('#store_body').html("");
		$("#store_title").text("");
		$("#store_title").text("STORE");

		$('#progress-confirm').hide();
	}

	function cancAudit(id) {
		var data = {
			id : id
		}

		if(confirm("Data Audit 1 yang dipilih akan dihapus.\nData tidak dapat dikembalikan.")){
			$("#loading").show();
			$.post('{{ url("fetch/stocktaking/update_audit_new/audit1") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
					var id = $("#qr_code").val();
					fillStore(id);
				}else{
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
	}

	function cancInput() {
		$('#input').hide();
		$('#main').show();

		$('#id').val("");
		$('#store').html("");
		$('#sub_store').html("");
		$('#category').html("");
		$('#material_number').html("");
		$('#location').html("");
		$('#material_description').html("");
		$('#model_key_surface').html("");
		$('#lot_uom').html("");
		$('#text_lot').html("");
		$('#lot').prop('disabled', false);

		resetCount();
		document.getElementById("sum_total").value = '';
	}

	function checkConf(store) {
		var data = {
			store : store
		}

		$.get('{{ url("fetch/stocktaking/check_confirm_new/audit1") }}', data, function(result, status, xhr){
			if(result.status){
				$('#confirm').prop('disabled', false);
			}else{
				$('#confirm').prop('disabled', true);
			}

			var persen = Math.floor(parseFloat(result.actual) / parseFloat(result.minimum) * 100);

			if(persen > 0 && persen < 100){
				$('#progress-bar').css('width', persen+'%');
				$('#progress-bar').css('font-size', '20px');
				$('#progress-bar').css('font-weight', 'bold');
				$('#progress-bar').html(persen + "% Complete");

				$('#progress-text').append().empty();
			}else if(persen == 0){
				$('#progress-bar').css('width', '0%');
				$('#progress-bar').append().empty();
				
				$('#progress-text').css('color', '#333');
				$('#progress-text').html("0% Complete");
			}else{
				$('#progress-bar').css('width', '100%');
				$('#progress-bar').css('font-size', '20px');
				$('#progress-bar').css('font-weight', 'bold');
				$('#progress-bar').html("100% Complete");
				
				$('#progress-text').append().empty();
			}
		});
	}

	function showAudit(id) {
		$('#input').show();		
		$('#main').hide();		

		var data = {
			id : id
		}

		$.get('{{ url("fetch/stocktaking/material_detail_audit") }}', data, function(result, status, xhr){

			if (result.status) {

				$("#id").val(id);
				$("#store").text(result.material[0].store);
				$("#sub_store").text(result.material[0].sub_store);
				$("#category").text(result.material[0].category);
				$("#material_number").text(result.material[0].material_number);
				$("#location").text(result.material[0].location);
				$("#material_description").text(result.material[0].material_description);
				$("#model_key_surface").text((result.material[0].model || '')+' '+(result.material[0].key || '')+' '+(result.material[0].surface || ''));
				$("#lot_uom").text((result.material[0].lot || '-') + ' ' + result.material[0].bun);
				lot_uom = (result.material[0].lot || 1);

				if(result.material[0].lot > 0){
					$("#text_lot").text(result.material[0].lot + ' x');
				}else{
					$("#text_lot").text('- x');
					$('#lot').prop('disabled', true);
				}

			} else {
				cancInput();
				openErrorGritter('Error');
			}

		});	
	}

	function save(){
		var id = $("#id").val();
		var quantity = $("#sum_total").val();
		var auditor_name = $("#auditor").val();
		var data = auditor_name.split(' - ');
		var auditor = data[0];

		var data = {
			id : id,
			quantity : quantity,
			auditor : auditor
		}

		$.post('{{ url("fetch/stocktaking/update_audit_new/audit1") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', result.message);
				var store = $("#qr_code").val();

				fillStore(store);
				cancInput();
			}else{
				openErrorGritter('Error', result.message);
			}
		});
	}

	function conf() {
		$("#loading").show();

		var str = $("#store_title").text();
		var store = str.replace("STORE : ", "");

		var data = {
			store : store	
		}

		if(confirm("Data Audit 1 akan disimpan oleh sistem.\nData tidak dapat dikembalikan.")){

			$.post('{{ url("fetch/stocktaking/update_process_new/audit1") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);
					$("#loading").hide();

					var store = $("#qr_code").val();

					fillStore(store);
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
		
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
			document.getElementById("total_"+i).value = total;

			sum_total += total;
		}

		document.getElementById("sum_total").value = sum_total;
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

</script>
@endsection