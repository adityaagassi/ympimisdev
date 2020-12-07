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
					<!-- <div class="pull-right"> -->
					<!-- </div> -->
				</div>
				<div class="box-body" style="padding-top: 0;">
					<table id="jigTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">Jig ID</th>
								<th style="width: 1%">Index</th>
								<th style="width: 3%">Name</th>
								<th style="width: 3%">Alias</th>
								<th style="width: 1%">Category</th>
								<th style="width: 1%">Drawing</th>
								<th style="width: 1%">Tag</th>
								<th style="width: 3%">Created At</th>
								<th style="width: 3%">Action</th>
							</tr>
						</thead>
						<tbody id="bodyJigTable">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Tambah Jig Data</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-6">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />

									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Parent<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_parent" placeholder="Jig Parent" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig ID<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_id" placeholder="Jig ID" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Index<span class="text-red">*</span></label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="jig_index" placeholder="Jig Index" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Name<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_name" placeholder="Jig Name" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Alias<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_alias" placeholder="Jig Alias (Berdasarkan Drawing)" required>
											Contoh : <b>TS-J-15619-6</b> atau <b>SP-0129</b>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="row">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Category<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select class="form-control select2" data-placeholder="Select Category" name="category" id="category" onchange="changeCategory(this.value)" style="width: 100%">
												<option value=""></option>
												<option value="KENSA">KENSA</option>
												<option value="PART">PART</option>
											</select>
										</div>
									</div>
									<div class="form-group row" align="right" id="tagjig" style="display: none">
										<label class="col-sm-3">Jig Tag<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_tag" placeholder="Tap RFID Jig Here" required>
											<input type="hidden" class="form-control" id="type">
										</div>
									</div>
									<div class="form-group row" align="right" id="periodcheck" style="display: none">
										<label class="col-sm-3">Check Period (Hari)<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="check_period" placeholder="Check Period" required>
										</div>
									</div>
									<div class="form-group row" align="right" id="jigusage" style="display: none">
										<label class="col-sm-3">Usage<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="usage" placeholder="Usage" required value="1">
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Drawing<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="file" class="form-control" id="drawing" placeholder="Drawing" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addJigData()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Simpan</button>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Jig Data</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-6">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									<input type="hidden" id="id_jig">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Parent<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_parent_edit" placeholder="Jig Parent" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig ID<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_id_edit" placeholder="Jig ID" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Index<span class="text-red">*</span></label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="jig_index_edit" placeholder="Jig Index" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Name<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_name_edit" placeholder="Jig Name" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig Alias<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_alias_edit" placeholder="Jig Alias (Berdasarkan Drawing)" required>
											Contoh : <b>TS-J-15619-6</b> atau <b>SP-0129</b>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="row">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Category<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select class="form-control select3" data-placeholder="Select Category" name="category_edit" id="category_edit" onchange="changeCategoryEdit(this.value)" style="width: 100%">
												<option value=""></option>
												<option value="KENSA">KENSA</option>
												<option value="PART">PART</option>
											</select>
										</div>
									</div>
									<div class="form-group row" align="right" id="tagjig_edit" style="display: none">
										<label class="col-sm-3">Jig Tag<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_tag_edit" placeholder="Tap RFID Jig Here" required>
											<input type="hidden" class="form-control" id="type_edit">
										</div>
									</div>
									<div class="form-group row" align="right" id="periodcheck_edit" style="display: none">
										<label class="col-sm-3">Check Period (Hari)<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="check_period_edit" placeholder="Check Period" required>
										</div>
									</div>
									<div class="form-group row" align="right" id="jigusage_edit" style="display: none">
										<label class="col-sm-3">Usage<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="usage_edit" placeholder="Usage" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Drawing<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="file" class="form-control" id="drawing_edit" placeholder="Drawing" required>
											<span id="drawing_now"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="updateJigData()"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
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
				<div class="modal-body">
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
		$('#category').val('').trigger('change');
		$('#jig_parent').val('');
		$('#jig_id').val('');
		$('#usage').val('');
		$('#jig_index').val('');
		$('#jig_alias').val('');
		$('#jig_name').val('');
		$('#jig_tag').val('');
		$('#check_period').val('');
		$('#drawing').val('');

		$('#category_edit').val('').trigger('change');
		$('#jig_parent_edit').val('');
		$('#jig_id_edit').val('');
		$('#usage_edit').val('');
		$('#jig_index_edit').val('');
		$('#jig_alias_edit').val('');
		$('#jig_name_edit').val('');
		$('#jig_tag_edit').val('');
		$('#check_period_edit').val('');
		$('#drawing_edit').val('');
	}

	function changeCategory(value) {
		if (value === 'KENSA') {
			$('#tagjig').show();
			$('#periodcheck').show();
			$('#type').val('JIG');
			$('#jigusage').hide();
		}else{
			$('#tagjig').hide();
			$('#periodcheck').hide();
			$('#jigusage').show();
			$('#type').val('PART');
		}
	}

	function changeCategoryEdit(value) {
		if (value === 'KENSA') {
			$('#tagjig_edit').show();
			$('#periodcheck_edit').show();
			$('#type_edit').val('JIG');
			$('#jigusage_edit').hide();
		}else{
			$('#tagjig_edit').hide();
			$('#periodcheck_edit').hide();
			$('#jigusage_edit').show();
			$('#type_edit').val('PART');
		}
	}

	function getData() {
		$.get('{{ url("fetch/welding/jig_data") }}', function(result, status, xhr){
			if(result.status){
				$('#jigTable').DataTable().clear();
				$('#jigTable').DataTable().destroy();
				$('#bodyJigTable').empty();
				var jigtable = '';
				var index = 1;

				$.each(result.jigs, function(key, value) {
					jigtable += '<tr>';
					jigtable += '<td>'+index+'</td>';
					jigtable += '<td>'+value.jig_id+'</td>';
					jigtable += '<td>'+value.jig_index+'</td>';
					jigtable += '<td>'+value.jig_name+'</td>';
					jigtable += '<td>'+value.jig_alias+'</td>';
					jigtable += '<td>'+value.category+'</td>';
					jigtable += '<td><a target="_blank" class="btn btn-primary btn-sm btn-block" href="{{ url("/jig/drawing/") }}/'+value.jig_parent+'/'+value.file_name+'"><i class="fa fa-file-pdf-o"></i> &nbsp;Drawing</a></td>';
					if (value.jig_tag == null) {
						jigtable += '<td></td>';
					}else{
						jigtable += '<td>'+value.jig_tag+'</td>';
					}
					jigtable += '<td>'+value.created_at+'</td>';
					jigtable += '<td><button class="btn btn-warning btn-sm" onclick="editJigData(\''+value.id_jig+'\')" style="margin-right: 5px"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button><button data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm" onclick="deleteJigData(\''+value.id_jig+'\',\''+value.jig_id+'\',\''+value.jig_name+'\',\''+value.jig_parent+'\')" style="margin-right: 5px"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</button></td>';
					jigtable += '</tr>';
					index++;
				});

				$('#bodyJigTable').append(jigtable);

				var table = $('#jigTable').DataTable({
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

	function addJigData() {
		if ($('#jig_parent').val() == "" || $('#jig_id').val() == "" || $('#jig_index').val() == "" || $('#jig_name').val() == "" || $('#jig_alias').val() == "" || $('#category').val() == "" || $('#drawing').val() == "") {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
			$('#loading').show();
			var jig_parent = $('#jig_parent').val();
			var jig_id = $('#jig_id').val();
			var usage = $('#usage').val();
			var jig_index = $('#jig_index').val();
			var jig_name = $('#jig_name').val();
			var jig_alias = $('#jig_alias').val();
			var category = $('#category').val();
			if (category == 'KENSA') {
				var jig_tag = $('#jig_tag').val();
				var check_period = $('#check_period').val();
			}else{
				var jig_tag = '';
				var check_period = '';
			}
			var type = $('#type').val();
			var fileData  = $('#drawing').prop('files')[0];
			var file=$('#drawing').val().replace(/C:\\fakepath\\/i, '').split(".");

			var formData = new FormData();
			formData.append('fileData', fileData);
			formData.append('jig_parent', jig_parent);
			formData.append('jig_id', jig_id);
			formData.append('usage', usage);
			formData.append('jig_index', jig_index);
			formData.append('jig_name', jig_name);
			formData.append('jig_alias', jig_alias);
			formData.append('category', category);
			formData.append('jig_tag', jig_tag);
			formData.append('check_period', check_period);
			formData.append('type', type);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('input/welding/jig_data') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					$('#loading').hide();
					$('#create_modal').modal('hide');
					emptyAll();
					getData();
					openSuccessGritter('Success!','Success Input Data');
				}
			});
		}
	}

	function editJigData(id) {
		var data = {
			id:id
		}
		$('#drawing_now').html("");
		$.get('{{ url("edit/welding/jig_data") }}', data,function(result, status, xhr){
			if(result.status){
				$.each(result.jigs, function(key, value) {
					$('#jig_parent_edit').val(value.jig_parent);
					$('#jig_id_edit').val(value.jig_id);
					$('#usage_edit').val(value.usage);
					$('#jig_index_edit').val(value.jig_index);
					$('#jig_name_edit').val(value.jig_name);
					$('#jig_alias_edit').val(value.jig_alias);
					$('#category_edit').val(value.category).trigger('change');
					$('#jig_tag_edit').val(value.jig_tag);
					$('#check_period_edit').val(value.check_period);
					$('#drawing_now').html("Drawing : "+value.file_name);
					$('#id_jig').val(value.id_jig);
				});

				$('#edit_modal').modal('show');
			}
		});
	}

	function updateJigData() {
		$('#loading').show();

		var jig_parent = $('#jig_parent_edit').val();
		var jig_id = $('#jig_id_edit').val();
		var id_jig = $('#id_jig').val();
		var usage = $('#usage_edit').val();
		var jig_index = $('#jig_index_edit').val();
		var jig_name = $('#jig_name_edit').val();
		var jig_alias = $('#jig_alias_edit').val();
		var category = $('#category_edit').val();
		if (category == 'KENSA') {
			var jig_tag = $('#jig_tag_edit').val();
			var check_period = $('#check_period_edit').val();
		}else{
			var jig_tag = '';
			var check_period = '';
		}
		var type = $('#type_edit').val();
		var fileData  = $('#drawing_edit').prop('files')[0];
		var file=$('#drawing_edit').val().replace(/C:\\fakepath\\/i, '').split(".");

		var formData = new FormData();
		formData.append('fileData', fileData);
		formData.append('jig_parent', jig_parent);
		formData.append('jig_id', jig_id);
		formData.append('id_jig', id_jig);
		formData.append('usage', usage);
		formData.append('jig_index', jig_index);
		formData.append('jig_name', jig_name);
		formData.append('jig_alias', jig_alias);
		formData.append('category', category);
		formData.append('jig_tag', jig_tag);
		formData.append('check_period', check_period);
		formData.append('type', type);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('update/welding/jig_data') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				$('#loading').hide();
				$('#edit_modal').modal('hide');
				emptyAll();
				getData();
				openSuccessGritter('Success!','Success Delete Data');
			}
		});
	}

	function deleteJigData(id,jig_id,jig_name,jig_parent) {
		var url = "{{ url('delete/welding/jig_data') }}";
		jQuery('.modal-body').text("Are you sure want to delete '" + jig_id + " - " + jig_name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+jig_id+'/'+jig_parent);
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