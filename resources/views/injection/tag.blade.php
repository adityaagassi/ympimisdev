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
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<button class="btn btn-info pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Tambahkan Data
		</button>
	</h1>

	<ol class="breadcrumb">
		<li>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
				</div>
				<div class="box-body" style="padding-top: 0;">
					<table id="tagTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">Tag</th>
								<th style="width: 1%">Material</th>
								<th style="width: 3%">Desc.</th>
								<th style="width: 1%">No. Kanban</th>
								<th style="width: 1%">Kanban Code</th>
								<th style="width: 3%">Operator</th>
								<th style="width: 3%">Part</th>
								<th style="width: 1%">Loc</th>
								<th style="width: 1%">Qty</th>
								<th style="width: 1%">Avail</th>
								<th style="width: 1%">Updated At</th>
								<th style="width: 1%">Action</th>
							</tr>
						</thead>
						<tbody id="bodyTagTable">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Tambah Injection Tag Data</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />

									<div class="form-group row" align="right">
										<label class="col-sm-3">Material<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select class="form-control select2" data-placeholder="Select Material" onchange="changeMaterial(this.value)" name="material_number" id="material_number" style="width: 100%">
												<option value=""></option>
												@foreach($material as $material)
												<option value="{{$material->gmc}}">{{$material->gmc}} - {{$material->part_name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Desc<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="mat_desc" placeholder="Material Desc" readonly required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">No. Kanban<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="no_kanban" placeholder="No. Kanban" readonly required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Kanban Code<span class="text-red">*</span></label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="concat_kanban" placeholder="Kanban Code" readonly required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Tag<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="tag" placeholder="Scan Tag Here" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addTagData()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Simpan</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Injection Tag Data</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									<input type="hidden" id="id_tag">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Material<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select class="form-control select3" data-placeholder="Select Material" onchange="changeMaterial2(this.value)" name="material_number_edit" id="material_number_edit" style="width: 100%">
												<option value=""></option>
												@foreach($material2 as $material2)
												<option value="{{$material2->gmc}}">{{$material2->gmc}} - {{$material2->part_name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Desc<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="mat_desc_edit" placeholder="Material Desc" readonly required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">No. Kanban<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="no_kanban_edit" placeholder="No. Kanban" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Kanban Code<span class="text-red">*</span></label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="concat_kanban_edit" readonly placeholder="Kanban Code" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Tag<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="tag_edit" placeholder="Scan Tag Here" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="updateTagData()"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
				</div>
				<div class="modal-body" id="modal_body_delete">
					Are you sure delete?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();
		$('#tagjig').hide();
		$('#periodcheck').hide();
		$('#jigusage').hide();

		$('#tagjig_edit').hide();
		$('#periodcheck_edit').hide();
		$('#jigusage_edit').hide();

		$('.select2').select2({
			dropdownParent: $('#create_modal')
		});

		$('.select3').select2({
			dropdownParent: $('#edit_modal')
		});
		emptyAll();
	});

	function emptyAll() {
		$('#material_number').val('').trigger('change');
		$('#no_kanban').val('');
		$('#mat_desc').val('');
		$('#concat_kanban').val('');
		$('#tag').val('');
	}

	function changeMaterial(material_number) {
		if ($('#material_number').val() !== "") {
			var data = {
				material_number:material_number
			}
			$.get('{{ url("fetch/injection/material") }}',data, function(result, status, xhr){
				if(result.status){
					var no_kanban = parseInt(result.material.no_kanban)+1;
					if (no_kanban.toString().length < 2) {
						no_kanban = "0"+no_kanban;
					}
					$('#no_kanban').val(no_kanban.toString());
					var concat_kanban = parseInt(result.materialall.concat_kanban.split('RC')[1])+1;
					if (concat_kanban.toString().length == 1) {
						concat_kanban = "000000"+concat_kanban;
					}else if(concat_kanban.toString().length == 2){
						concat_kanban = "00000"+concat_kanban;
					}else if(concat_kanban.toString().length == 3){
						concat_kanban = "0000"+concat_kanban;
					}else if(concat_kanban.toString().length == 4){
						concat_kanban = "000"+concat_kanban;
					}else if(concat_kanban.toString().length == 5){
						concat_kanban = "00"+concat_kanban;
					}else if(concat_kanban.toString().length == 6){
						concat_kanban = "0"+concat_kanban;
					}
					$('#concat_kanban').val("RC"+concat_kanban);
					$('#mat_desc').val(result.material.part_name);
					$('#tag').focus();
				}else{
					openErrorGritter('Error!','Gagal Mendapatkan Data');
				}
			});
		}
	}

	function changeMaterial2(material_number) {
		if ($('#material_number_edit').val() !== "") {
			var data = {
				material_number:material_number
			}
			$.get('{{ url("fetch/injection/material_edit") }}',data, function(result, status, xhr){
				if(result.status){
					$('#mat_desc_edit').val(result.material.part_name);
				}else{
					openErrorGritter('Error!','Gagal Mendapatkan Data');
				}
			});
		}
	}

	function getData() {
		$.get('{{ url("fetch/injection/tag") }}', function(result, status, xhr){
			if(result.status){
				$('#tagTable').DataTable().clear();
				$('#tagTable').DataTable().destroy();
				$('#bodyTagTable').empty();
				var tagtable = '';
				var index = 1;

				$.each(result.tag, function(key, value) {
					tagtable += '<tr>';
					tagtable += '<td>'+index+'</td>';
					tagtable += '<td>'+value.tag+'</td>';
					tagtable += '<td>'+value.material_number+'</td>';
					tagtable += '<td>'+value.material_description+'</td>';
					tagtable += '<td>'+value.no_kanban+'</td>';
					tagtable += '<td>'+value.concat_kanban+'</td>';
					if (value.operator_id != null) {
						tagtable += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
						tagtable += '<td>'+value.partsall+'</td>';
						tagtable += '<td>'+value.location+'</td>';
						tagtable += '<td>'+value.shot+'</td>';
						tagtable += '<td>'+value.availability+'</td>';
					}else{
						tagtable += '<td></td>';
						tagtable += '<td></td>';
						tagtable += '<td></td>';
						tagtable += '<td></td>';
						tagtable += '<td></td>';
					}
					tagtable += '<td>'+value.last_update+'</td>';
					tagtable += '<td><button class="btn btn-warning btn-sm" onclick="editTagData(\''+value.id_tag+'\')" style="margin-right: 5px"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button><button data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm" onclick="deleteTagData(\''+value.id_tag+'\',\''+value.injection_tag+'\',\''+value.material_number+'\',\''+value.material_description+'\',\''+value.no_kanban+'\')" style="margin-right: 5px"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</button>';
					if (value.partsall != null) {
						tagtable += '<button class="btn btn-success btn-sm" onclick="removeTagData(\''+value.id_tag+'\',\''+value.tag+'\',\''+value.material_number+'\',\''+value.cavity+'\')" style="margin-right: 5px"><i class="fa fa-window-close"></i>&nbsp;&nbsp;Kosongkan</button>';
					}
					tagtable += '</td>';
					tagtable += '</tr>';
					index++;
				});

				$('#bodyTagTable').append(tagtable);

				var table = $('#tagTable').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'processing': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}else{
				alert('Retireve Data Failed');
			}
		});
	}

	function addTagData() {
		if ($('#material_number').val() == "" || $('#no_kanban').val() == "" || $('#concat_kanban').val() == "" || $('#mat_desc').val() == "" || $('#tag').val() == "") {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
			$('#loading').show();
			var material_number = $('#material_number').val();
			var no_kanban = $('#no_kanban').val();
			var concat_kanban = $('#concat_kanban').val();
			var tag = $('#tag').val();
			var mat_desc = $('#mat_desc').val();

			var data = {
				material_number:material_number,
				no_kanban:no_kanban,
				concat_kanban:concat_kanban,
				tag:tag,
				mat_desc:mat_desc,
			}
			
			$.post('{{ url("input/injection/tag") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					getData();
					openSuccessGritter('Success','Tambah Data Berhasil');
					emptyAll();
					$('#create_modal').modal('hide');
				}else{
					$('#loading').hide();
					openErrorGritter('Error!','Tambah Data Gagal');
				}
			});
		}
	}

	function editTagData(id) {
		var data = {
			id:id
		}
		$.get('{{ url("edit/injection/tag") }}', data,function(result, status, xhr){
			if(result.status){
					$('#material_number_edit').val(result.tag.material_number).trigger('change');
					$('#no_kanban_edit').val(result.tag.no_kanban);
					$('#concat_kanban_edit').val(result.tag.concat_kanban);
					$('#tag_edit').val(result.tag.tag);
					$('#id_tag').val(result.tag.id);

				$('#edit_modal').modal('show');
			}
		});
	}

	function updateTagData() {
		$('#loading').show();

		var material_number = $('#material_number_edit').val();
		var mat_desc = $('#mat_desc_edit').val();
		var no_kanban = $('#no_kanban_edit').val();
		var tag = $('#tag_edit').val();
		var id_tag = $('#id_tag').val();

		var data = {
			material_number:material_number,
			mat_desc:mat_desc,
			no_kanban:no_kanban,
			tag:tag,
			id_tag:id_tag,
		}
		
		$.post('{{ url("update/injection/tag") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				getData();
				openSuccessGritter('Success','Update Data Berhasil');
				emptyAll();
				$('#edit_modal').modal('hide');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!','Update Data Gagal');
			}
		});
	}

	function removeTagData(id,tag,material_number,cavity) {
		$('#loading').show();

		var data = {
			id:id,
			tag:tag,
			material_number:material_number,
			cavity:cavity,
		}
		
		$.post('{{ url("remove/injection/tag") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				getData();
				openSuccessGritter('Success','Remove Data Berhasil');
				emptyAll();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!','Remove Data Gagal');
			}
		});
	}

	function deleteTagData(id,tag,material_number,mat_desc,no_kanban) {
		var url = "{{ url('delete/injection/tag') }}";
		jQuery('#modal_body_delete').text("Are you sure want to delete '" + material_number + " - " + mat_desc + " - No. Kanban : " + no_kanban + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id);
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