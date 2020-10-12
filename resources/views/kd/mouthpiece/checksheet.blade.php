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
						<table class="table table-hover table-bordered table-striped" id="tableList" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 15%;">Stuffing Date</th>
									<th style="width: 10%;">Material</th>
									<th style="width: 50%;">Description</th>
									<th style="width: 15%;">Destination</th>
									<th style="width: 10%;">Target</th>
								</tr>					
							</thead>
							<tbody id="tableBodyList">
							</tbody>
							<tfoot style="background-color: rgb(252, 248, 227);">
								<tr>
									<th colspan="4" style="text-align:center;">Total:</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
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
					<span style="font-weight: bold; font-size: 16px;">Stuffing Date:</span>
					<input type="text" id="shipment_date"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-4">
					<span style="font-weight: bold; font-size: 16px;">Target:</span>
					<input type="text" id="target_quantity"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 1vw;">Quantity Checksheet:</span>
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
								<th style="width: 1.5%">Tanggal Stuffing</th>
								<th style="width: 1%">Destinasi</th>
								<th style="width: 5%">List Item</th>
								<th style="width: 1%">Total Qty</th>
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
					checksheetTable += '<td>'+value.st_date+'</td>';
					checksheetTable += '<td>'+value.destination_shortname+'</td>';
					checksheetTable += '<td>'+value.item+'</td>';
					checksheetTable += '<td>'+value.total+'</td>';
					checksheetTable += '<td><button class="btn btn-info btn-sm" id="'+value.kd_number+'" onclick="reprintChecksheet(id)"><i class="fa fa-print"></i></button>&nbsp;<button class="btn btn-danger btn-sm" id="'+value.kd_number+'" onclick="deleteChecksheet(id)">Delete</button></td>';
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
		if($('#shipment_schedule_id').val() == ""){
			openErrorGritter('Error', "Pilih item terlebih dahulu.");
			audio_error.play();
			return false;
		}
		if($('#quantity').val() <= 0){
			openErrorGritter('Error', "Masukkan quantity checksheet.");
			audio_error.play();
			return false;
		}

		var shipment_schedule_id = $('#shipment_schedule_id').val();
		var material_number = $('#material_number').val();
		var material_description = $('#material_description').val();
		var destination = $('#destination').val();
		var quantity = $('#quantity').val();
		var shipment_date = $('#shipment_date').val();
		var target = $('#target_quantity').val();

		if(parseInt(quantity) > parseInt(target)){
			openErrorGritter('Error', "Quantity checksheet melebihi target ekspor.");
			audio_error.play();
			return false;			
		}


		for(var i = 0; i < item_list.length; i++){
			if(item_list[i]['shipment_schedule_id'] == shipment_schedule_id ){
				openErrorGritter('Error', "Material ini sudah masuk pada item list.");
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
		itemTable += '<td id="list_'+$('#shipment_schedule_id').val()+'">'+$('#destination').val()+'</td>';
		itemTable += '<td>'+$('#shipment_date').val()+'</td>';
		itemTable += '<td>'+$('#material_number').val()+'</td>';
		itemTable += '<td>'+$('#material_description').val()+'</td>';
		itemTable += '<td>'+quantity+'</td>';
		itemTable += '</tr>';

		item_list.push({ 
			shipment_schedule_id: shipment_schedule_id,
			material_number: material_number,
			material_description: material_description,
			destination: destination,
			shipment_date: shipment_date,
			quantity: quantity
		});

		$('#itemTableBody').append(itemTable);
		clearItem();

	}

	function fillField(id, material_number, description, destination, target, shipment_date){
		clearItem();
		$('#shipment_schedule_id').val(id);
		$('#material_number').val(material_number);
		$('#material_description').val(description);
		$('#destination').val(destination);
		$('#target_quantity').val(target);
		$('#shipment_date').val(shipment_date);
	}

	function fillTableList(){

		$.get('{{ url("fetch/kd_new/mouthpiece") }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			var total_target = 0;
			$.each(result.target, function(key, value) {
				tableData += '<tr onclick="fillField(\''+value.id+'\''+','+'\''+value.material_number+'\''+','+'\''+value.material_description+'\''+','+'\''+value.destination_shortname+'\''+','+'\''+value.target+'\''+','+'\''+value.st_date+'\')">';
				tableData += '<td>'+ value.st_date +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.destination_shortname +'</td>';
				tableData += '<td id="target_'+value.id+'">'+ value.target +'</td>';
				tableData += '</tr>';
				total_target += value.target;
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
				"footerCallback": function (tfoot, data, start, end, display) {
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$%,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					var api = this.api();
					var totalPlan = api.column(4).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(4).footer()).html(totalPlan.toLocaleString());
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