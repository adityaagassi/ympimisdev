@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
			<a href="javascript:void(0)" onclick="get_data('all')">All ({{ $requested+$verifying+$received+$listed+$inProgress+$finished+$pending+$canceled }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('0')">Requested ({{ $requested }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('2')">Received ({{ $received }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('3')">Listed ({{ $listed }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('4')">InProgress ({{ $inProgress }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('6')">Finished ({{ $finished }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('5')">Pending ({{ $pending }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" onclick="get_data('7')">Canceled ({{ $canceled }})</a>
		</li>
		<li>
			<?php 
			if (strpos(strtolower($employee->position), 'operator') !== false) {
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
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, Mohon tunggu...<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

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
					<form method="POST" id="createForm" autocomplete="off" enctype="multipart/form-data">
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
									<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select3" id="jenis_pekerjaan" name="jenis_pekerjaan" data-placeholder="Pilih Jenis Pengerjaan" required>
										<option></option>
										<option>Perbaikan</option>
										<option>Pemasangan</option>
										<option>Pelepasan</option>
										<option>Penggantian</option>
										<option>Relayout</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Kategori:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="kategori" name="kategori" data-placeholder="Pilih Kategori Pekerjaan" required onchange="get_machine('kategori')">
										<option></option>
										<optgroup label="Utilitas" value="Utility">
											<option>Listrik</option>
											<option>Jaringan</option>
											<option>Mesin Utilitas</option>
											<option>Utilitas Umum</option>
										</optgroup>
										<optgroup label="Mesin Produksi" value="Production Machine">
											<option>Kelistrikan Mesin</option>
											<option>Mekanis Mesin</option>
											<option>Otomatisasi Mesin</option>
										</optgroup>
										<optgroup label="WWT" value="WWT">
											<option>Suplai Air</option>
											<option>Pembuangan Limbah</option>
										</optgroup>
										<optgroup label="Lain - lain">
											<option>Informasi</option>
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
									<select class="form-control select3" id="bahaya" name="bahaya[]" placeholder="Pilih Bahaya yang Mungkin Terjadi" multiple="multiple" required>
										<option>Bahan Kimia Beracun</option>
										<option>Permukaan Panas</option>
										<option>Bekerja di Ketinggian</option>
										<option>Bekerja di Tempat Terbatas</option>
										<option>Tersengat Listrik</option>
										<option>Terjepit</option>
										<option>Putaran Mesin</option>
									</select>
								</div>
							</div>
							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Mesin:</span>
								</div>
								<div class="col-xs-7 mesin_div">
									<select class="form-control" id="nama_mesin" name="nama_mesin" data-placeholder="pilih mesin (Bila berhubungan mesin)">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%; display: none" id="mesin_detail_div">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Nama Mesin:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-7">
									<input type="text" class="form-control" name="nama_mesin_detail" id="nama_mesin_detail" placeholder="Masukkan Nama Mesin">
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
										<input class="form-control datepicker" id="target" name="target" placeholder="Pilih Target Selesai" required>
									</div>
								</div>

								<div class="col-xs-2">
									<div class="input-group date">
										<div class="input-group-addon bg-default">
											<i class="fa fa-clock-o"></i>
										</div>
										<input class="form-control timepicker" id="jam_target" name="jam_target" placeholder="Pilih Jam Selesai" required>
									</div>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;" id="safety_div">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Catatan Keamanan:</span>
								</div>
								<div class="col-xs-7">
									<textarea class="form-control" id="safety" name="safety" placeholder="Catatan Keamanan"></textarea>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="prioritas" name="prioritas" data-placeholder="Pilih Prioritas Pengerjaan">
										<option>Normal</option>
										<option>Urgent</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%; display: none" id="div_reason">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Reason Urgent:<span class="text-red">*</span></span>
								</div>

								<div class="col-xs-5">
									<textarea class="form-control" placeholder="Isikan Catatan Urgent" name="reason_urgent" id="reason_urgent" rows="1"></textarea>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-4" style="padding: 0px;" align="right">
									<span style="font-weight: bold; font-size: 16px;">Lampiran:</span>
								</div>
								<div class="col-xs-4">
									<input type="file" name="lampiran" id="lampiran">
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
								<span style="color: red !important; background-color: yellow; font-weight: bold;">Prioritas "Urgent" untuk mesin yang menyebabkan aliran produksi berhenti, tidak terdapat mesin cadangan</span>
							</div>
						</div>
					</form>
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
								<label class="col-xs-4" style="margin-top: 1%;">Reason Urgent</label>
								<div class="col-xs-7" align="left">
									<textarea class="form-control" id="reason_urgent_detail" rows="1" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Jenis Pekerjaan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="workType_detail" readonly>
								</div>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Kategori</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="kategori_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Kondisi Mesin</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="kondisi_mesin_detail" readonly>
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
								<label class="col-xs-2" style="margin-top: 1%;">Nama Mesin</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="mesin_detail" readonly>
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

						<div class="col-xs-12" id="keterangan_detail" style="display: none">
							<hr>
							<b>Maintenance Operator :</b>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Nama Operator</th>
										<th>Start</th>
										<th>Finish</th>
										<th>Status</th>
										<th>Keterangan</th>
									</tr>
								</thead>
								<tbody id="body_desc">
								</tbody>
							</table>
						</div>

						<div class="col-xs-12" id="report_detail" style="display: none">
							<b>Report :</b>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Penyebab</label>
								<div class="col-xs-8" align="left">
									<textarea class="form-control" id="penyebab_detail" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Penanganan</label>
								<div class="col-xs-8" align="left">
									<textarea class="form-control" id="penanganan_detail" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Sparepart</label>
								<div class="col-xs-8" align="left">
									<table class="table">
										<thead>
											<tr>
												<th>Item</th>
												<th>Qty</th>
											</tr>
										</thead>
										<tbody id="part_detail">
										</tbody>
									</table>
								</div>
							</div>

							<div class="row">
								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Foto</label>
									<div class="col-xs-8" align="left" id="img_detail">
										
									</div>
								</div>
								<!-- <div class="col-xs-12" id="img_detail">
									<img src="#">
								</div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Form SPK</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<form method="POST" id="editForm" autocomplete="off">
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="spk_edit" name="spk_edit" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nama Pengaju</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="pengaju_edit" readonly>
									</div>
								</div>
							</div>

							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="tanggal_edit" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="bagian_edit" readonly>
									</div>
								</div>
							</div>
							<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px"></div>
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Prioritas<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<select class="form-control select4" id="prioritas_edit" name="prioritas_edit" data-placeholder="Pilih Prioritas Pengerjaan">
											<option></option>
											<option>Urgent</option>
											<option>Normal</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right" id="div_reason_edit">
									<label class="col-xs-4" style="margin-top: 1%;">Reason Urgent<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<textarea class="form-control" placeholder="Isikan Catatan Urgent" name="reason_urgent_edit" id="reason_urgent_edit" rows="1"></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Jenis Pekerjaan<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<select class="form-control select4" id="workType_edit" name="workType_edit" data-placeholder="Pilih Jenis Pengerjaan" required>
											<option></option>
											<option>Perbaikan</option>
											<option>Pemasangan</option>
											<option>Pelepasan</option>
											<option>Penggantian</option>
											<option>Relayout</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Kategori<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<select class="form-control select4" id="kategori_edit" name="kategori_edit" data-placeholder="Pilih Kategori Pekerjaan" required onchange="get_machine('kategori_edit')">
											<option></option>
											<optgroup label="Utilitas" value="Utility">
												<option>Listrik</option>
												<option>Jaringan</option>
												<option>Mesin Utilitas</option>
												<option>Utilitas Umum</option>
											</optgroup>
											<optgroup label="Mesin Produksi" value="Production Machine">
												<option>Kelistrikan Mesin</option>
												<option>Mekanis Mesin</option>
												<option>Otomatisasi Mesin</option>
											</optgroup>
											<optgroup label="WWT" value="WWT">
												<option>Suplai Air</option>
												<option>Pembuangan Limbah</option>
											</optgroup>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Kondisi Mesin<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<select class="form-control select4" id="kondisi_mesin_edit" name="kondisi_mesin_edit" data-placeholder="Pilih Kondisi Mesin" required>
											<option></option>
											<option>Berhenti</option>
											<option>Berjalan</option>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Potensi Bahaya<span class="text-red">*</span></label>
									<div class="col-xs-7" align="left">
										<select class="form-control" id="bahaya_edit" name="bahaya_edit[]" data-placeholder="Pilih Bahaya yang Mungkin Terjadi" multiple="multiple" required>
											<option>Bahan Kimia Beracun</option>
											<option>Permukaan Panas</option>
											<option>Bekerja di Ketinggian</option>
											<option>Bekerja di Tempat Terbatas</option>
											<option>Tersengat Listrik</option>
											<option>Terjepit</option>
											<option>Putaran Mesin</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Mesin</label>
									<div class="col-xs-10 mesin_div2" align="left">
										<select class="form-control" id="mesin_edit" name="mesin_edit" data-placeholder="pilih mesin (Bila berhubungan mesin)">
										</select>
									</div>
								</div>

								<div class="form-group row" align="right" style="display: none" id="mesin_edit_detail_div">
									<label class="col-xs-2" style="margin-top: 1%;">Nama Mesin<span class="text-red">*</span></label>
									<div class="col-xs-10" align="left">
										<input type="text" class="form-control" name="mesin_edit_detail" id="mesin_edit_detail" placeholder="Masukkan Nama Mesin">
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Uraian Permintaan<span class="text-red">*</span></label>
									<div class="col-xs-10" align="left">
										<textarea class="form-control" id="uraian_edit" name="uraian_edit" required></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Catatan Keamanan</label>
									<div class="col-xs-8" align="left">
										<textarea class="form-control" id="keamanan_edit" name="keamanan_edit" rows="1" ></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Tanggal Target</label>
									<div class="col-xs-3" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-default">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control" id="target_edit" readonly>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-10" style="font-weight: bold !important">
										<span style="color: red !important; background-color: yellow">Note : </span><br>
										<span style="color: red !important; background-color: yellow">*) Wajib diisi</span><br>
										<span style="color: red !important; background-color: yellow; font-weight: bold;">Prioritas "Urgent" untuk mesin yang menyebabkan aliran produksi berhenti, tidak terdapat mesin cadangan</span>
									</div>
								</div>

								<div class=" form-group row">
									<div class="col-xs-2 pull-right">
										<center><button class="btn btn-primary" type="submit" id="edit_btn"><i class="fa fa-check"></i> Edit</button></center>
									</div>
								</div>
							</div>
						</form>
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
		$('.select2').select2({ dropdownParent: $('#createModal'), width: '100%', allowClear: true })
	})
	$(function () {
		$('.select3').select2({ dropdownParent: $('#createModal'), width: '100%', tags: true })
	})
	$(function () {
		$('.select4').select2({ dropdownParent: $('#editModal'), width: '100%' })
	})

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true
	});	

	$('.timepicker').timepicker({
		use24hours: true,
		showInputs: false,
		showMeridian: false,
		minuteStep: 5,
		defaultTime: '',
		timeFormat: 'hh:mm'
	})

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#bahaya_edit').select2({
			dropdownParent: $('#editModal'), 
			width: '100%', 
			tags: true
		});

		get_data('all');
		// get_machine('all');
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
					tableData += '<a style="padding: 10%; padding-top: 2%; padding-bottom: 2%; margin-right: 2%;" href="javascript:void(0)" onClick="modalEdit(\''+value.order_no+'\')" class="btn btn-warning">Edit</a>';
					tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+value.order_no+'\')" class="btn btn-primary">Detail</a>';

					if (value.remark == '2') {
						tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="cancelSPK(\''+value.order_no+'\')" class="btn btn-danger">Cancel</a>';
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

	// $('#modalEdit').on('shown.bs.modal', function () {
	// 	get_machine('all');
	// 	console.log('tes');
	// }) 


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
			$("#div_reason").show();
		} else {
			$("#div_reason").hide();
		}
	});	

	$("form#createForm").submit(function(e){

		if ($("#prioritas").val() == "Urgent" && $("#reason_urgent").val() == "") {
			openErrorGritter('Gagal', 'Harap Melengkapi Catatan Urgent');
			return false;
		}

		if ($("#kategori").val().indexOf("Mesin") >= 0 && $("#nama_mesin").val() == "") {
			openErrorGritter('Gagal', 'Harap Memilih Mesin');
			return false;
		}

		if ($("#nama_mesin option:selected").text() == "Lain - lain" && $("#nama_mesin_detail").val().length <= 0) {
			openErrorGritter('Gagal', 'Harap Mengisi Nama Mesin');
			return false;
		}

		$("#create_btn").attr("disabled", true);
		$("#loading").show();

		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/maintenance/spk") }}',
			type: 'POST',
			enctype: 'multipart/form-data',
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
			success: function (result, status, xhr) {
				if(result.status) {
					$('#createModal').modal('hide');
					$("#create_btn").attr("disabled", false);
					openSuccessGritter("Success", result.message);
					$("#loading").hide();
					$("#createForm")[0].reset();
					$('#prioritas').prop('selectedIndex', 0).change();
					$('#kondisi_mesin').prop('selectedIndex', 0).change();
					$("#kategori").prop('selectedIndex', 0).change();
					$("#jenis_pekerjaan").prop('selectedIndex', 0).change();
					$("#bahaya").prop('selectedIndex', 0).change();
					$("#nama_mesin").prop('selectedIndex', 0).change();

					// get_data("all");
					location.reload();
				} else {
					$("#create_btn").prop("disabled", false);
					$("#loading").hide();
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
			$("#spk_detail").val(result.detail[0].order_no);
			$("#pengaju_detail").val(result.detail[0].name);
			$("#tanggal_detail").val(result.detail[0].date);
			$("#bagian_detail").val(result.detail[0].section);

			if (result.detail[0].priority == "Normal") {
				$("#prioritas_detail").addClass("label-default");
			} else {
				$("#prioritas_detail").addClass("label-danger");
			}
			$("#prioritas_detail").text(result.detail[0].priority);

			$("#workType_detail").val(result.detail[0].type);
			$("#kategori_detail").val(result.detail[0].category);
			$("#kondisi_mesin_detail").val(result.detail[0].machine_condition);

			if (result.detail[0].machine_description) {
				$("#mesin_detail").val(result.detail[0].machine_description+" | "+result.detail[0].machine_name);
			} else {
				$("#mesin_detail").val(result.detail[0].machine_name+" | "+result.detail[0].machine_remark);
			}

			$("#bahaya_detail").val(result.detail[0].danger);
			$("#uraian_detail").val(result.detail[0].description);
			$("#keamanan_detail").val(result.detail[0].safety_note);
			$("#target_detail").val(result.detail[0].target_date);
			$("#status_detail").val(result.detail[0].process_name);

			$("#penyebab_detail").val(result.detail[0].cause);
			$("#penanganan_detail").val(result.detail[0].handling);

			$("#reason_urgent_detail").val(result.detail[0].note);

			if ($("#nama_mesin option:selected").text() == "Lain - lain" && $("#nama_mesin_detail").val().length <= 0) {
				openErrorGritter('Gagal', 'Harap Mengisi Nama Mesin');
				return false;
			}

			var stat = 0;
			$.each(result.detail, function(index, value){
				if (value.process_name == "Pending" || value.process_name == "Finished" || value.process_name == "InProgress") {
					stat = 1;
				}
			})

			var body = "";
			$("#img_detail").empty();

			$.each(result.detail, function(index, value){
				if (stat == 1) {
					$("#keterangan_detail").show();
					body += "<tr>";
					body += "<td>"+value.name_op+"</td>";
					body += "<td>"+(value.start_actual || "")+"</td>";
					body += "<td>"+(value.finish_actual || "")+"</td>";
					body += "<td>"+(value.status || "")+"</td>";
					body += "<td>"+(value.pending_desc || "")+"</td>";
					body += "</tr>";
				} else {
					$("#keterangan_detail").hide();
				}

				if (value.process_name == "Finished") {
					$("#report_detail").show();

					if (value.photo) {
						var photo = value.photo.split(", ");
						var img = "";

						$.each(photo, function(index2, value2){
							img += "<img src='{{ url('maintenance/spk_report') }}/"+value2+"' style='width: 30%'>";
						})

						$("#img_detail").append(img);
					}

				} else {
					$("#report_detail").hide();
				}

			})

			$("#body_desc").empty();
			$("#body_desc").append(body);

			var part = "";
			$("#part_detail").empty();

			if (result.part) {
				$.each(result.part, function(index, value){
					part += "<tr>";
					part += "<td>"+value.part_name+" - "+value.specification+"</td>";
					part += "<td>"+value.quantity+"</td>";
					part += "</tr>";
				})

				$("#part_detail").append(part);
			}

		})
	}

	function get_machine(cat) {
		var mesin_cat = $("#"+cat+" option:selected").parents("optgroup").attr("value");

		// if (mesin_cat != null) {

		// } else {

		// }

		console.log(mesin_cat);
		var options = "";
		var options2 = "";

		var data = {
			kategori : mesin_cat
		}

		$("#nama_mesin").empty();
		$("#mesin_edit").empty();

		$.get('{{ url("fetch/maintenance/list_mc") }}', data,  function(result, status, xhr){
			options += "<option></option>";
			options2 += "<option></option>";
			$.each(result.datas, function(index, value){
				options += "<option value='"+value.machine_id+"'>"+value.description+" --- "+value.area+"</option>";
				options2 += "<option value='"+value.machine_id+"'>"+value.description+" --- "+value.area+"</option>";
			})

			options += "<option value='Lain - lain'>Lain - lain</option>";
			options2 += "<option value='Lain - lain'>Lain - lain</option>";

			$("#nama_mesin").append(options);
			$("#mesin_edit").append(options2);
			
			$('#nama_mesin').select2({
				dropdownParent: $('.mesin_div'),
				width: '100%', 
				allowClear: true,
				// minimumInputLength: 3
			});

			$('#mesin_edit').select2({
				dropdownParent: $('.mesin_div2'),
				width: '100%', 
				allowClear: true,
				// minimumInputLength: 3
			});
		})
	}

	function modalEdit(order_no) {
		$("#editModal").modal("show");

		var data = {
			order_no : order_no
		}

		$.get('{{ url("fetch/maintenance/detail") }}', data,  function(result, status, xhr){
			$("#spk_edit").val(result.detail[0].order_no);
			$("#pengaju_edit").val(result.detail[0].name);
			$("#tanggal_edit").val(result.detail[0].date);
			$("#bagian_edit").val(result.detail[0].section);
			$("#reason_urgent_edit").val(result.detail[0].note);
			$("#kategori_edit").val(result.detail[0].category).trigger("change");

			setTimeout(function() {
				$("#mesin_edit").val(result.detail[0].machine_name).trigger("change");

				if ($("#mesin_edit").val() == 'Lain - lain') {
					$("#mesin_edit_detail").val(result.detail[0].machine_remark);
					$("#mesin_edit_detail_div").show();
				} else {
					$("#mesin_edit_detail").val("");
					$("#mesin_edit_detail_div").hide();
				}
			}, 3000);

			$("#prioritas_edit").val(result.detail[0].priority).trigger("change");
			$("#workType_edit").val(result.detail[0].type).trigger("change");
			$("#kondisi_mesin_edit").val(result.detail[0].machine_condition).trigger("change");

			var danger = result.detail[0].danger.split(', ');
			$("#bahaya_edit").val(danger).trigger("change");

			$("#uraian_edit").val(result.detail[0].description);

			if (result.detail[0].safety_note)
				$("#keamanan_edit").val(result.detail[0].safety_note);
			else
				$("#keamanan_edit").val("");

			$("#target_edit").val(result.detail[0].date);
		})
	}

	$("form#editForm").submit(function(e){
		if ($("#mesin_edit option:selected").text() == "Lain - lain" && $("#mesin_edit_detail").val().length <= 0) {
			openErrorGritter('Gagal', 'Harap Mengisi Nama Mesin');
			return false;
		}

		if ($("#prioritas_edit option:selected").text() == "Urgent" && $("#reason_urgent_edit").val().length <= 0) {
			openErrorGritter('Gagal', 'Harap Mengisi Catatan Urgent');
			return false;
		}

		$("#edit_btn").attr("disabled", true);
		$("#loading").show();
		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/maintenance/spk") }}',
			type: 'POST',
			data: formData,
			processData: false,
			cache: false,
			contentType: false,
			success: function (result, status, xhr) {
				if(result.status) {
					$('#editModal').modal('hide');
					$("#edit_btn").attr("disabled", false);
					$("loading").hide();
					openSuccessGritter("Success", result.message);

					get_data("all");
				} else {
					$("#edit_btn").prop("disabled", false);
					$("#loading").hide();
					openErrorGritter("Error", result.message);
				}
			},
			function (xhr, ajaxOptions, thrownError) {
				$("#edit_btn").prop("disabled", false);
				openErrorGritter(xhr.status, thrownError);
			}
		})
		
	});

	function cancelSPK(order_no) {
		if (confirm("Apakah Anda yakin akan membatalkan SPK dengan nomor '"+order_no+"' ?")) {
			var data = {
				order_no : order_no
			}

			$.post('{{ url("post/maintenance/spk/cancel") }}', data,  function(result, status, xhr){
				if (result.status) {
					openSuccessGritter("Success", "SPK Berhasil dibatalkan");
					get_data("all");
				} else {
					openErrorGritter("Gagal", result.message);
				}
			})
		}
	}

	$('#nama_mesin').on('change', function() {
		var data = $("#nama_mesin option:selected").text();
		if (data == 'Lain - lain') {
			$("#nama_mesin_detail").val("");
			$("#mesin_detail_div").show();
		} else {
			$("#mesin_detail_div").hide();
			$("#nama_mesin_detail").val("");
		}
	})

	$('#mesin_edit').on('change', function() {
		var data = $("#mesin_edit option:selected").text();
		if (data == 'Lain - lain') {
			$("#mesin_edit_detail").val("");
			$("#mesin_edit_detail_div").show();
		} else {
			$("#mesin_edit_detail_div").hide();
			$("#mesin_edit_detail").val("");
		}
	})

	$('#prioritas_edit').on('change', function() {
		var data = $("#prioritas_edit option:selected").text();

		if (data == 'Urgent') {
			$("#reason_urgent_edit").val("");
			$("#div_reason_edit").show();
		} else {
			$("#div_reason_edit").hide();
			$("#reason_urgent_edit").val("");
		}
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

</script>
@endsection