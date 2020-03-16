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
		{{ $title }}
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Kanban
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
		<div class="col-xs-12">
			<table id="tableKanban" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="15%">Material Number</th>
						<th>Material Description</th>
						<th>Jenis</th>
						<th>WS</th>
						<th width="10%">Qty</th>
						<th width="10%">Standard Time</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableKanban">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Kanban</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Materials<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" value="ZQ00001" id="material_number">
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Jenis<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Jenis" name="jenis" id="jenis" style="width: 100%">
											<option value=""></option>
											<option value="0">Alto</option>
											<option value="1">Tenor</option>
											<option value="2">A82Z</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">WS<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select WS" name="ws" id="ws" style="width: 100%">
											<option value=""></option>
											@foreach($list_ws as $list_ws)
											<option value="{{ $list_ws->ws_id }}">{{ $list_ws->ws_name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Qty<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Qty" name="qty" id="qty" style="width: 100%">
											<option value=""></option>
											<option value="15">15</option>
											<option value="8">8</option>
											<option value="10">10</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Standard Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="std_time" class="form-control" id="std_time" placeholder="Standard Time" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addKanban()"><i class="fa fa-plus"></i> Add Kanban</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: orange; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Kanban</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="kanban_id" id="kanban_id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Materials<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input class="form-control" type="text" id="editmaterial_number" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Desc<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input class="form-control" type="text" id="editdesc" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Qty<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input class="form-control" type="text" id="editqty" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">WS<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select3" data-placeholder="Select WS" name="editws" id="editws" style="width: 100%">
											<option value=""></option>
											@foreach($list_ws2 as $list_ws2)
											<option value="{{ $list_ws2->ws_id }}">{{ $list_ws2->ws_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Standard Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="editstd_time" class="form-control" id="editstd_time" placeholder="Standard Time" required>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
					<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;&nbsp;&nbsp;&nbsp;Standard time per PC (dalam detik)&nbsp;&nbsp;&nbsp;</span><br>
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
		var data = {
			loc : "{{$loc}}"
		};
		$.get('{{ url("fetch/welding/kanban") }}', data,function(result, status, xhr){
			if(result.status){
				$('#tableKanban').DataTable().clear();
				$('#tableKanban').DataTable().destroy();
				$('#bodyTableKanban').html("");
				var tableData = "";
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.gmcdesc +'</td>';
					if (value.jenis == 0) {
						var jenis = 'Alto';
					}else if(value.jenis  == 1){
						var jenis = 'Tenor';
					}else if(value.jenis == 2){
						var jenis = 'A82Z';
					}
					tableData += '<td>'+ jenis +'</td>';
					tableData += '<td>'+ value.ws_name +'</td>';
					tableData += '<td>'+ value.qty +'</td>';
					tableData += '<td>'+ value.std_time +' Detik</td>';
					tableData += '<td>';
					tableData += '<a style="padding: 3%; padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" href="{{ url("index/welding/detail_kanban/") }}/{{ $loc }}/'+value.id+'" class="btn btn-primary">Details</a>';
					tableData += '<a style="padding: 3%; padding-top: 2%; padding-bottom: 2%; margin-right: 2%; margin-left: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-warning" onclick="showEdit(\''+value.id+'\');">Edit</a>';
					tableData += '<a style="padding: 3%; padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.gmc+'\',\''+value.id+'\');">Delete</a>';
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#bodyTableKanban').append(tableData);


				$('#tableKanban tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				var table = $('#tableKanban').DataTable({
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
						}]
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

				$('#tableKanban tfoot tr').appendTo('#tableKanban thead');
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function deleteConfirmation(name,id) {
		var url	= '{{ url("index/welding/destroy_kanban") }}';
		var loc = '{{ $loc }}'
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+loc+'/'+id);
	}

	function showEdit(id) {
		var data = {
			id:id,
			loc : "{{$loc}}"
		}

		$.get('{{ url("fetch/welding/show_edit_kanban") }}',data, function(result, status, xhr){
			if(result.status){
				$("#editmaterial_number").val(result.lists[0].gmc);
				$("#editdesc").val(result.lists[0].gmcdesc);
				$("#editqty").val(result.lists[0].qty);
				$("#editws").val(result.lists[0].id_ws).trigger('change.select2');
				$("#editstd_time").val(result.lists[0].std_time);	


				$('#edit-modal').modal('show');
			}
		});
	}

	function update() {
		var gmc = $('#editmaterial_number').val();
		var ws = $('#editws').val();
		var std = $('#editstd_time').val();
		
		var data = {
			gmc:gmc,
			ws:ws,
			std:std,
			loc : "{{$loc}}"
		}

		$.post('{{ url("post/welding/edit_kanban") }}',data, function(result, status, xhr){
			if(result.status){

				$('#edit-modal').modal('hide');
				window.location.reload();
				openSuccessGritter('Success','Update Operator Success');
			}
			else{
				audio_error.play();
				openErrorGritter('Error','Update Failed');
			}
		});

	}

	function addKanban() {
		var material_number = $('#material_number').val();
		var jenis = $('#jenis').val();
		var ws = $('#ws').val();
		var qty = $('#qty').val();
		var std_time = $('#std_time').val();
		var loc = '{{$loc}}';

		if (material_number != "" && jenis != "" && ws != "" && qty != "" && std_time != "") {
			var data = {
				material_number:material_number,
				jenis:jenis,
				ws:ws,
				qty:qty,
				std_time:std_time,
				loc:loc
			}

			console.log(data);
			
			$.post('{{ url("post/welding/add_kanban") }}', data, function(result, status, xhr){
				if(result.status){
					$("#material_number").val("");
					$("#jenis").val("");
					$("#ws").val("");
					$("#qty").val("");
					$("#std_time").val("");

					$('#material_number').prop('selectedIndex',0);
					$('#jenis').prop('selectedIndex',0);
					$('#ws').prop('selectedIndex',0);
					$('#qty').prop('selectedIndex',0);

					$("#create_modal").modal('hide');

					window.location.reload();
					openSuccessGritter('Success','Insert Kanban Success');
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