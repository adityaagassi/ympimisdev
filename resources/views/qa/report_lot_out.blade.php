@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.buttonclass {
	  top: 0;
	  left: 0;
	  transition: all 0.15s linear 0s;
	  position: relative;
	  display: inline-block;
	  padding: 15px 25px;
	  background-color: #ffe800;
	  text-transform: uppercase;
	  color: #404040;
	  font-family: arial;
	  letter-spacing: 1px;
	  box-shadow: -6px 6px 0 #404040;
	  text-decoration: none;
	  cursor: pointer;
	}
	.buttonclass:hover {
	  top: 3px;
	  left: -3px;
	  box-shadow: -3px 3px 0 #404040;
	  color: white
	}
	.buttonclass:hover::after {
	  top: 1px;
	  left: -2px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass:hover::before {
	  bottom: -2px;
	  right: 1px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass::after {
	  transition: all 0.15s linear 0s;
	  content: "";
	  position: absolute;
	  top: 2px;
	  left: -4px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg);
	  z-index: -1;
	}
	.buttonclass::before {
	  transition: all 0.15s linear 0s !important;
	  content: "";
	  position: absolute;
	  bottom: -4px;
	  right: 2px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg) !important;
	  z-index: -1 !important;
	}

	a.buttonclass {
	  position: relative;
	}

	a:active.buttonclass {
	  top: 6px;
	  left: -6px;
	  box-shadow: none;
	}
	a:active.buttonclass:before {
	  bottom: 1px;
	  right: 1px;
	}
	a:active.buttonclass:after {
	  top: 1px;
	  left: 1px;
	}

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
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<form method="GET" action="{{ url('excel/qa/report/incoming') }}">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
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
						<div class="col-md-4">
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
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Location</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" onchange="changeLocation()" style="width: 100%;color: black !important"> 
									@foreach($location as $location)
									<?php $locs = explode("_", $location) ?>
									<option value="{{$locs[0]}}">{{$locs[1]}}</option>
									@endforeach
								</select>
								<input type="text" name="location" id="location" style="color: black !important" hidden>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Inspection Level</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id='inspectionLevelSelect' onchange="changeInspectionLevel()" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
									@foreach($inspection_levels as $inspection_level)
									<option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
									@endforeach
								</select>
								<input type="text" name="inspection_level" id="inspection_level" style="color: black !important" hidden>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Vendor</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
									@foreach($vendors as $vendor)
									<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
									@endforeach
								</select>
								<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Material</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
									@foreach($materials as $material)
									<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
									@endforeach
								</select>
								<input type="text" name="material" id="material" style="color: black !important" hidden>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-5">
							<div class="col-md-12">
								<div class="form-group pull-left">
									<a href="{{ url('index/qa') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/report/incoming') }}" class="btn btn-danger">Clear</a>
									<a class="btn btn-primary col-sm-14" href="javascript:void(0)" onclick="fillList()">Search</a>
								</div>
							</div>
						</div>
					</div>
					</form>
					<div class="col-xs-12" style="direction: rtl;transform: rotate(180deg);overflow-y: hidden;overflow-x: scroll;">
						<div class="row" id="divTable" style="direction: ltr;transform: rotate(-180deg);">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="evidence_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Make Evidence Incoming Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-2">Loc</label>
									<div class="col-sm-4" align="left">
										<input type="location_ev" class="form-control" id="location_ev" placeholder="Location" required readonly>
										<input type="hidden" class="form-control" id="incoming_check_code_ev" placeholder="Check Code" required>
										<input type="hidden" class="form-control" id="id_log_ev" placeholder="Check Code" required>
										<input type="hidden" class="form-control" id="type_ev" placeholder="Check Code" required>
									</div>
									<label class="col-sm-2">Date</label>
									<div class="col-sm-4" align="left">
										<input type="date_ev" class="form-control" id="date_ev" placeholder="Date" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Material</label>
									<div class="col-sm-4" align="left" id="divMaterial">
										<select class="form-control selectMaterial" id='material_ev' data-placeholder="Select Material" style="width: 100%;color: black !important" disabled>
											<option value=""></option>
											@foreach($materials as $material)
											<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
											@endforeach
										</select>
									</div>
									<label class="col-sm-2">Invoice</label>
									<div class="col-sm-4" align="left">
										<input type="invoice_ev" class="form-control" id="invoice_ev" placeholder="Invoice" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Inspection Level</label>
									<div class="col-sm-4" align="left" id="divInspectionLevel">
										<select class="form-control selectInspectionLevel" id="inspection_level_ev" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important" disabled>
											<option value=""></option>
											@foreach($inspection_levels as $inspection_level)
											<option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
											@endforeach
										</select>
									</div>
									<label class="col-sm-2">Lot Number</label>
									<div class="col-sm-4" align="left">
										<input type="number" class="form-control" id="lot_number_ev" placeholder="Lot Number" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Qty Recieve</label>
									<div class="col-sm-4" align="left">
										<input type="number" class="form-control" id="qty_rec_ev" placeholder="Qty Recieve" required readonly>
									</div>
									<label class="col-sm-2">Status Lot</label>
									<div class="col-sm-4" align="left" id="divLotStatus">
										<select class="form-control selectLotStatus" id="status_lot_ev" data-placeholder="Select Lot Status" style="width: 100%;color: black !important" disabled>
											<option value=""></option>
											<option value="Lot OK">Lot OK</option>
											<option value="Lot Out">Lot Out</option>
										</select>
									</div>
								</div>
								<div class="col-xs-12" style="background-color: #ffa53d">
									<h4 style="text-align: center; margin:5px; font-weight: bold;">NG List</h4>
								</div>
								<table class="table table-bordered table-striped">
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<th>Nama NG</th>
											<th>Qty NG</th>
											<th>Status</th>
											<th>Note</th>
										</tr>
									</thead>
									<tbody id="bodyNgList">
										
									</tbody>
								</table>

								<div class="col-xs-12" style="background-color: #3db8ff;margin-top: 10px">
									<h4 style="text-align: center; margin:5px; font-weight: bold;">Masukkan Evidence</h4>
								</div>
								<div class="form-group row" align="right">
									<div class="col-sm-12" align="left">
										<textarea id="report_evidence"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="inputEvidence()"><i class="fa fa-pencil-square-o"></i> Submit</button>
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
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		CKEDITOR.replace('report_evidence' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('.selectMaterial').select2({
			dropdownParent: $('#divMaterial'),
			allowClear:true
		});

		$('.selectInspectionLevel').select2({
			dropdownParent: $('#divInspectionLevel'),
			allowClear:true
		});

		$('.selectLotStatus').select2({
			dropdownParent: $('#divLotStatus'),
			allowClear:true
		});
		cancelAll();

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function cancelAll() {
		$('#location_ev').val('');
		$('#date_ev').val('');
		$('#material_ev').val('').trigger('change');
		$('#invoice_ev').val('');
		$('#inspection_level_ev').val('').trigger('change');
		$('#lot_number_ev').val('');
		$('#qty_rec_ev').val('');
		$('#status_lot_ev').val('').trigger('change');
		$('#incoming_check_code_ev').val('');
		$('#id_log_ev').val('');
		$('#bodyNgList').html('');
	}

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	function changeLocation() {
		$("#location").val($("#locationSelect").val());
	}

	function changeInspectionLevel() {
		$("#inspection_level").val($("#inspectionLevelSelect").val());
	}

	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th rowspan="2">Loc</th>';
		tableData += '<th rowspan="2">Date</th>';
		tableData += '<th rowspan="2">Inspector</th>';
		tableData += '<th rowspan="2">Vendor</th>';
		tableData += '<th rowspan="2">Invoice</th>';
		tableData += '<th rowspan="2">Inspection Level</th>';
		tableData += '<th rowspan="2">Lot Number</th>';
		tableData += '<th rowspan="2">Material</th>';
		tableData += '<th rowspan="2">Desc</th>';
		tableData += '<th rowspan="2">Qty Rec</th>';
		tableData += '<th rowspan="2">Qty Check</th>';
		tableData += '<th rowspan="2">Defect</th>';
		tableData += '<th colspan="3">Jumlah NG</th>';
		tableData += '<th rowspan="2">Note</th>';
		tableData += '<th rowspan="2">NG Ratio</th>';
		tableData += '<th rowspan="2">Lot Status</th>';
		tableData += '<th rowspan="2">Evidence</th>';
		tableData += '<th rowspan="2">Action Evidence</th>';
		tableData += '<th rowspan="2">Send Email</th>';
		tableData += '</tr>';
		tableData += '<tr>';
		tableData += '<th>Repair</th>';
		tableData += '<th>Return</th>';
		tableData += '<th>Scrap</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		tableData += "</tfoot>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}

	function fillList(){
	$('#loading').show();
	var date_from = $('#date_from').val();
	var date_to = $('#date_to').val();
	var vendor = $('#vendor').val();
	var material = $('#material').val();
	var location = $('#location').val();
	var inspection_level = $('#inspection_level').val();

	var data = {
		date_from:date_from,
		date_to:date_to,
		vendor:vendor,
		material:material,
		location:location,
		inspection_level:inspection_level,
	}
	$.get('{{ url("fetch/qa/report/incoming/lot_out") }}',data, function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				var index = 0;
				
				$.each(result.datas, function(key, value) {
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
					tableData += '<td rowspan="'+jumlah+'">'+ loc +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.created +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.employee_id +'<br>'+ value.name +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.vendor +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.invoice +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.inspection_level +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.lot_number +'</td>';
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
					tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'">'+ value.status_lot +'</td>';
					tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'">'+ (value.report_evidence || "") +'</td>';
					if (value.send_email_status == null) {
						if (value.report_evidence == null) {
							var ev = 'INPUT';
							tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'"><button class="btn btn-primary" onclick="makeEvidence(\''+ev+'\',\''+value.id_log+'\')">Make Evidence</button><input type="hidden" id="type_evs_'+value.id_log+'" value="INPUT"></td>';
						}else{
							var ev = 'EDIT';
							tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'"><button class="btn btn-warning" onclick="makeEvidence(\''+ev+'\',\''+value.id_log+'\')">Edit Evidence</button><input type="hidden" id="type_evs_'+value.id_log+'" value="EDIT"></td>';
						}
						tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'"><button class="btn btn-info" onclick="sendEmail(\''+value.id_log+'\')">Send Email</button></td>';
					}else{
						tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'"></td>';
						tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'"><span class="label label-success">Email Terkirim</span></td>';
					}
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
				$('#example1Body').append(tableData);
				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function makeEvidence(type,id) {
		$('#type_ev').val(type);
		$('#loading').show();
		cancelAll();
		var data = {
			id:id
		}
		$.get('{{ url("fetch/qa/report/incoming/edit") }}',data, function(result, status, xhr){
			if (result.status) {
				$.each(result.datas, function(key, value) {
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
					$('#location_ev').val(loc);
					$('#date_ev').val(value.date);
					$('#material_ev').val(value.material_number).trigger('change');
					$('#invoice_ev').val(value.invoice);
					$('#inspection_level_ev').val(value.inspection_level).trigger('change');
					$('#lot_number_ev').val(value.lot_number);
					$('#qty_rec_ev').val(value.qty_rec);
					$('#status_lot_ev').val(value.status_lot).trigger('change');
					$('#incoming_check_code_ev').val(value.incoming_check_code);
					$('#id_log_ev').val(value.id_log);

					var bodyNgList = "";
					if (value.ng_name.length != 0) {
						var ng_name = value.ng_name.split('_');
						var ng_qty = value.ng_qty.split('_');
						var status_ng = value.status_ng.split('_');
						var note_ng = "";
						if (value.note_ng != null) {
							var note_ng = value.note_ng.split('_');
						}
						for (var i = 0; i < ng_name.length; i++) {
							bodyNgList += '<tr>';
							bodyNgList += '<td>'+ng_name[i]+'</td>';
							bodyNgList += '<td>'+ng_qty[i]+'</td>';
							bodyNgList += '<td>'+status_ng[i]+'</td>';
							if (note_ng != "") {
								bodyNgList += '<td>'+note_ng[i]+'</td>';
							}else{
								bodyNgList += '<td></td>';
							}
							bodyNgList += '</tr>';
						}
					}
					$("#bodyNgList").append(bodyNgList);
					if (type === 'EDIT') {
						$("#report_evidence").html(CKEDITOR.instances.report_evidence.setData(value.report_evidence));
					}else{
						$("#report_evidence").html(CKEDITOR.instances.report_evidence.setData(""));
					}
				});
				$('#evidence_modal').modal('show');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function sendEmail(id) {
		// if (confirm('Apakah Anda yakin akan mengirim Email?')) {
			if ($('#type_evs_'+id).val() == 'EDIT') {

				if (confirm("Apakah Anda yakin?")) {
					$('#loading').show();
					var data = {
						id:id
					}
					$.get('{{ url("send/qa/report/incoming/lot_out") }}',data, function(result, status, xhr){
						if (result.status) {
							fillList();
							$('#loading').hide();
							openSuccessGritter('Success',result.message);
						}else{
							$('#loading').hide();
							openErrorGritter('Error!',result.message);
						}
					});
				}
			}else{
				$('#loading').hide();
				openErrorGritter('Error!','Masukkan Evidence');
			}
		// }
	}

	function inputEvidence() {
		$('#loading').show();
		var id_log = $('#id_log_ev').val();
		var type = $('#type_ev').val();
		var report_evidence = CKEDITOR.instances.report_evidence.getData();

		var data = {
			report_evidence:report_evidence,
			id_log:id_log,
			type:type,
		}

		if (report_evidence == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan Evidence');
		}else{
			$.post('{{ url("input/qa/report/incoming/lot_out/evidence") }}',data, function(result, status, xhr){
				if (result.status) {
					fillList();
					$('#loading').hide();
					$('#evidence_modal').modal('hide');
					cancelAll();
					openSuccessGritter('Success',result.message);
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
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