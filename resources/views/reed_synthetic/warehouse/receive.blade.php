@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td:hover {
		overflow: visible;
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
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		cursor: pointer;
		background-color: #7dfa8c;
		color: black;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.content{
		color: white;
	}
</style>
@stop

@section('header')
<section class="content-header">

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_material" style="margin-right: 5px">
				<i class="fa fa-plus"></i>&nbsp;&nbsp;Receive
			</button>
		</div>
		<div class="col-xs-12" style="margin-top: 1%;">
			<table id="table-material" class="table table-bordered table-hover" style="width: 100%;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 10%">Received At</th>
						<th style="width: 20%">Material</th>
						<th style="width: 40%">Material Description</th>
						<th style="width: 10%">Quantity</th>
						<th style="width: 10%">Bag</th>
						<th style="width: 10%">Print Label</th>
					</tr>
				</thead>
				<tbody id="body-material">
				</tbody>
			</table>

		</div>
	</div>

	<div class="modal modal-default fade" id="add_material" style="color: black;">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add New ReceiveW
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-3">Receive Date<span class="text-red">*</span></label>	
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-blue">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" name="date" id="date" placeholder="Select Date">											
										</div>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-3">Material<span class="text-red">*</span></label>
									<div class="col-sm-8" align="left">
										<select class="form-control select2" data-placeholder="Select Material" id="material_number" style="width: 100%">
											<option style="color:grey;" value="">Select Material</option>
											@foreach($materials as $material)
											<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-3">Quantity<span class="text-red">*</span></label>	
									<div class="col-sm-3" align="left" style="padding-right: 0px;">
										<input type="number" class="form-control" placeholder="Input Qty" id="quantity" style="width: 100%;">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 0px;">
										<input class="form-control" value="KG" id="quantity" style="width: 100%; text-align: center;" disabled="">
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="addMaterial()"> Add Material</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();


		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchTable();
		
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	
	$("#add_material").on("hidden.bs.modal", function () {
		$('#date').val('');
		$("#material_number").prop('selectedIndex', 0).change();
		$('#quantity').val('');		
	});

	function addMaterial() {
		var date = $('#date').val();
		var material_number = $('#material_number').val();
		var quantity = $('#quantity').val();

		if (date == '') {
			openErrorGritter('Error!', 'You need to select date');
			return false;
		}

		if (material_number == '') {
			openErrorGritter('Error!', 'You need to select material');
			return false;
		}

		if (quantity == '') {
			openErrorGritter('Error!', 'You need to input quantity');
			return false;
		}else if((quantity % 15) != 0){
			openErrorGritter('Error!', 'Lot is not a multiple of 15 KG');
			return false;
		}

		var data = {
			date : date,
			material_number : material_number,
			quantity : quantity
		}

		$("#loading").show();	

		$.post('{{ url("input/reed/resin_receive") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);

				$('#add_material').modal('hide');

				$("#material_number").prop('selectedIndex', 0).change();
				$('#quantity').val('');
				$('#date').val('');
				
				$('#table-material').DataTable().ajax.reload();
				
				$("#loading").hide();

			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}

	function fetchTable() {

		$('#table-material').DataTable().destroy();
		$('#table-material tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});

		var table_material = $('#table-material').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
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
				}
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
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/reed/resin_receive") }}"
			},
			"columns": [
			{ "data": "receive_date"},
			{ "data": "material_number"},
			{ "data": "material_description"},
			{ "data": "quantity"},
			{ "data": "bag_quantity"},
			{ "data": "print"}
			]
		});
		
	}

	function print(id) {

		data = {
			id : id
		}

		$.get('{{ url("print/reed/resin_receive") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				$('#table-material').DataTable().ajax.reload();	
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

</script>
@endsection

