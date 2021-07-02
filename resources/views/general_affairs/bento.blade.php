@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
<style type="text/css">
.modal-body {
	min-height: calc(100vh - 210px);
	overflow-y: auto;
	overflow-x: hidden;
	max-height: 100%;
	position: relative;
}
table > tr:hover {
	background-color: #7dfa8c;
}
thead input {
	width: 100%;
	padding: 3px;
	box-sizing: border-box;
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
	font-size: 0.8vw;
	border:1px solid black;
	padding-top: 5px;
	padding-bottom: 5px;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 2px;
	padding-right: 2px;
	vertical-align: middle;
}
table.table-bordered > tfoot > tr > th{
	font-size: 0.8vw;
	border:1px solid black;
	padding-top: 0;
	padding-bottom: 0;
	vertical-align: middle;
}
.blink_text {

	animation:1.2s blinker linear infinite;
	-webkit-animation:1.2s blinker linear infinite;
	-moz-animation:1.2s blinker linear infinite;

	color: yellow;
}

@-moz-keyframes blinker {  
	50% { opacity: 0.7; }
	100% { opacity: 1.0; }
}

@-webkit-keyframes blinker {
	50% { opacity: 0.7; }
	100% { opacity: 1.0; }
}

@keyframes blinker {  
	50% { opacity: 0.7; }
	100% { opacity: 1.0; }
}

.fc-event {
	font-size: 1vw;
	cursor: pointer;
}

.fc-event-time, .fc-event-title {
	padding: 0 1px;
	white-space: nowrap;
}

.fc-title {
	white-space: normal;
}
#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		@if(Auth::user()->role_code == 'GA' || Auth::user()->role_code == 'MIS')
		<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="modalLog();"><i class="fa fa-list"></i> Log</button>
		<button class="btn btn-warning pull-right" style="margin-left: 5px; width: 10%;" onclick="modalResume();"><i class="fa fa-list"></i> Resume</button>
		<a href="{{ url("index/ga_control/bento_approve") }}" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-check"></i> Confirm (<span style="font-weight: bold;" id="countConfirm">0</span>)</a>
		@endif
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="{{ $location }}">
		<input type="hidden" id="employee_list" value="{{ $employees }}">
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header">
					<center>
						{{-- <span style="font-weight: bold; font-size: 1.1vw;">*Note 備考:</span><br> --}}
						<span style="background-color: yellow; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Waiting 待機中&nbsp;&nbsp;</span>
						<span style="background-color: #ccff90; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Approved 承認済み&nbsp;&nbsp;</span>
						<span style="background-color: #ff6090; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Rejected 却下&nbsp;&nbsp;</span>
						<span style="background-color: black; color: white; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Cancelled 取消済み&nbsp;&nbsp;</span>
					{{-- 	<span style="background-color: #ffee58; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Revise 1 改訂１&nbsp;&nbsp;</span>
					<span style="background-color: #29b6f6; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Revise 2 改訂２以降&nbsp;&nbsp;</span> --}}
				</center>
			</div>
			<div class="box-body no-padding">
				<div id="calendar"></div>
			</div>
		</div>
	</div>
</div>
</section>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Create Your Order<br>予約を作成
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="nowDate" value="{{ date("Y-m-d") }}">
						<input type="hidden" id="lastDate" value="{{ date("Y-m-t") }}">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ordered By<br>予約者<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Charged To<br>請求先<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addCharge" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addChargeName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Date<br>日付<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="Select Date" onchange="checkServing(value)">
									<span class="help-block" id="checkDate"></span>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ordered For<br>予約対象者<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<select class="form-control select2" name="addEmployee" id="addEmployee" data-placeholder="Select Employee" style="width: 100%;" onchange="checkCharge(value)">
										<option></option>
										@foreach($employees as $employee)
										@if($employee->employee_id == Auth::user()->username)
										<option value="{{ $employee->employee_id }}_{{ $employee->name }}_{{ $employee->grade_code }}" selected>{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@else
										<option value="{{ $employee->employee_id }}_{{ $employee->name }}_{{ $employee->grade_code }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<a class="btn btn-primary pull-right" id="addCartBtn" onclick="addCart('param')">Add To<br>カートに追加 <i class="fa fa-shopping-cart"></i></a>
						</div>
					</form>
					<div class="col-xs-12" style="padding-top: 10px;">
						<div class="row">
							<span style="font-weight: bold; font-size: 1.2vw;"><i class="fa fa-shopping-cart"></i> Cart List</span>
							<table class="table table-hover table-bordered table-striped" id="tableOrder">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">ID</th>
										<th style="width: 5%;">Name</th>
										<th style="width: 1%;">Date</th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableOrderBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total: </th>
										<th id="countTotal"></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back<br>戻る</button>
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmOrder()">CONFIRM<br>確認 <i class="fa fa-shopping-cart"></i></button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Order Resume <span id="modalDetailTitle"></span></h3>
					<table class="table table-hover table-bordered table-striped">
						<thead>
							<tr>
								<th style="width: 1%;">Approved YMPI</th>
								<th style="width: 1%;">Approved YEMI</th>
								<th style="width: 1%;">Waiting</th>
								<th style="width: 1%;">Rejected</th>
								<th style="width: 1%;">Cancelled</th>
							</tr>
							<tr>
								<th id="countApprovedYMPI" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countApprovedYEMI" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countWaiting" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countRejected" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countCancelled" style="font-weight: bold; font-size: 2vw;"></th>
							</tr>
						</thead>
					</table>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table class="table table-hover table-bordered table-striped" id="tableDetailApproved">
						<thead style="background-color: #f39c12;">
							<tr>
								<th style="width: 1%;">Order By ID</th>
								<th style="width: 5%;">Order By Name</th>
								<th style="width: 1%;">Order For ID</th>
								<th style="width: 5%;">Order For Name</th>
								<th style="width: 1%;">Status</th>
							</tr>
						</thead>
						<tbody id="tableDetailApprovedBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Edit Your Order 予約を変更</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="editID" value="">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ordered By<br>予約者<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Charged To<br>請求先<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editCharge" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editChargeName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Date<br>日付<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date" onchange="checkServing(value)">
									<span class="help-block" id="checkDate2"></span>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ordered For<br>予約対象者<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<select class="form-control select3" name="editEmployee" id="editEmployee" data-placeholder="Select Employee" style="width: 100%;" onchange="checkCharge(value)">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}_{{ $employee->name }}_{{ $employee->grade_code }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</form>
					<div class="col-xs-4" style="padding-left: 0; padding-right: 0;">
						<button class="btn btn-danger" id="cancelOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 100%;" onclick="cancelOrder()">CANCEL ORDER<br>注文取消し
						</button>
					</div>
					<div class="col-xs-3" style="padding-right: 0;">
						<button class="btn btn-warning" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 100%;">Back<br>戻る</button>
					</div>
					<div class="col-xs-5" style="padding-right: 0;">
						<button class="btn btn-success" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 100%;" onclick="editOrder()">CONFIRM<br>確認</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMenu">
	<div class="modal-dialog modal-md" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header" id="modalMenuBody">
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUploadMenu">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Menu</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="form-group">
							<label for="" class="col-sm-4 control-label" style="padding-top: 0;">Period<span class="text-red"> :</span></label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="menuDate" name="menuDate" placeholder="Select Date" disabled>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-4 control-label" style="padding-top: 0;">Menu Name<span class="text-red"> :</span></label>
							<div class="col-sm-7">
								<input type="text" class="form-control" name="menuName" id="menuName" placeholder="Enter Menu Name">
							</div>					
						</div>
						<div class="form-group">
							<label for="" class="col-sm-4 control-label" style="padding-top: 0;">Quota<span class="text-red"> :</span></label>
							<div class="col-sm-7">
								<input type="number" class="form-control" name="menuQuota" id="menuQuota" placeholder="Enter Quantity">
							</div>					
						</div>
					</form>
					<button class="btn btn-success pull-right" id="editOrderBtn" style="font-weight: bold;" onclick="uploadMenu()">Save</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalResume">
	<div class="modal-dialog modal-lg" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Resume Report</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label" style="text-align: right;">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="resumeDate" name="resumeDate" placeholder="Select Date">
							<span class="help-block" id="checkDate2"></span>
						</div>
						<div class="col-sm-3">
							<button onclick="fetchResume()" class="btn btn-success">Search</button>
						</div>
					</div>
					<div class="col-xs-12">
						<table class="table table-hover table-bordered table-striped" id="tableResume">
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalLog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00c0ef; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Log Report</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label>Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="logFrom">
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="logTo">
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label>Status</label>
							<select class="form-control select4" multiple="multiple" id='logStatus' data-placeholder="Select Status" style="width: 100%;">
								<option value=""></option>
								<option value="Waiting">Waiting</option>
								<option value="Approved">Approved</option>
								<option value="Rejected">Rejected</option>
								<option value="Cancelled">Cancelled</option>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<button id="search" onclick="fetchLog()" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
						</div>
					</div>
					<div class="col-xs-12">
						<table class="table table-hover table-bordered table-striped" id="tableLog">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Due Date</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody id="tableLogBody">
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
{{-- <script src="{{ url("bower_components/jquery-ui/jquery-ui.min.js")}}"></script> --}}
<script src="{{ url("bower_components/moment/moment.js")}}"></script>
<script src="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		// var h = $('#boxQuota').height();
		// $('#boxMenu').css('height', h);

		var date = new Date();
		date.setDate(date.getDate()+1);

		$('#addDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			// todayHighlight: true,
			startDate: date	
		});
		$('#editDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			// todayHighlight: true,
			startDate: date	
		});
		$('#logFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});
		$('#logTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});
		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
		});
		$('#resumeDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
		});
		$('.select2').select2();
		$('#dateOk').hide();
		$('#dateError').hide();

		fetchCount();	
		fetchOrderList();
		setInterval(fetchCount, 30*1000);
		setInterval(fetchOrderList, 60*10*1000);
	});	

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#modalEdit')
		});
	})

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#modalLog')
		});
	})

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = [];
	var count = 0;
	var quota_left = 0;

	function fetchCount(){
		$.get('{{ url("fetch/ga_control/bento_order_count") }}', function(result, status, xhr){
			if(result.status){
				$('#countConfirm').text(result.count);
			}
			else{
				return false;
			}
		});
	}

	function formatDate(date) {
		var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}

	function checkCharge(val){
		str = val.split("_");
		employee_id = str[0];
		employee_name = str[1];
		grade_code = str[2];

		if(grade_code == 'J0-'){
			$('#addCharge').val(employee_id);
			$('#addChargeName').val(employee_name);
			$('#editCharge').val(employee_id);
			$('#editChargeName').val(employee_name);
		}
		else{
			$('#addCharge').val($('#addUser').val());
			$('#addChargeName').val($('#addUserName').val());
			$('#editCharge').val($('#addUser').val());
			$('#editChargeName').val($('#addUserName').val());
		}
	}

	function modalLog(){
		$('#modalLog').modal('show');
	}

	function modalResume(){
		$('#modalResume').modal('show');
	}

	function fetchResume(){
		var date = $('#resumeDate').val();
		var data = {
			resume:date
		}
		$.get('{{ url("fetch/ga_control/bento_order_list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableResume').html("");
				var tableResume = "";
				var calendars = result.calendars;

				tableResume += '<thead>';
				tableResume += '<tr>';
				tableResume += '<th style="width: 1%; font-size:0.8vw;">ID</th>';
				tableResume += '<th style="width: 5%; font-size:0.8vw;">Name</th>';

				var res = [];

				$.each(result.bentos, function(key, value){
					if($.inArray(value.employee_id+'_'+value.employee_name, res) === -1){
						res.push(value.employee_id+'_'+value.employee_name);
					}
				});


				$.each(result.calendars, function(key, value){
					if(value.remark == 'H'){
						tableResume += '<th style="width: 0.1%; font-size:0.8vw; background-color: rgba(80,80,80,0.5)">'+value.header+'</th>';
					}
					else{
						tableResume += '<th style="width: 0.1%; font-size:0.8vw;">'+value.header+'</th>';						
					}
				});

				tableResume += '<th style="width: 1%; font-size:0.8vw;">Qty</th>';
				tableResume += '<th style="width: 1%; font-size:0.8vw;">Amt</th>';
				tableResume += '</tr>';
				tableResume += '</thead>';
				tableResume += '<tbody>';
				var insert = false;
				var cnt = 0;

				for (var i = 0; i < res.length; i++) {
					var str = res[i].split('_');
					tableResume += '<tr>';
					tableResume += '<td>'+str[0]+'</td>';
					tableResume += '<td>'+str[1]+'</td>';
					for (var j = 0; j < calendars.length; j++) {
						insert = false;
						$.each(result.bentos, function(key, value){
							if(value.due_date == calendars[j].week_date && str[0] == value.employee_id){
								tableResume += '<td style="text-align: center; background-color: #ccff90;">'+value.qty+'</td>';
								insert = true;
								cnt += value.qty;
							}
						});
						if(insert == false){
							if(calendars[j].remark == 'H'){
								tableResume += '<td style="text-align: center; background-color: rgba(80,80,80,0.5);"></td>';
							}
							else{
								tableResume += '<td style="text-align: center; background-color: #ff6090;"></td>';								
							}
						}
					}
					tableResume += '<td style="text-align: center;">'+cnt+'</td>';
					tableResume += '<td style="text-align: center;">'+cnt*20000+'</td>';
					tableResume += '</tr>';
					cnt = 0;
				}

				tableResume += '</tbody>';

				$('#tableResume').append(tableResume);
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$('#tableResume').html('');
				$('#tableResume').append(tableResume);

				$('#tableResume').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
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
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				openErrorGritter(result.message);
				audio_error.play();
				return false;				
			}
		});
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function fetchLog(){
		$('#loading').show();
		var dateFrom = $('#logFrom').val();
		var dateTo = $('#logTo').val();
		var status = $('#logStatus').val();

		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo,
			status:status
		}

		$.get('{{ url("fetch/ga_control/bento_order_log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableLog').DataTable().clear();
				$('#tableLog').DataTable().destroy();
				$('#tableLogBody').html('');
				var tableLogBody = "";

				$.each(result.bentos, function(key, value){
					tableLogBody += '<tr>';
					tableLogBody += '<td>'+value.employee_id+'</td>';
					tableLogBody += '<td>'+value.employee_name+'</td>';
					tableLogBody += '<td>'+value.due_date+'</td>';
					tableLogBody += '<td>'+value.status+'</td>';
					tableLogBody += '</tr>';
				});
				$('#tableLogBody').append(tableLogBody);

				$('#tableLog').DataTable({
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
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	function titleCase(str) {
		var splitStr = str.toLowerCase().split(' ');
		for (var i = 0; i < splitStr.length; i++) {
			splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
		}
		return splitStr.join(' '); 
	}

	function fetchOrderList(){
		$.get('{{ url("fetch/ga_control/bento_order_list") }}', function(result, status, xhr){
			if(result.status){

				var cal = {};
				var cals = [];

				$.each(result.menus, function(key,value){
					if(value.remark == 'H'){
						cal = {
							title: 'Libur 休日',
							start: Date.parse(value.due_date),
							allDay: true,
							backgroundColor:  'white',
							textColor: '#ff1744',
							borderColor: 'black'
						}
						cals.push(cal);	
					}
					else if(value.menu == null){
						cal = {
							title: 'No Menu メニューがない',
							start: Date.parse(value.due_date),
							allDay: true,
							backgroundColor:  'white',
							textColor: '#ff1744',
							borderColor: 'black'
						}
						cals.push(cal);	
					}
					else{
						cal = {
							title: "#"+titleCase(value.menu)+" ("+value.serving_ordered+"/"+value.serving_quota+")",
							start: Date.parse(value.due_date),
							allDay: true,
							backgroundColor:  'rgb(126,86,134)',
							textColor: 'white',
							borderColor: 'black'
						}
						cals.push(cal);
					}
				});

				var bg = "";
				var tx = "";

				$.each(result.unconfirmed, function(key, value){
					if(value.status == 'Approved'){
						bg = '#ccff90';
						tx = 'black';
					}
					else if(value.status == 'Rejected'){
						bg = '#ff6090';
						tx = 'black';
					}
					else if(value.status == 'Waiting'){
						bg = 'yellow';
						tx = 'black';
					}
					else if(value.status == 'Cancelled'){
						bg = 'black';
						tx = 'white';
					}
					cal = {
						title: value.employee_name,
						start: Date.parse(value.due_date),
						allDay: true,
						backgroundColor: bg,
						textColor: tx,
						borderColor: 'black'
					}
					cals.push(cal);
				});

				$('#calendar').fullCalendar( 'removeEvents' );
				$('#calendar').fullCalendar( 'addEventSource', cals);

				$(function () {			
					$('#calendar').fullCalendar({
						height: 'auto',
						header    : {
							left  : 'prev,next today',
							center: 'title',
							right : 'month,agendaWeek,agendaDay'
						},
						buttonText: {
							today: 'today',
							month: 'month',
							week : 'week',
							day  : 'day'
						},
						eventOrder: 'color,start',
						dayClick: function(date, jsEvent, view) { 
							var d = addZero(formatDate(date));
							openModalCreateCal('new', d, '', '');
						},
						eventClick: function(info) {
							openModalCreateCal('edit', formatDate(info.start), info.title, info.backgroundColor);
						},
						events    : cals,
						editable  : false
					});

					var currColor = '#3c8dbc';
					var colorChooser = $('#color-chooser-btn');
					$('#color-chooser > li > a').click(function (e) {
						e.preventDefault();
						currColor = $(this).css('color');
						$('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor });
					});
					$('#add-new-event').click(function (e) {
						e.preventDefault();
						var val = $('#new-event').val();
						if (val.length == 0) {
							return;
						}

						var event = $('<div />');
						event.css({
							'background-color': currColor,
							'border-color'    : currColor,
							'color'           : '#fff'
						}).addClass('external-event');
						event.html(val);
						$('#external-events').prepend(event);

						init_events(event);

						$('#new-event').val('');
					})
				});
			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	function openModalCreateCal(cat, d, id, color){
		if(cat == 'new'){
			$('#addDate').val(d);
			$('#addDate').prop('disabled', true);
			// $("#addEmployee").prop('selectedIndex', 0).change();
			$('#checkDate').html("");
			$('#tableOrderBody').html("")
			employees = [];
			count = 0;
			$('#countTotal').text(count);

			$('#addCartBtn').removeClass('disabled');
			$('#addCartBtn').removeAttr('disabled','disabled');

			$('#editOrderBtn').removeClass('disabled');
			$('#editOrderBtn').removeAttr('disabled','disabled');
			$('#modalCreate').modal('show');
			checkServing(d);
		}
		if(cat == 'edit'){
			if(id.substring(0, 5) == 'Libur'){
				audio_error.play();
				openErrorGritter('Libur 休日');
				return false;	
			}
			else if(id.substring(0, 7) == 'No Menu' || id.substring(0, 1) == '#'){
				if("{{ Auth::user()->role_code }}" == 'MIS' || "{{ Auth::user()->role_code }}" == 'GA'){
					$('#menuDate').val(d);
					$('#menuName').val('');
					$('#menuQuota').val('');
					$('#modalUploadMenu').modal('show');
				}
				else{
					audio_error.play();
					openErrorGritter('You do not have authority.');
					return false;					
				}
			}
			else{
				var data = {
					due_date:d,
					employee_name:id,
					color:color
				}

				$.get('{{ url("fetch/ga_control/bento_order_edit") }}', data, function(result, status, xhr){
					if(result.status){
						$('#addCartBtn').removeClass('disabled');
						$('#addCartBtn').removeAttr('disabled','disabled');

						$('#editOrderBtn').removeClass('disabled');
						$('#editOrderBtn').removeAttr('disabled','disabled');

						$('#cancelOrderBtn').removeClass('disabled');
						$('#cancelOrderBtn').removeAttr('disabled','disabled');

						if(result.bento.status == 'Cancelled' || result.bento.status == 'Rejected'){
							$('#editOrderBtn').addClass('disabled');
							$('#editOrderBtn').attr('disabled','disabled');

							$('#cancelOrderBtn').addClass('disabled');
							$('#cancelOrderBtn').attr('disabled','disabled');
						}

						$('#editID').val(result.bento.id);
						$('#editUser').val(result.bento.order_by);
						$('#editUserName').val(result.bento.order_by_name);
						$('#editCharge').val(result.bento.charge_to);
						$('#editChargeName').val(result.bento.charge_to_name);
						$('#editDate').val(result.bento.due_date);
						var employee_list = JSON.parse($('#employee_list').val());
						$('#editEmployee').html("");
						var editEmployee = "";

						$.each(employee_list, function(key, value){
							editEmployee += '<option></option>';
							if(value.employee_id == result.bento.employee_id){
								editEmployee += '<option value="'+value.employee_id+'_'+value.name+'_'+value.grade_code+'" selected>'+value.employee_id+' - '+value.name+'</option>';
							}
							else{
								editEmployee += '<option value="'+value.employee_id+'_'+value.name+'_'+value.grade_code+'">'+value.employee_id+' - '+value.name+'</option>';				
							}
						});

						$('#editEmployee').append(editEmployee);
						$('#modalEdit').modal('show');
					}
					else{
						alert('Unidentified Error');
						audio_error.play();
						return false;
					}
				});
			}		


			// $('#addCartBtn').removeClass('disabled');
			// $('#addCartBtn').removeAttr('disabled','disabled');

			// $('#editOrderBtn').removeClass('disabled');
			// $('#editOrderBtn').removeAttr('disabled','disabled');

			// $('#editID').val(id);
			// $('#editUser').val(order_by);
			// $('#editUserName').val(order_by_name);
			// $('#editCharge').val(charge_to);
			// $('#editChargeName').val(charge_to_name);
			// $('#editDate').val(due_date);
			// var employee_list = JSON.parse($('#employee_list').val());
			// $('#editEmployee').html("");
			// var editEmployee = "";

			// $.each(employee_list, function(key, value){
			// 	editEmployee += '<option></option>';
			// 	if(value.employee_id == employee_id){
			// 		editEmployee += '<option value="'+value.employee_id+'-'+value.name+'" selected>'+value.employee_id+' - '+value.name+'</option>';
			// 	}
			// 	else{
			// 		editEmployee += '<option value="'+value.employee_id+'-'+value.name+'">'+value.employee_id+' - '+value.name+'</option>';				
			// 	}
			// });

			// $('#editEmployee').append(editEmployee);
			// $('#modalEdit').modal('show');
		}
	}

	function openModalMenu(id){
		$('#modalMenuBody').html("");
		var modalMenuBody = "";

		modalMenuBody = '<center><img class="img-responsive" src="'+id+'"></img></center>';

		$('#modalMenuBody').append(modalMenuBody);
		$('#modalMenu').modal('show');
	}

	function cancelOrder(){
		var location = $('#location').val();
		var id = $('#editID').val();
		var data = {
			id:id,
			status:'cancel',
			location:location
		}
		if(confirm("Are you sure want to cancel this order? この予約を削除しますか。")){
			$.post('{{ url("edit/ga_control/bento_order") }}', data, function(result, status, xhr){
				if(result.status){
					$('#modalEdit').modal('hide');
					audio_ok.play();
					openSuccessGritter(result.message);
					fetchOrderList();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter(result.message);
					return false;				
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function deleteOrder(){
		var id = $('#editID').val();
		var location = $('#location').val();
		var data = {
			id:id,
			status:'delete',
			location:location
		}
		if(confirm("Are you sure want to delete this order? この予約を削除しますか。")){
			$.post('{{ url("edit/ga_control/bento_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter(result.message);
					$('#modalEdit').modal('hide');
					$('#editID').val("");
					$('#editUser').val("");
					$('#editUserName').val("");
					$('#editCharge').val("");
					$('#editChargeName').val("");
					$('#editDate').val("");
					$('#editEmployee').html("");
					fetchOrderList();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter(result.message);
					return false;				
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function uploadMenu(){
		$('#loading').show();
		
		var date = $('#menuDate').val();
		var menu = $('#menuName').val();
		var quota = $('#menuQuota').val();

		var data = {
			date:date,
			menu:menu,
			quota:quota
		}

		$.post('{{ url("input/ga_control/bento_menu") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				openSuccessGritter(result.message);
				fetchOrderList();
				$('#modalUploadMenu').modal('hide');
				$('#loading').hide();

			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter(result.message);
				return false;				
			}
		});	
	}

	function editOrder(){
		$('#loading').show();
		var id = $('#editID').val();
		var order_by = $('#editUser').val();
		var order_by_name = $('#editUserName').val();
		var charge_to = $('#editCharge').val();
		var charge_to_name = $('#editChargeName').val();
		var employee_id = $('#editEmployee').val();
		var due_date = $('#editDate').val();
		var location = $('#location').val();

		var data = {
			id:id,
			status:'edit',
			order_by:order_by,
			order_by_name:order_by_name,
			charge_to:charge_to,
			charge_to_name:charge_to_name,
			employee_id:employee_id,
			due_date:due_date,
			location:location
		}

		if(confirm("Are you sure want to make this order? この予約内容でよろしいですか。")){
			$.post('{{ url("edit/ga_control/bento_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter(result.message);
					$('#modalCreate').modal('hide');
					$('#editID').val("");
					$('#editUser').val("");
					$('#editUserName').val("");
					$('#editCharge').val("");
					$('#editChargeName').val("");
					$('#editDate').val("");
					$('#editEmployee').html("");
					$('#modalEdit').modal('hide');
					fetchOrderList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter(result.message);
					return false;				
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function openModalCreate(){
		$('#addDate').prop('disabled', false);
		$('#modalCreate').modal('show');
		$('#addDate').val("");
		// $("#addEmployee").prop('selectedIndex', 0).change();
		$('#checkDate').html("");

		$('#addCartBtn').removeClass('disabled');
		$('#addCartBtn').removeAttr('disabled','disabled');

		$('#editOrderBtn').removeClass('disabled');
		$('#editOrderBtn').removeAttr('disabled','disabled');
	}

	function addCart(){
		var now_date = $('#nowDate').val();
		var date = $('#addDate').val();

		var str = $('#addEmployee').val();
		var employee_id = str.split("_")[0];
		var employee_name = str.split("_")[1];
		var grade_code = str.split("_")[2];

		if(date == "" || str == ""){
			audio_error.play();
			openErrorGritter('Please Select Date & Employee<br>日付と従業員を選定してください');
			return false;
		}

		if($.inArray(employee_id+'_'+date+'_'+grade_code, employees) != -1){
			audio_error.play();
			openErrorGritter('Employee with selected date already in the cart<br>選定した従業員はカートに入りました');
			return false;
		}

		if(grade_code != 'J0-'){
			if(count+1 > quota_left){
				audio_error.play();
				openErrorGritter('Orders exceeded quota<br>予約数は予約枠を超えています');
				return false;			
			}
		}

		var tableOrder = "";

		tableOrder += "<tr id='"+employee_id+'_'+date+'_'+grade_code+"'>";
		tableOrder += "<td>"+employee_id+"</td>";
		tableOrder += "<td>"+employee_name+"</td>";
		tableOrder += "<td>"+date+"</td>";
		tableOrder += "<td><a href='javascript:void(0)' onclick='remOrder(id)' id='"+employee_id+'_'+date+'_'+grade_code+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableOrder += "</tr>";

		employees.push(employee_id+'_'+date+'_'+grade_code);
		count += 1;

		$('#countTotal').text(count);
		$('#tableOrderBody').append(tableOrder);

	}

	function confirmOrder(){
		$('#loading').show();
		var order_by = $('#addUser').val();
		var charge_to = $('#addCharge').val();
		var order_list = employees;
		var location = $('#location').val();

		if(order_list.length <= 0){
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Please create your order list<br>予約内容を記入してください');
			return false;
		}

		if(confirm("Are you sure want to make this order? この予約内容でよろしいですか。")){

			var data = {
				order_by:order_by,
				charge_to:charge_to,
				order_list:order_list,
				location:location
			}

			$.post('{{ url("input/ga_control/bento_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter(result.message);
					$('#modalCreate').modal('hide');
					$('#addDate').val("");
					$("#addEmployee").prop('selectedIndex', 0).change();
					$('#countTotal').text(count);
					$('#tableOrderBody').html("");
					$('#checkDate').html("");
					employees = [];
					count = 0;
					quota_left = 0;
					fetchOrderList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter(result.message);
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function checkServing(val){
		var data = {
			due_date:val
		}
		$.get('{{ url("fetch/ga_control/bento_quota") }}', data, function(result, status, xhr){
			if(result.status){
				if($('#location').val() != 'YEMI'){
					quota_left = 0
					if(result.bento_quota != null){
						quota_left = result.bento_quota.serving_quota-result.bento_quota.serving_ordered;				
					}

					if(quota_left > 0){
						// $('#addCartBtn').removeClass('disabled');
						// $('#addCartBtn').removeAttr('disabled','disabled');

						// $('#editOrderBtn').removeClass('disabled');
						// $('#editOrderBtn').removeAttr('disabled','disabled');

						$('#checkDate').html("");
						$('#checkDate').append('<span style="color:green; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');

						$('#checkDate2').html("");
						$('#checkDate2').append('<span style="color:green; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');
					}
					else{
						// $('#addCartBtn').addClass('disabled');
						// $('#addCartBtn').attr('disabled','disabled');

						// $('#editOrderBtn').addClass('disabled');
						// $('#editOrderBtn').attr('disabled','disabled');

						$('#checkDate').html("");
						$('#checkDate').append('<span style="color:red; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');

						$('#checkDate2').html("");
						$('#checkDate2').append('<span style="color:red; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');
					}	
				}
			}
			else{
				audio_error.play();
				openErrorGritter(result.message);
				return false;
			}
		})
	}

	function remOrder(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotal').text(count);
		$('#'+id).remove();	
	}

	function truncate(str, n){
		return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
	};

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection