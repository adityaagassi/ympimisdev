@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
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

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	input[type=number] {
		-moz-appearance:textfield;
	}

	#loading { display: none; }
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
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<div class="box box-danger">
				<div class="box-body">
					<div class="col-xs-12" style="overflow-x: auto;">
						<div class="row">
							<div class="col-xs-4">
								<div class="form-group">
									<select class="form-control select2" id="addDestination" data-placeholder="Pilih Destinasi" style="width: 100%;">
										<option></option>
										@foreach($destinations as $destination)
										<option value="{{ $destination->destination_shortname }}">{{ $destination->destination_shortname }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="input-group date" style="width: 100%;">
									<input type="text" placeholder="Pilih Tanggal Packing" class="form-control pull-right" id="addPackingDate" name="prodFrom">
								</div>
							</div>
							<div class="col-xs-4">
								<div class="input-group date" style="width: 100%;">
									<input type="text" placeholder="Pilih Tanggal Ekspor" class="form-control pull-right" id="addStuffingDate" name="prodFrom">
								</div>
							</div>
							<div class="col-xs-12">
								<table class="table table-hover table-bordered table-striped" id="tableList" style="width: 100%;">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 10%;">Material</th>
											<th style="width: 50%;">Description</th>
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
		</div>
		<div class="col-xs-6">
			<div class="row">
				<input type="hidden" id="shipment_schedule_id">
				<div class="col-xs-4">
					<span style="font-weight: bold; font-size: 1vw;">Material Number:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 1.5vw; text-align: center;" disabled>
				</div>
				<div class="col-xs-8">
					<span style="font-weight: bold; font-size: 1vw;">Material Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 1.5vw; text-align: center;" disabled>
				</div>
				<div class="col-xs-4">
					<span style="font-weight: bold; font-size: 16px;">Destination:</span>
					<input type="text" id="destination"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-4">
					<span style="font-weight: bold; font-size: 16px;">Packing Date:</span>
					<input type="text" id="packing_date"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-4">
					<span style="font-weight: bold; font-size: 16px;">Stuffing Date:</span>
					<input type="text" id="shipment_date"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 1vw;">Quantity:</span>
				</div>
				<div class="col-xs-6">
					<input type="number" id="quantity" class="numpad" style="width: 100%; height: 50px; font-size: 1.5vw; text-align: center;" value="0">
				</div>
				<div class="col-xs-6" style="padding-bottom: 10px;">
					<button class="btn btn-primary" onclick="addItem()" style="font-size: 1.5vw; height: 50px; width: 100%; font-weight: bold; padding: 0;">
						ADD ITEM
					</button>
				</div>

				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 1.1vw;">ITEM LIST</span>
					<table id="itemTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 4%">Packing</th>
								<th style="width: 4%">Destinasi</th>
								<th style="width: 4%">Shipment</th>
								<th style="width: 4%">Material</th>
								<th style="width: 15%">Description</th>
								<th style="width: 4%">Quantity</th>
							</tr>
						</thead>
						<tbody id="itemTableBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-12">
					<button class="btn btn-success" onclick="createChecksheet()" style="width: 100%; font-size: 2vw; padding: 0; font-weight: bold;">CREATE CHECKSHEET</button>
				</div>

				<div class="col-xs-12" style="padding-top: 20px;">
					<table id="checksheetTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th style="width: 1.5%">ID Checksheet</th>
								<th style="width: 1.5%">Tanggal Packing</th>
								<th style="width: 5%">List Item</th>
								<th style="width: 1%">Total Qty</th>
								<th style="width: 1.5%">Tanggal Stuffing</th>
								<th style="width: 1%">Destinasi</th>
								<th style="width: 2%">Action</th>
							</tr>
						</thead>
						<tbody id="checksheetTableBody">
						</tbody>
					</table>
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
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('.select2').select2();
		$('#addPackingDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#addStuffingDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#addDestination').prop('selectedIndex', 0).change();
		$('#addPackingDate').val("");
		$('#addStuffingDate').val("");
		$('body').toggleClass("sidebar-collapse");

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		clearItem();
		fetchChecksheet();
		fillTableList();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function clearItem(){
		$('#shipment_schedule_id').val("");
		$('#material_number').val("");
		$('#material_description').val("");
		$('#destination').val("");
		$('#shipment_date').val("");
		$('#packing_date').val("");
		$('#quantity').val(0);
	}

	var item_list = [];

	function createChecksheet(){
		$('#loading').show();
		if(item_list.length <= 0){
			openErrorGritter('Error', "Pilih item untuk checksheet terlebih dahulu.");
			audio_error.play();
			return false;			
		}

		var data = {
			item_list:item_list,
			location:'mouthpiece'
		}
		$.post('{{ url("create/kd_mouthpiece/checksheet") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				clearItem();
				// $('#addDestination').prop('selectedIndex', 0).change();
				// $('#addPackingDate').val("");
				// $('#addStuffingDate').val("");
				fetchChecksheet();
				fillTableList();
				$('#loading').hide();
			}
			else{
				openErrorGritter('Error', result.message);
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		});
	}

	function reprintChecksheet(kd_number) {
		var data = {
			kd_number:kd_number,
			location:'mouthpiece'
		}

		if(confirm("Apakah anda yakin akan mencetak ulang KDO nomor "+kd_number+"?")){

			$.get('{{ url("reprint/kd_mouthpiece/checksheet") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);	
				}
			});
		}
	}

	function fetchChecksheet(){
		$.get('{{ url("fetch/kd_mouthpiece/checksheet") }}', function(result, status, xhr){
			if(result.status){

				var checksheetTable = "";
				$('#checksheetTableBody').html("");

				$.each(result.checksheets, function(key, value){
					checksheetTable += '<tr>';
					checksheetTable += '<td>'+value.kd_number+'</td>';
					checksheetTable += '<td>'+value.packing_date+'</td>';
					checksheetTable += '<td>'+value.item+'</td>';
					checksheetTable += '<td>'+value.total+'</td>';
					checksheetTable += '<td>'+value.st_date+'</td>';
					checksheetTable += '<td>'+value.destination_shortname+'</td>';
					if(value.print_status != 1){
						checksheetTable += '<td><button class="btn btn-info btn-sm" id="'+value.kd_number+'" onclick="reprintChecksheet(id)">Print</button>&nbsp;<button class="btn btn-danger btn-sm" id="'+value.kd_number+'" onclick="deleteChecksheet(id)"><i class="fa fa-trash"></i></button></td>';
					}
					else{
						checksheetTable += '<td><button class="btn btn-info btn-sm" id="'+value.kd_number+'" onclick="reprintChecksheet(id)">Re-Print</button>&nbsp;<button class="btn btn-danger btn-sm" id="'+value.kd_number+'" onclick="deleteChecksheet(id)"><i class="fa fa-trash"></i></button></td>';						
					}
					checksheetTable += '</tr>';
				});

				$('#checksheetTableBody').append(checksheetTable);
			}
			else{
				openErrorGritter('Error', result.message);
				audio_error.play();
				return false;				
			}
		});
	}

	function deleteChecksheet(id){
		$('#loading').show();
		var data = {
			id:id
		} 
		if(confirm("Apakah anda yakin akan menghapus checksheet nomor "+id+"?")){
			$.post('{{ url("delete/kd_mouthpiece/checksheet") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					fetchChecksheet();
					fillTableList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error', result.message);
					audio_error.play();
					return false;				
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function addItem(){
		if($('#material_numner').val() == ""){
			openErrorGritter('Error', "Pilih item terlebih dahulu.");
			audio_error.play();
			return false;
		}
		if($('#quantity').val() <= 0){
			openErrorGritter('Error', "Masukkan quantity checksheet.");
			audio_error.play();
			return false;
		}

		var material_number = $('#material_number').val();
		var material_description = $('#material_description').val();
		var target = $('#target_quantity').val();
		var destination = $('#destination').val();
		var quantity = $('#quantity').val();
		var shipment_date = $('#shipment_date').val();
		var packing_date = $('#packing_date').val();

		for(var i = 0; i < item_list.length; i++){
			if(item_list[i]['packing_date'] != packing_date ){
				openErrorGritter('Error', "Tidak bisa menambahkan tanggal packing yang berbeda dalam satu checksheet.");
				audio_error.play();
				return false;
			}
			if(item_list[i]['destination'] != destination ){
				openErrorGritter('Error', "Tidak bisa menambahkan destinasi yang berbeda dalam satu checksheet.");
				audio_error.play();
				return false;
			}
			if(item_list[i]['shipment_date'] != shipment_date ){
				openErrorGritter('Error', "Tidak bisa menambahkan tanggal shipment yang berbeda dalam satu checksheet.");
				audio_error.play();
				return false;
			}
		}

		var itemTable = "";

		itemTable += '<tr>';
		itemTable += '<td>'+$('#packing_date').val()+'</td>';
		itemTable += '<td>'+$('#destination').val()+'</td>';
		itemTable += '<td>'+$('#shipment_date').val()+'</td>';
		itemTable += '<td>'+$('#material_number').val()+'</td>';
		itemTable += '<td>'+$('#material_description').val()+'</td>';
		itemTable += '<td>'+quantity+'</td>';
		itemTable += '</tr>';

		item_list.push({ 
			packing_date: packing_date,
			material_number: material_number,
			material_description: material_description,
			destination: destination,
			shipment_date: shipment_date,
			quantity: quantity
		});

		$('#itemTableBody').append(itemTable);
		clearItem();
	}

	function fillField(material_number, description){
		clearItem();
		if($('#addDestination').val() != "" && $('#addPackingDate').val() != "" && $('#addStuffingDate').val() != ""){
			$('#material_number').val(material_number);
			$('#material_description').val(description);
			$('#destination').val($('#addDestination').val());
			$('#packing_date').val($('#addPackingDate').val());
			$('#shipment_date').val($('#addStuffingDate').val());
		}
		else{
			openErrorGritter('Error', "Tentukan destinas, tanggal packing dan tanggal ekspor terlebih dahulu.");
			audio_error.play();
			return false;	
		}
	}

	function fillTableList(){

		$.get('{{ url("fetch/kd_mouthpiece/material") }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			$.each(result.target, function(key, value) {
				tableData += '<tr onclick="fillField(\''+value.material_number+'\''+','+'\''+value.material_description+'\')">';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '</tr>';
			});
			$('#tableBodyList').append(tableData);


			$('#tableList').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'pageLength',
						className: 'btn btn-default',
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
				'pageLength': 10,
				'searching': true,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true

			});

		});
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