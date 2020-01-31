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

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<button href="javascript:void(0)" class="btn btn-warning btn-md pull-right" data-toggle="modal" data-target="#modal-close" style="margin-right: 5px">
			<i class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;Close WJO
		</button>
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
														<option value="{{ $employee->section }}_{{ $employee->group }}">{{ $employee->section }}-{{ $employee->group }}</option>
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
														<option value="{{ $workshop_material->item_name }}">{{ $workshop_material->item_name }}</option>
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
														<option value="{{ $workshop_material->item_number }}">{{ $workshop_material->item_number }} - {{ $workshop_material->material_description }}</option>
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
													<label>Progres</label>
													<select class="form-control select2" data-placeholder="Pilih Progres" name="remark" id="remark" style="width: 100% height: 35px; font-size: 15px;">
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
					<div class="col-md-12" style="overflow-x: auto;">
						<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">WJO</th>
									<th style="width: 1%;">No. Tag</th>
									<th style="width: 1%;">Tanggal Masuk</th>
									<th style="width: 1%;">Prioritas</th>
									<th style="width: 1%;">Pemohon</th>
									<th style="width: 1%;">Dept.</th>
									<th style="width: 1%;">Bag.</th>
									<th style="width: 1%;">Nama Barang</th>
									<th style="width: 1%;">Material</th>
									<th style="width: 1%;">Qty</th>
									<th style="width: 1%;">Approved By</th>
									<th style="width: 1%;">PIC</th>
									<th style="width: 1%;">Kesulitan</th>
									<th style="width: 1%;">Target Selesai</th>
									<th style="width: 1%;">Actual Selesai</th>
									<th style="width: 1%;">Progress</th>
									<th style="width: 1%;">Att</th>
									<th style="width: 1%;">Draw</th>
									<th style="width: 1%;">Detail</th>
									<th style="width: 1%;">Reject</th>
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

{{-- Modal Close --}}
<div class="modal modal-default fade" id="modal-close">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #e08e0b;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Close WJO</h1>
				</div>
			</div>
			<div class="modal-body" style="padding-top: 0px;">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body" style="padding-left: 0px;">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />

							<div class="col-xs-8 col-xs-offset-2" style="text-align: center;">
								<div class="input-group col-xs-12">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: grey;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
									<input type="text" style="text-align: center; border-color: grey; font-size: 3vw; height: 70px" class="form-control" id="close_tag" name="close_tag" placeholder=">> Tap WJO Tag <<" required>
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: grey;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
								</div>
								<br>
							</div>

							<div id="close_body">
								<div class="col-xs-12">
									<h2 id="closed_order_no" style="text-align: center; font-size: 3vw; margin-bottom: 2%"></h2>
								</div>
								<div class="col-xs-6">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%; padding-left: 0px;">Target Selesai</label>
										<div class="col-xs-8" align="left">
											<div class="input-group date">
												<div class="input-group-addon bg-default">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control" id="closed_target_date" disabled>
											</div>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_priority" disabled>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Dept.</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_department" disabled>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Bagian</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_bagian" disabled>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">PIC</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_pic" disabled>
										</div>
									</div>
								</div>

								<div class="col-xs-6">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Kategori</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_category" disabled>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Nama Barang</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_item_name" disabled>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Jumlah</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_quantity" disabled>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Material</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_material" disabled>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Kesulitan</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="closed_difficulty" disabled>
										</div>
									</div>
								</div>
							</div>			

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="padding-right: 4%;">
				<br>
				<button id="close-button" class="btn btn-success" onclick="closen()"><i class="fa fa-save"></i> Close</button>
			</div>
		</div>
	</div>
</div>

{{-- Modal Asssignment --}}
<div class="modal modal-default fade" id="modal-assignment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Penugasan WJO</h1>
				</div>
			</div>
			<div class="modal-body" style="padding-top: 0px;">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body" style="padding-left: 0px;">
							<form id="assign" method="post" enctype="multipart/form-data" autocomplete="off">

								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="col-xs-12" style="text-align: center;">
									<div class="input-group col-xs-12">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: grey;">
											<i class="glyphicon glyphicon-credit-card"></i>
										</div>
										<input type="text" style="text-align: center; border-color: grey; font-size: 3vw; height: 70px" class="form-control" id="tag" name="tag" placeholder=">> Tap WJO Tag <<" required>
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: grey;">
											<i class="glyphicon glyphicon-credit-card"></i>
										</div>
									</div>
									<br>
								</div>

								<div id="assign_body" style="padding: 2%;">
									<div class="nav-tabs-custom tab-danger">
										<ul class="nav nav-tabs">
											<li id="vendor-tab-1" class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">WJO Data</a></li>
											<li id="vendor-tab-2" class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Flow Processes</a></li>
											<li id="vendor-tab-3" class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Person in Charge</a></li>
										</ul>
									</div>
									<div class="tab-content">
										<div class="tab-pane active" id="tab_1">
											<div class="row">
												<div class="col-xs-6">
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" id="assign_priority" readonly>
														</div>
													</div>
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Order No.</label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" name="assign_order_no" id="assign_order_no" readonly>
														</div>
													</div>
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Dept.</label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" id="assign_department" readonly>
														</div>
													</div>
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Bagian</label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" id="assign_bagian" readonly>
														</div>
													</div>
												</div>
												<div class="col-xs-6">
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Tipe Pekerjaan<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" name="assign_type" id="assign_type" readonly>
														</div>
													</div>

													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Nama Barang<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" name="assign_item_name" id="assign_item_name" required>
														</div>
													</div>

													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Jumlah<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" name="assign_quantity" id="assign_quantity" required>
														</div>
													</div>

													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Material<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<input type="text" class="form-control" name="assign_material" id="assign_material" required>
														</div>
													</div>


												</div>
												<div class="col-xs-12">
													<div class="form-group row" align="right">
														<label class="col-xs-2" style="margin-top: 1%;">Uraian Permintaan<span class="text-red">*</span></label>
														<div class="col-xs-10" align="left">
															<textarea class="form-control" name="assign_problem_desc" id="assign_problem_desc" rows="3" required></textarea>
														</div>
													</div>
												</div>
												<div class="col-xs-6" style="margin-top: 5%;">
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Target Selesai<span class="text-red">*</span></label>
														<div class="col-xs-8">
															<div class="input-group date">
																<div class="input-group-addon bg-default">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" class="form-control datepicker" name="assign_target_date" id="assign_target_date" placeholder="Pilih Tanggal">
															</div>
														</div>
													</div>
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Kesulitan<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<select class="form-control select2" data-placeholder="Pilih Kesulitan" name="assign_difficulty" id="assign_difficulty" style="width: 100% height: 35px; font-size: 15px;" required>
																<option value=""></option>
																<option value="Biasa">Biasa</option>
																<option value="Sulit">Sulit</option>
																<option value="Sangat Sulit">Sangat Sulit</option>
																<option value="Spesial">Spesial</option>
																<option value="Sangat Spesial">Sangat Spesial</option>
															</select>
														</div>
													</div>
												</div>
												<div class="col-xs-6" style="margin-top: 5%;">
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">Kategori<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<select class="form-control select2" data-placeholder="Pilih Kategori" name="assign_category" id="assign_category" style="width: 100% height: 35px; font-size: 15px;" required>
																<option value=""></option>
																<option value="Molding">Molding</option>
																<option value="Jig">Jig</option>
																<option value="Equipment">Equipment</option>
															</select>
														</div>
													</div>
													<div id="drawing">
														<div class="form-group row" align="right">
															<label class="col-xs-4" style="margin-top: 1%;">Drawing</label>
															<div class="col-xs-8" align="left">
																<input style="height: 37px;" class="form-control" type="file" name="assign_drawing" id="assign_drawing">
															</div>
														</div>	
													</div>
												</div>

												<div class="col-xs-12">
													<a class="btn btn-primary btnNext pull-right">Next</a>
												</div>
												<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;Tanda bintang (*) wajib diisi.&nbsp;</span>

											</div>	
										</div>
										<div class="tab-pane" id="tab_2">
											<div class="row">
												<div class="col-xs-12">
													<div class="col-xs-12" style="margin-bottom: 1%;">
														<div class="col-xs-8" style="padding: 0px;">
															<label style="font-weight: bold; font-size: 18px;">
																<span><i class="fa fa-gears"></i> Flow Processes</span>
															</label>
														</div>
														<div class="col-xs-1" style="padding: 0px;">
															<button class="btn btn-success" onclick='addProcess();'><i class='fa fa-plus' ></i></button>
														</div>
													</div>
													<div id='process'></div>
													<input type="hidden" class="form-control" name="assign_proses" id="assign_proses">

												</div>
												<div class="col-xs-12">
													<a class="btn btn-primary btnNext pull-right">Next</a>
													<span class="pull-right">&nbsp;</span>				
													<a class="btn btn-primary btnPrevious pull-right">Previous</a>
												</div>
												<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;Standart time dalam menit.&nbsp;</span><br>
											</div>
										</div>
										<div class="tab-pane" id="tab_3">
											<div class="row">
												<div class="col-xs-6">
													<div class="form-group row" align="right">
														<label class="col-xs-4" style="margin-top: 1%;">PIC<span class="text-red">*</span></label>
														<div class="col-xs-8" align="left">
															<select class="form-control select2" data-placeholder="Pilih Operator" name="assign_pic" id="assign_pic" style="width: 100% height: 35px; font-size: 15px;" required>
																<option value=""></option>
																@foreach($operators as $operator)
																<option value="{{ $operator->operator_id }}">{{ $operator->operator_id }} - {{ $operator->name }}</option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
												<div class="col-xs-12">
													<br>
													<button class="btn btn-success pull-right" type="submit"><i class="fa fa-save"></i> Save</button>
													<span class="pull-right">&nbsp;</span>				
													<a class="btn btn-primary btnPrevious pull-right">Previous</a>

												</div>
											</div>
										</div>
									</div>

								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modal Reject --}}
<div class="modal modal-default fade" id="modal-reject">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #d73925;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Reject WJO</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body" style="padding-left: 0px;">						
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%; padding-left: 0px;">Tanggal Masuk</label>
									<div class="col-xs-8" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-default">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control" id="reject_created_at" disabled>
										</div>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Order No.</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_order_no" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Dept.</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_department" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Bagian</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_bagian" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_priority" disabled>
									</div>
								</div>							
							</div>
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nama Barang</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_item_name" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Jumlah</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_quantity" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Material</label>
									<div class="col-xs-8" align="left">
										<input type="text" class="form-control" id="reject_material" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Uraian Permintaan</label>
									<div class="col-xs-8" align="left">
										<textarea class="form-control" id="reject_problem_desc" rows="3" disabled></textarea>
									</div>
								</div>							
							</div>
							<div class="col-xs-12" style="margin-top: 5%;">
								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Alasan Ditolak<span class="text-red">*</span></label>
									<div class="col-xs-10">
										<textarea class="form-control" id="reject_reason" placeholder="Alasan WJO Ditolak" style="width: 100%;" required></textarea> 										
									</div>
								</div>		
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="padding-right: 4%;">
				<br>
				<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;Tanda bintang (*) wajib diisi&nbsp;</span>
				<button class="btn btn-success" onclick="reject()"><i class="fa fa-save"></i> Save</button>
			</div>
		</div>
	</div>
</div>


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

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		var opt = $("#sub_section option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section").append(opt);
		$('#sub_section').prop('selectedIndex', 0).change();

		$('#assign_body').hide();

		$('#close_body').hide();
		$('#close-button').hide();

		$('#drawing').hide();


		fillTable();

		$('.btnNext').click(function(){
			var item_name = $("#assign_item_name").val();
			var quantity = $("#assign_quantity").val();
			var material = $("#assign_material").val();
			var problem_description = $("#assign_problem_desc").val();

			var tag = $("#tag").val();
			var target_date = $("#assign_target_date").val(); 
			var category = $("#assign_category").val();
			
			if(item_name == "" || quantity == "" || material == "" || problem_description == "" || tag == "" || target_date == "" || category == ""){
				openErrorGritter('Error!', 'All fields must be filled');
			}
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});
		$('.btnPrevious').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});
	});


	var proses = 0;
	function addProcess() {
		++proses;

		var add = '';
		add += '<div class="col-xs-12" id="add_process_'+ proses +'">';
		add += '<div class="col-xs-6" style="color: black; padding: 0px; padding-right: 1%;">';
		add += '<div class="col-xs-1" style="color: black; padding: 0px;">';
		add += '<h3 id="flow_'+ proses +'" style="margin: 0px;">'+ proses +'</h3>';
		add += '</div>';
		add += '<div class="col-xs-11" style="color: black; padding: 0px;">';
		add += '<select style="width: 100%;" class="form-control select3" name="process_'+ proses +'" id="process_'+ proses +'" data-placeholder="Select Process">';
		add += '<option value=""></option>';
		add += '@php $group = array(); @endphp';
		add += '@foreach($machines as $machine)';
		add += '@if(!in_array($machine->machine_name, $group))';
		add += '<option value="{{ $machine->machine_code }}">{{ $machine->process_name }} - {{ $machine->machine_name }} - {{ $machine->area_name }}</option>';
		add += '@php array_push($group, $machine->machine_name); @endphp';
		add += '@endif';
		add += '@endforeach';
		add += '</select>';
		add += '</div>';
		add += '</div>';
		
		add += '<div class="col-xs-2" style="color: black; padding: 0px; padding-right: 1%;">';
		add += '<div class="form-group">';
		add += '<input class="form-control" type="number" name="process_qty_'+ proses +'" id="process_qty_'+ proses +'" placeholder="Std Time" style="width: 100%; height: 33px; font-size: 15px; text-align: center;">';
		add += '</div>';
		add += '</div>';
		add += '<div class="col-xs-1" style="padding: 0px;">';
		add += '<button class="btn btn-danger" id="'+proses+'" onclick="removeProcess(this)"><i class="fa fa-close"></i></button>';
		add += '</div>';
		add += '</div>';

		$('#process').append(add);

		$(function () {
			$('.select3').select2({
				dropdownParent: $('#modal-assignment')
			});
		})

		document.getElementById("assign_proses").value = proses;

		console.log(proses);

	}

	function removeProcess(elem) {
		var id = parseInt($(elem).attr("id"));
		
		if(id != proses){
			$("#add_process_"+id).remove();
			for (var i = id; i < proses; i++) {
				document.getElementById("flow_"+ (i+1)).innerHTML = i;				
				document.getElementById("flow_"+ (i+1)).id = "flow_"+ i;
				document.getElementById("add_process_"+ (i+1)).id = "add_process_"+ i;
				document.getElementById("process_"+ (i+1)).id = "process_"+ i;
				document.getElementById("process_qty_"+ (i+1)).id = "process_qty_"+ i;
				document.getElementById(""+(i+1)+"").id = i;
			}
		}else{
			$("#add_process_"+id).remove();
		}
		proses--;

		document.getElementById("assign_proses").value = proses;

		console.log(proses);

	}

	function clearConfirmation(){
		location.reload(true);		
	}

	$('#assign_category').on('change', function() {
		if(this.value != 'Equipment'){
			$('#drawing').show();
		}else{
			$('#drawing').hide();
		}
	});

	$('#modal-assignment').on('shown.bs.modal', function () {
		$("#tag").val("");
		$('#tag').focus();
	});

	$("#modal-assignment").on("hidden.bs.modal", function () {
		$('#assign_body').hide();
		$('#vendor-tab-2').removeClass('active');
		$('#tab_2').removeClass('active');
		$('#vendor-tab-3').removeClass('active');
		$('#tab_3').removeClass('active');
		$('#vendor-tab-1').addClass('active');
		$('#tab_1').addClass('active');
	});

	$("#modal-close").on("hidden.bs.modal", function () {
		$('#close_body').hide();
		$('#close_button').hide();
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				var tag = $("#tag").val();

				var data = {
					tag : tag,
				}
				$.post('{{ url("check/workshop/wjo_rfid") }}', data,  function(result, status, xhr){
					if(result.status){
						$('#assign_body').show();
						openSuccessGritter('Success', result.message);

					}else{
						$("#tag").val("");
						$('#tag').focus();
						openErrorGritter('Error!', result.message);
					}	
				});
			}
			else{
				openErrorGritter('Error!', 'WJO Tag invalid.');
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}
		}
	});

	$('#modal-close').on('shown.bs.modal', function () {
		$("#close_tag").val("");
		$('#close_tag').focus();
	});

	$('#close_tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#close_tag").val().length >= 10){
				var tag = $("#close_tag").val();
				var data = {
					tag : tag,
				}

				$("#loading").show();		
				$.get('{{ url("close/workshop/check_rfid") }}', data,  function(result, status, xhr){
					if(result.status){
						var group = result.wjo.sub_section.split("_");

						document.getElementById("closed_order_no").innerHTML = result.wjo.order_no;
						document.getElementById("closed_target_date").value = result.wjo.target_date;
						document.getElementById("closed_priority").value = result.wjo.priority;
						document.getElementById("closed_department").value = group[0];
						document.getElementById("closed_bagian").value = group[1];
						document.getElementById("closed_pic").value = result.wjo.name;
						document.getElementById("closed_category").value = result.wjo.category;
						document.getElementById("closed_item_name").value = result.wjo.item_name;
						document.getElementById("closed_quantity").value = result.wjo.quantity;
						document.getElementById("closed_material").value = result.wjo.material;
						document.getElementById("closed_difficulty").value = result.wjo.difficulty;

						$('#close_body').show();
						$('#close-button').show();

						$("#loading").hide();
						openSuccessGritter('Success', result.message);
					}else{
						$("#loading").hide();
						openErrorGritter('Error!', result.message);
					}
				});
			}
			else{
				openErrorGritter('Error!', 'WJO Tag invalid.');
				audiclose_o_error.play();
				$("#tag").val("");
				$("#close_tag").focus();
			}
		}
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

					var group = result.tableData[i].sub_section.split("_");


					var assign = '';
					if(result.tableData[i].process_name == 'Listed'){
						assign = ' onclick="showAssignment(\''+result.tableData[i].order_no+'\')"';
					}

					tableData += '<tr>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].order_no +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].tag || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].created_at +'</td>';
					if(result.tableData[i].priority == 'Urgent'){
						var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					}else{
						var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					}
					tableData += '<td'+ assign +'>'+ priority +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].requester || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ group[0] +'</td>';
					tableData += '<td'+ assign +'>'+ group[1] +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].item_name +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].material +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].quantity +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].approver || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].pic || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].difficulty || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].target_date || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].finish_date || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].process_name +'</td>';
					if(result.tableData[i].attachment != null){
						tableData += '<td><a href="javascript:void(0)" onClick="downloadAtt(\''+result.tableData[i].attachment+'\')" class="fa fa-paperclip"></a></td>';
					}else{
						tableData += '<td>-</td>';							
					}

					if(result.tableData[i].item_number != null){
						tableData += '<td><a href="javascript:void(0)" onClick="downloadDrw(\''+result.tableData[i].item_number+'\')" class="fa fa-paperclip"></a></td>';
					}else{
						tableData += '<td>-</td>';							
					}


					tableData += '<td style="text-align: center;">';
					tableData += '<button style="width: 50%; height: 100%;" class="btn btn-xs btn-primary form-control"><span><i class="glyphicon glyphicon-eye-open"></i></span></button>';
					tableData += '</td>';


					if((result.tableData[i].remark >= 1) && (result.tableData[i].remark <= 3)){
						tableData += '<td style="text-align: center;">';
						tableData += '<button style="width: 50%; height: 100%;" onclick="showReject(\''+result.tableData[i].order_no+'\')" class="btn btn-xs btn-danger form-control"><span><i class="glyphicon glyphicon-remove-sign"></i></span></button>';
						tableData += '</td>';
					}else{
						tableData += '<td>-</td>';							
					}

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

function closen() {
	var tag = $("#close_tag").val();

	var data = {
		tag : tag,
	}

	$("#loading").show();		
	$.post('{{ url("close/workshop/wjo") }}', data,  function(result, status, xhr){
		if(result.status){
			$("#close_tag").val("");

			fillTable();
			$("#loading").hide();
			$("#modal-close").modal('hide');
			openSuccessGritter('Success', result.message);
		}else{
			$("#loading").hide();
			openErrorGritter('Error!', result.message);
		}
	});
}

function assign() {
	var order_no = $("#assign_order_no").val();
	var item_name = $("#assign_item_name").val();
	var quantity = $("#assign_quantity").val();
	var material = $("#assign_material").val();
	var problem_description = $("#assign_problem_desc").val();

	var tag = $("#tag").val();
	var target_date = $("#assign_target_date").val(); 
	var category = $("#assign_category").val();
	var item_number = $("#assign_item_number").val();
	var pic = $("#assign_pic").val(); 
	var difficulty = $("#assign_difficulty").val(); 

	if(item_name == "" || quantity == "" || material == "" || problem_description == "" || tag == "" || category == "" || pic == "" || difficulty == ""){
		openErrorGritter('Error!', 'All fields must be filled');
		$("#loading").hide();
		return false;
	}

	var flow_process = [];
	for (var i = 1; i <= proses; i++) {
		flow_process.push({
			sequence_process : i,
			machine_code : $("#process_"+ i).val(),
			std_time: $("#process_qty_"+ i).val()
		});
	}

	var data = {
		order_no : order_no,
		item_name : item_name,
		quantity : quantity,
		material : material,
		problem_description : problem_description,
		tag : tag,
		target_date : target_date,
		category : category,
		item_number : item_number,
		pic : pic,
		difficulty : difficulty,
		flow_process : flow_process,
	}

	$("#loading").show();		
	$.post('{{ url("update/workshop/wjo") }}', data,  function(result, status, xhr){
		if(result.status){

			$("#tag").val("");
			$("#assign_target_date").val("");
			$('#assign_pic').prop('selectedIndex', 0).change();
			$('#assign_difficulty').prop('selectedIndex', 0).change();
			$('#assign_category').prop('selectedIndex', 0).change();
			$('#assign_item_number').prop('selectedIndex', 0).change();

			for (var i = 1; i <= proses; i++) {
				$("#add_process_"+i).remove();
			}

			fillTable();
			$("#loading").hide();
			$("#modal-assignment").modal('hide');
			$('#drawing').hide();
			openSuccessGritter('Success', result.message);
		}else{
			$("#tag").val("");
			$("#assign_target_date").val("");
			$('#assign_pic').prop('selectedIndex', 0).change();
			$('#assign_difficulty').prop('selectedIndex', 0).change();
			$('#assign_category').prop('selectedIndex', 0).change();
			$('#assign_item_number').prop('selectedIndex', 0).change();

			fillTable();
			$("#loading").hide();
			$("#modal-assignment").modal('hide');
			openErrorGritter('Error!', result.message);
		}
	});
}

$("form#assign").submit(function(e) {
	$("#loading").show();		

	e.preventDefault();    
	var formData = new FormData(this);

	$.ajax({
		url: '{{ url("update/workshop/wjo") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#tag").val("");
			$("#assign_target_date").val("");
			$('#assign_pic').prop('selectedIndex', 0).change();
			$('#assign_difficulty').prop('selectedIndex', 0).change();
			$('#assign_category').prop('selectedIndex', 0).change();
			$('#assign_item_number').prop('selectedIndex', 0).change();
			$("#assign_drawing").val("");


			$('#process').append().empty();
			// for (var i = 1; i <= proses; i++) {
			// 	$("#add_process_"+i).remove();
			// }
			proses = 0;


			fillTable();
			$("#loading").hide();
			$("#modal-assignment").modal('hide');
			$('#drawing').hide();
			openSuccessGritter('Success', result.message);
		},
		error: function(result, status, xhr){
			$("#tag").val("");
			$("#assign_target_date").val("");
			$('#assign_pic').prop('selectedIndex', 0).change();
			$('#assign_difficulty').prop('selectedIndex', 0).change();
			$('#assign_category').prop('selectedIndex', 0).change();
			$('#assign_item_number').prop('selectedIndex', 0).change();

			fillTable();
			$("#loading").hide();
			$("#modal-assignment").modal('hide');
			openErrorGritter('Error!', result.message);
		},
		cache: false,
		contentType: false,
		processData: false
	});
});

function showAssignment(order_no) {
	console.log(proses);
	var data = {
		order_no:order_no
	}
	$.get('{{ url("fetch/workshop/assign_form") }}', data, function(result, status, xhr){
		if(result.status){

			document.getElementById("assign_target_date").value = result.wjo.target_date;
			var group = result.wjo.sub_section.split("_");
			document.getElementById("assign_order_no").value = result.wjo.order_no;
			document.getElementById("assign_bagian").value = group[1];
			document.getElementById("assign_department").value = group[0];
			document.getElementById("assign_priority").value = result.wjo.priority;
			document.getElementById("assign_type").value = result.wjo.type;
			document.getElementById("assign_item_name").value = result.wjo.item_name;
			document.getElementById("assign_quantity").value = result.wjo.quantity;
			document.getElementById("assign_material").value = result.wjo.material;
			document.getElementById("assign_problem_desc").value = result.wjo.problem_description;

			$("#assign_category").val(result.wjo.category).trigger('change.select2');
			$('#assign_item_number').val(result.wjo.item_number).trigger('change');


			$("#modal-assignment").modal('show');

		}
	});
}

function reject(){
	var order_no = $("#reject_order_no").val(); 
	var reason = $("#reject_reason").val();

	if(reason == ""){
		openErrorGritter('Error!', 'All fields must be filled');
		$("#loading").hide();
		return false;
	}

	var data = {
		order_no : order_no,
		reason : reason
	}

	$("#loading").show();		
	$.post('{{ url("reject/workshop/wjo") }}', data,  function(result, status, xhr){
		if(result.status){

			$("#reject_reason").val("");

			fillTable();
			$("#loading").hide();
			$("#modal-reject").modal('hide');
			openSuccessGritter('Success', result.message);
		}else{
			$("#loading").hide();
			openErrorGritter('Error!', result.message);
		}
	});
}

function showReject(order_no) {
	var data = {
		order_no:order_no
	}
	$.get('{{ url("fetch/workshop/assign_form") }}', data, function(result, status, xhr){
		if(result.status){

			var datetime = result.wjo.created_at.split(" ");
			var group = result.wjo.sub_section.split("_");

			document.getElementById("reject_created_at").value = datetime[0];
			document.getElementById("reject_order_no").value = result.wjo.order_no;
			document.getElementById("reject_bagian").value = group[1];
			document.getElementById("reject_department").value = group[0];
			document.getElementById("reject_priority").value = result.wjo.priority;
			document.getElementById("reject_item_name").value = result.wjo.item_name;
			document.getElementById("reject_quantity").value = result.wjo.quantity;
			document.getElementById("reject_material").value = result.wjo.material;
			document.getElementById("reject_problem_desc").value = result.wjo.problem_description;

			$("#modal-reject").modal('show');

		}
	});
}

function downloadDrw(attachment) {
	var data = {
		file:attachment
	}
	$.get('{{ url("download/workshop/drawing") }}', data, function(result, status, xhr){
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