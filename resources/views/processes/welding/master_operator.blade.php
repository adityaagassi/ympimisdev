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
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Operator
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
			<table id="tableOperator" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th>NIK</th>
						<th>Nama Operator</th>
						<th width="10%">Shift</th>
						<th>Created at</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableOperator">
				</tbody>
				<tfoot>
					<tr style="color: black">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Operator</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Operator<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Operator" name="operator" id="operator" style="width: 100%">
											<option value=""></option>
											@foreach($list_op as $list_op)
											<option value="{{ $list_op->employee_id }}">{{ $list_op->employee_id }} - {{ $list_op->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Operator Code<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="operator_code" class="form-control" id="operator_code" placeholder="Tap ID Card Operator" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Shift<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Shift" name="group" id="group" style="width: 100%">
											<option value=""></option>
											<option value="A">Shift 1</option>
											<option value="B">Shift 2</option>
											<option value="C">Shift 3</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addOperator()"><i class="fa fa-plus"></i> Add Operator</button>
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
								<input type="hidden" name="operator_id" id="operator_id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Operator<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select3" data-placeholder="Select Operator" name="editoperator" id="editoperator" style="width: 100%">
											<option value=""></option>
											@foreach($list_op2 as $list_op2)
											<option value="{{ $list_op2->employee_id }}">{{ $list_op2->employee_id }} - {{ $list_op2->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Operator Code<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="editoperator_code" class="form-control" id="editoperator_code" placeholder="Tap ID Card Operator" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Shift<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select3" data-placeholder="Select Shift" name="editgroup" id="editgroup" style="width: 100%">
											<option value=""></option>
											<option value="A">Shift 1</option>
											<option value="B">Shift 2</option>
											<option value="C">Shift 3</option>
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
		$.get('{{ url("fetch/welding/operator") }}', function(result, status, xhr){
			if(result.status){
				$('#tableOperator').DataTable().clear();
				$('#tableOperator').DataTable().destroy();
				$('#bodyTableOperator').html("");
				var tableData = "";
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.operator_nik +'</td>';
					tableData += '<td>'+ value.operator_name +'</td>';
					if (value.group == 'A') {
						tableData += '<td>Shift 1</td>';
					}else if (value.group == 'B') {
						tableData += '<td>Shift 2</td>';
					}else if (value.group == 'C') {
						tableData += '<td>Shift 3</td>';
					}
					tableData += '<td>'+ value.operator_create_date +'</td>';
					tableData += '<td>';
					tableData += '<a style="margin-right: 2%; padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="editOperator(\''+value.operator_id+'\');">Edit</a>';
					tableData += '<a style="padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" href="" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.operator_nik+'\',\''+value.operator_id+'\');">Delete</a>';
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#bodyTableOperator').append(tableData);

				$('#tableOperator tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableOperator').DataTable({
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

				$('#tableOperator tfoot tr').appendTo('#tableOperator thead');

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function deleteConfirmation(name,id) {
		var url	= '{{ url("index/welding/destroy_operator") }}';
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id);
	}

	function editOperator(id) {
		var data = {
			id:id
		}

		$.get('{{ url("fetch/welding/get_operator") }}',data, function(result, status, xhr){
			if(result.status){
				$.each(result.lists, function(key, value) {
					// var hex = '{{ hexdec('+value.operator_code+') }}';
					$("#editoperator").val(value.operator_nik).trigger('change.select2');
					$("#editoperator_code").val(value.operator_code);
					$("#operator_id").val(value.operator_id);
					$("#editgroup").val(value.group).trigger('change.select2');
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function update() {
		var operator_id = $('#operator_id').val();
		var operator = $('#editoperator').val();
		var operator_code = $('#editoperator_code').val();
		var group = $('#editgroup').val();
		var data = {
			operator_id:operator_id,
			operator:operator,
			operator_code:operator_code,
			group:group
		}

		$.post('{{ url("index/welding/update_operator") }}',data, function(result, status, xhr){
			if(result.status){
				window.location.reload();
				openSuccessGritter('Success','Update Operator Success');
			}
			else{
				audio_error.play();
				openErrorGritter('Error',result.message);
			}
		});
	}

	function addOperator() {
		var operator = $('#operator').val();
		var operator_code = $('#operator_code').val();
		var group = $('#group').val();

		if (operator != "" && operator_code != "" && group != "") {
			var data = {
				operator:operator,
				operator_code:operator_code,
				group:group
			}
			
			$.post('{{ url("post/welding/add_operator") }}', data, function(result, status, xhr){
				if(result.status){
					$("#operator").val("");
					$("#operator_code").val("");
					$("#group").val("");

					$('#operator').prop('selectedIndex',0);
					$('#group').prop('selectedIndex',0);

					$("#create_modal").modal('hide');

					window.location.reload();
					openSuccessGritter('Success','Insert Operator Success');
				} else {
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		} else {
			audio_error.play();
			openErrorGritter('Error!',result.message);
		}

	}


</script>
@endsection