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
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance: textfield;
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
			<div class="box box-solid">
				<div class="box-body">

					<form method="GET" action="{{ url("fetch/shipping_order/excel_ship_reservation") }}">
						
						<input type="hidden" value="{{csrf_token()}}" name="_token" />

						<div class="col-xs-3">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-xs-6">
										<div class="form-group">
											<label>Stuffing From</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="stuffingFrom" id="stuffingFrom">
											</div>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label>Stuffing To</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="stuffingTo" id="stuffingTo">
											</div>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label>ETD From</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="etdFrom" id="etdFrom">
											</div>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label>ETD To</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="etdTo" id="etdTo">
											</div>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label>Due Date From</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="dueFrom" id="dueFrom">
											</div>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label>Due Date To</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Select Date" class="form-control datepicker pull-right" name="dueTo" id="dueTo">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-9">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-3">
												<div class="form-group">
													<label>Period</label>
													<input type="text" class="form-control monthpicker" name="search_period" id="search_period" placeholder="Select Period">
												</div>
											</div>

											<div class="col-xs-3">
												<div class="form-group">
													<label>YCJ Ref. No.</label>
													<input type="text" class="form-control" name="search_ycj_ref" id="search_ycj_ref" placeholder="Type YCJ Ref. Number">
												</div>
											</div>

											<div class="col-xs-3">
												<div class="form-group">
													<label>B/L No.</label>
													<input type="text" class="form-control" name="search_bl" id="search_bl" placeholder="Type Booking Number">
												</div>
											</div>

											<div class="col-xs-3">
												<div class="form-group">
													<label>Invoice No.</label>
													<input type="text" class="form-control" name="search_invoice" id="search_invoice" placeholder="Type Invoice Number">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-3">
												<div class="form-group">
													<label>Help</label>
													<select class="form-control select2" data-placeholder="Select Help" name="search_help" id="search_help" style="width: 100%">
														<option value=""></option>
														<option value="YES">YES</option>
														<option value="NO">NO</option>
													</select>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<label>Status</label>
													<select class="form-control select2" multiple="multiple" data-placeholder="Select Status" name="search_status" id="search_status" style="width: 100%">
														<option value=""></option>
														@foreach($statuses as $status) 
														<option value="{{ $status }}">{{ $status }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<label>Application Rate</label>
													<select class="form-control select2" multiple="multiple" data-placeholder="Select Application Rate" name="serach_application_rate" id="serach_application_rate" style="width: 100%">
														<option value=""></option>
														@foreach($application_rates as $application_rate) 
														<option value="{{ $application_rate }}">{{ $application_rate }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<label>POD</label>
													<select class="form-control select2" multiple="multiple" data-placeholder="Select POD" name="serach_pod" id="serach_pod" style="width: 100%">
														<option value=""></option>
														@foreach($pods as $pod) 
														<option value="{{ $pod->port_of_delivery }}">{{ $pod->port_of_delivery }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-3">
												<div class="form-group">
													<label>Carrier</label>
													<select class="form-control select2" multiple="multiple" data-placeholder="Select Carrier" name="serach_carier" id="serach_carier" style="width: 100%">
														<option value=""></option>
														@foreach($cariers as $carier) 
														<option value="{{ $carier->carier }}">{{ $carier->carier }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<label>Nomination</label>
													<select style="width: 100%;" multiple="multiple" class="form-control select2" id="search_nomination" name="search_nomination" data-placeholder="Pilih Diagnosa">
														<option value=""></option>
														@foreach($nominations as $nomination) 
														<option value="{{ $nomination->nomination }}">{{ $nomination->nomination }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>

								<button type="submit" class="btn btn-success">&nbsp;&nbsp;<span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;&nbsp;Excel&nbsp;</button>

								<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
							</div>
						</div>

					</form>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="form-group pull-left">
							<a href="{{ url("index/resume_shipping_order") }}" target="_blank" class="btn btn-primary pull-left">
								<span class="fa fa-bar-chart"></span>&nbsp;&nbsp; Booking Resume
							</a>
						</div>
						<div class="form-group pull-right">
							<a href="javascript:void(0)" data-toggle="modal"  data-target="#modalUpload" class="btn btn-primary">
								<span class="fa fa-upload"></span>&nbsp;&nbsp; Upload Weekly Shipment
							</a>
							<a href="javascript:void(0)" data-toggle="modal"  data-target="#modalAdd" class="btn btn-success">
								<span class="fa fa-plus"></span>&nbsp;&nbsp; Add Reservation
							</a>
						</div>
					</div>
					<div class="col-xs-12" style="overflow-x: auto;">
						<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%; font-size: 12px;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Period</th>
									<th style="width: 1%;">YCJ Ref. No.</th>
									<th style="width: 1%;">HELP</th>
									<th style="width: 1%;">Status</th>
									<th style="width: 1%;">Shipper</th>
									<th style="width: 1%;">POL</th>
									<th style="width: 20%;">POD</th>
									<th style="width: 1%;">40HC</th>
									<th style="width: 1%;">40'</th>
									<th style="width: 1%;">20'</th>
									<th style="width: 1%;">Booking No.</th>
									<th style="width: 1%;">Carrier</th>
									<th style="width: 1%;">Nomination</th>
									<th style="width: 1%;">Stuffing</th>
									<th style="width: 1%;">ETD</th>
									<th style="width: 10%;">Application Rate</th>
									<th style="width: 10%;">Remark</th>
									<th style="width: 1%;">Due Date</th>
									<th style="width: 1%;">I/V#</th>
									<th style="width: 1%;">Ref#</th>
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

<div class="modal fade" id="modalAdd">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h1 style="background-color: #00a65a; text-align: center;" class="modal-title">
					Add Ship Reservation
				</h1>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="box-body">
							<div class="form-group row" align="right">
								<label class="col-xs-3">Period<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control monthpicker" name="period" id="period" placeholder="select Shipment Period">
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">YCJ Ref. no.<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="ycj_ref_no" id="ycj_ref_no" placeholder="Enter YCJ Ref. Number">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Help<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectHelp">
									<select class="form-control selectHelp" data-placeholder="Select Help" name="help" id="help" style="width: 100%">
										<option value=""></option>
										<option value="YES">YES</option>
										<option value="NO">NO</option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Status<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectStatus">
									<select class="form-control selectStatus" data-placeholder="Select Status" name="status" id="status" style="width: 100%">
										<option value=""></option>
										@foreach($statuses as $status) 
										<option value="{{ $status }}">{{ $status }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Shipper<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="shipper" id="shipper" placeholder="Enter Shipper" value="YMPI">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">POL<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="pol" id="pol" placeholder="Enter Pol" value="SURABAYA">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">POD<span class="text-red">*</span></label>
								<div class="col-xs-7" align="left" id="selectPod">
									<select class="form-control selectPod" data-placeholder="Select POD" name="pod" id="pod" style="width: 100%">
										<option value=""></option>
										@foreach($pods as $pod) 
										<option value="{{ $pod->country }}-{{ $pod->port_of_delivery }}">{{ $pod->country }} - {{ $pod->port_of_delivery }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Size<span class="text-red">*</span></label>
								<label class="col-xs-1">40HC</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="fortyhc" id="fortyhc" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3"></label>
								<label class="col-xs-1">40'</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="forty" id="forty" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3"></label>
								<label class="col-xs-1">20'</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="twenty" id="twenty" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">B/L no.</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="bl" id="bl" placeholder="B/L No. or Booking No.">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Carrier/FWD<span class="text-red">*</span></label>
								<div class="col-xs-7" align="left" id="selectCarier">
									<select class="form-control selectCarier" data-placeholder="Select Carrier/FWD" name="carier" id="carier" style="width: 100%">
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Stuffing Date<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="stuffing" id="stuffing" placeholder="select Stuffing Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">ETD Date<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="etd" id="etd" placeholder="Select ETD Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Application Rate<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectRate">
									<select class="form-control selectRate" data-placeholder="Select Application Rate" name="application_rate" id="application_rate" style="width: 100%">
										<option value=""></option>
										@foreach($application_rates as $application_rate) 
										<option value="{{ $application_rate }}">{{ $application_rate }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Remark</label>
								<div class="col-xs-7" align="left">
									<textarea name="remark" id="remark" name="remark" class="form-control" rows="2" placecholder="Enter your remark for this shippment.."></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Due Date</label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="due_date" id="due_date" placeholder="Select Due Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Invoice</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="invoice" id="invoice" placeholder="Enter Invoice Number">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Ref #</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="ref" id="ref" placeholder="Enter Ref">
								</div>
							</div>							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> CANCEL</a>
				<button class="btn btn-success" onclick="saveList()"><i class="fa fa-check-square-o"></i> SUBMIT</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h1 style="background-color: #e08e0b; text-align: center;" class="modal-title">
					Edit Ship Reservation
				</h1>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<input type="text" name="shipment_reservation_id" id="shipment_reservation_id" hidden>

						<div class="box-body">
							<div class="form-group row" align="right">
								<label class="col-xs-3">Period<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control monthpicker" name="edit_period" id="edit_period" placeholder="select Shipment Period" readonly>
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">YCJ Ref. no.<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_ycj_ref_no" id="edit_ycj_ref_no" placeholder="Enter YCJ Ref. Number">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Help<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectEditHelp">
									<select class="form-control selectEditHelp" data-placeholder="Select Help" name="edit_help" id="edit_help" style="width: 100%">
										<option value=""></option>
										<option value="YES">YES</option>
										<option value="NO">NO</option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Status<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectEditStatus">
									<select class="form-control selectEditStatus" data-placeholder="Select Status" name="edit_status" id="edit_status" style="width: 100%">
										<option value=""></option>
										@foreach($statuses as $status) 
										<option value="{{ $status }}">{{ $status }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Shipper</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_shipper" id="edit_shipper" placeholder="Enter Shipper" value="YMPI" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">POL</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_pol" id="edit_pol" placeholder="Enter Pol" value="SURABAYA" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">POD</label>
								<div class="col-xs-7" align="left" id="selectEditPod">
									<input type="text" style="width: 100%" class="form-control" name="edit_pod" id="edit_pod" placeholder="Select POD" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Size<span class="text-red">*</span></label>
								<label class="col-xs-1">40HC</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="edit_fortyhc" id="edit_fortyhc" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3"></label>
								<label class="col-xs-1">40'</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="edit_forty" id="edit_forty" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3"></label>
								<label class="col-xs-1">20'</label>
								<div class="col-xs-2">
									<input type="int" style="width: 100%" class="form-control" name="edit_twenty" id="edit_twenty" placeholder="Qty">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">B/L no.</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_bl" id="edit_bl" placeholder="B/L No. or Booking No.">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Carrier/FWD</label>
								<div class="col-xs-7" align="left" id="selectEditCarier">
									<input type="text" style="width: 100%" class="form-control" name="edit_carier" id="edit_carier" placeholder="Select Carrier/FWD" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Stuffing Date<span class="text-red">*</span></label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="edit_stuffing" id="edit_stuffing" placeholder="select Stuffing Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">ETD Date</label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="edit_etd" id="edit_etd" placeholder="Select ETD Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Application Rate<span class="text-red">*</span></label>
								<div class="col-xs-5" align="left" id="selectEditRate">
									<select class="form-control selectEditRate" data-placeholder="Select Application Rate" name="edit_application_rate" id="edit_application_rate" style="width: 100%">
										<option value=""></option>
										@foreach($application_rates as $application_rate) 
										<option value="{{ $application_rate }}">{{ $application_rate }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Remark</label>
								<div class="col-xs-7" align="left">
									<textarea name="remark" id="edit_remark" name="edit_remark" class="form-control" rows="2" placecholder="Enter your remark for this shippment.."></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Due Date</label>
								<div class="col-xs-5">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="edit_due_date" id="edit_due_date" placeholder="Select Due Date" >
									</div>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Invoice</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_invoice" id="edit_invoice" placeholder="Enter Invoice Number">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-3">Ref #</label>
								<div class="col-xs-5">
									<input type="text" style="width: 100%" class="form-control" name="edit_ref" id="edit_ref" placeholder="Enter Ref">
								</div>
							</div>							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> CANCEL</a>
				<button class="btn btn-success" onclick="editList()"><i class="fa fa-check-square-o"></i> SUBMIT</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUpload">
	<div class="modal-dialog  modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h1 style="background-color: #367fa9; text-align: center;" class="modal-title">
					Upload Ship Reservation
				</h1>
			</div>
			<form id ="uploadReservation" method="post" enctype="multipart/form-data">
				<div class="modal-header">
					Format: [Stuffing Date][BL Date][Destination][TRANSPORTATION]<br>
					Sample: <a href="{{ url('download/manual/upload_shipment_reservation.xlsx') }}">upload_shipment_reservation.xlsx</a> Code: #Truncate
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Shipment Period<span class="text-red">*</span></label>
						<div class="input-group date">
							<input type="text" placeholder="Select Shipment Period" class="form-control monthpicker" name="upload_period" id="upload_period">
						</div>
					</div>

					<div class="form-group">
						<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>
				</div>
				<div class="modal-footer">
					<a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> CANCEL</a>
					<button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SUBMIT</button>
				</div>
			</form>
		</div>
	</div>
</div>


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

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

		$('.select2').select2({
			allowClear: true
		});

		$('.select3').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true
		});

		$('.selectHelp').select2({
			dropdownParent: $('#selectHelp'),
			allowClear: true
		});

		$('.selectStatus').select2({
			dropdownParent: $('#selectStatus'),
			allowClear: true
		});

		$('.selectPod').select2({
			dropdownParent: $('#selectPod'),
			allowClear: true
		});

		$('.selectCarier').select2({
			dropdownParent: $('#selectCarier'),
			allowClear: true
		});

		$('.selectRate').select2({
			dropdownParent: $('#selectRate'),
			allowClear: true
		});

		$('.select4').select2({
			dropdownParent: $('#modalEdit'),
			allowClear: true
		});

		$('.selectEditHelp').select2({
			dropdownParent: $('#selectEditHelp'),
			allowClear: true
		});

		$('.selectEditStatus').select2({
			dropdownParent: $('#selectEditStatus'),
			allowClear: true
		});

		$('.selectEditRate').select2({
			dropdownParent: $('#selectEditRate'),
			allowClear: true
		});

		// fillTable();

	});

	$("#pod").change(function(){
		var str = $(this).val();
		var dt = str.split('-');

		var data = {
			country : dt[0],
			pod : dt[1]
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "{{ url("fetch/shipping_order/get_carier") }}",
			data: data,
			success: function(message){
				$("#carier").html(message);                                                  
			}
		});                    
	});

	$("#edit_pod").change(function(){
		var str = $(this).val();
		var dt = str.split('-');

		var data = {
			country : dt[0],
			pod : dt[1]
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "{{ url("fetch/shipping_order/get_carier") }}",
			data: data,
			success: function(message){
				$("#edit_carier").html(message);                                                  
			}
		});                    
	});

	$('#uploadReservation').on('submit', function(event){
		event.preventDefault();
		var formdata = new FormData(this);

		var period = $('#upload_period').val();
		if(period == ''){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}

		$("#loading").show();

		$.ajax({
			url:"{{ url('fetch/shipping_order/upload_ship_reservation') }}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					clearAll();
					$('#modalUpload').modal('hide');
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', 'Fatal Error');
			}
		});
	});

	function clearAll(){
		$('#upload_period').val('');
		$('#upload_file').val('');

		$("#period").val('');
		$("#ycj_ref_no").val('');
		$("#help").prop('selectedIndex', 0).change();
		$("#status").prop('selectedIndex', 0).change();
		$("#shipper").val('');
		$("#pol").val('');
		$("#pod").prop('selectedIndex', 0).change();
		$("#bl").val('');
		$("#fortyhc").val('');
		$("#forty").val('');
		$("#twenty").val('');
		$("#carier").prop('selectedIndex', 0).change();
		$("#stuffing").val('');
		$("#etd").val('');
		$("#application_rate").prop('selectedIndex', 0).change();
		$("#remark").val('');
		$("#due_date").val('');
		$("#invoice").val('');
		$("#ref").val('');

		$("#shipment_reservation_id").val('');
		$("#edit_period").val('');
		$("#edit_ycj_ref_no").val('');
		$("#edit_help").prop('selectedIndex', 0).change();	
		$("#edit_status").prop('selectedIndex', 0).change();		
		$("#edit_bl").val('');
		$("#edit_fortyhc").val('');
		$("#edit_forty").val('');
		$("#edit_twenty").val('');
		$("#edit_stuffing").val('');
		$("#edit_etd").val('');
		$("#edit_application_rate").prop('selectedIndex', 0).change();
		$("#edit_remark").val('');
		$("#edit_due_date").val('');
		$("#edit_invoice").val('');
		$("#edit_ref").val('');
	}

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){

		var stuffingFrom = $('#stuffingFrom').val();
		var stuffingTo = $('#stuffingTo').val();
		var etdFrom = $('#etdFrom').val();
		var etdTo = $('#etdTo').val();
		var dueFrom = $('#dueFrom').val();
		var dueTo = $('#dueTo').val();

		var search_period = $('#search_period').val();
		var search_ycj_ref = $('#search_ycj_ref').val();
		var search_bl = $('#search_bl').val();
		var search_invoice = $('#search_invoice').val();

		var search_help = $('#search_help').val();
		var search_status = $('#search_status').val();
		var serach_application_rate = $('#serach_application_rate').val();
		var serach_pod = $('#serach_pod').val();

		var serach_carier = $('#serach_carier').val();
		var search_nomination = $('#search_nomination').val();

		var data = {
			stuffingFrom : stuffingFrom,
			stuffingTo : stuffingTo,
			etdFrom : etdFrom,
			etdTo : etdTo,
			dueFrom : dueFrom,
			dueTo : dueTo,
			search_period : search_period,
			search_ycj_ref : search_ycj_ref,
			search_bl : search_bl,
			search_invoice : search_invoice,
			search_help : search_help,
			search_status : search_status,
			serach_application_rate : serach_application_rate,
			serach_pod : serach_pod,
			serach_carier : serach_carier,
			search_nomination : search_nomination,
		}

		$.get('{{ url("fetch/shipping_order/ship_reservation") }}', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";

				for (var i = 0; i < result.data.length; i++) {

					tableData += '<tr id="'+result.data[i].id+'" onClick="showEdit(id)">';

					tableData += '<td>'+ result.data[i].period +'</td>';
					tableData += '<td>'+ result.data[i].ycj_ref_number +'</td>';
					if(result.data[i].help == 'YES'){
						tableData += '<td><span><i class="fa fa-circle"></i></span></td>';
					}else{
						tableData += '<td></td>';
					}
					tableData += '<td>'+ result.data[i].status +'</td>';
					tableData += '<td>'+ result.data[i].shipper +'</td>';
					tableData += '<td>'+ result.data[i].port_loading +'</td>';
					tableData += '<td>'+ result.data[i].port_of_delivery +', '+ result.data[i].country+'</td>';
					tableData += '<td>'+ (result.data[i].fortyhc || '') +'</td>';
					tableData += '<td>'+ (result.data[i].forty || '') +'</td>';
					tableData += '<td>'+ (result.data[i].twenty || '') +'</td>';
					tableData += '<td>'+ (result.data[i].booking_number || '') +'</td>';
					tableData += '<td>'+ result.data[i].carier +'</td>';
					tableData += '<td>'+ result.data[i].nomination +'</td>';
					tableData += '<td>'+ result.data[i].stuffing_date +'</td>';
					tableData += '<td>'+ (result.data[i].etd_date || '') +'</td>';
					tableData += '<td>'+ (result.data[i].application_rate || '') +'</td>';
					tableData += '<td>'+ (result.data[i].remark || '') +'</td>';
					tableData += '<td>'+ (result.data[i].due_date || '') +'</td>';
					tableData += '<td>'+ (result.data[i].invoice_number || '') +'</td>';
					tableData += '<td>'+ (result.data[i].ref || '') +'</td>';

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
					'ordering': true,
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

	function showEdit(id){

		$("#shipment_reservation_id").val(id);
		$("#edit_period").val($('#'+id).find('td').eq(0).text());
		$("#edit_ycj_ref_no").val($('#'+id).find('td').eq(1).text());

		var help = '';
		if($('#'+id).find('td').eq(2).html() == ''){
			help = 'NO';
		}else{
			help = 'YES';
		}
		$("#edit_help").val(help).trigger('change.select2');

		$("#edit_status").val($('#'+id).find('td').eq(3).text()).trigger('change.select2');		
		$("#edit_shipper").val($('#'+id).find('td').eq(4).text());
		$("#edit_pol").val($('#'+id).find('td').eq(5).text());


		var pod = $('#'+id).find('td').eq(6).text();
		var data = pod.split(', ');
		$("#edit_pod").val(data[1] + ' - ' + data[0]).trigger('change.select2');

		$("#edit_bl").val($('#'+id).find('td').eq(10).text());

		var fortyhc = '';
		if($('#'+id).find('td').eq(7).text() > 0){
			fortyhc = $('#'+id).find('td').eq(7).text();
		}

		var forty = '';
		if($('#'+id).find('td').eq(8).text() > 0){
			forty= $('#'+id).find('td').eq(8).text();
		}

		var twenty = '';
		if($('#'+id).find('td').eq(9).text() > 0){
			twenty = $('#'+id).find('td').eq(9).text();
		}

		$("#edit_fortyhc").val(fortyhc);
		$("#edit_forty").val(forty);
		$("#edit_twenty").val(twenty);
		var carier = $('#'+id).find('td').eq(11).text() + ' - ' + $('#'+id).find('td').eq(12).text();
		$("#edit_carier").val(carier);
		$("#edit_stuffing").val($('#'+id).find('td').eq(13).text());
		$("#edit_etd").val($('#'+id).find('td').eq(14).text());
		$("#edit_application_rate").val($('#'+id).find('td').eq(15).text()).trigger('change.select2');
		$("#edit_remark").val($('#'+id).find('td').eq(16).text());
		$("#edit_due_date").val($('#'+id).find('td').eq(17).text());
		$("#edit_invoice").val($('#'+id).find('td').eq(18).text());
		$("#edit_ref").val($('#'+id).find('td').eq(19).text());

		$("#modalEdit").modal('show');
	}

	function editList(){
		var shipment_reservation_id = $("#shipment_reservation_id").val();
		var period = $("#edit_period").val();
		var ycj_ref_no = $("#edit_ycj_ref_no").val();
		var help = $("#edit_help").val();
		var status = $("#edit_status").val();
		var bl = $("#edit_bl").val();
		var fortyhc = $("#edit_fortyhc").val();
		var forty = $("#edit_forty").val();
		var twenty = $("#edit_twenty").val();
		var stuffing = $("#edit_stuffing").val();
		var etd = $("#edit_etd").val();
		var application_rate = $("#edit_application_rate").val();
		var remark = $("#edit_remark").val();
		var due_date = $("#edit_due_date").val();
		var invoice = $("#edit_invoice").val();
		var ref = $("#edit_ref").val();

		if(ycj_ref_no == '' || help == '' || status == '' || stuffing == '' || application_rate == ''){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}

		if((fortyhc + forty + twenty) == 0){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}


		var data = {
			shipment_reservation_id : shipment_reservation_id,
			period : period,
			ycj_ref_no : ycj_ref_no,
			help : help,
			status : status,
			bl : bl,
			fortyhc : fortyhc,
			forty : forty,
			twenty : twenty,
			stuffing : stuffing,
			etd : etd,
			remark : remark,
			application_rate : application_rate,
			due_date : due_date,
			invoice : invoice,
			ref : ref
		}

		$("#loading").show();

		$.post('{{ url("fetch/shipping_order/edit_ship_reservation") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();
				clearAll();
				$("#loading").hide();
				$("#modalEdit").modal('hide');
				openSuccessGritter('Success', result.message);

			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}
		});	
	}

	function saveList(){
		var period = $("#period").val();
		var ycj_ref_no = $("#ycj_ref_no").val();
		var help = $("#help").val();
		var status = $("#status").val();
		var shipper = $("#shipper").val();
		var pol = $("#pol").val();
		var pod = $("#pod").val();
		var bl = $("#bl").val();
		var fortyhc = $("#fortyhc").val();
		var forty = $("#forty").val();
		var twenty = $("#twenty").val();
		var carier = $("#carier").val();
		var stuffing = $("#stuffing").val();
		var etd = $("#etd").val();
		var application_rate = $("#application_rate").val();
		var remark = $("#remark").val();
		var due_date = $("#due_date").val();
		var invoice = $("#invoice").val();
		var ref = $("#ref").val();

		if(period == '' || ycj_ref_no == '' || help == '' || status == '' || shipper == ''|| pol == ''|| pod == '' || carier == '' || stuffing == '' || application_rate == ''){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}

		if((fortyhc + forty + twenty) == 0){
			openErrorGritter('Error!', '(*) must be filled');
			return false;
		}

		var data = {
			period : period,
			ycj_ref_no : ycj_ref_no,
			help : help,
			status : status,
			shipper : shipper,
			pol : pol,
			pod : pod,
			bl : bl,
			fortyhc : fortyhc,
			forty : forty,
			twenty : twenty,
			carier : carier,
			stuffing : stuffing,
			etd : etd,
			application_rate : application_rate,
			remark : remark,
			due_date : due_date,
			invoice : invoice,
			ref : ref
		}


		$("#loading").show();

		$.post('{{ url("fetch/shipping_order/add_ship_reservation") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();
				clearAll();
				$("#loading").hide();
				$("#modalAdd").modal('hide');
				openSuccessGritter('Success', result.message);

			}else{
				$("#loading").hide();
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