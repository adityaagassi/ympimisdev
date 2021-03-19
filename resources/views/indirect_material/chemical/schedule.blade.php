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
	
	@if(in_array('S41', $navs))	
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#new_modal" style="margin-right: 5px">
		<i class="fa fa-industry"></i>&nbsp;&nbsp;Pembuatan Larutan
	</button>
	
	<button class="btn btn-primary btn-sm pull-right" data-toggle="modal"  data-target="#addition_modal" style="margin-right: 5px">
		<i class="fa fa-plus"></i>&nbsp;&nbsp;Penambahan Chemical
	</button>
	@endif

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
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-xs-2 col-xs-offset-1">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="datefrom" placeholder="Select Date">
								</div>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="form-group">
								<label>Section</label>
								<select class="form-control select2" multiple="multiple" name="section" id='section' data-placeholder="Select Section" style="width: 100%;">
									<option style="color:grey;" value="">Select Section</option>
									@foreach($sections as $section)
									<option value="{{ $section->id }}">{{ $section->section }} - {{ $section->location }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="form-group">
								<label>Larutan</label>
								<select class="form-control select2" multiple="multiple" name="larutan" id='larutan' data-placeholder="Select Larutan" style="width: 100%;">
									<option style="color:grey;" value="">Select Larutan</option>
									@foreach($larutans as $larutan)
									<option value="{{ $larutan->id }}">{{ $larutan->solution_name }} - {{ $larutan->section }} - {{ $larutan->location }}</option>
									@endforeach
								</select>
							</div>
						</div>

					</div>

					<div class="row">

						<div class="col-xs-2 col-xs-offset-1">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="dateto" placeholder="Select Date">
								</div>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" name="status" id='status' data-placeholder="Select Status" style="width: 100%;">
									<option style="color:grey;" value="">Select Status</option>
									<option value="Picked">Picked</option>
									<option value="Scheduled">Scheduled</option>
								</select>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="form-group">
								<label>Material</label>
								<select class="form-control select2" multiple="multiple" name="material" id='material' data-placeholder="Select Material" style="width: 100%;">
									<option style="color:grey;" value="">Select Material</option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>
							</div>
						</div>

					</div>
					

					<div class="col-xs-3 col-xs-offset-8">
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
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Schedule</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
						<table id="table-material" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th width="5%">Due Date</th>
									<th>Shift</th>
									<th>Category</th>
									<th>Larutan</th>
									<th>Location</th>
									<th width="5%">Material</th>
									<th style="width: 50%;">Material Description</th>
									<th>Storage Location</th>
									<th>Qty</th>
									<th>Bun</th>
									<th>Picked By</th>
									<th>Picked Time</th>
									<th>Changed By</th>
									<th>Changed Time</th>
									<th>Change</th>
									<th>Delete</th>
								</tr>
							</thead>
							<tbody id="body-material">
							</tbody>
						</table>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="new_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Pembuatan Larutan</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-3 control-label">Tanggal<span class="text-red">*</span></label>
									<div class="col-sm-3">
										<div class="input-group date">
											<div class="input-group-addon bg-purple" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="new_date" placeholder="Select Date" >
										</div>
									</div>
									<div class="col-sm-3">								
										<div class="input-group date">
											<div class="input-group-addon bg-purple" style="border: none;">
												<i class="fa fa-clock-o"></i>
											</div>
											<select class="form-control select2" data-placeholder="Select Shift" id="new_shift" style="width: 100%">
												<option style="color:grey;" value="">Select Shift</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
											</select>
										</div>
									</div>
								</div>

								
								<div class="form-group row" align="right">
									<label class="col-sm-3">Larutan<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left">
										<select class="form-control select2" data-placeholder="Select Larutan" id="new_solution_id" style="width: 100%">
											<option style="color:grey;" value="">Select Larutan</option>
											@foreach($new_materials as $material)
											<option value="{{ $material->id }}"><strong>{{ $material->solution_name }}</strong> - {{ $material->section }} - {{ $material->location }}</option>
											@endforeach
											
										</select>
									</div>	
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Note</label>
									<div class="col-sm-7" align="left">
										
										<textarea class="form-control" placeholder="Type your note ..." id="new_note"></textarea>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="addNew()"> Submit</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="addition_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Penambahan Chemical</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-2 control-label">Tanggal<span class="text-red">*</span></label>
									<div class="col-sm-3">
										<div class="input-group date">
											<div class="input-group-addon bg-purple" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="addition_date" placeholder="Select Date" >
										</div>
									</div>
									<div class="col-sm-3">								
										<div class="input-group date">
											<div class="input-group-addon bg-purple" style="border: none;">
												<i class="fa fa-clock-o"></i>
											</div>
											<select class="form-control select2" data-placeholder="Select Shift" id="addition_shift" style="width: 100%">
												<option style="color:grey;" value="">Select Shift</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
											</select>
										</div>
									</div>
								</div>
								
								<div class="form-group row" align="right">
									<label class="col-sm-2">Larutan<span class="text-red">*</span></label>
									<div class="col-sm-8" align="left">
										<select class="form-control select3" data-placeholder="Select Larutan" id="addition_id" style="width: 100%">
											<option style="color:grey;" value="">Select Larutan</option>
											@foreach($addition_materials as $material)
											<option value="{{ $material->solution_id }}">{{ $material->solution_name }} - {{ $material->section }} - {{ $material->location }}</option>
											@endforeach
										</select>
									</div>	
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-2">Chemical<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select3" data-placeholder="Select Chemical" name="composer" id="composer" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<div class="col-sm-3 col-sm-offset-2" align="left">
										<input class="form-control" type="number" id="addition_qty" placeholder="Input Qty">
									</div>
									<div class="col-sm-1" align="left" style="padding-left: 0px; text-align: center;">
										<input class="form-control" type="text" id="addition_bun" readonly>
									</div>
									<div class="col-sm-4" align="left" style="padding-left: 0px;">
										<button class="btn btn-primary" onclick="add()">
											&nbsp;&nbsp;&nbsp; Tambahkan &nbsp;&nbsp;&nbsp;
										</button>
									</div>
								</div>

								<div class="col-xs-12">
									<span style="font-weight: bold; font-size: 1vw;">Material<span class="text-red">*</span></span>
									<table class="table table-hover table-bordered table-striped" id="tableAdd">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 10%;">Material</th>
												<th style="width: 40%;">Material Desc.</th>
												<th style="width: 20%;">Storage Location</th>
												<th style="width: 10%;">Qty</th>
												<th style="width: 10%;">Bun</th>
												<th style="width: 10%;">#</th>
											</tr>
										</thead>
										<tbody id="tableAddBody">
										</tbody>
									</table>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="addition()"> Submit</button>
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

	$("#addition_id").change(function(){

		var solution_id = $(this).val(); 
		var data = {
			solution_id : solution_id
		}

		if(solution_id != ''){
			$("#loading").show();
			$.ajax({
				type: "GET",
				dataType: "html",
				url: "{{ url("fetch/get_addition_chm") }}",
				data: data,
				success: function(message){
					$("#composer").html(message);                                                   
					$("#loading").hide();                                                        
				}
			});
		}                  
	});

	$("#addition_modal").on("hidden.bs.modal", function () {
		$('#addition_date').val('');
		$("#addition_shift").prop('selectedIndex', 0).change();
		$("#addition_id").prop('selectedIndex', 0).change();
		$("#composer").prop('selectedIndex', 0).change();
		$('#addition_qty').val('');
		$('#addition_bun').val('');
		$('#tableAddBody').html('');

		composer = [];
	});

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#addition_modal')
		});
	})

	$("#new_modal").on("hidden.bs.modal", function () {
		$('#new_date').val('');
		$("#new_shift").prop('selectedIndex', 0).change();
		$("#new_solution_id").prop('selectedIndex', 0).change();
	});

	$("#composer").change(function(){
		var material = $('#composer').val();
		var material = material.split('(ime)');
		$('#addition_bun').val(material[3]);

	});



	var composer = [];

	function add(){
		if($('#composer').val() != "" && $('#addition_qty').val() != ""){

			var material = $('#composer').val();
			var quantity = $('#addition_qty').val();
			var material = material.split('(ime)');

			
			tableData = "";

			tableData += "<tr id='rowAdd"+material[0]+"'>";
			tableData += '<td>'+material[0]+'</td>';
			tableData += '<td>'+material[1]+'</td>';
			tableData += '<td>'+material[2]+'</td>';
			tableData += '<td>'+quantity+'</td>';
			tableData += '<td>'+material[3]+'</td>';
			tableData += "<td><a href='javascript:void(0)' onclick='remAdd(id)' id='"+material[0]+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableData += '</tr>';


			composer.push([material[0],quantity]);

			$('#tableAddBody').append(tableData);
			$("#composer").prop('selectedIndex', 0).change();
			$('#addition_qty').val('');
			$('#addition_bun').val('');

			console.log(composer);			

		}
		else{
			openErrorGritter('Error!', 'Pilih chemical & Input Qty terlebih dahulu');
		}
	}

	function remAdd(id) {
		$('#rowAdd'+id).remove();

		console.log(id);	


		for (var i = 0; i < composer.length; i++) {
			if(composer[i][0] == id){
				composer.splice(i, 1);
			}
		}

		console.log(composer);	

	}

	function addition() {

		var date = $('#addition_date').val();
		var shift = $('#addition_shift').val();
		var solution_id = $('#addition_id').val();

		if(date == '' || shift == ''){
			openErrorGritter('Error', 'Semua field harus di isi');
			return false;
		}

		if(composer.length < 1){
			openErrorGritter('Error', 'Chemical belum dipilih');
			return false;
		}

		var data = {
			date : date,
			shift : shift,
			solution_id : solution_id,
			composer : composer
		}

		$("#loading").show();

		$.post('{{ url("index/chm_input_addition") }}', data, function(result, status, xhr){
			if(result.status){
				$('#table-material').DataTable().ajax.reload();
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

				$('#addition_modal').modal('hide');

				$('#addition_date').val('');
				$("#addition_shift").prop('selectedIndex', 0).change();
				$("#addition_id").prop('selectedIndex', 0).change();
				$("#composer").prop('selectedIndex', 0).change();
				$('#addition_qty').val('');
				$('#addition_bun').val('');
				$('#tableAddBody').html('');

				composer = [];
			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});
	}

	function addNew() {
		var date = $('#new_date').val();
		var shift = $('#new_shift').val();
		var solution_id = $('#new_solution_id').val();
		var note = $('#new_note').val();

		if(date == '' || shift == '' || solution_id == ''){
			openErrorGritter('Error', 'Semua field harus di isi');
			return false;
		}

		var data = {
			date : date,
			shift : shift,
			solution_id : solution_id,
			note : note
		}

		$("#loading").show();

		$.post('{{ url("index/chm_input_new") }}', data, function(result, status, xhr){
			if(result.status){
				$('#table-material').DataTable().ajax.reload();
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

				$('#new_modal').modal('hide');

				$('#new_date').val('');
				$("#new_shift").prop('selectedIndex', 0).change();
				$("#new_solution_id").prop('selectedIndex', 0).change();

			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}

	function fetchTable() {
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var section = $('#section').val();
		var status = $('#status').val();
		var larutan = $('#larutan').val();
		var material = $('#material').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			section:section,
			status:status,
			larutan:larutan,
			material:material
		}

		$('#table-material').DataTable().destroy();
		
		var table_material = $('#table-material').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 25, 50, 100, -1 ],
			[ '25 rows', '50 rows', '100 rows', 'Show all' ]
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
				"url" : "{{ url("fetch/chm_picking_schedule") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "schedule_date"},
			{ "data": "schedule_shift"},
			{ "data": "category"},
			{ "data": "solution_name"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "width": "50%", "data": "material_description"},
			{ "data": "storage_location"},
			{ "data": "quantity"},
			{ "data": "bun"},
			{ "data": "picked_name"},
			{ "data": "picked_time"},
			{ "data": "changed_name"},
			{ "data": "changed_time"},
			{ "data": "change"},
			{ "data": "delete"}
			]
		});
		
		
	}

	function deleteSchedule(id) {
		$("#loading").show();

		var data = {
			id : id
		}

		if(confirm("Apakah anda yakin ingin menghapus schedule ini ?\nData tidak dapat dikembalikan.")){
			$.post('{{ url("delete/chm_schedule") }}', data, function(result, status, xhr){
				if(result.status){
					$('#table-material').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
		
	}

	function change(id) {
		$("#loading").show();

		var data = {
			id : id
		}

		if(confirm("Apakah anda yakin schedule ini sudah dilakukan penggantian chemical ?\nData yang sudah disimpan tidak dapat dikembalikan.")){
			$.post('{{ url("change/chm_schedule") }}', data, function(result, status, xhr){
				if(result.status){
					$('#table-material').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
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

