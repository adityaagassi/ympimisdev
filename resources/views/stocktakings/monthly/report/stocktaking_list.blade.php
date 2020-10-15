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
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_material" style="margin-right: 5px">
		Tambah List Baru
	</button>
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
				<div class="box-header">
					<h3 class="box-title">Stocktaking List Filter</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Store</label>
									<select class="form-control select2" multiple="multiple" name="store" id='store' data-placeholder="Select Store" style="width: 100%;">
										<option value=""></option>
										@foreach($stores as $store)
										<option value="{{ $store->store }}">{{ $store->store }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Material Number</label>
									<select class="form-control select2" multiple="multiple" name="material_number" id='material_number' data-placeholder="Select Material Number" style="width: 100%;">
										<option value=""></option>
										@foreach($materials as $material)
										<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
										@endforeach
									</select>
								</div>
							</div>			
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Storage Location</label>
									<select class="form-control select2" multiple="multiple" name="storage_location" id='storage_location' data-placeholder="Select Storage Location" style="width: 100%;">
										<option value=""></option>
										@foreach($storage_locations as $storage_location)
										<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Area</label>
									<select class="form-control select2" multiple="multiple" name="area" id='area' data-placeholder="Select Area" style="width: 100%;">
										<option value=""></option>
										@php
										$area = array();
										@endphp
										@foreach($storage_locations as $storage_location)
										@if(!in_array($storage_location->area, $area))
										<option value="{{ $storage_location->area }}">{{ $storage_location->area }}</option>
										@php
										array_push($area, $storage_location->area);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>			
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-6">
								<div class="form-group pull-right">
									<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
									<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<table id="resumeTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Area</th>
										<th style="width: 2%">Store</th>
										<th style="width: 1%">Material</th>
										<th style="width: 6%">Description</th>
										<th style="width: 1%">Uom</th>
										<th style="width: 1%">Location</th>
										<th style="width: 1%">Category</th>
										<th style="width: 1%">Status</th>
										<th style="width: 1%">Action</th>
									</tr>
								</thead>
								<tbody id="resumeTableBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

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
								<label class="col-sm-4">Area<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<select class="form-control select2" data-placeholder="Select Group" name="newArea" id="newArea" style="width: 100%">
										<option value=""></option>
										@php
										$area = array();
										@endphp
										@foreach($storage_locations as $storage_location)
										@if(!in_array($storage_location->area, $area))
										<option value="{{ $storage_location->area }}">{{ $storage_location->area }}</option>
										@php
										array_push($area, $storage_location->area);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-sm-4">Storage Location<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<select class="form-control select2" data-placeholder="Select Store" name="newLocation" id="newLocation" style="width: 100%">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-sm-4">Store<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<select class="form-control select2" data-placeholder="Select Store" name="newStore" id="newStore" style="width: 100%">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right" id="other">
								<div class="col-sm-4 col-sm-offset-4" align="left">
									<input class="form-control" type="text" id="other-store" name="other-store" placeholder="Fill Store Name">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-sm-4">Category<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<select class="form-control select2" data-placeholder="Select Category" name="newCategory" id="newCategory" style="width: 100%">
										<option style="color:grey;" value="">Select Category</option>
										<option value="ASSY">ASSY</option>
										<option value="SINGLE">SINGLE</option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-sm-4">Material<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<input oninput="checkMaterial()" class="form-control" type="text" id="newMaterial" name="newMaterial" placeholder="Fill Material Number">
								</div>
							</div>

							<div class="form-group row" align="right" id="other">
								<div class="col-sm-4 col-sm-offset-4" align="left">
									<label style="color: grey; margin-left: 3%" id="material_description"></label>
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

<div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h1 style="background-color: #f39c12; text-align: center;" class="modal-title">
					Edit Stocktaking List
				</h1>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">

							<div class="form-group row" align="right">
								<label class="col-sm-4">ID</label>
								<div class="col-sm-4">
									<input type="text" style="width: 100%" class="form-control" id="editId" placeholder="Enter ID">
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Store<span class="text-red">*</span></label>
								<div class="col-sm-4">
									<input type="text" style="width: 100%" class="form-control" id="editStore" placeholder="Enter Store">
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Material<span class="text-red">*</span></label>
								<div class="col-sm-4">
									<input type="text" style="width: 100%" class="form-control" id="editMaterial" placeholder="Enter Material">
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Location<span class="text-red">*</span></label>
								<div class="col-sm-4">
									<input type="text" style="width: 100%" class="form-control" id="editLocation" placeholder="Enter Location">
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Category<span class="text-red">*</span></label>
								<div class="col-sm-4" align="left">
									<select class="form-control select2" data-placeholder="Select Category" name="editCategory" id="editCategory" style="width: 100%">
										<option style="color:grey;" value="">Select Category</option>
										<option value="ASSY">ASSY</option>
										<option value="SINGLE">SINGLE</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="saveList()"><i class="fa fa-pencil"></i> Save Edit</button>
			</div>
		</div>
	</div>
</div>

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
		$('.select2').select2();
		$('#other').hide();

	});

	function fetchTable(){
		$('#loading').show();
		var store = $('#store').val();
		var material_number = $('#material_number').val();
		var location = $('#location').val();
		var area = $('#area').val();

		var data = {
			store:store,
			material_number:material_number,
			area:area,
			location:location
		}

		$.get('{{ url("fetch/stocktaking/stocktaking_list") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#resumeTable').DataTable().clear();
				$('#resumeTable').DataTable().destroy();
				$('#resumeTableBody').html("");
				var dataTable = "";

				$.each(result.stocktaking_lists, function(key, value){
					dataTable += '<tr id="'+value.id+'">';
					dataTable += '<td>'+value.area+'</td>';
					dataTable += '<td>'+value.store+'</td>';
					dataTable += '<td>'+value.material_number+'</td>';
					dataTable += '<td>'+value.material_description+'</td>';
					dataTable += '<td>'+value.bun+'</td>';
					dataTable += '<td>'+value.location+'</td>';
					dataTable += '<td>'+value.category+'</td>';
					if(value.print_status == 0){
						dataTable += '<td style="background-color: #ff1744;">Belum Cetak</td>';
					}
					else if(value.print_status == 1){
						dataTable += '<td style="background-color: #ffea00;">Sudah Cetak</td>';
					}
					else if(value.process == 1){
						dataTable += '<td style="background-color: #76ff03;">Sudah Input</td>';
					}
					else if(value.process == 2){
						dataTable += '<td style="background-color: #00e5ff;">Sudah Audit</td>';
					}
					else if(value.process == 4){
						dataTable += '<td style="background-color: #d500f9;">Sudah Breakdown</td>';
					}
					dataTable += '<td>';
					dataTable += '<button onCLick="editList(\''+value.id+'\''+','+'\''+value.store+'\''+','+'\''+value.material_number+'\''+','+'\''+value.location+'\''+','+'\''+value.category+'\')" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></button>&nbsp;';
					dataTable += '<button onCLick="deleteList('+value.id+')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>';				
					dataTable += '</td>';
					dataTable += '</tr>';
				});

				$('#resumeTableBody').append(dataTable);

				$('#resumeTable').DataTable({
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

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});

	}

	function saveList(){
		var id = $('#editId').val();
		var store = $('#editStore').val();
		var material = $('#editMaterial').val();
		var location = $('#editLocation').val();
		var category = $('#editCategory').val();
		var data = {
			id:id,
			store:store,
			material:material,
			location:location,
			category:category
		}
		$.post('{{ url("edit/stocktaking/stocktaking_list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#editId').val('');
				$('#editStore').val('');
				$('#editMaterial').val('');
				$('#editLocation').val('');
				$('#editCategory').val('');
				
				fetchTable();


				$('#modalEdit').modal('hide');
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error', result.message);
			}
		});
	}

	function editList(id, store, material_number, location, category){
		$('#editId').val('');
		$('#editStore').val('');
		$('#editMaterial').val('');
		$('#editLocation').val('');
		$('#editCategory').val('');


		$('#editId').val(id);
		$('#editId').prop('disabled', true);
		$('#editStore').val(store);
		$('#editMaterial').val(material_number);
		$('#editLocation').val(location);
		$('#editCategory').val(category).trigger('change.select2');


		$('#modalEdit').modal('show');

	}

	$("#newStore").change(function(){
		var store = $(this).val(); 

		if(store == 'LAINNYA'){
			$('#other').show();
		}else{
			$('#other').hide();
		}

	});

	$("#newLocation").change(function(){
		$("#loading").show();

		var location = $(this).val(); 
		var data = {
			location : location
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "{{ url("fetch/stocktaking/get_store") }}",
			data: data,
			success: function(message){
				$("#newStore").html(message);                                                   
				$("#loading").hide();                                                      
			}
		});                    
	});

	$("#newArea").change(function(){
		$("#loading").show();

		var group = $(this).val(); 
		var data = {
			group : group
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "{{ url("fetch/stocktaking/get_storage_location") }}",
			data: data,
			success: function(message){
				$("#newLocation").html(message);                                                   
				$("#loading").hide();                                                        
			}
		});                    
	});

	function addMaterial(argument) {
		var location = $('#storage-location').val(); 
		var store = $('#store').val();
		var category = $('#category').val(); 
		var material = $('#material_number').val();
		var material_description = $('#material_description').text();

		if(store == 'LAINNYA'){
			store = $('#other-store').val();
		}

		if(material_description == ''){
			alert("Input material number");
			return false;
		}

		var data = {
			location : location,
			store : store,
			category : category,
			material : material
		}

		$.post('{{ url("fetch/stocktaking/add_material") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);

				$("#add_material").modal('hide');

				$("#group").prop('selectedIndex', 0).change();
				$("#storage-location").prop('selectedIndex', 0).change();
				$("#store").prop('selectedIndex', 0).change();
				$("#other-store").val("");
				$("#category").prop('selectedIndex', 0).change();
				$("#material_number").val("");
				$('#material_description').text('');

				var area = $('#filter_area').val();
				var location = $('#filter_location').val();
				var store = $('#filter_store').val();

				if(area != '' || location != '' || store != ''){
					// $('#store_table').DataTable().ajax.reload();
					$('#store_detail').DataTable().ajax.reload();
				}

				$("#loading").hide();

			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}


	function checkMaterial() {
		var material_number = $('#newMaterial').val();
		if(material_number.length >= 7){
			var data = {
				material : material_number
			}

			$.get('{{ url("fetch/stocktaking/check_material") }}', data, function(result, status, xhr){
				if(result.status){
					if(result.material){
						$('#material_description').text(result.material.material_description);
					}
				}
			});
		}else{
			$('#material_description').text('');
		}
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