@extends('layouts.master')
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
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.selected {
		background: gold !important;
	}
</style>
@stop

@section('header')
<section class="content-header">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach

	<h1>
		{{ $title }}
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">

		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Larutan</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="table-larutan" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 25%">Larutan</th>
									<th style="width: 25%">Location</th>
									<th style="width: 15%">Category</th>
									<th style="width: 10%">Target Warning</th>
									<th style="width: 10%">Target Max</th>
									<th style="width: 10%">Actual Quantity</th>
									<th style="width: 5%">Edit</th>
								</tr>
							</thead>
							<tbody id="body-larutan">
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #f39c12;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Larutan</h1>
					</div>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="text" id="larutan_id" hidden>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Larutan<span class="text-red">*</span></label>	
									<div class="col-sm-6" align="left">
										<input type="text" class="form-control" id="larutan" style="width: 100%;" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>	
									<div class="col-sm-6" align="left">
										<input type="text" class="form-control" id="location" style="width: 100%;" readonly>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4" style="text-align: right;">Category<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<select class="form-control select2" id="category" onchange="categoryChg()" data-placeholder="Select Category" style="width: 100%">
											<option value="">Select Category</option>
											<option value="CONTROLLING CHART">CONTROLLING CHART</option>
											<option value="SCHEDULLING">SCHEDULLING</option>
											<option value="ADD BY ANALYZING">ADD BY ANALYZING</option>
										</select>
									</div>
								</div>

								<div id="target">
									<div class="form-group row" align="right">
										<label class="col-sm-4">Target Warning<span class="text-red">*</span></label>	
										<div class="col-sm-4" align="left">
											<input type="number" class="form-control" min="0" id="target_warning" style="width: 100%;">
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-sm-4">Target Max<span class="text-red">*</span></label>	
										<div class="col-sm-4" align="left">
											<input type="number" class="form-control" min="0" id="target_max" style="width: 100%;">
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="updateLarutan()"> Submit</button>
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

		fetchTable();
		
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	$("#edit").on("hidden.bs.modal", function () {
		$("#material_number").prop('selectedIndex', 0).change();
		$('#quantity').val('');
	});

	function updateLarutan() {
		var id = $('#larutan_id').val();
		var category = $('#category').val();
		var target_warning = $('#target_warning').val();
		var target_max = $('#target_max').val();


		if(category == 'CONTROLLING CHART'){
			if(target_warning == '' || target_max == ''){
				openErrorGritter('Error', 'Target harus diisi');
				return false;
			}			
		}

		var data = {
			id : id,
			category : category,
			target_warning : target_warning,
			target_max : target_max
		}

		$("#loading").show();	

		$.post('{{ url("update/chm_larutan") }}', data, function(result, status, xhr){
			if(result.status){
				$('#table-larutan').DataTable().ajax.reload();
				$('#edit').modal('hide');
				$("#loading").hide();
				openSuccessGritter('Success', result.message);



			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}

	function categoryChg() {

		var category = $('#category').val();
		if(category == 'CONTROLLING CHART'){
			$('#target').show();
		}else{
			$('#target').hide();
			$('#target_warning').val('');
			$('#target_max').val('');
		}
	}

	function editSolution(id) {

		var data = {
			id : id
		}

		$.get('{{ url("fetch/chm_larutan_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#larutan_id').val(result.data.id);
				$('#larutan').val(result.data.solution_name);
				$('#location').val(result.data.location);
				$("#category").val(result.data.category).trigger('change.select2');
				$('#target_warning').val(result.data.target_warning);
				$('#target_max').val(result.data.target_max);

				if(result.data.category == 'CONTROLLING CHART'){
					$('#target').show();
					$('#target_warning').val(result.data.target_warning);
					$('#target_max').val(result.data.target_max);
				}else{
					$('#target').hide();
					$('#target_warning').val(result.data.target_warning);
					$('#target_max').val(result.data.target_max);
				}

				$('#edit').modal('show');

			}
		});
	}

	function fetchTable() {

		var material_number = $('#filter_material_number').val();
		var data = {
			material_number:material_number
		}

		$('#table-larutan').DataTable().destroy();
		$('#table-larutan tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table_larutan = $('#table-larutan').DataTable({
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
			// "serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/chm_larutan") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "solution_name"},
			{ "data": "location"},
			{ "data": "category"},
			{
				"data": "target_warning",
				"defaultContent": "<i>-</i>"
			},
			{
				"data": "target_max",
				"defaultContent": "<i>-</i>"
			},
			{
				"data": "actual_quantity",
				"defaultContent": "<i>-</i>"
			},
			{ "data": "edit"}
			]
		});
		table_larutan.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#table-larutan tfoot tr').appendTo('#table-larutan thead');


		
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

