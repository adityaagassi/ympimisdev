@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Kanagata
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div class="row">
		<div class="col-xs-12 pull-left">
			<!-- <h2 style="margin-top: 0px;">Master Operator Welding</h2> -->
			<table id="TableKanagata" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th>Material Number</th>
						<th>Material Name</th>
						<th>Material Description</th>
						<th>Process</th>
						<th>Product</th>
						<th>Part</th>
						<th>Kanagata Number</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableKanagata">
				</tbody>
				<tfoot>
					<tr style="color: black">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Kanagata</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-4">Material Number<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<!-- <input type="material_number" class="form-control" id="material_number" placeholder="Material Number" value="VCP2930" required> -->
										<select class="form-control select2" data-placeholder="Select Materials" name="material_number" id="material_number" style="width: 100%">
											<option value=""></option>
											@foreach($mpdl as $mpdl)
												<option value="{{ $mpdl->material_number }}">{{ $mpdl->material_number }} - {{ $mpdl->material_description }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material Name<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="material_name" class="form-control" id="material_name" placeholder="Material Name" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Part<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Part" name="part" id="part" style="width: 100%">
											<option value=""></option>
											<option value="DIE">DIES</option>
											<option value="PUNCH">PUNCH</option>
											<option value="PUNCH FLAT">PUNCH FLAT</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Kanagata Number<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="punch_die_number" class="form-control" id="punch_die_number" placeholder="Kanagata Number" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Product<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Product" name="product" id="product" style="width: 100%">
											<option value=""></option>
											@foreach($product as $product)
												<option value="{{ $product->origin_group_name }}">{{ $product->origin_group_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addKanagata()"><i class="fa fa-plus"></i> Add Kanagata</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Operator</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								
								<div class="form-group row">
									<label class="col-sm-4">Material Number<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<!-- <input type="editmaterial_number" class="form-control" id="editmaterial_number" placeholder="Material Number" required> -->
										<select class="form-control select3" data-placeholder="Select Materials" name="editmaterial_number" id="editmaterial_number" style="width: 100%">
											<option value=""></option>
											@foreach($mpdl2 as $mpdl2)
												<option value="{{ $mpdl2->material_number }}">{{ $mpdl2->material_number }} - {{ $mpdl2->material_description }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material Name<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="editmaterial_name" class="form-control" id="editmaterial_name" placeholder="Material Name" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Part<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select3" data-placeholder="Select Part" name="editpart" id="editpart" style="width: 100%">
											<option value=""></option>
											<option value="DIE">DIES</option>
											<option value="PUNCH">PUNCH</option>
											<option value="PUNCH FLAT">PUNCH FLAT</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Kanagata Number<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="editpunch_die_number" class="form-control" id="editpunch_die_number" placeholder="Kanagata Number" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Product<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select3" data-placeholder="Select Product" name="editproduct" id="editproduct" style="width: 100%">
											<option value=""></option>
											@foreach($product2 as $product2)
												<option value="{{ $product2->origin_group_name }}">{{ $product2->origin_group_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>




</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datetime').datetimepicker({
			format: 'YYYY-MM-DD HH:mm:ss'
		});
	});

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#create_modal')
		});
		$('.select3').select2({
			dropdownParent: $('#edit-modal')
		});
	})

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

	function fillList(){
		$.get('{{ url("fetch/press/master_kanagata") }}', function(result, status, xhr){
			if(result.status){
				$('#TableKanagata').DataTable().clear();
				$('#TableKanagata').DataTable().destroy();
				$('#bodyTableKanagata').html("");
				var tableData = "";
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_name +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.process +'</td>';
					tableData += '<td>'+ value.product +'</td>';
					tableData += '<td>'+ value.part +'</td>';
					tableData += '<td>'+ value.punch_die_number +'</td>';
					tableData += '<td>';
					tableData += '<a style="margin-right: 2%; padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="editKanagata(\''+value.id+'\');">Edit</a>';
					tableData += '<a style="padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" href="" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.material_number+' - '+value.part+' - '+value.punch_die_number+'\',\''+value.id+'\');">Delete</a>';
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#bodyTableKanagata').append(tableData);

				$('#TableKanagata tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#TableKanagata').DataTable({
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
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#TableKanagata tfoot tr').appendTo('#TableKanagata thead');

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function deleteConfirmation(name,id) {
		var url	= '{{ url("index/press/destroy_kanagata") }}';
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id);
	}

	function editKanagata(id) {
		var data = {
			id:id
		}

		$.get('{{ url("fetch/press/get_kanagata") }}',data, function(result, status, xhr){
			if(result.status){
				console.table(result.lists.id);
				// $.each(result.lists, function(key, value) {
					// var hex = '{{ hexdec('+value.operator_code+') }}';
					// $("#editmaterial_number").val(value.operator_nik).trigger('change.select2');
					$("#id").val(result.lists.id);
					$("#editmaterial_number").val(result.lists.material_number).trigger('change.select2');
					$("#editmaterial_name").val(result.lists.material_name);
					$("#editpart").val(result.lists.part).trigger('change.select2');
					$("#editpunch_die_number").val(result.lists.punch_die_number);
					$("#editproduct").val(result.lists.product).trigger('change.select2');
				// });
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function update() {
		var id = $('#id').val();
		var material_number = $('#editmaterial_number').val();
		var material_name = $('#editmaterial_name').val();
		var product = $('#editproduct').val();
		var part = $('#editpart').val();
		var punch_die_number = $('#editpunch_die_number').val();
		var data = {
			id:id,
			material_number:material_number,
			material_name:material_name,
			product:product,
			part:part,
			punch_die_number:punch_die_number
		}

		$.post('{{ url("post/press/update_kanagata") }}',data, function(result, status, xhr){
			if(result.status){
				window.location.reload();
				openSuccessGritter('Success','Update Kanagata Success');
			}
			else{
				audio_error.play();
				openErrorGritter('Error','Update Failed');
			}
		});
	}

	function addKanagata() {
		var material_number = $('#material_number').val();
		var material_name = $('#material_name').val();
		var part = $('#part').val();
		var punch_die_number = $('#punch_die_number').val();
		var product = $('#product').val();

		if (material_number != "" && material_name != "" && part != "" && punch_die_number != "" && product != "") {
			var data = {
				material_number:material_number,
				material_name:material_name,
				part:part,
				punch_die_number:punch_die_number,
				product:product
			}
			
			$.post('{{ url("post/press/add_kanagata") }}', data, function(result, status, xhr){
				if(result.status){

					$("#create_modal").modal('hide');

					window.location.reload();
					openSuccessGritter('Success','Insert Kanagata Success');
				} else {
					audio_error.play();
					openErrorGritter('Error','Insert Failed');
				}
			})
		} else {
			audio_error.play();
			openErrorGritter('Error','Invalid Value');
		}

	}


</script>
@endsection