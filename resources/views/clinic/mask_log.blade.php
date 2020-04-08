@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	
	input {
		line-height: 22px;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { 
		display: none;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	.urgent{
		background-color: red;
	}

	.blink {
		-webkit-animation: notif 1s infinite; /* Safari 4+ */
		-moz-animation:    notif 1s infinite; /* Fx 5+ */
		-o-animation:      notif 1s infinite; /* Opera 12+ */
		animation:         notif 1s infinite; /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes notif {
		0%, 49% {
			/*background-color: #fff;*/
			border: 3px solid #e50000;
		}
		50%, 100% {
			/*background-color: #e50000;*/
			border: 3px solid #fff;
			/*border: 3px solid rgb(117,209,63);*/
		}

	</style>
	@stop
	@section('header')
	<section class="content-header">
		<h1>
			{{ $title }}
			<small><span class="text-purple"> {{ $title_jp }}</span></small>
		</h1>
	</section>
	@stop
	@section('content')
	<input type="hidden" id="green">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary">
					<div class="box-body">
						<div class="col-md-6 col-xs-offset-3">
							<div class="box-body">
								<div class="col-md-6">
									<div class="form-group">
										<label>Kunjungan Mulai</label>
										<div class="input-group date" style="width: 100%;">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="visitFrom" id="visitFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Kunjungan Sampai</label>
										<div class="input-group date" style="width: 100%;">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="visitTo" id="visitTo">
										</div>
									</div>
								</div>
								<div class="col-md-12 pull-right">
									<div class="form-group pull-right">
										<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
										<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-12" style="overflow-x: auto;">
							<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 10%;">Visited At</th>
										<th style="width: 10%;">Employee ID</th>
										<th style="width: 20%;">Name</th>
										<th style="width: 15%;">Paramedic</th>
										<th style="width: 20%;">Purpose</th>
										<th style="width: 5%;">Quantity</th>
									</tr>
								</thead>
								<tbody id="tableBodyList">
								</tbody>
							</table>
						</div>
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

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('#visitFrom').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true
			});
			$('#visitTo').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true
			});

		});


		function fillTable() {
			var visitFrom = $('#visitFrom').val();
			var visitTo = $('#visitTo').val();

			if(visitFrom == "" || visitTo == ""){
				alert("Masukkan tanggal mulai dan sampai");
				return false;
			}

			var data = {
				visitFrom:visitFrom,
				visitTo:visitTo
			}

			$.get('{{ url("fetch/mask_visit_log") }}', data, function(result, status, xhr){
				if(result.status){

					$('#tableList').DataTable().clear();
					$('#tableList').DataTable().destroy();
					$('#tableBodyList').html("");

					var tableData = "";

					for (var i = 0; i < result.logs.length; i++) {

						tableData += '<tr>';
						tableData += '<td>'+ result.logs[i].visited_at +'</td>';
						tableData += '<td>'+ result.logs[i].employee_id +'</td>';
						tableData += '<td>'+ (result.logs[i].name || 'Not Found') +'</td>';
						tableData += '<td>'+ result.logs[i].paramedic +'</td>';
						tableData += '<td>'+ result.logs[i].purpose +'</td>';
						tableData += '<td>'+ result.logs[i].quantity +'</td>';
						tableData += '</tr>';


					}

					$('#tableBodyList').append(tableData);

					var table = $('#tableList').DataTable({
						'dom': 'Bfrtip',	
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]],
						'buttons':{
							buttons:
							[{
								extend: 'pageLength',
								className: 'btn btn-default',
							},{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},{
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
						'ordering': false,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true,
					});
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