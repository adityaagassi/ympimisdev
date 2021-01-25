@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
</style>
@endsection

@section('header')
<section class="content-header">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach

	@if(in_array('S36', $navs))	
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_material" style="margin-right: 5px">
		<i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah List Baru
	</button>
	@endif

	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<table id="resumeTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Material</th>
										<th style="width: 6%">Description</th>
										<th style="width: 1%">Created By</th>
									</tr>
								</thead>
								<tbody id="resumeTableBody">
								</tbody>
								<tfoot>
									<th></th>
									<th></th>
									<th></th>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal modal-default fade" id="add_material">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h1 style="background-color: #00a65a; text-align: center;" class="modal-title">
						Add New Material
					</h1>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material Number<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" data-placeholder="Select Material" name="material_number" id="material_number" style="width: 100%">
											<option value=""></option>
											@foreach($materials as $material)
											<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
											@endforeach
										</select>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addMaterial()"><i class="fa fa-plus"></i> Add Material</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true,
			minimumInputLength: 3

		});
		
		fetchTable();
	});


	function fetchTable(){

		$('#resumeTable').DataTable().destroy();

		$('#resumeTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
		} );

		var table = $('#resumeTable').DataTable({
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
				"url" : "{{ url("fetch/stocktaking/material_forecast") }}"
			},
			"columns": [
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "name" }
			]
		});

		table.columns().every( function () {
			var that = this;
			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#resumeTable tfoot tr').appendTo('#resumeTable thead');	
	}

	function addMaterial(argument) {
		var material = $('#material_number').val();
		$("#loading").show();

		var data = {
			material : material
		}

		$.post('{{ url("add/stocktaking/material_forecast") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);

				$("#add_material").modal('hide');
				$("#material_number").prop('selectedIndex', 0).change();

				$("#loading").hide();
				clearConfirmation();

			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});
	}

	function deleteList(id){
		if(confirm("Apakah anda yakin akan menghapus list item tersebut?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/stocktaking/stocktaking_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#'+id).remove();
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error', result.message);
				}
			});
		};
	}

	function clearConfirmation(){
		location.reload(true);
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
</script>

@endsection