@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	
	input {
		line-height: 22px;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { 
		display: none;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance: textfield;
	}
	
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>

		<div class="form-group pull-right">
			<a href="javascript:void(0)" data-toggle="modal"  data-target="#modalAdd" class="btn btn-success">
				<span class="fa fa-plus"></span>&nbsp;&nbsp; Add Shipping Agency
			</a>
		</div>

	</h1>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="col-xs-12" style="margin-top: 1%;">
						<div class="row">
							<div class="col-xs-3">
								<div class="form-group">
									<select class="form-control select2" data-placeholder="Select Consignee" name="search_consignee" id="search_consignee" style="width: 100%">
										<option value=""></option>
										@foreach($consignee as $pod) 
										<option value="{{ $pod->consignee }}">{{ $pod->consignee }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<select class="form-control select2" style="width: 100%" data-placeholder="Select Port of Discharge" name="search_port_of_discharge" id="search_port_of_discharge">
										<option value=""></option>
										@foreach($port_of_discharge as $pod) 
										<option value="{{ $pod->port_of_discharge }}">{{ $pod->port_of_discharge }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<select class="form-control select2" style="width: 100%" data-placeholder="Select Port of Delivery" name="search_port_of_delivery" id="search_port_of_delivery">
										<option value=""></option>
										@foreach($port_of_delivery as $pod) 
										<option value="{{ $pod->port_of_delivery }}">{{ $pod->port_of_delivery }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
									<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
								</div>
							</div>									
						</div>
					</div>

					

				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="overflow-x: auto;">
						<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%; font-size: 12px;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%;">Ship ID</th>
									<th style="width: 5%;">Shipper</th>
									<th style="width: 10%;">Port Loading</th>
									<th style="width: 5%;">Consignee</th>
									<th style="width: 10%;">Transship Port</th>
									<th style="width: 15%;">Port of Discharge</th>
									<th style="width: 15%;">Port of Delivery</th>
									<th style="width: 10%;">Country</th>
									<th style="width: 15%;">Carrier</th>
									<th style="width: 5%;">Nomination</th>
								</tr>
							</thead>
							<tbody id="tableBodyList">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalAdd">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h1 style="background-color: #00a65a; text-align: center;" class="modal-title">
					Add Ship Agency
				</h1>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="box-body">						

							<div class="form-group row" align="right">
								<label class="col-xs-4">Ship ID<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="ship_id" id="ship_id" placeholder="Input Ship ID">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Shipper<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="shipper" id="shipper" placeholder="Input Shipper" value="YMPI" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Port Loading<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="port_loading" id="port_loading" placeholder="Input Shipper" value="SURABAYA" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Consignee<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="consignee" id="consignee" placeholder="Input Consignee">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Transship Port<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="transship_port" id="transship_port" placeholder="Input Transship Port">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Port of Discharge<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="port_of_discharge" id="port_of_discharge" placeholder="Input Port of Discharge">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Port of Delivery<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="port_of_delivery" id="port_of_delivery" placeholder="Input Port of Delivery">
								</div>
							</div>							

							<div class="form-group row" align="right">
								<label class="col-xs-4">Country<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="country" id="country" placeholder="Input Country">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Carrier<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="carier" id="carier" placeholder="Input Carrier">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Nomination<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="nomination" id="nomination" placeholder="Input Nomination">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> CANCEL</a>
				<button class="btn btn-success" onclick="saveList()"><i class="fa fa-check-square-o"></i> SUBMIT</button>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear: true
		});

		$('.select3').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true
		});

		fillTable();

	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function saveList(){
		var ship_id = $("#ship_id").val();
		var shipper = $("#shipper").val();
		var port_loading = $("#port_loading").val();
		var consignee = $("#consignee").val();
		var transship_port = $("#transship_port").val();
		var port_of_discharge = $("#port_of_discharge").val();
		var port_of_delivery = $("#port_of_delivery").val();
		var country = $("#country").val();
		var carier = $("#carier").val();
		var nomination = $("#nomination").val();

		if(ship_id == '' ||shipper == '' || port_loading == '' ||consignee == '' || transship_port == '' || port_of_discharge == '' || port_of_delivery == '' || country == '' || carier == ''|| nomination == ''){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}

		var data = {
			ship_id : ship_id,
			shipper : shipper,
			port_loading : port_loading,
			consignee : consignee,
			transship_port : transship_port,
			port_of_discharge : port_of_discharge,
			port_of_delivery : port_of_delivery,
			country : country,
			carier : carier,
			nomination : nomination
		}

		$("#loading").show();

		$.post('{{ url("fetch/add_shipping_agency") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();

				$("#loading").hide();
				$("#modalAdd").modal('hide');
				openSuccessGritter('Success', result.message);

			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}
		});		
	}

	function fillTable(){

		$('#tableList').DataTable().destroy();

		var consignee = $('#search_consignee').val();
		var port_of_delivery = $('#search_port_of_delivery').val();
		var port_of_discharge = $('#search_port_of_discharge').val();
		
		var data = {
			consignee:consignee,
			port_of_delivery:port_of_delivery,
			port_of_discharge:port_of_discharge
		}

		var table = $('#tableList').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/shipping_agency") }}",
				"data" : data
			},
			"columns": [
			{ "data": "ship_id" },
			{ "data": "shipper" },
			{ "data": "port_loading" },
			{ "data": "consignee" },
			{ "data": "transship_port" },
			{ "data": "port_of_discharge" },
			{ "data": "port_of_delivery" },
			{ "data": "country" },
			{ "data": "carier" },
			{ "data": "nomination" }
			]
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
			ttle: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}


</script>
@endsection