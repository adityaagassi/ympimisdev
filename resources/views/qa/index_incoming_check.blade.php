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
	#ngTemp {
		height:200px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:385px;
		overflow-y: scroll;
		padding-top: 5px;
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

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
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
	<input type="hidden" id="location" value="{{ $location }}">
	<input type="hidden" id="employee_id" value="{{$emp->employee_id}}">
	<input type="hidden" id="start_time" value="">
	<input type="hidden" id="incoming_check_code" value="">
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Date</th>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Inspector</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;" id="date">{{date("Y-m-d")}}</td>
						<td style="background-color: #14213d; color: white; text-align: center; font-size:15px; width: 30%;" id="op">{{$emp->employee_id}}</td>
						<td style="background-color: #fca311; text-align: center; color: #14213d; font-size: 15px;" id="op2">{{$emp->name}}</td>
					</tr>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Loc</th>
						<th colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Urutan Lot Dalam Satu Kedatangan (Bukan Qty Recieve)</th>
					</tr>
					<tr>
						<td style="background-color: #14213d; text-align: center;color: white; font-size:15px;" id="loc">{{$loc}}</td>
						<td colspan="2">
							<input type="text" class="pull-right numpad2" name="lot_number" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="lot_number" placeholder="Urutan Lot Dalam Satu Kedatangan">
						</td>
					</tr>
					
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
							MATERIAL
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" class="pull-right" name="material_number" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="material_number" placeholder="Material Number" onkeyup="checkMaterial(this.value)">
						</td>
					</tr>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">
							Material Description
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">
							Vendor
						</td>
					</tr>
					<tr>
						<td id="material_description" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">-
						</td>
						<td id="vendor" style="background-color: #14213d; text-align: center; color: #fff; font-size: 20px;">-
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
							QTY
						</td>
					</tr>
					<tr>
						<td>
							<input type="number" class="pull-right numpad2" name="qty_rec" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_rec" placeholder="Quantity Recieve">
						</td>
						<td>
							<input type="number" class="pull-right numpad" name="qty_check" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check" placeholder="Quantity Check" onchange="inputQtyCheck(this.value)">
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INVOICE NUMBER
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INSPECTION LEVEL
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="invoice" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="invoice" placeholder="Invoice">
						</td>
						<td>
							<select name="inspection_level" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="inspection_level" data-placeholder="Inspection Level">
								<option value="-">Pilih Inspection Level</option>
								@foreach($inspection_level as $inspection)
									<option value="{{$inspection->inspection_level}}">{{$inspection->inspection_level}}</option>
								@endforeach
							</select>
						</td>
					</tr>
					<tr>
						<td style="background-color: #80e5ff; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							REPAIR
						</td>
						<td style="background-color: #da96ff; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							QTY OK
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="repair" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="repair" placeholder="Qty Repair" readonly value="0">
						</td>
						<td>
							<input type="text" class="pull-right" name="total_ok" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="total_ok" placeholder="Qty OK" readonly value="0">
						</td>
					</tr>
					<tr>
						<td style="background-color: #69ffaf; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							SCRAP
						</td>
						<td style="background-color: #ff8c8c; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							NG RATIO (%)
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="scrap" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="scrap" placeholder="Qty Scrap" readonly value="0">
						</td>
						<td>
							<input type="text" class="pull-right" name="ng_ratio" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="ng_ratio" placeholder="NG Ratio (%)" readonly value="0">
						</td>
					</tr>
					<tr>
						<td style="background-color: #ffe06e; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							RETURN
						</td>
						<td style="background-color: #ffcd9c; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							STATUS LOT
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="return" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="return" placeholder="Qty Return" readonly value="0">
						</td>
						<td>
							<select name="status_lot" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="status_lot" data-placeholder="Status Lot">
								<option value="-">Pilih Status Lot</option>
								<option value="Lot OK">Lot OK</option>
								<option value="Lot Out">Lot Out</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			<div class="row" style="padding-left: 15px;padding-right: 15px">
				<button class="btn btn-info pull-right" onclick="fetchDetailRecord()" style="width: 100%;font-weight: bold;font-size: 20px">Record</button>
			</div>
			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1" id="tableNgList">
					<thead>
						<tr>
							<th style="width: 65%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Nama NG</th>
						</tr>
					</thead>
					<tbody id="bodyTableNgList">
						
					</tbody>
				</table>
			</div>
			<div id="ngTemp">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Nama NG</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Qty</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Status</th>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Note</th>
							<th style="width: 20%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Action</th>
						</tr>
					</thead>
					<tbody id="bodyNgTemp">
					</tbody>
				</table>
			</div>

			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
				<button class="btn btn-danger" id="btn_cancel" onclick="cancelAll()" style="font-size: 25px;font-weight: bold;width: 100%">
					CANCEL
				</button>
			</div>
			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-left: 5px">
				<button class="btn btn-success" id="btn_confirm" onclick="confirmNgLog()" style="font-size: 25px;font-weight: bold;width: 100%">
					CONFIRM
				</button>
			</div>
		</div>
	</div>
</section>

<!-- <div class="modal fade" id="modalOperator">
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
</div> -->

<div class="modal fade" id="modalNg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="ng_name" style="width: 100%;background-color: #fca311;font-size: 25px;font-weight: bold;padding: 5px;text-align: center;color: #14213d"></h4>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
						<tbody>
							<tr>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									QTY
								</td>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Status
								</td>
							</tr>
							<tr>
								<td>
									<input type="number" class="pull-right numpad" name="qty_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_ng" placeholder="Qty NG">
								</td>
								<td>
									<select name="status_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="status_ng" data-placeholder="Status NG">
										<option value="-">Pilih Status NG</option>
										<option value="Repair">Repair</option>
										<option value="Scrap">Scrap</option>
										<option value="Return">Return</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Note
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<textarea type="text" class="pull-right" name="note_ng" style="height: 50px;font-size: 20px;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="note_ng" placeholder="Note"></textarea>
								</td>
							</tr>
						</tbody>
					</table>

					<div style="padding-top: 10px">
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="record-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffc654;padding: 5px"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;"></h4></center>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-3 col-md-offset-3">
						<span style="font-weight: bold;">Date From</span>
						<div class="form-group">
							<div class="input-group date">
								<div class="input-group-addon bg-white">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<span style="font-weight: bold;">Date To</span>
						<div class="form-group">
							<div class="input-group date">
								<div class="input-group-addon bg-white">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 col-md-offset-3">
						<span style="font-weight: bold;">Vendor</span>
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
								@foreach($vendors as $vendor)
								<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
								@endforeach
							</select>
							<input type="text" name="vendor" id="vendor_choose" style="color: black !important" hidden>
						</div>
					</div>
					<div class="col-md-3">
						<span style="font-weight: bold;">Material</span>
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
								@foreach($materials as $material)
								<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
								@endforeach
							</select>
							<input type="text" name="material" id="material_choose" style="color: black !important" hidden>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-2">
						<div class="col-md-10">
							<div class="form-group pull-right">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
								<button class="btn btn-primary col-sm-14" onclick="fetchDetailRecord()">Search</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" style="overflow-x: scroll;">
					<table class="table table-bordered" id="tableDetail">
						<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
				            <tr>
				                <th style="font-weight: bold;" rowspan="2">#</th>
				                <th style="font-weight: bold;" rowspan="2">Loc</th>
				                <th style="font-weight: bold;" rowspan="2">Lot Number</th>
				                <th style="font-weight: bold;" rowspan="2">Date</th>
				                <th style="font-weight: bold;" rowspan="2">Inspector</th>
				                <th style="font-weight: bold;" rowspan="2">Vendor</th>
				                <th style="font-weight: bold;" rowspan="2">Invoice</th>
				                <th style="font-weight: bold;" rowspan="2">Inspection Level</th>
				                <th style="font-weight: bold;" rowspan="2">Material</th>
				                <th style="font-weight: bold;" rowspan="2">Desc</th>
				                <th style="font-weight: bold;" rowspan="2">Qty Rec</th>
				                <th style="font-weight: bold;" rowspan="2">Qty Check</th>
				                <th style="font-weight: bold;" rowspan="2">Defect</th>
				                <th style="font-weight: bold;" colspan="3">Jumlah NG</th>
				                <th style="font-weight: bold;" rowspan="2">Note</th>
				                <th style="font-weight: bold;" rowspan="2">NG Ratio</th>
				            </tr>
				            <tr>
				                <th style="font-weight: bold;">Repair</th>
				                <th style="font-weight: bold;">Return</th>
				                <th style="font-weight: bold;">Scrap</th>
				            </tr>
				        </thead>
						<tbody id="bodyTableDetail">
							
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<!-- <script src="{{ url("js/jqbtk.js") }}"></script> -->

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
		// $('#modalOperator').modal({
		// 	backdrop: 'static',
		// 	keyboard: false
		// });
		// $("#operator").val('');
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		cancelAll();
		// $('#invoice').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#material_number').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#note_ng').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#ng_search').keyboard();
		ng_list();
	});

	function inputQtyCheck(value) {
		$('#total_ok').val(value);
	}

	function changeVendor() {
		$("#vendor_choose").val($("#vendorSelect").val());
	}

	function changeMaterial() {
		$("#material_choose").val($("#materialSelect").val());
	}

	function cancelAll() {
		$('#material_number').val('');
		$('#invoice').val('');
		$('#qty_check').val('');
		$('#qty_rec').val('');
		$('#lot_number').val('');
		$('#material_description').html('-');
		$('#vendor').html('-');
		$('#inspection_level').val('-').trigger('change');
		$('#note_ng').val('');
		$('#qty_ng').val('');
		$('#status_ng').val('-').trigger('change');
		$('#start_time').val('');
		$('#repair').val('0');
		$('#scrap').val('0');
		$('#return').val('0');
		$('#total_ok').val('0');
		$('#ng_ratio').val('0');
		$('#status_lot').val('-').trigger('change');

		if ($('#incoming_check_code').val() != "") {
			var data = {
				incoming_check_code:$('#incoming_check_code').val()
			}
			$.get('{{ url("delete/qa/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fetchNgTemp();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
		$('#ng_ratio').val('0');
		$('#incoming_check_code').val("");
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	// $('#modalOperator').on('shown.bs.modal', function () {
	// 	$('#operator').focus();
	// });

	// $('#operator').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#operator").val().length >= 8){
	// 			var data = {
	// 				employee_id : $("#operator").val(),
	// 			}
				
	// 			$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', result.message);
	// 					$('#modalOperator').modal('hide');
	// 					$('#modalMesin').modal('show');
	// 					$('#op').html(result.employee.employee_id);
	// 					$('#op2').html(result.employee.name);
	// 					$('#employee_id').val(result.employee.employee_id);
	// 					$('#material_number').focus();
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

	function checkMaterial(material_number) {
		if (material_number.length === 7) {
			var data = {
				material_number:material_number
			}
			$.get('{{ url("fetch/qa/check_material") }}', data, function(result, status, xhr){
				if(result.status){
					$('#material_description').html(result.material.material_description);
					$('#vendor').html(result.material.vendor);
					$('#material_number').focus();
					$('#start_time').val(getActualFullDate());
				}
				else{
					$('#material_description').html("-");
					$('#vendor').html("-");
				}
			});
		}else{
			$('#material_description').html("-");
			$('#vendor').html("-");
		}
	}

	function showModalNg(ng_name) {
		if ($('#material_number').val() == "" || $('#qty_rec').val() == "" || $('#lot_number').val() == "" || $('#qty_check').val() == "" || $('#invoice').val() == "" || $('#inspection_level').val() == "-") {
			openErrorGritter('Error!','Masukkan Semua Data');
		}else{
			$('#note_ng').val('');
			$('#qty_ng').val('');
			$('#status_ng').val('-').trigger('change');
			$('#ng_name').html(ng_name);
			$('#modalNg').modal('show');
		}
	}

	function confNgTemp() {
		if ($('#qty_ng').val() == "" || $('#status_ng').val() == "-") {
			alert('Isi Semua Data');
		}else{
			var material_number = $('#material_number').val();
			var material_description = $('#material_description').text();
			var vendor = $('#vendor').text();
			var qty_rec = $('#qty_rec').val();
			var lot_number = $('#lot_number').val();
			var qty_check = $('#qty_check').val();
			var invoice = $('#invoice').val();
			var inspection_level = $('#inspection_level').val();
			var ng_name = $('#ng_name').text();
			var qty_ng = $('#qty_ng').val();
			var status_ng = $('#status_ng').val();
			var note_ng = $('#note_ng').val();
			var inspector = $('#employee_id').val();
			var location = $('#location').val();

			var data = {
				material_number:material_number,
				material_description:material_description,
				vendor:vendor,
				qty_rec:qty_rec,
				lot_number:lot_number,
				qty_check:qty_check,
				invoice:invoice,
				inspection_level:inspection_level,
				ng_name:ng_name,
				qty_ng:qty_ng,
				status_ng:status_ng,
				note_ng:note_ng,
				location:location,
				inspector:inspector,
			}

			$.post('{{ url("input/qa/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#note_ng').val('');
					$('#qty_ng').val('');
					$('#status_ng').val('-').trigger('change');
					$('#modalNg').modal('hide');
					$('#incoming_check_code').val(result.incoming_check_code);
					fetchNgTemp();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function fetchNgTemp() {
		data = {
			incoming_check_code:$('#incoming_check_code').val()
		}
		$.get('{{ url("fetch/qa/ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				var ngTemp = "";
				$('#bodyNgTemp').html("");
				var index = 1;

				var repair = 0;
				var scrap = 0;
				var returns = 0;
				var total_ok = 0;
				var total_ng = 0;
				var ng_ratio = 0;

				$.each(result.ng_temp, function(key,value){
					if (index % 2 === 0) {
						var color = 'style="background-color: #e1e5f2"';
					}else{
						var color = 'style="background-color: #bfdbf7"';
					}
					ngTemp += '<tr '+color+'>';
					ngTemp += '<td>'+value.ng_name+'</td>';
					ngTemp += '<td>'+value.qty_ng+'</td>';
					ngTemp += '<td>'+value.status_ng+'</td>';
					ngTemp += '<td>'+(value.note_ng || "")+'</td>';
					ngTemp += '<td><button onclick="deleteNgTemp(\''+value.id+'\')" class="btn btn-danger btn-sm">Delete</button></td>';
					ngTemp += '</tr>';
					index++;

					if (value.status_ng == 'Return') {
						returns = returns + parseInt(value.qty_ng);
					}
					if (value.status_ng == 'Scrap') {
						scrap = scrap + parseInt(value.qty_ng);
					}
					if (value.status_ng == 'Repair') {
						repair = repair + parseInt(value.qty_ng);
					}

					total_ng = total_ng + parseInt(value.qty_ng);
				});

				$('#repair').val(repair);
				$('#return').val(returns);
				$('#scrap').val(scrap);

				var check = $('#qty_check').val();

				total_ok = check - total_ng;
				if (check != "") {
					ng_ratio = (total_ng / check) * 100;
				}

				$('#total_ok').val(total_ok);
				$('#ng_ratio').val(ng_ratio);

				$('#bodyNgTemp').append(ngTemp);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function deleteNgTemp(id) {
		if (confirm('Are you sure want to delete this data?')) {
			var data = {
				id:id
			}
			$.get('{{ url("delete/qa/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fetchNgTemp();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
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

	function ng_list() {
		var data = {
			location:'{{$location}}'
		}

		$.get('{{ url("fetch/qa/ng_list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableNgList').DataTable().clear();
				$('#tableNgList').DataTable().destroy();
				var tableng = "";
				$('#bodyTableNgList').html('');
				var index = 1;
				for(var i = 0; i < result.ng_list.length; i++){
					if (index % 2 === 0 ) {
						var color = 'style="background-color: #fffcb7"';
					} else {
						var color = 'style="background-color: #ffd8b7"';
					}
					tableng += '<tr '+color+'>';
					tableng += '<td id="'+result.ng_list[i].ng_name+'" onclick="showModalNg(\''+result.ng_list[i].ng_name+'\')" style="font-size: 35px;">'+result.ng_list[i].ng_name+'</td>';
					tableng += '</tr>';
					index++;
				}
				$('#bodyTableNgList').append(tableng);
				var table = $('#tableNgList')
					.on('search.dt', function() {
				      // $('input[type="search"]').keyboard({
				      //   usePreview: false,
				      //   change: function(e, kb) {
				      //     table.search(kb.el.value).draw();
				      //   }
				      // });
				      $('.dataTables_filter').addClass('pull-left');
				    })
				    .DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'searching': true,
					'ordering': false,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "simple",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function confirmNgLog() {
		if ($('#status_lot').val() == '-' || $('#material_number').val() == "" || $('#qty_rec').val() == "" || $('#lot_number').val() == "" || $('#qty_check').val() == "" || $('#invoice').val() == "" || $('#inspection_level').val() == "-") {
			alert('Isi Semua Data');
		}else{
			var incoming_check_code = $('#incoming_check_code').val();
			var material_number = $('#material_number').val();
			var material_description = $('#material_description').text();
			var vendor = $('#vendor').text();
			var qty_rec = $('#qty_rec').val();
			var lot_number = $('#lot_number').val();
			var qty_check = $('#qty_check').val();
			var invoice = $('#invoice').val();
			var inspection_level = $('#inspection_level').val();
			var inspector = $('#employee_id').val();
			var location = $('#location').val();
			var repair = $('#repair').val();
			var scrap = $('#scrap').val();
			var returns = $('#return').val();
			var total_ok = $('#total_ok').val();
			var total_ng = parseInt(qty_check) - parseInt($('#total_ok').val());
			var ng_ratio = $('#ng_ratio').val();
			var status_lot = $('#status_lot').val();

			var data = {
				incoming_check_code:incoming_check_code,
				material_number:material_number,
				material_description:material_description,
				vendor:vendor,
				qty_rec:qty_rec,
				lot_number:lot_number,
				qty_check:qty_check,
				invoice:invoice,
				inspection_level:inspection_level,
				location:location,
				inspector:inspector,
				repair:repair,
				scrap:scrap,
				returns:returns,
				total_ok:total_ok,
				total_ng:total_ng,
				ng_ratio:ng_ratio,
				status_lot:status_lot,
			}
			$.post('{{ url("input/qa/ng_log") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					cancelAll();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function fetchDetailRecord() {
		$('#loading').show();
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		var vendor = $('#vendor_choose').val();
		var material = $('#material_choose').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
			vendor:vendor,
			material:material,
		}
		$.get('{{ url("fetch/qa/detail_record") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableDetail').html("");
				var tableData = "";
				var index = 1;
				
				$.each(result.detail, function(key, value) {
					if (value.location == 'wi1') {
			  			var loc = 'Woodwind Instrument (WI) 1';
			  		}else if (value.location == 'wi2') {
			  			var loc = 'Woodwind Instrument (WI) 2';
			  		}else if(value.location == 'ei'){
			  			var loc = 'Educational Instrument (EI)';
			  		}else if(value.location == 'sx'){
			  			var loc = 'Saxophone Body';
			  		}else if (value.location == 'cs'){
			  			var loc = 'Case';
			  		}else if(value.location == 'ps'){
			  			var loc = 'Pipe Silver';
			  		}
			  		var jumlah = 0;

			  		if (value.ng_name != null) {
						var ng_name = value.ng_name.split('_');
						var ng_qty = value.ng_qty.split('_');
						var status_ng = value.status_ng.split('_');
						if (value.note_ng != null) {
							var note_ng = value.note_ng.split('_');
						}else{
							var note_ng = "";
						}
						jumlah = ng_name.length;
					}else{
						jumlah = 1;
					}

					
					tableData += '<tr>';
					tableData += '<td rowspan="'+jumlah+'">'+ index +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ loc +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.lot_number +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.created +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.employee_id +'<br>'+ value.name +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.vendor +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.invoice +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.inspection_level +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.material_number +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.material_description +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.qty_rec +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.qty_check +'</td>';
					if (value.ng_name != null) {
						tableData += '<td>';
						tableData += '<span class="label label-danger">'+ng_name[0]+'</span><br>';
						tableData += '</td>';
						if (status_ng[0] == 'Repair') {
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
						}else if (status_ng[0] == 'Return') {
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
						}else if (status_ng[0] == 'Scrap') {
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
						}
						if (note_ng.length > 0) {
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+note_ng[0]+'</span><br>';
							tableData += '</td>';
						}else{
							tableData += '<td>';
							tableData += '</td>';
						}
					}else{
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
					}
					tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'">'+ value.ng_ratio.toFixed(2) +'</td>';
					tableData += '</tr>';
					if (value.ng_name != null) {
						for (var i = 1 ;i < ng_name.length; i++) {
							tableData += '<tr>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_name[i]+'</span><br>';
							tableData += '</td>';
							if (status_ng[i] == 'Repair') {
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
							}else if (status_ng[i] == 'Return') {
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
							}else if (status_ng[i] == 'Scrap') {
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
							}
							if (note_ng.length > 0) {
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+note_ng[i]+'</span><br>';
								tableData += '</td>';
							}else{
								tableData += '<td>';
								tableData += '</td>';
							}
							tableData += '</tr>';
						}
					}

					index++;
				});
				$('#bodyTableDetail').append(tableData);
				$('#myModalLabel').html("Detail Record of "+$('#op2').text());
				$('#loading').hide();
				$('#record-modal').modal('show');
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

</script>
@endsection