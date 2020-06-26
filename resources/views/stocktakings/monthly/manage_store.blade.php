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
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_material" style="margin-right: 5px">
		<i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Material
	</button>

	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
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
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Store Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-md-3 col-md-offset-1">
							<div class="form-group">
								<label>Group</label>
								<select class="form-control select2" multiple="multiple" name="filter_area" id='filter_area' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($groups as $group) 
									<option value="{{ $group->area }}">{{ $group->area }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Location</label>
								<select class="form-control select2" multiple="multiple" name="filter_location" id='filter_location' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($locations as $location) 
									<option value="{{ $location->location }}">{{ $location->location }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Store</label>
								<select class="form-control select2" multiple="multiple" name="filter_store" id='filter_store' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($stores as $store) 
									<option value="{{ $store->store }}">{{ $store->store }}</option>
									@endforeach
								</select>
							</div>
						</div>		
					</div>

					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

				</div>
			</div>
		</div>





		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Store</a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Store Detail</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="store_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 20%">Group</th>
									<th style="width: 30%">Location</th>
									<th style="width: 30%">Store</th>
									<th style="width: 10%">Material Qty</th>
									<th style="width: 5%">Reprint</th>
									<th style="width: 5%">Delete</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
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
					<div class="tab-pane" id="tab_2">
						<table id="store_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 5%">Group</th>
									<th style="width: 10%">Location</th>
									<th style="width: 20%">Store</th>
									<th style="width: 10%">Category</th>
									<th style="width: 10%">Material</th>
									<th style="width: 30%">Material Description</th>
									<th style="width: 5%">UOM</th>
									<th style="width: 5%">Reprint</th>
									<th style="width: 5%">Delete</th>
								</tr>
							</thead>
							<tbody>
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

	<div class="modal modal-default fade" id="add_material">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add New Material
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Group<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<select class="form-control select2" data-placeholder="Select Group" name="group" id="group" style="width: 100%">
											<option value=""></option>
											@foreach($groups as $group) 
											<option value="{{ $group->area }}">{{ $group->area }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Storage Location<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<select class="form-control select2" data-placeholder="Select Store" name="storage-location" id="storage-location" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Store<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<select class="form-control select2" data-placeholder="Select Store" name="store" id="store" style="width: 100%">
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
										<select class="form-control select2" data-placeholder="Select Category" name="category" id="category" style="width: 100%">
											<option style="color:grey;" value="">Select Category</option>
											<option value="ASSY">ASSY</option>
											<option value="SINGLE">SINGLE</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<input oninput="checkMaterial()" class="form-control" type="text" id="material_number" name="material_number" placeholder="Fill Material Number">
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();

		// fetchTable();

		$('#other').hide();
	});

	$("#add_material").on("hidden.bs.modal", function () {
		$('#material_description').text('');
	});

	$("#group").change(function(){
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
				$("#storage-location").html(message);                                                   
				$("#loading").hide();                                                        
			}
		});                    
	});

	$("#storage-location").change(function(){
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
				$("#store").html(message);                                                   
				$("#loading").hide();                                                      
			}
		});                    
	});

	$("#store").change(function(){
		var store = $(this).val(); 

		if(store == 'LAINNYA'){
			$('#other').show();
		}else{
			$('#other').hide();
		}

	});


	function checkMaterial() {
		var material_number = $('#material_number').val();
		if(material_number.length == 7){
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



	function addMaterial(argument) {
		var location = $('#storage-location').val(); 
		var store = $('#store').val();
		var category = $('#category').val(); 
		var material = $('#material').val();
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
				$("#material").val("");
				$('#material_description').text('');


				$('#store_table').DataTable().ajax.reload();
				$('#store_detail').DataTable().ajax.reload();

				$("#loading").hide();

			}else{
				openSuccessGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}

	function fetchTable() {
		//Store
		$('#store_table').DataTable().destroy();

		var area = $('#filter_area').val();
		var location = $('#filter_location').val();
		var store = $('#filter_store').val();

		var data = {
			area:area,
			location:location,
			store:store
		}

		
		$('#store_table tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var store_table = $('#store_table').DataTable({
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
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/store") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "store"},
			{ "data": "quantity"},
			{ "data": "reprint"},
			{ "data": "delete"}
			]
		});

		store_table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#store_table tfoot tr').appendTo('#store_table thead');
		


		//Detail
		$('#store_detail').DataTable().destroy();

		$('#store_detail tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var store_detail = $('#store_detail').DataTable({
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
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/store_details") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "store"},
			{ "data": "category"},
			{ "data": "material_number"},
			{ "data": "material_description"},
			{ "data": "uom"},
			{ "data": "reprint"},
			{ "data": "delete"}
			]
		});

		store_detail.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#store_detail tfoot tr').appendTo('#store_detail thead');

	}

	function reprintID(id){
		var data = {
			id:id
		}

		$("#loading").show();

		$.get('{{ url("reprint/stocktaking/summary_of_counting_id") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

			} else {
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}

		});
	}

	function reprintStore(store){
		var data = {
			store:store
		}

		$("#loading").show();

		$.get('{{ url("reprint/stocktaking/summary_of_counting_store") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

			} else {
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}

		});

	}

	function deleteStore(store) {
		$("#loading").show();

		var data = {
			store : store
		}

		if(confirm("Data yang dihapus tidak dapat dikembalikan.")){
			$.post('{{ url("fetch/stocktaking/delete_store") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);

					$('#store_table').DataTable().ajax.reload();
					$('#store_detail').DataTable().ajax.reload();

					$("#loading").hide();

				}else{
					openSuccessGritter('Error', result.message);
					$("#loading").hide();

				}
			});
		}else{
			$("#loading").hide();
		}
	}

	function deleteMaterial(id) {
		$("#loading").show();

		var data = {
			id : id
		}

		if(confirm("Data yang dihapus tidak dapat dikembalikan.")){
			$.post('{{ url("fetch/stocktaking/delete_material") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);

					$('#store_table').DataTable().ajax.reload();
					$('#store_detail').DataTable().ajax.reload();

					$("#loading").hide();

				}else{
					openSuccessGritter('Error', result.message);
					$("#loading").hide();

				}
			});
		}else{
			$("#loading").hide();
		}
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

