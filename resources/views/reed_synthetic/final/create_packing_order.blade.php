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
		<input type="hidden" id="location" value="packing">
		<input type="hidden" id="proses" value="packing">
		<div class="col-xs-4">
			<div class="box box-danger">
				<div class="box-body">
					<div class="col-xs-12" style="overflow-x: auto;">
						<div class="row">
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
		<div class="col-xs-8">
			<div class="row">
				<div class="col-xs-12" style="padding-top: 20px;">
					<table id="checksheetTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th style="width: 15%">Order ID</th>
								<th style="width: 10%">Tanggal Packing</th>
								<th style="width: 20%">Material</th>
								<th style="width: 35%">Description</th>
								<th style="width: 15%">Total Qty</th>
								<th style="width: 15%">Action</th>
							</tr>
						</thead>
						<tbody id="checksheetTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="checksheetModal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h1 style="background-color: #00a65a; text-align: center;" class="modal-title">
						Create Packing Order
					</h1>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
						</div>
						<div class="col-xs-12">
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
							<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 25px; text-align: center;" disabled>
						</div>
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Packing Date</span>
						</div>
						<div class="col-xs-12">
							<input type="text" id="packing_date" placeholder="Select Packing Date"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;">

						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success" onclick="createOrder()">Submit</button>
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
		$('#packing_date').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

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
		$('#material_number').val("");
		$('#material_description').val("");
		$('#packing_date').val("");
	}


	function createOrder(){

		var material_number = $('#material_number').val();
		var packing_date = $('#packing_date').val();
		var proses = $('#proses').val();


		if(packing_date == ''){
			openErrorGritter('Error', "Select packing date.");
			audio_error.play();
			return false;			
		}

		var data = {
			material_number : material_number,			 
			packing_date : packing_date,
			proses : proses 
		}

		$('#loading').show();
		$.post('{{ url("create/reed/packing_order") }}', data, function(result, status, xhr){
			if(result.status){
				
				fetchChecksheet();
				$('#loading').hide();

				clearItem();
				$('#checksheetModal').modal('hide');
				openSuccessGritter('Success', result.message);

			}
			else{
				openErrorGritter('Error', result.message);
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		});
	}

	function reprintChecksheet(order_id) {
		var data = {
			order_id:order_id
		}

		if(confirm("Apakah anda yakin akan mencetak Packing Order nomor "+order_id+"?")){

			$.get('{{ url("reprint/reed/packing_order") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);	
				}else{
					openErrorGritter('Error!', result.message);	
				}
			});
		}
	}

	function fetchChecksheet(){
		$.get('{{ url("fetch/reed/packing_order") }}', function(result, status, xhr){
			if(result.status){

				var checksheetTable = "";
				$('#checksheetTable').DataTable().clear();
				$('#checksheetTable').DataTable().destroy();
				$('#checksheetTableBody').html("");

				$.each(result.checksheets, function(key, value){
					checksheetTable += '<tr>';
					checksheetTable += '<td>'+value.order_id+'</td>';
					checksheetTable += '<td>'+value.due_date+'</td>';
					checksheetTable += '<td>'+value.material_number+'</td>';
					checksheetTable += '<td>'+value.material_description+'</td>';
					checksheetTable += '<td>'+value.quantity+'</td>';
					if(value.print != 1){
						checksheetTable += '<td>';
						checksheetTable += '<button class="btn btn-primary btn-sm" id="'+value.order_id+'" onclick="reprintChecksheet(id)">Print</button>';
						checksheetTable += '&nbsp;';
						// checksheetTable += '<button class="btn btn-danger btn-sm" id="'+value.order_id+'" onclick="deleteChecksheet(id)"><i class="fa fa-trash"></i></button>';
						checksheetTable += '</td>';
					}
					else{
						checksheetTable += '<td>';
						checksheetTable += '<button class="btn btn-info btn-sm" id="'+value.order_id+'" onclick="reprintChecksheet(id)">Re-Print</button>';
						checksheetTable += '&nbsp;';
						// checksheetTable += '<button class="btn btn-danger btn-sm" id="'+value.order_id+'" onclick="deleteChecksheet(id)"><i class="fa fa-trash"></i></button>';
						checksheetTable += '</td>';					
					}
					checksheetTable += '</tr>';
				});

				$('#checksheetTableBody').append(checksheetTable);

				$('#checksheetTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ -1 ],
					[ 'Show all' ]
					],
					'buttons': {
						buttons:[
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
					"processing": true

				});


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

	function fillField(material_number, description){
		clearItem();
		$('#material_number').val(material_number);
		$('#material_description').val(description);

		$('#checksheetModal').modal('show');

	}

	function fillTableList(){

		$.get('{{ url("fetch/reed/packing_material") }}',  function(result, status, xhr){
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