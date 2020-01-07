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
		<button href="javascript:void(0)" class="btn btn-success btn-md pull-right" data-toggle="modal" data-target="#modal-close" style="margin-right: 5px">
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
									<th style="width: 1%;">Masuk</th>
									<th style="width: 1%;">Dept.</th>
									<th style="width: 1%;">Bag.</th>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
				<h4 class="modal-title">
					Close WJO
				</h4>
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

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="padding-right: 4%;">
			</div>
		</div>
	</div>
</div>

{{-- Modal Asssignment --}}
<div class="modal modal-default fade" id="modal-assignment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
				<h4 class="modal-title">
					Penugasan WJO
				</h4>
			</div>
			<div class="modal-body" style="padding-top: 0px;">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body" style="padding-left: 0px;">
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

							<div id="assign_body">
								<div class="col-xs-6">
									<div class="form-group row" align="right" id="show_request">
										<label class="col-xs-4" style="margin-top: 1%; padding-left: 0px;">Permintaan Selesai</label>
										<div class="col-xs-8" align="left">
											<div class="input-group date">
												<div class="input-group-addon bg-default">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control" id="assign_created_at" disabled>
											</div>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_priority" disabled>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Order No.</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_order_no" disabled>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Dept.</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_department" disabled>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Bagian</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_bagian" disabled>
										</div>
									</div>


								</div>
								<div class="col-xs-6">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Nama Barang</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_item_name" required>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Jumlah</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_quantity" required>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Material</label>
										<div class="col-xs-8" align="left">
											<input type="text" class="form-control" id="assign_material" required>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Uraian Permintaan</label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="assign_problem_desc" rows="3" required></textarea>
										</div>
									</div>							
								</div>
								<div class="col-xs-6" style="margin-top: 5%;">
									<div class="form-group row" align="right" id="show_target">
										<label class="col-xs-4" style="margin-top: 1%;">Target Selesai</label>
										<div class="col-xs-8">
											<div class="input-group date">
												<div class="input-group-addon bg-default">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="assign_target_date" placeholder="Pilih Tanggal">
											</div>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Kategori</label>
										<div class="col-xs-8" align="left">
											<select class="form-control select2" data-placeholder="Pilih Kategori" id="assign_category" style="width: 100% height: 35px; font-size: 15px;" required>
												<option value=""></option>
												<option value="Molding">Molding</option>
												<option value="Jig">Jig</option>
												<option value="Equipment">Equipment</option>
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Drawing</label>
										<div class="col-xs-8" align="left">
											<select class="form-control select2" data-placeholder="Pilih Drawing" id="assign_item_number" style="width: 100% height: 35px; font-size: 15px;" >
												<option value=""></option>
												@foreach($workshop_materials as $material)
												@if($material->remark == 'drawing')
												<option value="{{ $material->item_number }}">{{ $material->item_number }} ({{ $material->item_description }})</option>
												@endif
												@endforeach
											</select>
										</div>
									</div>

								</div>
								<div class="col-xs-6" style="margin-top: 5%;">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">PIC</label>
										<div class="col-xs-8" align="left">
											<select class="form-control select2" data-placeholder="Pilih Operator" id="assign_pic" style="width: 100% height: 35px; font-size: 15px;" required>
												<option value=""></option>
												@foreach($employees as $employee)
												@if(in_array($employee->group, ['Workshop']))
												<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }}-{{ $employee->name }}</option>
												@endif
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Kesulitan</label>
										<div class="col-xs-8" align="left">
											<select class="form-control select2" data-placeholder="Pilih Kesulitan" id="assign_difficulty" style="width: 100% height: 35px; font-size: 15px;" required>
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
							</div>						

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="padding-right: 4%;">
				<button id="assign-button" class="btn btn-success" onclick="assign()"><i class="fa fa-save"></i> Save</button>
			</div>
		</div>
	</div>
</div>


{{-- Modal Reject --}}
<div class="modal modal-default fade" id="modal-reject">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
				<h4 class="modal-title">
					Reject WJO
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body" style="padding-left: 0px;">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%; padding-left: 0px;">Permintaan Selesai</label>
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
									<label class="col-xs-2" style="margin-top: 1%;">Alasan Ditolak</label>
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
		$('#assign-button').hide();

		$('#show_request').hide();
		$('#show_target').hide();


		fillTable();
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	$('#modal-assignment').on('shown.bs.modal', function () {
		$("#tag").val("");
		$('#tag').focus();
	});

	$("#modal-assignment").on("hidden.bs.modal", function () {
		$('#assign_body').hide();
		$('#assign-button').hide();
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
						$('#assign-button').show();
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
				close();
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

					if(result.tableData[i].priority == 'Urgent'){
						tableData += '<tr style="background-color: rgba(213,0,0 ,.5);">';
					}else{
						tableData += '<tr>';
					}

					var assign = '';
					if(result.tableData[i].process_name == 'Listed'){
						assign = ' onclick="showAssignment(\''+result.tableData[i].order_no+'\')"';
					}

					tableData += '<td'+ assign +'>'+ result.tableData[i].order_no +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].created_at +'</td>';
					tableData += '<td'+ assign +'>'+ group[0] +'</td>';
					tableData += '<td'+ assign +'>'+ group[1] +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].approver || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].item_name +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].material +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].quantity +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].pic || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].difficulty || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].priority +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].target_date || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ (result.tableData[i].finish_date || '-') +'</td>';
					tableData += '<td'+ assign +'>'+ result.tableData[i].process_name +'</td>';
					if(result.tableData[i].attachment != null){
						tableData += '<td><a href="javascript:void(0)" onClick="downloadAtt(\''+result.tableData[i].attachment+'\')" class="fa fa-paperclip"></a></td>';
					}else{
						tableData += '<td>-</td>';							
					}

					tableData += '<td'+ assign +'>'+ (result.tableData[i].item_number || '-') +'</td>';
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

	function close() {
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

		if(item_name == "" || quantity == "" || material == "" || problem_description == "" || tag == "" || target_date == "" || category == "" || pic == "" || difficulty == ""){
			openErrorGritter('Error!', 'All fields must be filled');
			$("#loading").hide();
			return false;
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
				
				fillTable();
				$("#loading").hide();
				$("#modal-assignment").modal('hide');
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

	function showAssignment(order_no) {
		var data = {
			order_no:order_no
		}
		$.get('{{ url("fetch/workshop/assign_form") }}', data, function(result, status, xhr){
			if(result.status){

				if(result.wjo.priority == 'Urgent'){
					var datetime = result.wjo.request_date.split(" ");
					document.getElementById("assign_created_at").value = datetime[0];
					document.getElementById("assign_target_date").value = datetime[0];

					$('#show_request').show();
					$('#show_target').show();
				}else{
					$('#show_request').hide();
					$('#show_target').hide();
				}

				var group = result.wjo.sub_section.split("_");

				document.getElementById("assign_order_no").value = result.wjo.order_no;
				document.getElementById("assign_bagian").value = group[1];
				document.getElementById("assign_department").value = group[0];
				document.getElementById("assign_priority").value = result.wjo.priority;
				document.getElementById("assign_item_name").value = result.wjo.item_name;
				document.getElementById("assign_quantity").value = result.wjo.quantity;
				document.getElementById("assign_material").value = result.wjo.material;
				document.getElementById("assign_problem_desc").value = result.wjo.problem_description;


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