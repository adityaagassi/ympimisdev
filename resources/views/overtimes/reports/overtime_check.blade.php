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
		padding-top: 0;
		padding-bottom: 0;
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
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Overtime Check Data Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<h4>OT Lebih Dari 3 Jam di Hari Kerja</h4>
							<table id="ot_3Table" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 1%">Name</th>
										<th style="width: 1%">Cost Center</th>
										<th style="width: 1%">Section</th>
										<th style="width: 1%">Plan From</th>
										<th style="width: 1%">Plan To</th>
										<th style="width: 1%">OT (Hour)</th>
									</tr>
								</thead>
								<tbody id="ot_3TableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						<div class="col-md-12">
							<h4>OT Lebih Dari 14 Jam Dalam 1 Minggu Hari Kerja</h4>
							<table id="ot_14Table" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 1%">Name</th>
										<th style="width: 1%">Cost Center</th>
										<th style="width: 1%">Section</th>
										<th style="width: 1%">Week</th>
										<th style="width: 1%">OT (Hour)</th>
									</tr>
								</thead>
								<tbody id="ot_14TableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						<div class="col-md-12">
							<h4>OT Lebih Dari 56 Jam Dalam 1 Bulan Hari Kerja</h4>
							<table id="ot_56Table" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 1%">Name</th>
										<th style="width: 1%">Cost Center</th>
										<th style="width: 1%">Section</th>
										<th style="width: 1%">OT (Hour)</th>
									</tr>
								</thead>
								<tbody id="ot_56TableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						<div class="col-md-12">
							<h4>Karyawan Dengan Status NSO, NSI, ABS</h4>
							<table id="nsonsiabsTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 1%">Name</th>
										<th style="width: 1%">Shift</th>
										<th style="width: 1%">Start</th>
										<th style="width: 1%">End</th>
										<th style="width: 1%">Check In</th>
										<th style="width: 1%">Check Out</th>
										<th style="width: 1%">Attend Code</th>
									</tr>
								</thead>
								<tbody id="nsonsiabsTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
					</div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
	});

	function clearConfirmation(){
		location.reload(true);		
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

	function fetchTable(){
		$('#loading').show();
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to
		}

		$.get('{{ url("fetch/report/overtime_check") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#ot_3Table').DataTable().clear();
				$('#ot_3Table').DataTable().destroy();
				$('#ot_14Table').DataTable().clear();
				$('#ot_14Table').DataTable().destroy();
				$('#ot_56Table').DataTable().clear();
				$('#ot_56Table').DataTable().destroy();
				$('#nsonsiabsTable').DataTable().clear();
				$('#nsonsiabsTable').DataTable().destroy();

				ot_3_data = "";
				$('#ot_3TableBody').html("");
				ot_14_data = "";
				$('#ot_14TableBody').html("");
				ot_56_data = "";
				$('#ot_56TableBody').html("");
				nsonsiabs_data = "";
				$('#nsonsiabsTableBody').html("");

				$.each(result.ot_3, function(key, value){
					ot_3_data += '<tr>';
					ot_3_data += '<td>'+value.emp_no+'</td>';
					ot_3_data += '<td>'+value.Full_name+'</td>';
					ot_3_data += '<td>'+value.cost_center+'</td>';
					ot_3_data += '<td>'+value.section+'</td>';
					ot_3_data += '<td>'+value.ovtplanfrom+'</td>';
					ot_3_data += '<td>'+value.ovtplanto+'</td>';
					ot_3_data += '<td>'+value.ot+'</td>';
					ot_3_data += '</tr>';
				});

				$.each(result.ot_14, function(key, value){
					ot_14_data += '<tr>';
					ot_14_data += '<td>'+value.emp_no+'</td>';
					ot_14_data += '<td>'+value.Full_name+'</td>';
					ot_14_data += '<td>'+value.cost_center+'</td>';
					ot_14_data += '<td>'+value.section+'</td>';
					ot_14_data += '<td>'+value.ot+'</td>';
					ot_14_data += '</tr>';
				});

				$.each(result.nsonsiabs, function(key, value){
					nsonsiabs_data += '<tr>';
					nsonsiabs_data += '<td>'+value.emp_no+'</td>';
					nsonsiabs_data += '<td>'+value.official_name+'</td>';
					nsonsiabs_data += '<td>'+value.shiftdaily_code+'</td>';
					nsonsiabs_data += '<td>'+value.shiftstarttime+'</td>';
					nsonsiabs_data += '<td>'+value.shiftendtime+'</td>';
					nsonsiabs_data += '<td>'+value.starttime+'</td>';
					nsonsiabs_data += '<td>'+value.endtime+'</td>';
					nsonsiabs_data += '<td>'+value.Attend_Code+'</td>';
					nsonsiabs_data += '</tr>';
				});


				$('#ot_3TableBody').append(ot_3_data);
				$('#ot_14TableBody').append(ot_14_data);
				$('#ot_56TableBody').append(ot_56_data);
				$('#nsonsiabsTableBody').append(nsonsiabs_data);

				$('#ot_3Table').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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

				$('#ot_14Table').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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

				$('#ot_56Table').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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

				$('#nsonsiabsTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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
				openErrorGritter('Error', result.message);
			}
		});
}

</script>

@endsection