@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		font-size: 0.93vw;
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
	#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-danger pull-right" style="margin-left: 5px; width: 20%;" onclick="modalUploadMenu();"><i class="fa fa-upload"></i> Upload Menu</button>
		<button class="btn btn-warning pull-right" style="margin-left: 5px; width: 20%;" onclick="modalResume();"><i class="fa fa-list"></i> Resume Report</button>
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
		<div class="col-xs-6">
			<div class="box box-danger">				
				<div class="box-header">
					<h3 class="box-title">Quota Information <span class="text-purple">予約枠の情報</span></h3>
					<button class="btn btn-info pull-right" onclick="fetchOrderList()"><i class="fa fa-refresh"></i> Refresh</button>
				</div>
				<div class="box-body">
					<div id="container" style="height: 150px;">

					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="box box-danger">				
				<div class="box-header">
					<h3 class="box-title">Menu Information <span class="text-purple">メニューの情報</span></h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-6">
							<table class="table table-hover table-bordered table-striped" id="tableInformation">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 2%;">Period</th>
										<th style="width: 1%;">Info</th>
									</tr>
								</thead>
								<tbody>
									@foreach($bentos as $bento)
									<tr>
										<td style="font-weight: bold; font-size: 1.3vw;">{{ $bento->period }}</td>
										<td style="font-weight: bold; font-size: 1.3vw;"><a class="fa fa-image" id="{{ url($bento->menu_image) }}" onclick="openModalMenu(id)"></a></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-xs-6">
							<button class="btn btn-success" style="font-weight: bold; font-size: 1.5vw; height: 150px; width: 100%;" onclick="openModalCreate()">
								<span class="blink_text"><i class="glyphicon glyphicon-cutlery"></i><br>Click Here To Order<br>予約するにはここを押す</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-info">				
				<div class="box-header">
					<h3 class="box-title">Unconfirmed Order List <span class="text-purple">未確認の予約</span></h3>
				</div>
				<div class="box-body">
					<table class="table table-hover table-bordered table-striped" id="tableOrderList">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">Order By ID<br>予約者</th>
								<th style="width: 3%;">Order By Name<br>予約者</th>
								{{-- <th style="width: 3%;">Charged To<br>請求先</th> --}}
								<th style="width: 1%;">Date<br>日付</th>
								<th style="width: 1%;">Order For ID<br>予約対象者</th>
								<th style="width: 3%;">Order For Name<br>予約対象者</th>
								<th style="width: 3%;">Dept<br>部門</th>
								<th style="width: 2%;">Sect</th>
								<th style="width: 1%;">Status</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="tableOrderListBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="box box-success">				
				<div class="box-header">
					<h3 class="box-title">History Order <span class="text-purple">確認済の予約</span></h3>
				</div>
				<div class="box-body">
					<table class="table table-hover table-bordered table-striped" id="tableHistory">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">Order By ID<br>予約者</th>
								<th style="width: 3%;">Order By Name<br>予約者</th>
								{{-- <th style="width: 3%;">Charged To<br>請求先</th> --}}
								<th style="width: 1%;">Date<br>日付</th>
								<th style="width: 1%;">Order For ID<br>予約対象者</th>
								<th style="width: 3%;">Order For Name<br>予約対象者</th>
								<th style="width: 3%;">Dept<br>部門</th>
								<th style="width: 2%;">Sect</th>
								<th style="width: 1%;">Status</th>
								<th style="width: 3%;">GA</th>
							</tr>
						</thead>
						<tbody id="tableHistoryBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Create Your Order<br>予約を作成</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="nowDate" value="{{ date("Y-m-d") }}">
						<input type="hidden" id="lastDate" value="{{ date("Y-m-t") }}">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Ordered By 予約者<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Charged To 請求先<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addCharge" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addChargeName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date 日付<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="Select Date" onchange="checkServing(value)">
									<span class="help-block" id="checkDate"></span>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Ordered For 予約対象者<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<select class="form-control select2" name="addEmployee" id="addEmployee" data-placeholder="Select Employee" style="width: 100%;">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}-{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<a class="btn btn-primary pull-right" id="addCartBtn" onclick="addCart('param')">Add To <i class="fa fa-shopping-cart"></i></a>
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
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="confirmOrder()">CONFIRM ORDER 確認 <i class="fa fa-shopping-cart"></i></button>
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
							</tr>
							<tr>
								<th id="countApprovedYMPI" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countApprovedYEMI" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countWaiting" style="font-weight: bold; font-size: 2vw;"></th>
								<th id="countRejected" style="font-weight: bold; font-size: 2vw;"></th>
							</tr>
						</thead>
					</table>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<h4 style="background-color: #ccff90; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Approved</h4>
					<table class="table table-hover table-bordered table-striped" id="tableDetailApproved">
						<thead style="background-color: #ccff90;">
							<tr>
								<th style="width: 1%;">Order By ID</th>
								<th style="width: 5%;">Order By Name</th>
								<th style="width: 1%;">Order For ID</th>
								<th style="width: 5%;">Order For Name</th>
							</tr>
						</thead>
						<tbody id="tableDetailApprovedBody">
						</tbody>
					</table>
					<h4 style="background-color: yellow; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Waiting</h4>
					<table class="table table-hover table-bordered table-striped" id="tableDetailWaiting">
						<thead style="background-color: yellow;">
							<tr>
								<th style="width: 1%;">Order By ID</th>
								<th style="width: 5%;">Order By Name</th>
								<th style="width: 1%;">Order For ID</th>
								<th style="width: 5%;">Order For Name</th>
							</tr>
						</thead>
						<tbody id="tableDetailWaitingBody">
						</tbody>
					</table>
					<h4 style="background-color: #ff6090; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Rejected</h4>
					<table class="table table-hover table-bordered table-striped" id="tableDetailRejected">
						<thead style="background-color: #ff6090;">
							<tr>
								<th style="width: 1%;">Order By ID</th>
								<th style="width: 5%;">Order By Name</th>
								<th style="width: 1%;">Order For ID</th>
								<th style="width: 5%;">Order For Name</th>
							</tr>
						</thead>
						<tbody id="tableDetailRejectedBody">
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
								<label for="" class="col-sm-3 control-label">Ordered By 予約者<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Charged To 請求先<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editCharge" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editChargeName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date 日付<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date" onchange="checkServing(value)">
									<span class="help-block" id="checkDate2"></span>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Ordered For 予約対象者<span class="text-red"> :</span></label>
								<div class="col-sm-9">
									<select class="form-control select3" name="editEmployee" id="editEmployee" data-placeholder="Select Employee" style="width: 100%;">
										{{-- <option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}-{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach --}}
									</select>
								</div>
							</div>
						</div>
					</form>
					<button class="btn btn-danger pull-left" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="deleteOrder()">DELETE <i class="fa fa-trash"></i></button>
					<button class="btn btn-warning pull-right" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="editOrder()">EDIT <i class="fa fa-shopping-cart"></i></button>
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
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="menuDate" name="menuDate" placeholder="Select Date">
							<span class="help-block" id="checkDate2"></span>
						</div>
					</div>
					<center><input type="file" name="menuFile" id="menuFile"></center>
					<div class="modal-footer" style="margin-top: 10px;">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button onclick="uploadMenu()" class="btn btn-success">Upload</button>
					</div>
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

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		var date = new Date();
		date.setDate(date.getDate()+1);

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			// todayHighlight: true,
			startDate: date	
		});
		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#resumeDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('.select2').select2();
		$('#dateOk').hide();
		$('#dateError').hide();

		fetchOrderList();
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = [];
	var count = 0;
	var quota_left = 0;

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
				alert('Unidentified Error');
				audio_error.play();
				return false;				
			}
		});
	}

	function fetchOrderList(){
		$.get('{{ url("fetch/ga_control/bento_order_list") }}', function(result, status, xhr){
			if(result.status){
				var quota = [];
				var ordered = [];
				var cat = [];
				var percentage = [];

				$.each(result.quotas, function(key, value){
					quota.push(value.serving_quota);
					ordered.push(value.serving_ordered);
					cat.push(value.due_date);
					percentage.push((value.serving_ordered/value.serving_quota)*100);
				});


				Highcharts.chart('container', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: cat,
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						max:100,
						title: {
							text: null
						},
						labels: {
							enabled: false,
							format: '{value}%'
						},
						opposite: true
					}],
					legend: {
						enabled:false
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 1,
							borderColor: 'black',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.category);
									}
								}
							}
						}
					},
					series: [{
						name: 'Quota',
						data: quota,
						color: '#7cb342'

					}, {
						name: 'Order',
						data: ordered,
						color: '#d81b60'
					},{
						name: 'Order %',
						type: 'spline',
						yAxis: 1,
						color: 'red',
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,1)+'%';
							}
						},
						data: percentage

					}]
				});

				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableOrderListBody').html("");
				$('#tableHistoryBody').html("");
				var tableOrderList = "";
				var tableHistory = "";

				$.each(result.unconfirmed, function(key, value){
					if(value.status == 'Waiting For Confirmation'){
						tableOrderList += '<tr>';
						tableOrderList += '<td>'+value.order_by+'</td>';
						tableOrderList += '<td>'+value.order_by_name+'</td>';
						// tableOrderList += '<td>'+value.charge_to+'<br>'+value.charge_to_name+'</td>';
						tableOrderList += '<td>'+value.due_date+'</td>';
						tableOrderList += '<td>'+value.employee_id+'</td>';
						tableOrderList += '<td>'+value.employee_name+'</td>';
						tableOrderList += '<td>'+value.department+'</td>';
						tableOrderList += '<td>'+value.section+'</td>';
						tableOrderList += '<td style="background-color: yellow; font-weight:bold;">'+value.status+'</td>';
						tableOrderList += '<td><button class="btn btn-warning" onclick="openModalEdit(\''+value.id+'\''+','+'\''+value.order_by+'\''+','+'\''+value.order_by_name+'\''+','+'\''+value.charge_to+'\''+','+'\''+value.charge_to_name+'\''+','+'\''+value.due_date+'\''+','+'\''+value.employee_id+'\')" id="'+value.id+'"><i class="fa fa-pencil"></i></button></td>';
						tableOrderList += '</tr>';
					}
					else{
						tableHistory += '<tr>';
						tableHistory += '<td>'+value.order_by+'</td>';
						tableHistory += '<td>'+value.order_by_name+'</td>';
						// tableHistory += '<td>'+value.charge_to+'<br>'+value.charge_to_name+'</td>';
						tableHistory += '<td>'+value.due_date+'</td>';
						tableHistory += '<td>'+value.employee_id+'</td>';
						tableHistory += '<td>'+value.employee_name+'</td>';
						tableHistory += '<td>'+value.department+'</td>';
						tableHistory += '<td>'+value.section+'</td>';
						if(value.status == 'Rejected'){
							tableHistory += '<td style="background-color: #ff6090; font-weight:bold;">'+value.status+'</td>';							
						}
						else{
							tableHistory += '<td style="background-color: #ccff90; font-weight:bold;">'+value.status+'</td>';							
						}
						tableHistory += '<td>'+value.approver_id+'<br>'+value.approver_name+'</td>';
						tableHistory += '</tr>';
					}
				});

				$('#tableOrderListBody').append(tableOrderList);
				$('#tableHistoryBody').append(tableHistory);

				$('#tableHistory').DataTable({
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
			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
}

function fetchDetail(date){
	$('#loading').show();
	var data = {
		date:date
	}
	$.get('{{ url("fetch/ga_control/bento_order_list") }}', data, function(result, status, xhr){
		if(result.status){
			$('#tableDetailApprovedBody').html('');
			$('#tableDetailRejectedBody').html('');
			$('#tableDetailWaitingBody').html('');

			$('#tableDetailApproved').DataTable().clear();
			$('#tableDetailApproved').DataTable().destroy();

			var tableDetailApprovedBody = "";
			var tableDetailRejectedBody = "";
			var tableDetailWaitingBody = "";

			var countApprovedYEMI = 0;
			var countApprovedYMPI = 0;
			var countWaiting = 0;
			var countRejected = 0;

			$.each(result.bentos, function(key, value){
				if(value.status == 'Approved' && value.department != 'YEMI'){
					countApprovedYMPI += 1;
					tableDetailApprovedBody += '<tr>';
					tableDetailApprovedBody += '<td>'+value.order_by+'</td>';
					tableDetailApprovedBody += '<td>'+value.order_by_name+'</td>';
					tableDetailApprovedBody += '<td>'+value.employee_id+'</td>';
					tableDetailApprovedBody += '<td>'+value.employee_name+'</td>';
					tableDetailApprovedBody += '</tr>';
				}
				else if(value.status == 'Approved' && value.department == 'YEMI'){
					countApprovedYEMI += 1;
					tableDetailApprovedBody += '<tr>';
					tableDetailApprovedBody += '<td>'+value.order_by+'</td>';
					tableDetailApprovedBody += '<td>'+value.order_by_name+'</td>';
					tableDetailApprovedBody += '<td>'+value.employee_id+'</td>';
					tableDetailApprovedBody += '<td>'+value.employee_name+'</td>';
					tableDetailApprovedBody += '</tr>';
				}
				else if(value.status == 'Rejected'){
					countRejected += 1;
					tableDetailRejectedBody += '<tr>';
					tableDetailRejectedBody += '<td>'+value.order_by+'</td>';
					tableDetailRejectedBody += '<td>'+value.order_by_name+'</td>';
					tableDetailRejectedBody += '<td>'+value.employee_id+'</td>';
					tableDetailRejectedBody += '<td>'+value.employee_name+'</td>';
					tableDetailRejectedBody += '</tr>';
				}
				else{
					countWaiting += 1;
					tableDetailWaitingBody += '<tr>';
					tableDetailWaitingBody += '<td>'+value.order_by+'</td>';
					tableDetailWaitingBody += '<td>'+value.order_by_name+'</td>';
					tableDetailWaitingBody += '<td>'+value.employee_id+'</td>';
					tableDetailWaitingBody += '<td>'+value.employee_name+'</td>';
					tableDetailWaitingBody += '</tr>';
				}
			});

			$('#tableDetailApprovedBody').append(tableDetailApprovedBody);
			$('#tableDetailRejectedBody').append(tableDetailRejectedBody);
			$('#tableDetailWaitingBody').append(tableDetailWaitingBody);

			$('#countApprovedYEMI').text(countApprovedYEMI);
			$('#countApprovedYMPI').text(countApprovedYMPI);
			$('#countWaiting').text(countWaiting);
			$('#countRejected').text(countRejected);

			$('#tableDetailApproved').DataTable({
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

			$('#modalDetailTitle').text(date);
			$('#modalDetail').modal('show');
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

function openModalMenu(id){
	$('#modalMenuBody').html("");
	var modalMenuBody = "";

	modalMenuBody = '<center><img class="img-responsive" src="'+id+'"></img></center>';

	$('#modalMenuBody').append(modalMenuBody);
	$('#modalMenu').modal('show');
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
		$.post('{{ url("edit/ga_control/bento_order") }}', function(result, status, xhr){
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
			}
			else{
				audio_error.play();
				openErrorGritter(result.message);
				return false;				
			}
		});
	}
	else{
		return false;
	}
}

function modalUploadMenu(){
	$('#modalUploadMenu').modal('show');
}

function uploadMenu(){

	if($('#menuDate').val() == ""){
		openErrorGritter('Error!', 'Please input period');
		audio_error.play();
		return false;	
	}

	var formData = new FormData();
	var newAttachment  = $('#menuFile').prop('files')[0];
	var file = $('#menuFile').val().replace(/C:\\fakepath\\/i, '').split(".");

	formData.append('newAttachment', newAttachment);
	formData.append('menuDate', $("#menuDate").val());

	formData.append('extension', file[1]);
	formData.append('file_name', file[0]);

	$.ajax({
		url:"{{ url('upload/ga_control/bento_menu') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				openSuccessGritter('Success!',data.message);
				audio_ok.play();
				$('#menuDate').val("");
				$('#menuFile').val("");
				$('#modalUploadMenu').modal('hide');
				location.reload(true);
			}else{
				openErrorGritter('Error!',data.message);
				audio_error.play();
			}

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
			audio_error.play();
			openErrorGritter(result.message);
			return false;				
		}
	});
}

function openModalEdit(id, order_by, order_by_name, charge_to, charge_to_name, due_date, employee_id){
	$('#addCartBtn').removeClass('disabled');
	$('#addCartBtn').removeAttr('disabled','disabled');

	$('#editOrderBtn').removeClass('disabled');
	$('#editOrderBtn').removeAttr('disabled','disabled');

	$('#editID').val(id);
	$('#editUser').val(order_by);
	$('#editUserName').val(order_by_name);
	$('#editCharge').val(charge_to);
	$('#editChargeName').val(charge_to_name);
	$('#editDate').val(due_date);
	var employee_list = JSON.parse($('#employee_list').val());
	$('#editEmployee').html("");
	var editEmployee = "";

	$.each(employee_list, function(key, value){
		editEmployee += '<option></option>';
		if(value.employee_id == employee_id){
			editEmployee += '<option value="'+value.employee_id+'-'+value.name+'" selected>'+value.employee_id+' - '+value.name+'</option>';
		}
		else{
			editEmployee += '<option value="'+value.employee_id+'-'+value.name+'">'+value.employee_id+' - '+value.name+'</option>';				
		}
	});

	$('#editEmployee').append(editEmployee);
	$('#modalEdit').modal('show');
}

function openModalCreate(){
	$('#modalCreate').modal('show');
	$('#addDate').val("");
	$("#addEmployee").prop('selectedIndex', 0).change();
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
	var employee_id = str.split("-")[0];
	var employee_name = str.split("-")[1];

	if(date == "" || str == ""){
		audio_error.play();
		openErrorGritter('Please Select Date & Employee<br>日付と従業員を選定してください');
		return false;
	}

	if($.inArray(employee_id+'_'+date, employees) != -1){
		audio_error.play();
		openErrorGritter('Employee with selected date already in the cart<br>選定した従業員はカートに入りました');
		return false;
	}

	if($('#location').val() != 'YEMI'){
		if(count+1 > quota_left){
			audio_error.play();
			openErrorGritter('Orders exceeded quota<br>予約数は予約枠を超えています');
			return false;			
		}
	}

	var tableOrder = "";

	tableOrder += "<tr id='"+employee_id+'_'+date+"'>";
	tableOrder += "<td>"+employee_id+"</td>";
	tableOrder += "<td>"+employee_name+"</td>";
	tableOrder += "<td>"+date+"</td>";
	tableOrder += "<td><a href='javascript:void(0)' onclick='remOrder(id)' id='"+employee_id+'_'+date+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
	tableOrder += "</tr>";

	employees.push(employee_id+'_'+date);
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
					$('#addCartBtn').removeClass('disabled');
					$('#addCartBtn').removeAttr('disabled','disabled');

					$('#editOrderBtn').removeClass('disabled');
					$('#editOrderBtn').removeAttr('disabled','disabled');

					$('#checkDate').html("");
					$('#checkDate').append('<span style="color:green; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');

					$('#checkDate2').html("");
					$('#checkDate2').append('<span style="color:green; font-weight:bold;">'+quota_left+' serving(s) left 人前が残っています</span>');
				}
				else{
					$('#addCartBtn').addClass('disabled');
					$('#addCartBtn').attr('disabled','disabled');

					$('#editOrderBtn').addClass('disabled');
					$('#editOrderBtn').attr('disabled','disabled');

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