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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
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
					<table id="jigTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 2%">Jig ID</th>
								<th style="width: 2%">Quantity</th>
								<th style="width: 2%">Min. Stock</th>
								<th style="width: 3%">Min. Order</th>
								<th style="width: 3%">Material</th>
								<th style="width: 3%">Qty Order</th>
								<th style="width: 3%">WJO</th>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Tambah Jig Part</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />

									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig ID<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select name="jig_id" id="jig_id" class="form-control select2" style="width: 100%" data-placeholder="Select Jig ID">
												<option value=""></option>
												@foreach($jig_part as $jig_part)
													<option value="{{$jig_part->jig_id}}">{{$jig_part->jig_id}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Quantity<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="quantity" placeholder="Qty" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Min. Stock<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="min_stock" placeholder="Min. Stock" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Min. Order<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="min_order" placeholder="Min. Order" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Material<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="material" placeholder="Material" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addJigPart()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Simpan</button>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Jig Part</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									<input type="hidden" id="id_jig_part">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig ID<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<select name="jig_id_edit" id="jig_id_edit" class="form-control select3" style="width: 100%" data-placeholder="Select Jig ID">
												<option value=""></option>
												@foreach($jig_part2 as $jig_part)
													<option value="{{$jig_part->jig_id}}">{{$jig_part->jig_id}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Quantity<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="quantity_edit" placeholder="Quantity" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Min. Stock<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="min_stock_edit" placeholder="Min. Stock" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Min. Order<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="number" class="form-control" id="min_order_edit" placeholder="Min. Order" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Material<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="material_edit" placeholder="Material" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="updateJigBom()"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
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

		$('.select2').select2({
			dropdownParent: $('#create_modal')
		});

		$('.select3').select2({
			dropdownParent: $('#edit_modal')
		});
		emptyAll();
	});

	function emptyAll() {
		$('#jig_id').val('').trigger('change');
		$('#quantity').val('');
		$('#min_stock').val('');
		$('#min_order').val('');
		$('#material').val('');
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
		$.get('{{ url("fetch/welding/jig_part") }}', function(result, status, xhr){
			if(result.status){
				$('#jigTable').DataTable().clear();
				$('#jigTable').DataTable().destroy();
				$('#bodyJigTable').empty();
				var jigtable = '';

				var index = 1;

				$.each(result.jig_part, function(key, value) {
					jigtable += '<tr>';
					jigtable += '<td>'+index+'</td>';
					jigtable += '<td>'+value.jig_id+'</td>';
					jigtable += '<td>'+value.quantity+'</td>';
					jigtable += '<td>'+value.min_stock+'</td>';
					jigtable += '<td>'+value.min_order+'</td>';
					jigtable += '<td>'+value.material+'</td>';
					if (value.quantity_order != null) {
						jigtable += '<td>'+value.quantity_order+'</td>';
						jigtable += '<td>'+value.remark+'</td>';
					}else{
						jigtable += '<td></td>';
						jigtable += '<td></td>';
					}
					jigtable += '<td><button class="btn btn-warning btn-sm" onclick="editJigPart(\''+value.id+'\')" style="margin-right: 5px"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button><button data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm" onclick="deleteJigPart(\''+value.id+'\',\''+value.jig_id+'\')" style="margin-right: 5px"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</button></td>';
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

	function addJigPart() {
		if ($('#jig_id').val() == "" || $('#quantity').val() == "" || $('#min_stock').val() == "" || $('#min_order').val() == "" || $('#material').val() == "") {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
			$('#loading').show();
			var jig_id = $('#jig_id').val();
			var quantity = $('#quantity').val();
			var min_stock = $('#min_stock').val();
			var min_order = $('#min_order').val();
			var material = $('#material').val();
			var data = {
				jig_id:jig_id,
				quantity:quantity,
				min_stock:min_stock,
				min_order:min_order,
				material:material,
			}

			$.post('{{ url("input/welding/jig_part") }}', data,function(result, status, xhr){
				if(result.status){
					$('#create_modal').modal('hide');
					$('#loading').hide();
					openSuccessGritter('Success',result.message);
					emptyAll();
					getData();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function editJigPart(id) {
		var data = {
			id:id
		}
		$.get('{{ url("edit/welding/jig_part") }}', data,function(result, status, xhr){
			if(result.status){
				// $.each(result.jig_part, function(key, value) {
					$('#jig_id_edit').val(result.jig_part.jig_id).trigger('change');
					$('#quantity_edit').val(result.jig_part.quantity);
					$('#min_stock_edit').val(result.jig_part.min_stock);
					$('#min_order_edit').val(result.jig_part.min_order);
					$('#material_edit').val(result.jig_part.material);
					$('#id_jig_part').val(result.jig_part.id);
				// });

				$('#edit_modal').modal('show');
			}
		});
	}

	function updateJigBom() {
		$('#loading').show();

		var jig_id = $('#jig_id_edit').val();
		var quantity = $('#quantity_edit').val();
		var id_jig_part = $('#id_jig_part').val();
		var min_stock = $('#min_stock_edit').val();
		var min_order = $('#min_order_edit').val();
		var material = $('#material_edit').val();
		
		var data = {
			jig_id:jig_id,
			quantity:quantity,
			id_jig_part:id_jig_part,
			min_stock:min_stock,
			min_order:min_order,
			material:material,
		}

		$.post('{{ url("update/welding/jig_part") }}', data,function(result, status, xhr){
			if(result.status){
				$('#edit_modal').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success',result.message);
				emptyAll();
				getData();
			}else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});
	}

	function deleteJigPart(id,jig_id) {
		var url = "{{ url('delete/welding/jig_part') }}";
		jQuery('.modal-body').text("Are you sure want to delete '" + jig_id + "'?");
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