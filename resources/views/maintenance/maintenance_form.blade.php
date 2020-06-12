@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}s
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" onclick="get_data('all')">All ({{ $requested+$verifying+$received+$inProgress+$finished+$noPart+$canceled }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('0')">Requested ({{ $requested }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('1')">Verifying ({{ $verifying }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('2')">Received ({{ $received }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('3')">InProgress ({{ $inProgress }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('5')">Finished ({{ $finished }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('4')">No Part ({{ $noPart }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('6')">Canceled ({{ $canceled }})</a>
		</li>
		<li>
			<?php 
			if (strpos(strtolower($employee->position), 'operator') !== false || strpos(strtolower($employee->position), 'sub') !== false) {
				// echo $employee->position;
			} else {
				echo '<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Buat SPK Baru</a>';
			}
			?>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<table id="masterTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 5%">Tanggal Pengajuan</th>
						<th style="width: 5%">SPK</th>
						<th style="width: 5%">Prioritas</th>
						<th style="width: 10%">Jenis Pekerjaan</th>
						<th>Uraian</th>
						<th style="width: 5%">Target</th>
						<th style="width: 5%">Status</th>
						<th style="width: 8%">Action</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Pembuatan Form SPK</h1>
					</div>
				</div>
				<div class="modal-body">
					<form method="POST" id="createForm" autocomplete="off">
						<div class="row">
							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Tanggal:</span>
								</div>
								<div class="col-xs-4">
									<div class="input-group date">
										<div class="input-group-addon bg-default">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" readonly>
									</div>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Bagian:</span>
								</div>
								<div class="col-xs-5">
									<input type="text" class="form-control" id="bagian" name="bagian" value="{{$employee->department.'_'.$employee->section}}" readonly>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="prioritas" name="prioritas" data-placeholder="Pilih Prioritas Pengerjaan">
										<option></option>
										<option>Urgent</option>
										<option>Normal</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select3" id="jenis_pekerjaan" name="jenis_pekerjaan" data-placeholder="Pilih Jenis Pengerjaan" required>
										<option></option>
										<option>Perbaikan</option>
										<option>Pemasangan</option>
										<option>Pelepasan</option>
										<option>Penggantian</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Kategori:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="kategori" name="kategori" data-placeholder="Pilih Kategori Pekerjaan" required>
										<option></option>
										<optgroup label="Utilitas">
											<option>Listrik</option>
											<option>Jaringan</option>
											<option>Mesin Utilitas</option>
											<option>Utilitas Umum</option>
										</optgroup>
										<optgroup label="Mesin Produksi">
											<option>Kelistrikan Mesin</option>
											<option>Mekanis Mesin</option>
										</optgroup>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Kondisi Mesin:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="kondisi_mesin" name="kondisi_mesin" data-placeholder="Pilih Kondisi Mesin" required>
										<option></option>
										<option>Berhenti</option>
										<option>Berjalan</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Sumber bahaya yang harus diperhatikan:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-6">
									<select class="form-control select3" id="bahaya" name="bahaya[]" data-placeholder="Pilih Bahaya yang Mungkin Terjadi" multiple="multiple" required>
										<option></option>
										<option>Bahan Kimia Beracun</option>
										<option>Listrik</option>
										<option>Terjepit</option>
										<option>Putaran Mesin</option>
									</select>
								</div>
							</div>
							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Penjelasan Pekerjaan:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-7">
									<textarea class="form-control" id="detail" name="detail" placeholder="Uraian Pekerjaan" required></textarea>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;" id="target_div">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Target Selesai:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<div class="input-group date">
										<div class="input-group-addon bg-default">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control datepicker" id="target" name="target" placeholder="Pilih Target Selesai">
									</div>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;" id="safety_div">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Catatan Keamanan:<span class="text-red">**</span></span>
								</div>
								<div class="col-xs-7">
									<textarea class="form-control" id="safety" name="safety" placeholder="Catatan Keamanan"></textarea>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-2 pull-right">
								<center><button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Submit</button></center>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-10" style="font-weight: bold !important">
								<span style="color: red !important; background-color: yellow">Note : </span><br>
								<span style="color: red !important; background-color: yellow">*) Wajib diisi</span><br>
								<span style="color: red !important; background-color: yellow">**) Diisi Pemohon, Jika pekerjaan berkaitan dengan chemical diisi oleh Chemical Staff atau kosongkan</span><br>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="detailModal" style="color: black;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Detail SPK</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="spk_detail" readonly>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Nama Pengaju</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="pengaju_detail" readonly>
							</div>
						</div>
					</div>

					<div class="col-xs-6">
						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="tanggal_detail" readonly>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="bagian_detail" readonly>
							</div>
						</div>
					</div>
					<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px"></div>
					<div class="col-xs-6">
						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
							<div class="col-xs-7" align="left">
								<span style="font-size: 13px;" class="label" id="prioritas_detail"></span>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Jenis Pekerjaan</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="workType_detail" readonly>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Kategori</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="kategori_detail" readonly>
							</div>
						</div>
					</div>

					<div class="col-xs-6">
						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Kondisi Mesin</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="mesin_detail" readonly>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Potensi Bahaya</label>
							<div class="col-xs-7" align="left">
								<input type="text" class="form-control" id="bahaya_detail" readonly>
							</div>
						</div>
					</div>

					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label class="col-xs-2" style="margin-top: 1%;">Uraian Permintaan</label>
							<div class="col-xs-10" align="left">
								<textarea class="form-control" id="uraian_detail" readonly></textarea>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-2" style="margin-top: 1%;">Catatan Keamanan</label>
							<div class="col-xs-8" align="left">
								<textarea class="form-control" id="keamanan_detail" rows="1" readonly></textarea>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-2" style="margin-top: 1%;">Tanggal Target</label>
							<div class="col-xs-3" align="left">
								<div class="input-group date">
									<div class="input-group-addon bg-default">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control" id="target_detail" readonly>
								</div>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-2" style="margin-top: 1%;">Status</label>
							<div class="col-xs-3" align="left">
								<input type="text" class="form-control" id="status_detail" readonly>
							</div>
						</div>
					</div>
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

	$(function () {
		$('.select2').select2({ dropdownParent: $('#createModal'), width: '100%' })
	})
	$(function () {
		$('.select3').select2({ dropdownParent: $('#createModal'), width: '100%', tags: true })
	})

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#target_div").hide();
		get_data('all');
	})

	function get_data(param) {
		var data = {
			status:param
		}
		$.get('{{ url("fetch/maintenance/list_spk/user") }}', data, function(result, status, xhr){
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			$('#tableBody').html("");

			var tableData = "";

			$.each(result.datas, function(index, value){
				tableData += '<tr>';
				tableData += '<td>'+ value.date +'</td>';
				tableData += '<td>'+ value.order_no +'</td>';
				if(value.priority == 'Urgent'){
					var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
				}else{
					var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
				}
				tableData += '<td>'+ priority +'</td>';
				tableData += '<td>'+ value.type +'</td>';
				tableData += '<td>'+ value.description +'</td>';
				tableData += '<td>'+ (value.target_date || '-') +'</td>';
				tableData += '<td>'+ value.process_name +'</td>';

				if(value.remark == '0' || value.remark == '2'){
					tableData += '<td>';
					tableData += '<a style="padding: 10%; padding-top: 2%; padding-bottom: 2%; margin-right: 2%;" href="javascript:void(0)" onClick="modalEdit(\''+value.id+'\')" class="btn btn-warning">Edit</a>';
					tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+value.order_no+'\')" class="btn btn-primary">Detail</a>';

					if (value.remark == '2') {
						tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="cancelWjo(\''+value.order_no+'\')" class="btn btn-danger">Cancel</a>';
					}
					tableData += '</td>';
				}else{
					tableData += '<td><a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+value.order_no+'\')" class="btn btn-primary">Detail</a></td>';							
				}

				tableData += '</tr>';	
			})


			$('#tableBody').append(tableData);
			$('#masterTable').DataTable({
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
				'pageLength': 10,
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
		})
	}


	$('#bahaya').on('change', function() {
		var first = $('#bahaya option:eq(1)').text();

		$.each($(this).val(), function(index, value){
			if (value == first) {
				$("#safety_div").hide();
				return false;
				// console.log("dada");
			} else {
				$("#safety_div").show();
			}
		})

		if ($(this).val().length == 0) {
			$("#safety_div").show();
		}
	});

	$('#prioritas').on('change', function() {
		if ($(this).val() == 'Urgent') {
			$("#target_div").show();
		} else {
			$("#target_div").hide();
		}
	});	

	$("form#createForm").submit(function(e){
		$("#create_btn").attr("disabled", true);
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/maintenance/spk") }}',
			type: 'POST',
			data: formData,
			processData: false,
			cache: false,
			contentType: false,
			success: function (result, status, xhr) {
				if(result.status) {
					$('#createModal').modal('hide');
					$("#create_btn").attr("disabled", false);
					openSuccessGritter("Success", result.message);
					$("#createForm")[0].reset();
					$('#prioritas').prop('selectedIndex', 0).change();
					$('#kondisi_mesin').prop('selectedIndex', 0).change();
					$("#kategori").prop('selectedIndex', 0).change();
					$("#jenis_pekerjaan").prop('selectedIndex', 0).change();
					$("#bahaya").prop('selectedIndex', 0).change();

					get_data("all");
				} else {
					$("#create_btn").prop("disabled", false);
					openErrorGritter("Error", result.message);
				}
			},
			function (xhr, ajaxOptions, thrownError) {
				$("#create_btn").prop("disabled", false);
				openErrorGritter(xhr.status, thrownError);
			}
		})
		
	});

	function showDetail(order_no) {
		$("#detailModal").modal("show");

		var data = {
			order_no : order_no
		}

		$.get('{{ url("fetch/maintenance/detail") }}', data,  function(result, status, xhr){
			$("#spk_detail").val(result.detail.order_no);
			$("#pengaju_detail").val(result.detail.name);
			$("#tanggal_detail").val(result.detail.date);
			$("#bagian_detail").val(result.detail.section);

			if (result.detail.priority == "Normal") {
				$("#prioritas_detail").addClass("label-default");
			} else {
				$("#prioritas_detail").addClass("label-danger");
			}
			$("#prioritas_detail").text(result.detail.priority);

			$("#workType_detail").val(result.detail.type);
			$("#kategori_detail").val(result.detail.category);
			$("#mesin_detail").val(result.detail.machine_condition);
			$("#bahaya_detail").val(result.detail.danger);
			$("#uraian_detail").val(result.detail.description);
			$("#keamanan_detail").val(result.detail.safety_note);
			$("#target_detail").val(result.detail.target_date);
			$("#status_detail").val(result.detail.process_name);
		})
	}

	function insert() {
		$("#tanggal").val();
		$("#bagian").val();
		$("#prioritas").val();
		$("#jenis_pekerjaan").val();
		$("#kondisi_mesin").val();
		$("#bahaya").val();
		$("#detail").val();
		$("#target").val();
		$("#safety").val();
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