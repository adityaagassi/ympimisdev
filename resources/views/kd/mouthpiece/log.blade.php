@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
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
					<h3 class="box-title">Detail Filters <span class="text-purple">フィルター詳細</span></span></h3>
				</div>
				<div class="box-body">
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="col-md-6">
									<div class="form-group">
										<label>Prod. Date From</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="prodFrom" name="prodFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Prod. Date To</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="prodTo" name="prodTo">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Ship. Date From</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="shipFrom" name="shipFrom">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Ship. Date To</label>
										<div class="input-group date">
											<input type="text" placeholder="mm/dd/yyyy" class="form-control pull-right" id="shipTo" name="shipTo">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="form-group">
									<label>Nomor Checksheet</label>
									<input type="text" class="form-control" name="kd_number" id="kd_number" placeholder="Masukkan Nomor Checksheet">
								</div>
								<div class="form-group">
									<label>Material Number</label>
									<select class="form-control select2" multiple="multiple" name="material_number" id="material_number" data-placeholder="Pilih Material Number" style="width: 100%;">
										<option></option>
										@foreach($materials as $material)
										<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="form-group">
									<label>Operator Packing</label>
									<select class="form-control select2" multiple="multiple" name="employee_id" id="employee_id" data-placeholder="Pilih Operator" style="width: 100%;">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} ({{ $employee->name }})</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label>Destination</label>
									<select class="form-control select2" multiple="multiple" name="destination_code" id="destination_code" data-placeholder="Pilih Destinasi" style="width: 100%;">
										<option></option>
										@foreach($destinations as $destination)
										<option value="{{ $destination->destination_shortname }}">{{ $destination->destination_shortname }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button  type="submit" onClick="fetchTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
						</div>
					</div>
					<div class="col-md-12">
						<table id="logTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Date</th>
									<th style="width: 1%;">ID</th>
									<th style="width: 1%;">Material</th>
									<th style="width: 5%;">Description</th>
									<th style="width: 0.7%;">Quantity</th>
									<th style="width: 1%;">Stuffing</th>
									<th style="width: 1%;">Destinasi</th>
									<th style="width: 4%;">PIC</th>
									<th style="width: 0.7%;">Packing (Menit)</th>
									<th style="width: 1%;">Picking</th>
								</tr>
							</thead>
							<tbody id="logTableBody">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<span style="font-weight: bold; font-size: 1.3vw;">KD Number : <span id="modal_kd_number"></span></span><br>
					<span style="font-weight: bold; font-size: 1.3vw;">Stuffing Date : <span id="modal_st_date"></span></span><br>
					<span style="font-weight: bold; font-size: 1.3vw;">Destinasi : <span id="modal_destinasi"></span></span><br><br>

					<span style="font-weight: bold; font-size: 1vw;">Packing List :</span>
					<table class="table table-hover table-bordered table-striped" id="checksheetTable">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 0.5%;">#</th>
								<th style="width: 0.5%;">Material</th>
								<th style="width: 5%;">Description</th>
								<th style="width: 0.5%;">Quantity</th>
								<th style="width: 2%;">Jam Packing</th>
								<th style="width: 4%;">PIC</th>
							</tr>
						</thead>
						<tbody id="checksheetTableBody">
						</tbody>
					</table>

					<span style="font-weight: bold; font-size: 1vw;">Picking List :</span>
					<table class="table table-hover table-bordered table-striped" id="detailTable">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 0.5%;">#</th>
								<th style="width: 0.5%;">Material</th>
								<th style="width: 5%;">Description</th>
								<th style="width: 0.5%;">Quantity</th>
								<th style="width: 2%;">Jam Picking</th>
								<th style="width: 4%;">PIC</th>
							</tr>
						</thead>
						<tbody id="detailTableBody">
						</tbody>
					</table>
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
		$('#prodFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#prodTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#shipFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#shipTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
	});

	var checksheet_detail;
	var checksheet;

	function clearConfirmation(){
		location.reload(true);		
	}

	function fetchTable(){
		$('#loading').show();
		var prodFrom = $('#prodFrom').val();
		var prodTo = $('#prodTo').val();
		var shipFrom = $('#shipFrom').val();
		var shipTo = $('#shipTo').val();
		var kd_number = $('#kd_number').val();
		var material_number = $('#material_number').val();
		var employee_id = $('#employee_id').val();
		var destination_shortname = $('#destination_shortname').val();
		var data = {
			prodFrom:prodFrom,
			prodTo:prodTo,
			shipFrom:shipFrom,
			shipTo:shipTo,
			kd_number:kd_number,
			material_number:material_number,
			employee_id:employee_id,
			destination_shortname:destination_shortname
		}
		$.get('{{ url("fetch/kd_mouthpiece/log") }}', data, function(result, status, xhr){
			if(result.status){
				checksheet_detail = result.checksheet_details;
				checksheet = result.checksheets;

				var logTable = "";
				$('#logTable').DataTable().clear();
				$('#logTable').DataTable().destroy();
				$('#logTableBody').html('');

				$.each(result.checksheets, function(key, value){
					logTable += '<tr>';
					logTable += '<td style="width: 1%;">'+value.created_at+'</td>';
					logTable += '<td style="width: 1%;">'+value.kd_number+'</td>';
					logTable += '<td style="width: 1%;">'+value.material_number+'</td>';
					logTable += '<td style="width: 5%;">'+value.material_description+'</td>';
					logTable += '<td style="width: 0.5%;">'+value.quantity+'</td>';
					logTable += '<td style="width: 1%;">'+value.st_date+'</td>';
					logTable += '<td style="width: 1%;">'+value.destination_shortname+'</td>';
					logTable += '<td style="width: 4%;">'+value.employee_id+'<br>'+value.name+'</td>';
					logTable += '<td style="width: 0.5%;">'+value.packing+'</td>';
					logTable += '<td style="width: 0.5%;"><button onclick="detailLog(id)" id="'+value.kd_number+'" class="btn btn-info">Detail</button></td>';
					logTable += '</tr>';
				});

				$('#logTableBody').append(logTable);

				$('#logTable').DataTable({
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

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});

	}

	function detailLog(id){
		$('#loading').show();

		var tableDetail = "";
		var tableChecksheet = "";
		$('#detailTableBody').html('');
		$('#checksheetTableBody').html('');

		$.each(checksheet, function(key, value){
			if(value.kd_number == id){
				$('#modal_kd_number').text(value.kd_number);
				$('#modal_st_date').text(value.st_date);
				$('#modal_destinasi').text(value.destination_shortname);

				tableChecksheet += '<tr>';
				tableChecksheet += '<td>'+(key+1)+'</td>';
				tableChecksheet += '<td>'+value.material_number+'</td>';
				tableChecksheet += '<td>'+value.material_description+'</td>';
				tableChecksheet += '<td>'+value.quantity+'</td>';
				tableChecksheet += '<td>'+value.start_packing+'</td>';
				tableChecksheet += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
				tableChecksheet += '</tr>';
			}
		});
		$('#checksheetTableBody').append(tableChecksheet);

		$.each(checksheet_detail, function(key, value){
			if(value.kd_number == id){
				tableDetail += '<tr>';
				tableDetail += '<td>'+(key+1)+'</td>';
				tableDetail += '<td>'+value.material_number+'</td>';
				tableDetail += '<td>'+value.material_description+'</td>';
				tableDetail += '<td>'+value.quantity+'</td>';
				tableDetail += '<td>'+value.end_picking+'</td>';
				tableDetail += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
				tableDetail += '</tr>';
			}
		});
		$('#detailTableBody').append(tableDetail);
		$('#loading').hide();
		$('#modalDetail').modal('show');
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