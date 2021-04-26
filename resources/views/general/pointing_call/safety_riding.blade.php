@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}
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
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 20%;" onclick="openModalCreate();"><i class="fa fa-plus"></i> Create Safety Riding</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<table class="table table-hover table-bordered table-striped" id="tableList">
		<thead style="background-color: rgba(126,86,134,.7);">
			<tr>
				<th style="width: 1%;">Periode</th>
				<th style="width: 1%;">Location</th>
				<th style="width: 4%;">Department</th>
				<th style="width: 4%;">Created By</th>
				<th style="width: 4%;">Created At</th>
				<th style="width: 1%;">Action</th>
			</tr>
		</thead>
		<tbody id="tableListBody">
		</tbody>
	</table>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: green; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Create Safety Riding</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<input type="hidden" id="createLocation">
					<div class="col-xs-12">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Department<span class="text-red"> :</span></label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="createDepartment" placeholder="Select Date" disabled>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 10px; padding-bottom: 10px;">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Select Periode<span class="text-red"> :</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="createPeriod" placeholder="Select Date" onchange="fetchMember(value)">
							</div>
						</div>
					</div>
					<table class="table table-hover table-bordered table-striped" id="tableCreate">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">ID</th>
								<th style="width: 4%;">Name</th>
								<th style="width: 6%;">Safety Riding</th>
							</tr>
						</thead>
						<tbody id="tableCreateBody">
						</tbody>
					</table>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button onclick="inputSafety()" class="btn btn-success">Confirm</button>
				</div>
			</div>
		</div>
	</div>
</div>

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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

		$('#createPeriod').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		fetchSafety();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var count = 1;

	function fetchSafety(){
		$.get('{{ url("fetch/general/safety_riding") }}', function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableListBody').html("");
				var tableList = "";

				$.each(result.safety_ridings, function(key, value){
					var param = value.period+'_'+value.location+'_'+value.department;
					tableList += '<tr>';
					tableList += '<td>'+value.vperiod+'</td>';
					tableList += '<td>'+value.location+'</td>';
					tableList += '<td>'+value.department+'</td>';
					tableList += '<td>'+value.username+' - '+value.name+'</td>';
					tableList += '<td>'+value.vcreated+'</td>';
					tableList += '<td><a style="color: white;" class="btn btn-info" href="{{ url('fetch/general/safety_riding_pdf') }}/'+param+'" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>';
					tableList += '</tr>';
				});

				$('#tableListBody').append(tableList);

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

			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;	
			}
		});
	}

	function fetchMember(period){
		count = 1;
		var data = {
			period:period
		}
		$.get('{{ url("fetch/general/safety_riding_member") }}', data, function(result, status, xhr){
			if(result.status){
				$('#createLocation').val(result.employees[0].remark);
				$('#createDepartment').val(result.employees[0].department);
				$('#tableCreateBody').html("");
				var tableCreate = "";

				$.each(result.employees, function(key, value){
					tableCreate += '<tr>';
					tableCreate += '<td>'+count+'</td>';
					tableCreate += '<td><input type="text" class="form-control" id="employee_'+count+'" value="'+value.employee_id+'" disabled></td>';
					tableCreate += '<td><input type="text" class="form-control" id="name_'+count+'" value="'+value.name+'" disabled></td>';
					if(value.safety_riding == null){
						tableCreate += '<td><input type="text" class="form-control" id="safety_'+count+'" value=""></td>';
					}
					else{
						tableCreate += '<td><input type="text" class="form-control" id="safety_'+count+'" value="'+value.safety_riding+'"></td>';
					}
					tableCreate += '</tr>';
					count += 1;
				});

				$('#tableCreateBody').append(tableCreate);
			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;				
			}
		});
	}

	function openModalCreate(){
		$('#tableCreateBody').html("");
		$('#createPeriod').val("");
		$('#modalCreate').modal('show');
	}

	function inputSafety(){
		if($('#createPeriod').val() == ""){
			openErrorGritter('Error!', 'Pilih periode terlebih dahulu');
			return false;
		}

		// for (var i = 1; i < count; i++) {
		// 	if($('#safety_'+i+'').val() == ""){
		// 		openErrorGritter('Error!', 'Pastikan semua safety riding karyawan terisi');
		// 		return false;
		// 	}
		// }

		var safety_ridings = [];
		var department = $('#createDepartment').val();
		var period = $('#createPeriod').val();
		var location = $('#createLocation').val();


		for (var i = 1; i < count; i++) {
			safety_ridings.push($('#employee_'+i+'').val()+'_'+$('#name_'+i+'').val()+'_'+$('#safety_'+i+'').val());
		}

		var data = {
			safety_ridings:safety_ridings,
			department:department,
			period:period,
			location:location
		}

		$.post('{{ url("create/general/safety_riding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				$('#tableCreateBody').html("");
				$('#createPeriod').val("");
				$('#modalCreate').modal('hide');
				audio_ok.play();
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				return false;	
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
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection