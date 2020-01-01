@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	#loading, #error { display: none; }

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
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<form method="GET" action="{{ url("export/workshop/list_wjo") }}">
						<div class="col-md-4">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-6">
										<div class="form-group">
											<label>Request Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="reqFrom" id="reqFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Request Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="reqTo" id="reqTo">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="targetFrom" id="targetFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="targetTo" id="targetTo">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Selesai Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="finFrom" id="finFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Selesai Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="finTo" id="finTo">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Order No</label>
													<input type="text" class="form-control" name="orderNo" id="orderNo" placeholder="Masukkan Order No">
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Bagian Pemohon</label>
													<select class="form-control select2" data-placeholder="Pilih Bagian" name="sub_section" id="sub_section" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														@php
														$group = array();
														@endphp
														@foreach($employees as $employee)
														@if(!in_array($employee->section.'-'.$employee->group, $group))
														<option value="{{ $employee->section }}">{{ $employee->section }}-{{ $employee->group }}</option>
														@php
														array_push($group, $employee->section.'-'.$employee->group);
														@endphp
														@endif
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Prioritas</label>
													<select class="form-control select2" data-placeholder="Pilih Prioritas" name="priority" id="priority" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="normal">Normal</option>
														<option value="urgent">Urgent</option>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Jenis Pekerjaan</label>
													<select class="form-control select2" data-placeholder="Pilih Jenis Pekerjaan" name="workType" id="workType" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="pembuatan baru">Pembuatan Baru</option>
														<option value="perbaikan ketidaksesuain">Perbaikan Ketidaksesuain</option>
														<option value="lain-lain">Lain-lain</option>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Material Awal</label>
													<select class="form-control select2" multiple="multiple" name="rawMaterial" id="rawMaterial" data-placeholder="Pilih Material Awal" style="width: 100%;">
														<option></option>
														@foreach($workshop_materials as $workshop_material)
														@if(in_array($workshop_material->remark, ['raw']))
														<option value="{{ $workshop_material->material_description }}">{{ $workshop_material->material_description }}</option>
														@endif
														@endforeach
														<option value="LAINNYA">LAINNYA</option>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Material Number</label>
													<select class="form-control select2" multiple="multiple" name="material" id="material" data-placeholder="Select Material Number" style="width: 100%;">
														<option></option>
														@foreach($workshop_materials as $workshop_material)
														@if(in_array($workshop_material->remark, ['jig','molding','equipment']))
														<option value="{{ $workshop_material->material_number }}">{{ $workshop_material->material_number }} - {{ $workshop_material->material_description }}</option>
														@endif
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Operator</label>
													<select class="form-control select2" data-placeholder="Pilih Operator" name="pic" id="pic" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														@foreach($employees as $employee)
														@if(in_array($employee->group, ['Workshop']))
														<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }}-{{ $employee->name }}</option>
														@endif
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Remark</label>
													<select class="form-control select2" data-placeholder="Pilih Remark" name="remark" id="remark" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														@foreach($statuses as $status)
														<option value="{{ $status->process_code }}">{{ $status->process_name }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Approved By</label>
													<select class="form-control select2" data-placeholder="Pilih Approver" name="approvedBy" id="approvedBy" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="PI1108003">Andik Yayan</option>
														<option value="PI9903004">M. Fadoli</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Excel</button>
								<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<table id="tableList" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">WJO</th>
									<th style="width: 1%;">Masuk</th>
									<th style="width: 1%;">Bag.</th>
									<th style="width: 1%;">Dept.</th>
									<th style="width: 1%;">Approved By</th>
									<th style="width: 1%;">Nama Barang</th>
									<th style="width: 1%;">Material</th>
									<th style="width: 1%;">Qty</th>
									<th style="width: 1%;">PIC</th>
									<th style="width: 1%;">Kesulitan</th>
									<th style="width: 1%;">Prioritas</th>
									<th style="width: 1%;">Target Selesai</th>
									<th style="width: 1%;">Actual Selesai</th>
									<th style="width: 1%;">Progress</th>
									<th style="width: 1%;">Att</th>
									<th style="width: 1%;">Draw</th>
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
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>



<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#reqFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#reqTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#targetFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#targetTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#finFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#finTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
	});

	function exportExcel(){
		var reqFrom = $('#reqFrom').val();
		var reqTo = $('#reqTo').val();
		var targetFrom = $('#targetFrom').val();
		var targetTo = $('#targetTo').val();
		var finFrom = $('#finFrom').val();
		var finTo = $('#finTo').val();
		var orderNo = $('#orderNo').val();
		var sub_section = $('#sub_section').val();
		var workType = $('#workType').val();
		var rawMaterial = $('#rawMaterial').val();
		var material = $('#material').val();
		var pic = $('#pic').val();
		var remark = $('#remark').val(); 
		var approvedBy = $('#approvedBy').val(); 
		var data = {
			reqFrom:reqFrom,
			reqTo:reqTo,
			targetFrom:targetFrom,
			targetTo:targetTo,
			finFrom:finFrom,
			finTo:finTo,
			orderNo:orderNo,
			sub_section:sub_section,
			workType:workType,
			rawMaterial:rawMaterial,
			material:material,
			pic:pic,
			remark:remark,
			approvedBy:approvedBy
		}

		$.get('{{ url("export/workshop/list_wjo") }}', data, function(result, status, xhr){

		});
	}

	function fillTable() {
		var reqFrom = $('#reqFrom').val();
		var reqTo = $('#reqTo').val();
		var targetFrom = $('#targetFrom').val();
		var targetTo = $('#targetTo').val();
		var finFrom = $('#finFrom').val();
		var finTo = $('#finTo').val();
		var orderNo = $('#orderNo').val();
		var sub_section = $('#sub_section').val();
		var workType = $('#workType').val();
		var rawMaterial = $('#rawMaterial').val();
		var material = $('#material').val();
		var pic = $('#pic').val();
		var remark = $('#remark').val(); 
		var approvedBy = $('#approvedBy').val(); 
		var data = {
			reqFrom:reqFrom,
			reqTo:reqTo,
			targetFrom:targetFrom,
			targetTo:targetTo,
			finFrom:finFrom,
			finTo:finTo,
			orderNo:orderNo,
			sub_section:sub_section,
			workType:workType,
			rawMaterial:rawMaterial,
			material:material,
			pic:pic,
			remark:remark,
			approvedBy:approvedBy
		}

		$.get('{{ url("fetch/workshop/list_wjo") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				for (var i = 0; i < result.tableData.length; i++) {
					if(result.tableData[i].priority == 'urgent'){
						tableData += '<tr>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].order_no +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].created_at +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].sub_section +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].sub_section +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].approver +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].item_name +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].material +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].quantity +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].pic +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].difficulty +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].priority +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].target_date +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].finish_date +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].process_name +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].attachment +'</td>';
						tableData += '<td onclick="assignment(\''+result.tableData[i].order_no+'\')">'+ result.tableData[i].drawing_number +'</td>';
						tableData += '</tr>';
					}					
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
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 5,
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
				openErrorGritter('Error!', result.message);
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