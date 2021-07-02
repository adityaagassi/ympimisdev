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
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
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
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.zoom-out {cursor: zoom-out;}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Catalog Item List <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
<!-- 
		<li>
			<a href="javascript:void(0)" onclick="openHistory()" class="btn btn-md bg-green" style="color:white"><i class="fa fa-list"></i> Cek History Pembelian Item</a>
		</li> -->

		<?php if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") { ?>
			<li>
				<!-- <a href="{{ url("index/purchase_item/create_category")}}" class="btn btn-md bg-blue" style="color:white">
					<i class="fa fa-plus"></i> Create New Item Category
				</a> -->
				<a class="btn btn-md bg-purple" onclick="openModalCreate()" style="color:white"><i class="fa fa-plus"></i> Create New Item</a>
			</li>
		<?php } ?>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						
						<div class="col-md-3">
							<div class="form-group">
								<label>Keyword By GMC</label>
								<input type="text" class="form-control" id="keyword2" placeholder="Masukkan Kata Kunci">
							</div>
						</div>

						
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchShowItem()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="row">
								<div class="col-md-12"  >
									<br>
									<div class="col-md-12">
										<table id="history_table" class="table table-bordered table-striped table-hover" style="width: 100%; overflow-y: scroll;">
											<thead style="background-color: rgba(126,86,134,.7);">
												<tr>
													<th style="width: 4%">GMC</th>
													<th style="width: 18%">Description</th>
													<th style="width: 3%">Uom</th>
													<th style="width: 9%">Code Vendor</th>
													<th style="width: 14%">Supplier</th>
													<th style="width: 3%">Size</th>
													<th class="zoom-out" style="width: 10%">Gambar</th>
													<th style="width: 12%">Action</th>
												</tr>
											</thead>
											<tbody id="tableBodyHistory">
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #469c6b">
					<div class="col-xs-12">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white;">Create Catalog Item</h1>
					</div>
				</div>
				
				<div class="modal-body">
					<form method="POST" id="createForm" autocomplete="off" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Gmc<span class="text-red">*</span></label>
									<input type="text" class="form-control" id="gmc" name="gmc" placeholder="Gmc">
								</div>

								<div class="form-group">
									<label id="label_section">Description<span class="text-red">*</span></label>
									<input type="text" class="form-control" id="description" name="description" placeholder="Description">
								</div>

								<div class="form-group">
									<label>UOM<span class="text-red">*</span></label>
									<select class="form-control select2" id="uom" name="uom" data-placeholder="Select UOM" style="width: 100%;">
										<option selected></option>
										<option>BAG</option>
										<option>BT</option>
										<option>BX</option>
										<option>BG</option>
										<option>CAN</option>
										<option>CN</option>
										<option>DZ</option>
										<option>L</option>
										<option>M</option>
										<option>KG</option>
										<option>PR</option>
										<option>PACK</option>
										<option>PAC</option>
										<option>PC</option>
										<option>PR</option>
										<option>SET</option>
										<option>ST</option>
										<option>SHT</option>
										<option>TB</option>
										<option>RL</option>
									</select>

								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label>Supllier<span class="text-red">*</span></label>
									<select class="form-control select2" id="supplier_code" name="supplier_code" data-placeholder='Supplier' style="width: 100%">
										<option value="">&nbsp;</option>
										@foreach($vendor as $ven)
										<option value="{{$ven->vendor_code}}-{{$ven->supplier_name}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
										@endforeach
									</select>



								</div>
								<div class="form-group">
									<label>Size<span class="text-red">*</span></label>
									<input type="number" class="form-control" id="size" name="size" placeholder="Size">
								</div>
								<div class="form-group">
									<label>Foto<span class="text-red">*</span></label>
									<input type="file" id="foto" name="foto">
								</div>
							</div>
						</div>
					</div>
				<div class="modal-footer">
					<button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Submit</button>
				</div>
				</form>
			</div>
		</div>
	</div>

	<input type="hidden" value="{{csrf_token()}}" name="_token" />
	<div class="modal fade" id="modalEdit">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Edit Material Catalog Item</h4>
					<br>
					<div class="nav-tabs-custom tab-danger">
						<ul class="nav nav-tabs">
							<li class="vendor-tab active disabledTab"><a href="#tab_1_edit" data-toggle="tab" id="tab_header_1">Data Catalog Item</a></li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1_edit">
							<form method="POST" id="saveForm" autocomplete="off" enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-6">
											<div class="form-group" hidden="hidden">
												<input type="text" class="form-control pull-right" id="id" name="id" readonly>
											</div>
											<input type="hidden" name="cob" id="cob">
											<div class="form-group">
												<label>Gmc<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="gmc_edit" name="gmc_edit">
											</div>
											<div class="form-group">
												<label>Description<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="description_edit" name="description_edit">
											</div>
											<div class="form-group">
												<label id="label_uom_edit">UOM<span class="text-red">*</span></label>
												<select class="form-control select2" id="uom_edit" name="uom_edit" data-placeholder='Pilih Sub Group' style="width: 100%">
													<option value="">&nbsp;</option>
													@foreach($uom as $row)
													<option value="{{$row->uom}}"> {{$row->uom}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label id="label_supplier_edit">Supplier<span class="text-red">*</span></label>
												<select class="form-control select2" id="supplier_edit" name="supplier_edit" data-placeholder='Pilih Sub Group' style="width: 100%">
													<option value="">&nbsp;</option>
													@foreach($edit_vendor as $ed)
													<option value="{{$ed->code_vendor}}-{{$ed->supplier}}">{{$ed->code_vendor}} - {{$ed->supplier}}</option>
													@endforeach
												</select>
												<input type="hidden" class="form-control" id="supplier_name_edit" name="supplier_name_edit" readonly="">
											</div>
											<div class="form-group">
												<label>Size<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="size_edit" name="size_edit">
											</div>
											<div class="form-group">
												<label for="image">Old Photo</label>
												: <div name="image_edit" id="image_edit"></div>
											</div>
											<div class="form-group">
												<label>New Photo<span class="text-red">*</span></label>
												<input type="file" id="foto_edit" name="foto_edit">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-success" type=submit" id="save_edit"><i class="fa fa-check"></i> Save</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" value="{{csrf_token()}}" name="_token" />
	<div class="modal fade" id="modalImage">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
				<!-- 	<div class="col-md-12" id="image_show">
						<img src="{{ url("/images/pch_katalog/not_found.jpg") }}" width="100">
					</div>
				-->
				<div class="form-group">
					<div  name="image_show" id="image_show"></div>

				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalHistory">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #1da12e; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Cek History Pembelian</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-9">
						<div class="form-group">
							<label>Enter Keyword</label>
							<input type="text" class="form-control" id="keyword" name="keyword" placeholder="Masukkan Kode Item / Deskripsi">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="col-md-12">
								<label style="color: white;"> xxxxxxxxxxxxxxxxxx</label>
								<button id="search" onclick="fetchLog()" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<table class="table table-hover table-bordered table-striped" id="tableLog">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>No</th>
									<th>Vendor</th>
									<th>Nomor PO</th>
									<th>Nama Item</th>
									<th>Harga</th>
									<th>Tanggal PO</th>
								</tr>
							</thead>
							<tbody id="tableLogBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal modal-danger fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<input type="hidden" id="ids">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
			</div>
			<div class="modal-body">
				Apakah anda yakin ingin cancel Catalog Item Ini ?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<a name="modalbuttoncancel" type="button"  onclick="DeleteForm()" class="btn btn-danger">Yes</a>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		$('.select2').select2();
		// fetchTable();
		$('body').toggleClass("sidebar-collapse");

	});


	var ids;

	fetchCatalogItem();
	$('#keyword2').val("");

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$('#keyword2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fetchShowItem();
		}
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function openHistory(){
		$('#modalHistory').modal('show');
	}

	$('#keyword').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fetchLog();
		}
	});

	function cancelPO(id) {
		$('[name=modalbuttoncancel]').attr("id",id);
	}

	function openModalCreate(){
		$('#modalCreate').modal('show');
	}

	$("form#createForm").submit(function(e){
		if ($("#gmc").val() == "") {
			alert("Gmc Tidak Boleh Kosong");
			$("html").scrollTop(0);
			return false;
		}
		if ($("#description").val() == "") {
			alert("Description Item Tidak Boleh Kosong");
			$("html").scrollTop(0);
			return false;
		}
		if ($("#uom").val() == "") {
			alert("Uom Item Tidak Boleh Kosong");
			$("html").scrollTop(0);
			return false;
		}
		if ($("#supplier_code").val() == "") {
			alert("Supllier Item Tidak Boleh Kosong");
			$("html").scrollTop(0);
			return false;
		}
		if ($("#size").val() == "") {
			alert("Size Item Tidak Boleh Kosong");
			$("html").scrollTop(0);
			return false;
		}

		$("#create_btn").attr("disabled", true);
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/catalog/item") }}',
			type: 'POST',
			enctype: 'multipart/form-data',
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
			success: function (result, status, xhr) {
				if(result.status) {
					$('#modalCreate').modal('hide');
					$("#create_btn").attr("disabled", false);
					openSuccessGritter("Success", 'Create Catalog item Success');
					$("#loading").hide();
					$("#createForm")[0].reset();
					$('#supllier').prop('selectedIndex', 0).change();
					fetchCatalogItem();

				} else {
					$("#create_btn").prop("disabled", false);
					$("#loading").hide();
					openErrorGritter("Error", "Not save");
				}
			},
			function (xhr, ajaxOptions, thrownError) {
				$("#create_btn").prop("disabled", false);
				openErrorGritter(xhr.status, thrownError);
			}
		})
	});

	function fetchCatalogItem(){
		$('#loading').show();
		var keyword = $('#keyword').val();

		$.get('{{ url("fetch/detail/catalog/item") }}', function(result, status, xhr){
			if(result.status){
				$('#history_table').DataTable().clear();
				$('#history_table').DataTable().destroy();
				$('#tableBodyHistory').html('');
				var tableLogBody = "";
				var no = 1;
				var images = "";
				$.each(result.catalogs, function(key, value){
					tableLogBody += '<tr>';
					tableLogBody += '<td>'+value.gmc+'</td>';
					tableLogBody += '<td>'+value.desc+'</td>';
					tableLogBody += '<td>'+value.uom+'</td>';
					tableLogBody += '<td>'+value.code_vendor+'</td>';
					tableLogBody += '<td>'+value.supplier+'</td>';
					if (value.size == null) {
						tableLogBody += '<td>-</td>';
					}else{
						tableLogBody += '<td>'+value.size+'</td>';
					}
					if (value.foto == null) {
						tableLogBody += '<td>-</td>';
					}else{
						tableLogBody += '<td><img src="{{ url("images/pch_katalog") }}/'+value.foto+'" width="150px"   style="cursor: zoom-out" onclick="showImage(\''+value.id+'\')"></td>';
						
					}	
					
					tableLogBody += '<td><a class="btn btn-warning btn-xs" onclick="editForm(\''+value.id+'\');" style="color:white; width:30%; margin:1%;"><i class="fa fa-edit"></i>Edit</a><a class="btn btn-danger btn-xs" onclick="delShow(\''+value.id+'\');" style="color:white; margin:1%; width:30%;"><i class="fa fa-trash"></i> Delete </a></td>';
					tableLogBody += '</tr>';
					no++;

				});
				$('#tableBodyHistory').append(tableLogBody);

				$('#history_table').DataTable({
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
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}




	function fetchShowItem(){
		var keyword = $('#keyword2').val();
		var data = {
			keyword:keyword
		}

		$.get('{{ url("fetch/search/item") }}', data, function(result, status, xhr){
			if(result.status){
				$('#history_table').DataTable().clear();
				$('#history_table').DataTable().destroy();
				$('#tableBodyHistory').html('');
				var tableLogBody = "";
				var no = 1;
				var images = "";
				$.each(result.check, function(key, value){
					tableLogBody += '<tr>';
					tableLogBody += '<td>'+value.gmc+'</td>';
					tableLogBody += '<td>'+value.desc+'</td>';
					tableLogBody += '<td>'+value.uom+'</td>';
					tableLogBody += '<td>'+value.code_vendor+'</td>';
					tableLogBody += '<td>'+value.supplier+'</td>';
					if (value.size == null) {
						tableLogBody += '<td>-</td>';
					}else{
						tableLogBody += '<td>'+value.size+'</td>';
					}
					if (value.foto == null) {
						tableLogBody += '<td>-</td>';
					}else{
						tableLogBody += '<td><img src="{{ url("images/pch_katalog") }}/'+value.foto+'" width="130px"   style="cursor: zoom-out" onclick="showImage(\''+value.id+'\')"></td>';
						
					}	
					
					tableLogBody += '<td><a class="btn btn-warning btn-xs" onclick="editForm(\''+value.id+'\');" style="color:white"><i class="fa fa-edit"></i>Edit</a><a class="btn btn-danger btn-xs" onclick="delShow(\''+value.id+'\');" style="color:white"><i class="fa fa-trash"></i> Delete </a></td>';
					tableLogBody += '</tr>';
					no++;

				});
				$('#tableBodyHistory').append(tableLogBody);

				$('#history_table').DataTable({
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
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	function showImage(id) {
		$('#modalImage').modal('show');
		var data = {
			id:id
		}

		$.get('{{ url("show/image") }}',data, function(result, status, xhr){

			var images_show = "";
			$("#image_show").html("");

			if(result.status){
				$.each(result.show_img, function(key, value) {

					images_show += '<img style="cursor:zoom-in" src="{{ url("images/pch_katalog") }}/'+result.show_img[0].foto+'" width="100%" >';

					$("#image_show").append(images_show);
				});
			}
			else{
				alert('No Data');
			}
		});


	}

	function editForm(id) {

		$('#modalEdit').modal('show');

		var data = {
			id:id
		}

		$.get('{{ url("fetch/edit/catalog") }}',data, function(result, status, xhr){

			var images = "";
			$("#image_edit").html("");

			if(result.status){
				$.each(result.edit_catalog, function(key, value) {
					$("#cob").val(value.id);
					$("#gmc_edit").val(value.gmc);
					$("#description_edit").val(value.desc);
					$("#supplier_name_edit").val(value.supplier);
					$("#uom_edit").val(value.uom).trigger('change');
					$("#supplier_edit").val(value.code_vendor +'-'+value.supplier).trigger('change');
					if (value.size == null) {
						$test = "-";
					}else{
						$test = value.size;
					}
					$("#size_edit").val($test);
					if (result.edit_catalog[0].foto == null) {
						images += '<img src="{{ url("images/pch_katalog/not_found.jpg") }}" width="100" alt="Image">';

					}else{

						images += '<img src="{{ url("images/pch_katalog") }}/'+result.edit_catalog[0].foto+'" width="100">';
					}

					$("#image_edit").append(images);


				});
			}
			else{
				alert('No Data');
			}
		});
	}


	$("form#saveForm").submit(function(e){
		$("#save_edit").attr("disabled", true);
		// $("#loading").show();

		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/save/catalog") }}',
			type: 'POST',
			enctype: 'multipart/form-data',
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
			success: function (result, status, xhr) {
				if(result.status) {
					$('#modalEdit').modal('hide');
					$("#save_edit").attr("disabled", false);
					openSuccessGritter("Success", 'Create Catalog item Success');
					$("#loading").hide();
					$("#saveForm")[0].reset();
					fetchCatalogItem();
					// $('#supllier').prop('selectedIndex', 0).change();
				} else {
					// $("#create_btn").prop("disabled", false);
					$("#loading").hide();
					openErrorGritter("Error", "Not save");
				}
			},
			function (xhr, ajaxOptions, thrownError) {
				$("#save_edit").prop("disabled", false);
				openErrorGritter(xhr.status, thrownError);
			}
		})

	});

	function delShow(id){
		$("#ids").val(id);
		$('#modaldelete').modal('show');
	}


	function DeleteForm() {

		var test = $('#ids').val();
		var data = {
			id:test
		}

		$.post('{{ url("delete/catalog") }}', data,  function(result, status, xhr){
			if(result.status){
				// fillTable();
				openSuccessGritter('Success', result.message);
				$("#modaldelete").modal('hide');
				fetchCatalogItem();
			}else{
				openErrorGritter('Error!', result.message);
			}
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

