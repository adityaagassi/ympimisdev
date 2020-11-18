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
		<!-- <button class="btn btn-info pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Tambahkan Data
		</button> -->
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
								<th style="width: 2%">Jig Name</th>
								<th style="width: 2%">Jig Index</th>
								<th style="width: 2%">Schedule Date</th>
								<th style="width: 2%">Kensa Time</th>
								<th style="width: 2%">Kensa Status</th>
								<th style="width: 2%">PIC Kesna</th>
								<th style="width: 2%">Repair Time</th>
								<th style="width: 2%">Repair Status</th>
								<th style="width: 2%">PIC Repair</th>
								<th style="width: 2%">Schedule Status</th>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Jig Schedule</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									<input type="hidden" id="id_jig_schedule">
									<div class="form-group row" align="right">
										<label class="col-sm-3">Jig ID<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control" id="jig_id" placeholder="Jig Parent" required>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-3">Schedule Date<span class="text-red">*</span></label>
										<div class="col-sm-7" align="left">
											<input type="text" class="form-control datepicker" id="jig_schedule_edit" placeholder="Jig Schedule Date" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="updateJigSchedule()"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
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

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});
	});

	function emptyAll() {
		$('#jig_id').val('');
		$('#jig_schedule_edit').val('');
		$('#id_jig_schedule').val('');
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
		$.get('{{ url("fetch/welding/jig_schedule") }}', function(result, status, xhr){
			if(result.status){
				$('#jigTable').DataTable().clear();
				$('#jigTable').DataTable().destroy();
				$('#bodyJigTable').empty();
				var jigtable = '';

				var index = 1;

				$.each(result.jig_schedule, function(key, value) {
					jigtable += '<tr>';
					jigtable += '<td>'+index+'</td>';
					jigtable += '<td>'+value.jig_id+'</td>';
					jigtable += '<td>'+value.jig_name+'</td>';
					jigtable += '<td>'+value.jig_index+'</td>';
					jigtable += '<td>'+value.schedule_date+'</td>';
					if (value.kensa_time != null) {
						jigtable += '<td>'+value.kensa_time+'</td>';
						jigtable += '<td>'+value.kensa_status+'</td>';
						jigtable += '<td>'+value.kensa_pic+' - '+value.kensaname+'</td>';
					}else{
						jigtable += '<td><span class="label label-danger"></span></td>';
						jigtable += '<td><span class="label label-danger"></span></td>';
						jigtable += '<td><span class="label label-danger"></span></td>';
					}
					if (value.repair_time != null) {
						jigtable += '<td>'+value.repair_time+'</td>';
						jigtable += '<td>'+value.repair_status+'</td>';
						jigtable += '<td>'+value.repair_pic+' - '+value.repairname+'</td>';
					}else{
						jigtable += '<td></td>';
						jigtable += '<td></td>';
						jigtable += '<td></td>';
					}
					if (value.schedule_status == 'Close') {
						jigtable += '<td><span class="label label-success">'+value.schedule_status+'</span></td>';
					}else{
						jigtable += '<td><span class="label label-danger">'+value.schedule_status+'</span></td>';
					}
					jigtable += '<td><button class="btn btn-warning btn-sm" onclick="editJigSchedule(\''+value.id_jig_schedule+'\')" style="margin-right: 5px"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button></td>';
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

	function editJigSchedule(id) {
		var data = {
			id:id
		}
		$.get('{{ url("edit/welding/jig_schedule") }}', data,function(result, status, xhr){
			if(result.status){
				// $.each(result.jig_schedule, function(key, value) {
					$('#jig_id').val(result.jig_schedule.jig_id);
					$('#jig_schedule_edit').val(result.jig_schedule.schedule_date);
					$('#id_jig_schedule').val(result.jig_schedule.id);
				// });

				$('#edit_modal').modal('show');
			}
		});
	}

	function updateJigSchedule() {
		$('#loading').show();

		var schedule_date = $('#jig_schedule_edit').val();
		var id_jig_schedule = $('#id_jig_schedule').val();
		
		var data = {
			id:id_jig_schedule,
			schedule_date:schedule_date,
		}

		$.post('{{ url("update/welding/jig_schedule") }}', data,function(result, status, xhr){
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