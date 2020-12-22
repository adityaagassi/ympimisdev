@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
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
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">
	<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Buffing Operator <i class="fa fa-angle-double-down"></i></span>
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
			<div class="box box-danger">
				<div class="box-body">

					<div class="row">
						<div class="col-xs-3" style="margin-bottom: 1%;">
							<button href="javascript:void(0)" class="btn btn-success btn-sm" data-toggle="modal"  data-target="#add_modal" style="padding-top: 3%; padding-bottom: 3%; padding-left: 2.1%; padding-right: 2.1%;">
								<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Employee
							</button>
						</div>
					</div>
					<table id="tableList" class="table table-bordered table-striped" style="width: 100%; margin-bottom: 1%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%;">#</th>
								<th style="width: 20%;">Employee ID</th>
								<th style="width: 30%;">Name</th>
								<th style="width: 5%;">Group</th>
								<th style="width: 20%;">Tag RFID</th>
								<th style="width: 20%;">Action</th>
							</tr>
						</thead>
						<tbody id='tableBodyList'>
						</tbody>
					</table>

					<div class="col-md-2 pull-right" id="delete_button">
					</div>

				</div>
			</div>
		</div>
	</div>

	{{-- Modal Update --}}
	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Edit Employee Group
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-xs-4">Employee ID</label>
									<div class="col-xs-5" align="left">
										<input type="text" class="form-control" id="employee_id" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Name</label>
									<div class="col-xs-5" align="left">
										<input type="text" class="form-control" id="name" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Group</label>
									<div class="col-xs-5" align="left">
										<select class="form-control select2" data-placeholder="Select Group" name="group" id="group" style="width: 100%">
											<option value=""></option>
											<option value="A">A</option>
											<option value="B">B</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Tag RFID</label>
									<div class="col-xs-5" align="left">
										<input type="text" class="form-control" id="tag" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">New Tag RFID</label>
									<div class="col-xs-5" align="left">
										<input type="text" class="form-control" id="new_tag">
									</div>
								</div>								

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="edit()"><span><i class="fa fa-save"></i> Save</span></button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Add --}}
	<div class="modal modal-default fade" id="add_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add Employee Group
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-xs-4">Employee ID</label>
									<div class="col-xs-6" align="left">
										<select class="form-control select2" id='add_employee_id' data-placeholder="Select Employee ID" style="width: 100%;">
											<option value="">Select Employee ID</option>
											@foreach($employees as $employee)
											<option value="{{ $employee->employee_id }}-{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Name</label>
									<div class="col-xs-6" align="left">
										<input type="text" class="form-control" id="add_name" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Group</label>
									<div class="col-xs-5" align="left">
										<select class="form-control select2" data-placeholder="Select Group" id="add_group" style="width: 100%">
											<option value=""></option>
											<option value="A">A</option>
											<option value="B">B</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Tag RFID</label>
									<div class="col-xs-5" align="left">
										<input type="text" class="form-control" id="add_tag">
									</div>
								</div>				

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="saveEmp()"><span><i class="fa fa-save"></i> Save</span></button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Delete --}}
	<div class="modal modal-danger fade" id="delete_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Delete Operator 
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">					
						<div class="col-xs-12">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="form-group row" align="right">
								<label class="col-xs-4">Employee ID</label>
								<div class="col-xs-5" align="left">
									<input type="text" class="form-control" id="del_employee_id" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Name</label>
								<div class="col-xs-5" align="left">
									<input type="text" class="form-control" id="del_name" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4">Group</label>
								<div class="col-xs-5" align="left">
									<input type="text" class="form-control" id="del_group" readonly>
								</div>
							</div>	
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-danger" onclick="delete_op()"><span><i class="fa fa-trash"></i> Delete</span></button>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
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
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		fillTable();

		$('.select2').select2();

	});

	function fillTable(){

		$.get('{{ url("fetch/middle/buffing_operator", "bff") }}', function(result, status, xhr) {
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				var count = 0;
				for (var i = 0; i < result.operator.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+ ++count +'</td>';
					tableData += '<td>'+ result.operator[i].employee_id +'</td>';
					tableData += '<td>'+ result.operator[i].name +'</td>';
					tableData += '<td>'+ result.operator[i].group +'</td>';
					tableData += '<td>'+ result.operator[i].tag +'</td>';
					tableData += '<td style="text-align: center;">';
					tableData += '<button style="width: 30%; margin: 1%;" onClick="showEdit(this)" id="'+result.operator[i].employee_id+'+'+ result.operator[i].name +'+'+ result.operator[i].group +'+'+ result.operator[i].tag +'" class="btn btn-xs btn-warning">Edit</button>';
					tableData += '<button style="width: 30%; margin: 1%;" onClick="showDelete(this)" id="'+result.operator[i].employee_id+'+'+ result.operator[i].name +'+'+ result.operator[i].group +'+'+ result.operator[i].tag +'" class="btn btn-xs btn-danger">Delete</button>';
					tableData += '</td>';
					tableData += '</tr>';
				}

				$('#tableBodyList').append(tableData);
				$('#tableList').DataTable({
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

			}

		});

	}

	$("#add_employee_id").change(function(){
		var data = $(this).val(); 
		var employee = data.split('-');

		$('#add_name').val(employee[1]);

	});

	function saveEmp() {
		var data = $("#add_employee_id").val();
		var employee = data.split('-');

		var employee_id = employee[0];
		var group = $("#add_group").val();
		var tag = $("#add_tag").val();

		var data = {
			employee_id : employee_id,
			group : group,
			tag : tag
		}

		$.post('{{ url("insert/middle/buffing_operator") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();

				$("#add_employee_id").prop('selectedIndex', 0).change();
				$("#add_name").val('');
				$("#add_group").prop('selectedIndex', 0).change();
				$("#add_tag").val('');

				$("#add_modal").modal('hide');
				openSuccessGritter('Success', result.message);

			}else{
				openErrorGritter('Error!', result.message);
			}
		});

	}

	$('#edit_modal').on('shown.bs.modal', function () {
		$('#new_tag').focus();
	});

	function showEdit(elem){
		var target = $(elem).attr("id");
		var data = target.split("+");

		var employee_id = data[0];
		var name = data[1];
		var group = data[2];
		var tag = data[3];

		document.getElementById("employee_id").value = employee_id;
		document.getElementById("name").value = name;
		$("#group").val(group).trigger('change.select2');		
		document.getElementById("tag").value = tag;

		$("#new_tag").val('');
		$("#new_tag").focus();

		$("#edit_modal").modal('show');
	}

	function showDelete(elem){
		var target = $(elem).attr("id");
		var data = target.split("+");

		var employee_id = data[0];
		var name = data[1];
		var group = data[2];
		var tag = data[3];

		$("#del_employee_id").val(employee_id);
		$("#del_name").val(name);
		$("#del_group").val(group);

		$("#delete_modal").modal('show');
	}

	function delete_op() {
		var employee_id = $("#del_employee_id").val();

		var data = {
			employee_id : employee_id,
		}

		$.post('{{ url("delete/middle/buffing_operator") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();
				openSuccessGritter('Success', result.message);
				$("#delete_modal").modal('hide');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function edit(){
		var employee_id = $("#employee_id").val();
		var group = $("#group").val();
		var new_tag = $("#new_tag").val();

		if(new_tag == ''){
			openErrorGritter('Error!', 'Scan New RFID Tag');
			return false;
		}

		var data = {
			employee_id : employee_id,
			group : group,
			new_tag : new_tag,
		}

		$.post('{{ url("update/middle/buffing_operator") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();
				openSuccessGritter('Success', result.message);
				$("#edit_modal").modal('hide');
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function isNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
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