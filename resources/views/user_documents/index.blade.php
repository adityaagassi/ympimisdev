@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-hover > tbody > tr > td:hover, table.table-hover > thead > tr > th:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input {
		line-height: 24px;
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
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;

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
		User Document <span class="text-purple"> ユーザの在留資格等に関する書類 </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Document Number:</label>
								<select class="form-control select2" multiple="multiple" id='documentNumber' data-placeholder="Select Doc. Number" style="width: 100%;">
									<option></option>
									@foreach($documents as $document)
									<option value="{{ $document->document_number }}">{{ $document->document_number }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Employee ID:</label>
								<select class="form-control select2" multiple="multiple" id='employeId' data-placeholder="Select Employee ID" style="width: 100%;">
									<option></option>
									@foreach($users as $user)
									<option value="{{ $user->employee_id }}">{{ $user->employee_id }} {{ $user->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Category:</label>
								<select class="form-control select2" multiple="multiple" id='category' data-placeholder="Select Category" style="width: 100%;">
									<option></option>
									@foreach($categories as $category)
									<option value="{{ $category }}">{{ $category }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Condition:</label>
								<select class="form-control select2" multiple="multiple" id='condition' data-placeholder="Select Condition" style="width: 100%;">
									<option></option>
									@foreach($conditions as $condition)
									<option value="{{ $condition }}">{{ $condition }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-1">
							<div class="form-group">
								<label style="color: white;"> x</label>
								<button onClick="fillTable()" class="btn btn-success form-control">search</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-2" style="padding-right: 0.5%">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">PASPOR</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_paspor }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_paspor }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="paspor_expired" title="PASPOR Expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="paspor_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="paspor_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding-left: 0.5%; padding-right: 0.5%;">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">KITAS</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_kitas }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_kitas }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="kitas_expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="kitas_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="kitas_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding-left: 0.5%; padding-right: 0.5%;">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">MERP</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_merp }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_merp }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="merp_expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="merp_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="merp_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding-left: 0.5%; padding-right: 0.5%;">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">NOTIF</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_notif }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_notif }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="notif_expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="notif_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="notif_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding-left: 0.5%; padding-right: 0.5%;">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">SKJ</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_skj }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_skj }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="skj_expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="skj_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="skj_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding-left: 0.5%;">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="3">SKLD</th>
							</tr>
							<tr>
								<th width="34%" style="background-color : rgba(33,33,33 ,1); padding: 0px; color: white">Expired
									<br><span style="color: white; font-size: 7pt;">Now <br>> ValidTo</span>
								</th>
								<th width="33%" style="background-color : rgba(242, 75, 75, 0.8); padding: 0px;">At Risk
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>< {{ $exp_skld }} Days</span>
								</th>
								<th width="33%" style="background-color : rgba(107, 255, 104, 0.6); padding: 0px;">Safe
									<br><span style="color: black; font-size: 7pt;">ValidTo <br>> {{ $exp_skld }} Days</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;" onclick="detail(id)" id="skld_expired">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="skld_at_risk">0</td>
								<td style="padding: 10px;" onclick="detail(id)" id="skld_safe">0</td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>


			<div class="row">
				<div class="col-md-12">
					<div class="box no-border">
						<div class="box-header">
							<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#create_modal"><span><i class="fa fa-plus"></i> Create</span></button>
						</div>
						<div class="box-body" style="padding-top: 0;">
							<table id="docTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="">Employee ID</th>
										<th style="width: 20%">Name</th>
										<th style="">Posisi</th>
										<th style="">Category</th>
										<th style="">No. Document</th>
										<th style="">Valid From</th>
										<th style="">Valid To</th>
										<th style="">Status</th>
										<th style="">Condition</th>
										<th style="width: 13%">Action</th>
									</tr>
								</thead>
								<tbody>
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

	{{-- Modal Create --}}
	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Create Document
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Document Number<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left">
										<input type="text" class="form-control" id="create_document_number" placeholder="Document Number" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Employee ID<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left">
										<select class="form-control select2" id='create_employee_id' data-placeholder="Employee ID" style="width: 100%;" required>
											<option></option>
											@foreach($employees as $employee_id)
											<option value="{{ $employee_id->employee_id }}">{{ $employee_id->employee_id }} - {{ $employee_id->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Category<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" id='create_category' data-placeholder="Select Category" style="width: 100%;">
											<option value=""></option>
											@foreach($categories as $category)
											<option value="{{ $category }}">{{ $category }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="create_valid_from" placeholder="select Date" >
										</div>
									</div>

								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid To<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="create_valid_to" placeholder="select Date" >
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="create()"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Renew --}}
	<div class="modal modal-default fade" id="renew_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Renew Document
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Document Number<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="renew_document_number">
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Employee ID</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="renew_employee_id" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Name</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="renew_name" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Category</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="renew_category" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Status</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="renew_status" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="renew_valid_from" placeholder="select Date">
										</div>
									</div>

								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid To<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="renew_valid_to" placeholder="select Date">
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="renew()"><span><i class="fa fa-save"></i> Save</span></button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Inactive --}}
	<div class="modal modal-warning fade" id="inactive_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Update Document Status 
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5 id="inactive_confirmation_text"></h5>
					</div>
					<input type="hidden" id="inactive_document_number">
					<input type="hidden" id="inactive_status">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="updateInactive()"><span><i class="fa fa-save"></i> Update</span></button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Active --}}
	<div class="modal modal-success fade" id="active_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Update Document Status 
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5 id="active_confirmation_text"></h5>
					</div>
					<input type="hidden" id="active_document_number">
					<input type="hidden" id="active_status">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="updateActive()"><span><i class="fa fa-save"></i> Update</span></button>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Detail --}}
	<div class="modal modal-default fade" id="detail_modal">
		<div class="modal-dialog modal-lg" style="width: 85%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h2 class="modal-title" id="detail_title"></h2>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-4">
							<div class="col-xs-3" style="padding-right: 0px; text-align: center;">
								<span class="label" style="background-color: black; color: white">Expired</span>
							</div>
							<div class="col-xs-9" style="padding-left: 0px;">
								<p id="detail_text_expired">Dokumen melebihi tanggal valid</p>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="col-xs-3" style="padding-right: 0px; text-align: center;">
								<span class="label label-danger">At Risk</span>
							</div>
							<div class="col-xs-9" style="padding-left: 0px;">
								<p id="detail_text_at_risk">Dokumen kurang 210 hari lagi expired</p>
							</div>
						</div>		
						<div class="col-xs-4">
							<div class="col-xs-3" style="padding-right: 0px; text-align: center;">
								<span class="label label-success">Safe</span>
							</div>
							<div class="col-xs-9" style="padding-right: 0px; padding-left: 0px; ">
								<p id="detail_text_safe">Masa aktif dokumen lebih dari 210 hari</p>
							</div>

						</div>					
					</div>

					<div class="row">
						<div class="col-xs-12">
							<table id="detailTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="">Employee ID</th>
										<th style="width: 20%">Name</th>
										<th style="">Posisi</th>
										<th style="">Category</th>
										<th style="">No. Document</th>
										<th style="">Valid From</th>
										<th style="">Valid To</th>
										<th style="">Status</th>
										<th style="">Condition</th>
										<th style="">Reminder</th>
										<th style="">Active</th>
									</tr>
								</thead>
								<tbody id="detailBody">
								</tbody>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2();
		fillTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
	});

	function capitalizeFirstLetter(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	function detail(id) {
		var data = id.split('_');

		var category = data[0];
		var condition = '';

		for (var i = 1; i < data.length; i++) {
			condition += data[i];

			if(i != data.length-1){
				condition += ' ';
			}
		}

		var data = {
			category : category.toUpperCase(),
			condition : capitalizeFirstLetter(condition)	
		}

		$.get('{{ url("fetch/resume_user_document_detail") }}', data, function(result, status, xhr){
			if(result.status){

				$("#detail_title").html('');
				$("#detail_title").html('<center>RESUME ' + category.toUpperCase() + ' ' + condition.toUpperCase() + '</center>');

				if(result.detail.length > 0){
					$('#detailBody').html("");

					$('#detail_text_expired').text("Document expired");
					$('#detail_text_at_risk').text("Document is less than "+ result.detail[0].reminder +" days to expire");
					$('#detail_text_safe').text("Document Active period more than "+ result.detail[0].reminder +" days");


					var tableData = '';
					for (var i = 0; i < result.detail.length; i++) {
						tableData += '<tr>';

						tableData += '<td>'+ result.detail[i].employee_id +'</td>';
						tableData += '<td>'+ result.detail[i].name +'</td>';
						tableData += '<td>'+ result.detail[i].position +'</td>';
						tableData += '<td>'+ result.detail[i].category +'</td>';
						tableData += '<td>'+ result.detail[i].document_number +'</td>';
						tableData += '<td>'+ result.detail[i].valid_from +'</td>';
						tableData += '<td>'+ result.detail[i].valid_to +'</td>';
						tableData += '<td>'+ result.detail[i].status +'</td>';
						if(result.detail[i].condition == 'Expired'){
							tableData += '<td><span class="label" style="background-color: black; color: white">'+ result.detail[i].condition +'</span></td>';
						}else if(result.detail[i].condition == 'At Risk'){
							tableData += '<td><span class="label label-danger">'+ result.detail[i].condition +'</span></td>';
						}else if(result.detail[i].condition == 'Safe'){
							tableData += '<td><span class="label label-success">'+ result.detail[i].condition +'</span></td>';
						}
						tableData += '<td>'+ result.detail[i].reminder +' Days Before Expired</td>';
						tableData += '<td>'+ result.detail[i].diff +' Days Remaining</td>';

						tableData += '</tr>';
					}

					$('#detailBody').append(tableData);
				}else{
					$('#detailBody').html("");
					var tableData = '';

					tableData += '<tr>';
					tableData += '<td colspan="11">No data available in table</td>';
					tableData += '</tr>';

					$('#detailBody').append(tableData);
				}


				$("#detail_modal").modal('show');


			}
		});	
	}

	function fillTable(){
		$('#docTable').DataTable().destroy();


		var documentNumber = $('#documentNumber').val();
		var employeId = $('#employeId').val();
		var category = $('#category').val();
		var condition = $('#condition').val();

		var data = {
			documentNumber:documentNumber,
			employeId:employeId,
			category:category,
			condition:condition
		}

		var table = $('#docTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default'
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
				}
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
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/user_document") }}",
				"data" : data
			},
			"columnDefs": [ {
				"targets": [8],
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData =='Safe' ) {
						$(td).css('background-color', 'rgba(107, 255, 104, 0.6)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}
					else if ( cellData =='At Risk' ){
						$(td).css('background-color', 'rgba(242, 75, 75, 0.8)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}
					else if ( cellData =='In Progress' ){
						$(td).css('background-color', 'rgba(92,107,192 ,0.8)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}
					else if ( cellData =='Expired' ){
						$(td).css('background-color', 'rgba(33,33,33 ,1)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'white');
					}
				}
			},{
				"targets": [7],
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData =='Active' ) {
						$(td).css('background-color', 'rgba(107, 255, 104, 0.6)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}
					else if ( cellData =='Inactive' ){
						$(td).css('background-color', 'rgba(243, 156, 18, 0.8)');
						$(td).css('font-weight', 'bold');
						$(td).css('color', 'black');	
					}	
				}
			}],
			"columns": [
			{ "data": "employee_id" },
			{ "data": "name" },
			{ "data": "position" },
			{ "data": "category" },
			{ "data": "document_number" },
			{ "data": "valid_from" },
			{ "data": "valid_to" },
			{ "data": "status" },
			{ "data": "condition" },
			{ "data": "button" }
			]
		});	

		$('#kitas_expired').text(0);
		$('#kitas_at_risk').text(0);
		$('#kitas_safe').text(0);

		$('#merp_expired').text(0);
		$('#merp_at_risk').text(0);
		$('#merp_safe').text(0);

		$('#notif_expired').text(0);
		$('#notif_at_risk').text(0);
		$('#notif_safe').text(0);

		$('#paspor_expired').text(0);
		$('#paspor_at_risk').text(0);
		$('#paspor_safe').text(0);

		$('#skj_expired').text(0);
		$('#skj_at_risk').text(0);
		$('#skj_safe').text(0);

		$('#skld_expired').text(0);
		$('#skld_at_risk').text(0);
		$('#skld_safe').text(0);

		$.get('{{ url("fetch/resume_user_document") }}', data, function(result, status, xhr){
			if(result.status){
				for (var i = 0; i < result.resume.length; i++) {
					var key = result.resume[i].category + '_' + result.resume[i].condition.replace(' ', '_');
					$('#' + key.toLowerCase()).text(result.resume[i].quantity);
				}
			}
		});	
	}

	$('#create_modal').on('hidden.bs.modal', function () {
		$('#create_document_number').val('');
		$('#create_employee_id').prop('selectedIndex', 0).change();
		$('#create_category').prop('selectedIndex', 0).change();
		$('#create_valid_from').val('');
		$('#create_valid_to').val('');
	});

	function create(){
		var documentNumber = $('#create_document_number').val();
		var employeId = $('#create_employee_id').val();
		var category = $('#create_category').val();
		var validFrom = $('#create_valid_from').val();
		var validTo = $('#create_valid_to').val();

		var data = {
			documentNumber:documentNumber,
			employeId:employeId,
			category:category,
			validFrom:validFrom,
			validTo:validTo
		}

		$.post('{{ url("fetch/user_document_create") }}', data, function(result, status, xhr){
			if(result.status){
				$("#create_modal").modal('hide');

				$('#create_document_number').val('');
				$('#create_employee_id').prop('selectedIndex', 0).change();
				$('#create_category').prop('selectedIndex', 0).change();
				$('#create_valid_from').val('');
				$('#create_valid_to').val('');

				$('#docTable').DataTable().ajax.reload();
				openSuccessGritter('Success','Create Document Success');
			}else{
				audio_error.play();
				openErrorGritter('Error','Create Document Failed');
			}
		});
	}

	function showRenew(elem){
		var documentNumber = $(elem).attr("id");
		var data = {
			documentNumber:documentNumber,
		}

		$.get('{{ url("fetch/user_document_detail") }}', data, function(result, status, xhr){
			if(result.status){
				document.getElementById("renew_document_number").value = result.document[0].document_number;
				document.getElementById("renew_employee_id").value = result.document[0].employee_id;
				document.getElementById("renew_name").value = result.document[0].name;
				document.getElementById("renew_category").value = result.document[0].category;
				document.getElementById("renew_status").value = result.document[0].status;
				// document.getElementById("renew_valid_from").value = result.document[0].valid_from;
				// document.getElementById("renew_valid_to").value = result.document[0].valid_to;

				$("#renew_modal").modal('show');
			}
			
		});
	}

	$('#renew_modal').on('hidden.bs.modal', function () {
		$("#renew_document_number").val('');
		$("#renew_valid_from").val('');
		$("#renew_valid_to").val('');
	});

	function renew(){
		var documentNumber = $('#renew_document_number').val();
		var employee_id = $('#renew_employee_id').val();
		var category = $('#renew_category').val();
		var validFrom = $('#renew_valid_from').val();
		var validTo = $('#renew_valid_to').val();

		var data = {
			documentNumber:documentNumber,
			employee_id:employee_id,
			category:category,
			validFrom:validFrom,
			validTo:validTo
		}

		$.post('{{ url("fetch/user_document_renew") }}', data, function(result, status, xhr){
			if(result.status){
				$("#renew_modal").modal('hide');

				$("#renew_document_number").val('');
				$("#renew_valid_from").val('');
				$("#renew_valid_to").val('');

				$('#docTable').DataTable().ajax.reload();
				openSuccessGritter('Success','Renew Document Success');
			} else {
				audio_error.play();
				openErrorGritter('Error','Renew Document Failed');
			}
		});
	}

	function showUpdate(elem){
		var documentNumber = $(elem).attr("id");
		var data = documentNumber.split("+");

		if(data[1] == 'Inactive'){
			$("#inactive_confirmation_text").append().empty();
			$("#inactive_confirmation_text").append("Are you sure want to update <b>"+data[0]+"</b> to <b>"+data[1]+"</b> ?");
			document.getElementById("inactive_document_number").value = data[0];
			document.getElementById("inactive_status").value = data[1];
			$("#inactive_modal").modal('show');
		}else if(data[1] == 'Active'){
			$("#active_confirmation_text").append().empty();
			$("#active_confirmation_text").append("Are you sure want to update <b>"+data[0]+"</b> to <b>"+data[1]+"</b> ?");
			document.getElementById("active_document_number").value = data[0];
			document.getElementById("active_status").value = data[1];
			$("#active_modal").modal('show');

		}
	}

	function updateInactive(){
		var documentNumber = $('#inactive_document_number').val();
		var status = $('#inactive_status').val();

		var data = {
			documentNumber:documentNumber,
			status:status,
		}

		$.post('{{ url("fetch/user_document_update") }}', data, function(result, status, xhr){
			if(result.status){
				$("#inactive_modal").modal('hide');

				$('#docTable').DataTable().ajax.reload();
				openSuccessGritter('Success','Update Document Success');
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Document Failed');
			}
		});
	}

	function updateActive(){
		var documentNumber = $('#active_document_number').val();
		var status = $('#active_status').val();

		var data = {
			documentNumber:documentNumber,
			status:status,
		}

		$.post('{{ url("fetch/user_document_update") }}', data, function(result, status, xhr){
			if(result.status){
				$("#active_modal").modal('hide');

				$('#docTable').DataTable().ajax.reload();
				openSuccessGritter('Success','Update Document Success');
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Document Failed');
			}
		});

	}



</script>
@endsection

