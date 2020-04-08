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
		List of {{ $page }}s
		<small>it all starts here</small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" id="6" onclick="fetchTable(id)">Canceled ({{ $canceled }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="5" onclick="fetchTable(id)">Rejected ({{ $rejected }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="0" onclick="fetchTable(id)">Requested ({{ $requested }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="1" onclick="fetchTable(id)">Listed ({{ $listed }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="2" onclick="fetchTable(id)">Approved ({{ $approved }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="3" onclick="fetchTable(id)">InProgress ({{ $inprogress }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="4" onclick="fetchTable(id)">Finished ({{ $finished }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="all" onclick="fetchTable(id)">All ({{ $rejected+$requested+$listed+$approved+$inprogress+$finished }})</a>
		</li>
		<li>
			<?php 
			if (strpos(strtolower($employee->position), 'operator') !== false || strpos(strtolower($employee->position), 'sub') !== false) {
				// echo $employee->position;
			} else {
				echo '<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Buat WJO Baru</a>';
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
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<table id="traceabilityTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 5%">Tanggal Pengajuan</th>
						<th style="width: 5%">WJO</th>
						<th style="width: 5%">Prioritas</th>
						<th style="width: 10%">Jenis Pekerjaan</th>
						<th style="width: 15%">Nama Barang</th>
						<th style="width: 5%">Jumlah</th>
						<th style="width: 9%">Material</th>
						<th style="width: 5%">Target</th>
						<th style="width: 5%">Status</th>
						<th style="width: 7%">Att</th>
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

	<div class="modal fade" id="detailModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Workshop Job Orders Detail</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12">
								<table class="table table-bordered">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th>WJO</th>
											<th>Nama Barang</th>
											<th>Jumlah</th>
											<th>Target</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td id="wjo_num2"></td>
											<td id="item_name2"></td>
											<td id="quantity2"></td>
											<td id="target2"></td>
											<td id="status2"></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-10 col-xs-offset-2" id="reject">
								<div class="form-group row">
									<label class="col-xs-2" style="margin-top: 1%;">Alasan Ditolak</label>
									<div class="col-xs-8" align="left">
										<textarea class="form-control" readonly id="detail_reject_reason"></textarea>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-right: 0px; padding-left: 0px;">
								<div id="step"></div>
							</div>
							<div class="col-xs-6" style="padding-right: 0px; padding-left: 0px;">
								<div id="actual"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Pembuatan Form WJO</h1>
					</div>
					<form id="data" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Bagian:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<select class="form-control select3" data-placeholder="Pilih Bagian" name="sub_section" id="sub_section" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									@php
									$group = array();
									@endphp
									@foreach($sections as $section)
									@if($section->group == null)
									<option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
									@else
									<option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>					

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Prioritas Pengerjaan" name="priority" id="priority" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Normal">Normal</option>
									<option value="Urgent">Urgent</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Jenis Pekerjaan" name="type" id="type" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Pembuatan Baru">Pembuatan Baru</option>
									<option value="Perbaikan Ketidaksesuaian">Perbaikan Ketidaksesuaian</option>
									<option value="Lain-lain">Lain-lain</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Kategori:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Kategori" name="category" id="category" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Molding">Molding</option>
									<option value="Jig">Jig</option>
									<option value="Equipment">Equipment</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Nama Barang:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="item_name" id="item_name" rows='1' placeholder="Nama Barang" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div id="drawing-field">
							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Nama Drawing:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="drawing_name" id="drawing_name" rows='1' placeholder="Nama Drawing" style="width: 100%; font-size: 15px;">
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">No. Drawing:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<input type="text" class="form-control" name="item_number" id="item_number" rows='1' placeholder="Nomor Drawing" style="width: 100%; font-size: 15px;">
								</div>
								<div class="col-xs-2" style="padding-left: 0px;">
									<input type="text" class="form-control" name="part_number" id="part_number" rows='1' placeholder="Nomor Part" style="width: 100%; font-size: 15px;">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Jumlah:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="number" name="quantity" id="quantity" placeholder="Jumlah Barang" style="width: 100%; height: 33px; font-size: 15px;" min="0" required>
							</div>
						</div>
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Material Awal:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Material Awal" name="material" id="material" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									@foreach($materials as $material)
									@if($material->remark == 'raw')
									<option value="{{ $material->item_description }}">{{ $material->item_description }}</option>
									@endif
									@endforeach
									<option value="Lainnya">LAINNYA</option>
								</select>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="material-other" id="material-other" placeholder="Material Lainnya" style="width: 100% height: 35px; font-size: 15px;">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Uraian Permintaan:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<textarea class="form-control" rows='3' name="problem_desc" id="problem_desc" placeholder="Uraian Permintaan / Masalah" style="width: 100%; font-size: 15px;" required></textarea>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;" id="request">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Request Selesai:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<div class="input-group date">
									<div class="input-group-addon bg-default">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" name="request_date" id="request_date" placeholder="Pilih Tanggal">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Lampiran:&nbsp;&nbsp;</span>
							</div>
							<div class="col-xs-8">
								<input style="height: 37px;" class="form-control" type="file" name="upload_file" id="upload_file">
							</div>
						</div>

						<div class="col-xs-12" style="padding-right: 12%;">
							<br>
							<button type="submit" class="btn btn-success pull-right">Submit</button>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;&nbsp;&nbsp;&nbsp;Note :&nbsp;&nbsp;&nbsp;</span><br>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;- Tanda bintang (*) wajib diisi&nbsp;&nbsp;</span><br>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;- 1 WJO hanya untuk 1 drawing&nbsp;</span><br>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #f39c12;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Form WJO</h1>
					</div>
					<form id="edit" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Bagian:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<select class="form-control select4" data-placeholder="Pilih Bagian" name="sub_section_edit" id="sub_section_edit" style="width: 100% height: 35px; font-size: 15px;" readonly>
									<option value=""></option>
									@php
									$group = array();
									@endphp
									@foreach($sections as $section)
									@if($section->group == null)
									<option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
									@else
									<option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>					

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select4" data-placeholder="Pilih Prioritas Pengerjaan" name="priority_edit" id="priority_edit" style="width: 100% height: 35px; font-size: 15px;" readonly>
									<option value=""></option>
									<option value="Normal">Normal</option>
									<option value="Urgent">Urgent</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select4" data-placeholder="Pilih Jenis Pekerjaan" name="type_edit" id="type_edit" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Pembuatan Baru">Pembuatan Baru</option>
									<option value="Perbaikan Ketidaksesuaian">Perbaikan Ketidaksesuaian</option>
									<option value="Lain-lain">Lain-lain</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Kategori:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select4" data-placeholder="Pilih Kategori" name="category_edit" id="category_edit" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Molding">Molding</option>
									<option value="Jig">Jig</option>
									<option value="Equipment">Equipment</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Nama Barang:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="item_name_edit" id="item_name_edit" rows='1' placeholder="Nama Barang" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div id="drawing-field-edit">
							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Nama Drawing:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="drawing_name_edit" id="drawing_name_edit" rows='1' placeholder="Nama Drawing" style="width: 100%; font-size: 15px;">
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">No. Drawing:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<input type="text" class="form-control" name="item_number_edit" id="item_number_edit" rows='1' placeholder="Nomor Drawing" style="width: 100%; font-size: 15px;">
								</div>
								<div class="col-xs-2" style="padding-left: 0px;">
									<input type="text" class="form-control" name="part_number_edit" id="part_number_edit" rows='1' placeholder="Nomor Part" style="width: 100%; font-size: 15px;">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Jumlah:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="number" name="quantity_edit" id="quantity_edit" placeholder="Jumlah Barang" style="width: 100%; height: 33px; font-size: 15px;" required>
							</div>
						</div>
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Material Awal:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select4" data-placeholder="Pilih Material Awal" name="material_edit" id="material_edit" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									@foreach($materials as $material)
									@if($material->remark == 'raw')
									<option value="{{ $material->item_description }}">{{ $material->item_description }}</option>
									@endif
									@endforeach
									<option value="Lainnya">LAINNYA</option>
								</select>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="material-other_edit" id="material-other_edit" placeholder="Material Lainnya" style="width: 100% height: 35px; font-size: 15px;">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Uraian Permintaan:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<textarea class="form-control" rows='3' name="problem_desc_edit" id="problem_desc_edit" placeholder="Uraian Permintaan / Masalah" style="width: 100%; font-size: 15px;" required></textarea>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;" id="request_edit">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Request Selesai:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<div class="input-group date">
									<div class="input-group-addon bg-default">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" name="request_date_edit" id="request_date_edit" placeholder="Pilih Tanggal">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Lampiran:&nbsp;&nbsp;</span>
							</div>
							<div class="col-xs-8">
								<input style="height: 37px;" class="form-control" type="file" name="upload_file_edit" id="upload_file_edit">
							</div>
						</div>

						<div class="col-xs-12" style="padding-right: 12%;">
							<br>
							<input type="hidden" id="id_edit" name="id_edit">
							<button type="submit" class="btn btn-success pull-right" ><i class="fa fa-pencil"></i> Simpan</button>
							<!-- <button type="submit" class="btn btn-success pull-right">Submit</button> -->
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;&nbsp;&nbsp;&nbsp;Note :&nbsp;&nbsp;&nbsp;</span><br>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;- Tanda bintang (*) wajib diisi&nbsp;&nbsp;</span><br>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;- 1 WJO hanya untuk 1 drawing&nbsp;</span><br>
						</div>
					</form>
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

		$("#reject").hide();

		var opt = $("#sub_section option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section").append(opt);
		$('#sub_section').prop('selectedIndex', 0).change();

		var opt = $("#sub_section_edit option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section_edit").append(opt);
		$('#sub_section_edit').prop('selectedIndex', 0).change();

		$('#request').hide();

		$('#material-other').hide();

		$('#material-other_edit').hide();

		$('#drawing-field').hide();

		$('#drawing-field-edit').hide();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchTable('all');
	});

	$("#quantity").change(function(){
		var val = $(this).val();
		if(val.includes('-')) {
			val = val.replace("-", "");
			$(this).val(val);
		}
	}) 

	$(function () {
		$('.select2').select2()
	})

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#createModal')
		});
	})

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#editModal')
		});
	})

	$('#category').on('change', function() {
		if(this.value != 'Equipment'){
			$('#drawing-field').show();
		}else{
			$('#drawing-field').hide();
		}
	});

	$('#category_edit').on('change', function() {
		if(this.value != 'Equipment'){
			$('#drawing-field-edit').show();
		}else{
			$('#drawing-field-edit').hide();
		}
	});

	$('#material').on('change', function() {
		if(this.value == 'Lainnya'){
			$('#material-other').show();
		}else{
			$('#material-other').hide();
		}
	});

	$('#material_edit').on('change', function() {
		if(this.value == 'Lainnya'){
			$('#material-other_edit').show();
			$('#material-other_edit').val('');
		}else{
			$('#material-other_edit').hide();
		}
	});

	$('#priority').on('change', function() {
		if(this.value == 'Urgent'){
			$('#request').show();
		}else if(this.value == 'Normal'){
			$('#request').hide();
		}
	});

	$('#priority_edit').on('change', function() {
		if(this.value == 'Urgent'){
			$('#request_edit').show();
		}else if(this.value == 'Normal'){
			$('#request_edit').hide();
		}
	});

	$('form').on('focus', 'input[type=number]', function (e) {
		$(this).on('wheel.disableScroll', function (e) {
			e.preventDefault()
		})
	})

	$('form').on('blur', 'input[type=number]', function (e) {
		$(this).off('wheel.disableScroll')
	})

	$("form#data").submit(function(e) {
		$("#loading").show();

		var category = $("#category").val();
		var drawing_name = $("#drawing_name").val();
		var drawing_number = $("#drawing_number").val();
		var part_number = $("#part_number").val();

		if(category != "Equipment"){
			if(drawing_name == "" || drawing_number == "" || part_number == ""){
				openErrorGritter('Error!', 'Tanda (*) harus diisi');
				$("#loading").hide();
				return false;
			}
		}

		var material = $("#material").val();
		if(material == "Lainnya"){
			var material_other = $("#material-other").val();
			if(material_other == ""){
				openErrorGritter('Error!', 'Kolom Material Lainnya harus diisi');
				$("#loading").hide();
				return false;
			}
		}

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/workshop/wjo") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#sub_section').prop('selectedIndex', 0).change();
				$("#category").val("");
				$("#item_name").val("");
				$("#quantity").val("");
				$("#request_date").val("");
				$('#priority').prop('selectedIndex', 0).change();
				$('#type').prop('selectedIndex', 0).change();
				$("#material").prop('selectedIndex', 0).change();
				$("#material-other").val("");
				$("#problem_desc").val("");
				$("#upload_file").val("");
				$("#drawing").prop('selectedIndex', 0).change();

				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);		

			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$("form#edit").submit(function(e) {
		$("#loading").show();

		var category = $("#category_edit").val();
		var drawing_name = $("#drawing_name_edit").val();
		var drawing_number = $("#item_number_edit").val();
		var part_number = $("#part_number_edit").val();

		if(category != "Equipment"){
			if(drawing_name == "" || drawing_number == "" || part_number == ""){
				openErrorGritter('Error!', 'Tanda (*) harus diisi');
				$("#loading").hide();
				return false;
			}
		}

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("index/workshop/edit_wjo") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#sub_section').prop('selectedIndex', 0).change();
				$("#category").val("");
				$("#item_name").val("");
				$("#quantity").val("");
				$("#request_date").val("");
				$('#priority').prop('selectedIndex', 0).change();
				$('#type').prop('selectedIndex', 0).change();
				$("#material").prop('selectedIndex', 0).change();
				$("#material-other").val("");
				$("#problem_desc").val("");
				$("#upload_file").val("");
				$("#drawing").prop('selectedIndex', 0).change();

				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);		

			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});

	});

	function fetchTable(id){
		var username = $('#username').val();
		var data = {
			remark: id,
			username: username,
			order: 'order_no desc'
		}
		$.get('{{ url("fetch/workshop/list_wjo") }}', data, function(result, status, xhr){
			if(result.status){
				$('#traceabilityTable').DataTable().clear();
				$('#traceabilityTable').DataTable().destroy();
				$('#tableBody').html("");

				var tableData = "";
				for (var i = 0; i < result.tableData.length; i++) {

					tableData += '<tr>';
					tableData += '<td>'+ result.tableData[i].created_at +'</td>';
					tableData += '<td>'+ result.tableData[i].order_no +'</td>';
					if(result.tableData[i].priority == 'Urgent'){
						var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					}else{
						var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					}
					tableData += '<td>'+ priority +'</td>';
					tableData += '<td>'+ result.tableData[i].type +'</td>';
					tableData += '<td>'+ result.tableData[i].item_name +'</td>';
					tableData += '<td>'+ result.tableData[i].quantity +'</td>';
					tableData += '<td>'+ result.tableData[i].material +'</td>';
					tableData += '<td>'+ (result.tableData[i].target_date || '-') +'</td>';
					tableData += '<td>'+ result.tableData[i].process_name +'</td>';	

					if(result.tableData[i].attachment != null){
						tableData += '<td><a href="javascript:void(0)" onClick="downloadAtt(\''+result.tableData[i].attachment+'\')" class="fa fa-paperclip"></a></td>';
					}else{
						tableData += '<td>-</td>';							
					}

					if(result.tableData[i].remark == '0' || result.tableData[i].remark == '1'){
						tableData += '<td>';
						tableData += '<a style="padding: 10%; padding-top: 2%; padding-bottom: 2%; margin-right: 2%;" href="javascript:void(0)" onClick="modalEdit(\''+result.tableData[i].id+'\')" class="btn btn-warning">Edit</a>';
						tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+result.tableData[i].order_no+'\')" class="btn btn-primary">Detail</a>';

						if (result.tableData[i].remark == '1' && result.tableData[i].priority == 'Normal') {
							tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="cancelWjo(\''+result.tableData[i].order_no+'\')" class="btn btn-danger">Cancel</a>';
						}
						tableData += '</td>';
					}else{
						tableData += '<td><a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+result.tableData[i].order_no+'\')" class="btn btn-primary">Detail</a></td>';							
					}

					tableData += '</tr>';	
				}

				$('#tableBody').append(tableData);
				$('#traceabilityTable').DataTable({
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
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function showDetail(wjo_num) {
		$('#detailModal').modal('show');

		var data = {
			order_no : wjo_num
		}

		$.get('{{ url("fetch/workshop/process_detail") }}', data, function(result, status, xhr){
			$("#wjo_num2").html(result.detail.order_no);
			$("#item_name2").html(result.detail.item_name);
			$("#quantity2").html(result.detail.quantity);
			$("#target2").html(result.detail.target_date);
			$("#status2").html(result.detail.process_name);
			$("#step").append().empty();
			$("#actual").append().empty();

			if (result.detail.remark == 5) {
				$("#reject").show();
				$('#detail_reject_reason').val(result.detail.reject_reason);
			} else {
				$("#reject").hide();
				$('#detail_reject_reason').val("");
			}

			var step = '';
			var actual = '';
			var green = '';

			if(result.flow.length > 0){
				$('#process_progress_bar').show();
				if(result.act.length == 0){
					green = 0;
				}else{
					green = result.act.length;
				}
				step += '<ul class="timeline">';
				step += '<li class="time-label">';
				step += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Plan&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				step += '</li>';
				for (var i = 0; i < result.flow.length; i++) {
					step += '<li style="margin-bottom: 5px;">';
					step += '<i class="fa fa-stack-1x" style="font-size: 15px;">'+ result.flow[i].sequence_process +'</i>';
					step += '<div class="timeline-item" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
					step += '<p style="padding: 0px; margin-bottom: 0px; font-size: 16px;">'+ result.flow[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ (result.flow[i].std_time / 60) +'m<span></p>';
					step += '<p style="padding: 0px; font-size: 14px; font-weight: bold; margin-bottom: 0px">'+ result.flow[i].machine_name +'</p><p>&nbsp;</p>';
					step += '</div>';
					step += '</li>';
				}
				step += '<li>';
				step += '<i class="fa fa-check-square-o bg-blue"></i>';
				step += '</li>';
				step += '</ul>';

			}

			if(result.act.length > 0){
				$('#process_progress_bar').show();

				actual += '<ul class="timeline">';
				actual += '<li class="time-label">';
				actual += '<span style="margin-left: 0.4%;" class="bg-blue">&nbsp;&nbsp;&nbsp;Actual&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				actual += '</li>';
				for (var i = 0; i < result.act.length; i++) {
					actual += '<li style="margin-bottom: 5px;">';
					actual += '<i class="fa fa-stack-1x bg-green" style="font-size: 15px;">'+ result.act[i].sequence_process +'</i>';
					actual += '<div class="timeline-item bg-green" style="padding-top: 1%; padding-left: 2%; padding-bottom: 0.25%;">';
					actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 16px;">'+ result.act[i].process_name +'<span class="pull-right" style="margin-right: 3%;">'+ Math.ceil(result.act[i].actual / 60) +'m<span></p>';
					actual += '<p style="padding: 0px; margin-bottom: 0px; font-size: 14px; font-weight: bold;">'+ result.act[i].machine_name +'</p>';
					actual += '<p style="padding: 0px; font-size: 12px;">PIC : '+ result.act[i].pic +'</p>';
					actual += '</div>';
					actual += '</li>';
				}
			}

			$("#step").append(step);
			$("#actual").append(actual);

			document.getElementById("green").value = green;

			for (var i = 0; i < green; i++) {
				$("#timeline_number_" + i).addClass('bg-green');
				$("#timeline_box_" + i).addClass('bg-green');						
			}
		})
	}

	function modalEdit(id) {
		var data = {
			id:id
		};


		$.get('{{ url("index/workshop/edit_wjo") }}', data, function(result, status, xhr){

			if(result.datas.priority == "Normal"){
				$('#request_edit').hide();
			}

			else if(result.datas.priority == "Urgent"){
				$('#request_edit').show();
			}

			for(var i = 0; i < result.material.length; i++){
				if (result.material[i].remark == 'raw') {
					console.log(result.datas.material);
					if (result.material[i].item_description == result.datas.material) {
						$("#material_edit").val(result.datas.material).trigger('change.select2');
						$('#material-other_edit').hide();
						break;
					}
					else{
						$("#material_edit").val('Lainnya').trigger('change.select2');
						$('#material-other_edit').show();
						$('#material-other_edit').val(result.datas.material);
						break;
					}
				}
			}

			// if(result.datas.material != "Lainnya"){
			// 	$('#material-other_edit').show();
			// }

			$("#id_edit").val(id);
			$("#sub_section_edit").val(result.datas.sub_section).trigger('change.select2');
			$("#priority_edit").val(result.datas.priority).trigger('change.select2');
			$("#type_edit").val(result.datas.type).trigger('change.select2');
			$("#category_edit").val(result.datas.category).trigger('change.select2');
			$("#item_name_edit").val(result.datas.item_name);
			$("#quantity_edit").val(result.datas.quantity);	        
			$("#problem_desc_edit").val(result.datas.problem_description);
			$("#request_date_edit").val(result.datas.target_date);

			if(result.datas.category != 'Equipment'){
				$("#drawing_name_edit").val(result.datas.drawing_name);
				$("#item_number_edit").val(result.datas.item_number);
				$("#part_number_edit").val(result.datas.part_number);
				$("#drawing-field-edit").show();
			}

			$('#editModal').modal('show');


		});

	}

	function edit() {

		if ($("#material_edit").val() == 'Lainnya') {
			var material = $("#material-other_edit").val();
		}else{
			var material = $("#material_edit").val();
		}

		var data = {
			id: $("#id_edit").val(),
			sub_section: $("#sub_section_edit").val(),
			priority: $("#priority_edit").val(),
			type: $("#type_edit").val(),
			category: $("#category_edit").val(),
			item_name: $("#item_name_edit").val(),
			quantity: $("#quantity_edit").val(),
			material: material,
			problem_description: $("#problem_desc_edit").val(),
			target_date: $("#request_date_edit").val()
		};

		if (material == '') {
			alert('Isi Material');
		}else{
			$.post('{{ url("index/workshop/edit_wjo") }}', data, function(result, status, xhr){
				if (result.status == true) {
					$("#id_edit").val("");
					$("#sub_section_edit").prop('selectedIndex', 0).change();
					$("#priority_edit").prop('selectedIndex', 0).change();
					$("#type_edit").prop('selectedIndex', 0).change();
					$("#category_edit").prop('selectedIndex', 0).change();
					$("#item_name_edit").val("");
					$("#quantity_edit").val("");	        
					$("#problem_desc_edit").val("");
					$("#request_date_edit").val("");

					$("#drawing_name_edit").val("");	        
					$("#item_number_edit").val("");
					$("#part_number_edit").val("");

					openSuccessGritter("Success", "WJO has been edited.");
					$('#modalEdit').modal('hide');
					window.location.reload();
				} else {
					openErrorGritter("Error","Failed to edit WJO.");
				}
			})
		}
	}

	function downloadAtt(attachment) {
		var data = {
			file:attachment
		}
		$.get('{{ url("download/workshop/attachment") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					window.open(result.file_path);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

	}

	function cancelWjo(order_no) {
		if (confirm("Apakah anda yakin akan membatalkan '"+order_no+"'?")) {
			var data = {
				wjo_num : order_no
			}
			$.get('{{ url("cancel/workshop/wjo") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success','WJO has been Canceled.');
						fetchTable("all");
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}
			});
		}
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