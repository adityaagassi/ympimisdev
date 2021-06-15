@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
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
	tfoot>tr>td{
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
	table.table-bordered > tfoot > tr > td{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
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
	.fc-content {
	    cursor: pointer;
	}
	.content{
		padding-top: 0px;
		padding-left: 7px;
		padding-right: 7px;
		padding-bottom: 0px;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		
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
	<!-- <div class="col-xs-12" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;padding-top: 0px">
		<span style="font-size: 20px;color: white;width: 100%;" id="periode">Live Cooking Periode {{$monthTitle}}</span>
	</div> -->
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-success">				
				
				<div class="box-body">
					<div class="col-xs-12" style="padding-top: 0px;padding-bottom: 10px">
						<a href="{{url('home')}}" class="btn btn-danger ">
							<i class="fa fa-arrow-left"></i> Back
						</a>
						<?php if ($role == 'GA' || $role == 'MIS'): ?>
							<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="modalUploadMenu();"><i class="fa fa-upload"></i> Upload Menu</button>
							<button class="btn btn-warning pull-right" style="margin-left: 5px; width: 10%;" onclick="modalRandomize();"><i class="fa fa-random"></i> Randomize</button>
							<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalReport();"><i class="fa fa-file-text-o"></i> Report</button>
						<?php endif ?>
					</div>
					<div id="calendar"></div>
				</div>
			</div>
		</div>
	</div>
</section>

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
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Menu</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="menuDate" name="menuDate" placeholder="Select Month">
						</div>
						<div class="col-sm-4" align="left">
							<a class="btn btn-info pull-right" href="{{url('download/ga_control/live_cooking')}}">Example</a>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<input type="file" name="menuFile" id="menuFile">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="uploadMenu()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalRandomize">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Random Live Cooking</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="menuDateRandom" name="menuDateRandom" placeholder="Select Month">
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Date From<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control datepicker" id="dateFromRandom" name="dateFromRandom" placeholder="Select Date From">
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Date To<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control datepicker" id="dateToRandom" name="dateToRandom" placeholder="Select Date To">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="startRandomize()" class="btn btn-success pull-right"><i class="fa fa-random"></i> Start Randomize</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
						<input type="hidden" id="firstDate" value="{{ date("Y-m-01") }}">
						<input type="hidden" id="total_day" value="{{ $total_day }}">
						<input type="hidden" id="month_now" value="{{ $month_now }}">
						<input type="hidden" id="role" value="{{ $role }}">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addUser" value="{{ strtoupper(Auth::user()->username) }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="Select Date">
								</div>
							</div>
							<!-- <a class="btn btn-primary pull-right" id="addCartBtn" onclick="addCart('param')">Add To <i class="fa fa-shopping-cart"></i></a> -->
						</div>
					</form>
					<!-- <div class="col-xs-12" style="padding-top: 10px;">
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
										<td>Total: </td>
										<td id="countTotal"></td>
										<td></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div> -->
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="confirmOrder()">CONFIRM ORDER 確認</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Edit Your Order</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="editID" value="">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date">
								</div>
							</div>
						</div>
					</form>
					<button class="btn btn-danger pull-left" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="deleteOrder()">DELETE <i class="fa fa-trash"></i></button>
					<button class="btn btn-warning pull-right" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="updateOrder()">EDIT <i class="fa fa-shopping-cart"></i></button>
				</div>
			</div>
		</div>
	</div>
</div> -->
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 1000px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleDetail">Live Cooking Detail</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<input type="hidden" id="due_date">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 5%;">Name</th>
								<th style="width: 5%;">Dept</th>
								<th style="width: 5%;">Sect</th>
								<th style="width: 5%;">Group</th>
								<th style="width: 5%;">Sub Group</th>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Menu</th>
								<?php if ($role == 'GA' || $role == 'MIS'): ?>
									<th style="width: 1%;">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody id="bodyTableDetail">
							
						</tbody>
					</table>
					<div class="col-xs-12" id="divEdit">
						<div class="row">
							<form class="form-horizontal">
								<input type="hidden" id="editID" value="">
								<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
									<div class="form-group">
										<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
										<div class="col-sm-9">
											<select class="form-control select4" name="editEmployee" id="editEmployee" data-placeholder="Select Employee" style="width: 100%;">
												<option></option>
												@foreach($employees as $employee)
												<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
										<div class="col-sm-9">
											<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date">
										</div>
									</div>
								</div>
							</form>
							<div class="col-xs-4">
								<button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw;width: 100%" onclick="cancelEdit()"><i class="fa fa-close"></i> CANCEL</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-danger" style="font-weight: bold; font-size: 1.3vw;width: 100%" onclick="deleteOrder()"><i class="fa fa-trash"></i> DELETE</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-warning" style="font-weight: bold; font-size: 1.3vw;width: 100%" onclick="updateOrder()"><i class="fa fa-edit"></i> EDIT</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalReport">
	<div class="modal-dialog modal-lg" style="width: 1000px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleReport">Live Cooking Report</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom" placeholder="Date From">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto"  placeholder="Date To">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-4">
							<div class="form-group pull-right">
								<button id="close" onClick="$('#modalReport').modal('hide')" class="btn btn-danger">Close</button>
								<button id="search" onClick="fetchReport()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
					<table class="table table-hover table-bordered table-striped" id="tableReport">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">No.</th>
								<th style="width: 1%;">ID</th>
								<th style="width: 5%;">Name</th>
								<th style="width: 5%;">Dept</th>
								<th style="width: 5%;">Sect</th>
								<th style="width: 5%;">Group</th>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Menu</th>
								<th style="width: 1%;">Kehadiran</th>
								<th style="width: 2%;">Waktu</th>
							</tr>
						</thead>
						<tbody id="bodyTableReport">
							
						</tbody>
					</table>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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
		var date = new Date();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: date
		});

		$('#datefrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#dateto').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		// $('.datepicker').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });

		$('#menuDateRandom').datepicker({
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

	function modalUploadMenu() {
		$('#modalUploadMenu').modal('show');
	}

	function modalRandomize() {
		$('#menuDateRandom').val('');
		$('#dateFromRandom').val('');
		$('#dateToRandom').val('');
		$('#modalRandomize').modal('show');
	}

	function startRandomize() {
		$('#loading').show();
		if ($('#menuDateRandom').val() == '' || $('#dateFromRandom').val() == '' || $('#dateToRandom').val() == '') {
			openErrorGritter('Error!','Isi Semua Data');
			$('#loading').hide();
		}else{
			var menuDateRandom = $('#menuDateRandom').val();
			var dateFromRandom = $('#dateFromRandom').val();
			var dateToRandom = $('#dateToRandom').val();

			if (dateFromRandom.split('-')[0]+'-'+dateFromRandom.split('-')[1] != menuDateRandom) {
				openErrorGritter('Error!','Tanggal tidak sesuai dengan periode');
				return false;
			}
			if (dateToRandom.split('-')[0]+'-'+dateToRandom.split('-')[1] != menuDateRandom) {
				openErrorGritter('Error!','Tanggal tidak sesuai dengan periode');
				return false;
			}

			var data = {
				menuDateRandom:menuDateRandom,
				dateFromRandom:dateFromRandom,
				dateToRandom:dateToRandom,
			}

			$.get('{{ url("fetch/ga_control/live_cooking_randomize") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success',result.message);
					$('#modalRandomize').modal('hide');
					fetchOrderList();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
					audio_error.play();
					return false;
				}
			});
		}
	}

	function openModalCreate(cat, d, id,color){
		// if(cat == 'new'){
		// 	if ($('#total_day').val() <= 7 && $('#month_now').val() != d.split('-')[0]+'-'+d.split('-')[1] && d > $("#nowDate").val() && id != 'Libur 休日') {
		// 		$('#addDate').val(d);
		// 		$('#addDate').prop('disabled', true);

		// 		$('#addCartBtn').removeClass('disabled');
		// 		$('#addCartBtn').removeAttr('disabled','disabled');

		// 		$('#editOrderBtn').removeClass('disabled');
		// 		$('#editOrderBtn').removeAttr('disabled','disabled');
		// 		$('#modalCreate').modal('show');
		// 	}
		// }
		if(cat == 'detail'){

			if (color === "#d2ff8a" || color === "#ff1744") {
				
			}else{
				// if ($('#role').val() == 'GA' || $('#role').val() == 'MIS') {
					$('#divEdit').hide();
					$('#titleDetail').html('Live Cooking Detail on '+d+'<br>With Menu '+id);
					fetchDetail(d);
					$('#modalDetail').modal('show');
				// }
			}
		}
	}

	function fetchDetail(d) {
		$('#loading').show();
		$('#due_date').val(d);
		$("#bodyTableDetail").html("");
		var bodyDetail = "";

		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();

		var data = {
			due_date:d
		}
		$.get('{{ url("detail/ga_control/live_cooking") }}',data, function(result, status, xhr){
			if(result.status){
				$.each(result.datas, function(key, value){
					bodyDetail += '<tr>';
					bodyDetail += '<td>'+value.employee_id+'</td>';
					bodyDetail += '<td>'+value.name+'</td>';
					bodyDetail += '<td>'+(value.department || "")+'</td>';
					bodyDetail += '<td>'+(value.section || "")+'</td>';
					bodyDetail += '<td>'+(value.group || "")+'</td>';
					bodyDetail += '<td>'+(value.sub_group || "")+'</td>';
					bodyDetail += '<td>'+value.due_date+'</td>';
					bodyDetail += '<td>'+value.menu_name+'</td>';
					if ($('#role').val() == 'GA' || $('#role').val() == 'MIS') {
						if (value.due_date >= value.date_now) {
							bodyDetail += '<td><button class="btn btn-warning" onclick="openModalEdit(\''+value.id_live+'\',\''+value.employee_id+'\',\''+value.due_date+'\')"><i class="fa fa-edit"></i> Edit</button></td>';
						}else{
							bodyDetail += '<td></td>';
						}
					}
					bodyDetail += '</tr>';
				});
				$('#bodyTableDetail').append(bodyDetail);
				$('#tableDetail').DataTable({
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
				$('#tableDetail').show();
				$('#loading').hide();
				$('#divEdit').hide();
			}else{
				$('#loading').hide();
				$('#modalDetail').modal('hide');
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function confirmOrder(){
		var order_by = $('#addUser').val();
		var date = $('#addDate').val();

		if(confirm("Apakah Anda yakin dengan order berikut?")){
			$('#loading').show();
			var data = {
				order_by:order_by,
				date:date
			}

			$.post('{{ url("input/ga_control/live_cooking_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter('Success',result.message);
					$('#modalCreate').modal('hide');
					$('#addDate').val("");
					fetchOrderList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
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
		if($('#menuDate').val() == ""){
			openErrorGritter('Error!', 'Please input period');
			audio_error.play();
			$('#loading').hide();
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
			url:"{{ url('upload/ga_control/live_cooking_menu') }}",
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
					$('#loading').hide();
					fetchOrderList();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function fetchOrderList(){
		$.get('{{ url("fetch/ga_control/live_cooking_order_list") }}', function(result, status, xhr){
			if(result.status){
				var quota = [];
				var ordered = [];
				var cat = [];
				var percentage = [];

				$.each(result.quota, function(key, value){
					quota.push(value.serving_quota);
					ordered.push(value.serving_ordered);
					cat.push(value.due_date);
					percentage.push((value.serving_ordered/value.serving_quota)*100);
				});


				// Highcharts.chart('container', {
				// 	chart: {
				// 		type: 'column',
				// 		backgroundColor	: null
				// 	},
				// 	title: {
				// 		text: null
				// 	},
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	xAxis: {
				// 		tickInterval: 1,
				// 		gridLineWidth: 1,
				// 		categories: cat,
				// 		crosshair: true
				// 	},
				// 	yAxis: [{
				// 		min: 0,
				// 		title: {
				// 			text: null
				// 		}
				// 	}, { 
				// 		min: 0,
				// 		max:100,
				// 		title: {
				// 			text: null
				// 		},
				// 		labels: {
				// 			enabled: false,
				// 			format: '{value}%'
				// 		},
				// 		opposite: true
				// 	}],
				// 	legend: {
				// 		enabled:false
				// 	},
				// 	tooltip: {
				// 		headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				// 		pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				// 		'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
				// 		footerFormat: '</table>',
				// 		shared: true,
				// 		useHTML: true
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			pointPadding: 0.05,
				// 			groupPadding: 0.1,
				// 			borderWidth: 1,
				// 			borderColor: 'black',
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						fetchDetail(this.category);
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},
				// 	series: [{
				// 		name: 'Quota',
				// 		data: quota,
				// 		color: '#7cb342'

				// 	}, {
				// 		name: 'Order',
				// 		data: ordered,
				// 		color: '#d81b60'
				// 	},{
				// 		name: 'Order %',
				// 		type: 'spline',
				// 		yAxis: 1,
				// 		color: 'red',
				// 		dataLabels: {
				// 			enabled: true,
				// 			formatter: function () {
				// 				return Highcharts.numberFormat(this.y,1)+'%';
				// 			}
				// 		},
				// 		data: percentage

				// 	}]
				// });

				// $('#tableOrderList').DataTable().clear();
				// $('#tableOrderList').DataTable().destroy();
				// $('#tableOrderListBody').html("");
				// var tableOrderList = "";
				// var tableHistory = "";

				// $.each(result.resumes, function(key, value){
				// 	if (value.due_date == result.now) {
				// 		var color = "style='background-color:#a8c2ff'";
				// 	}else{
				// 		var color = "";
				// 	}
				// 	tableOrderList += '<tr '+color+'>';
				// 	tableOrderList += '<td>'+value.order_by+'</td>';
				// 	tableOrderList += '<td>'+value.name_by+'</td>';
				// 	tableOrderList += '<td>'+value.due_date+'</td>';
				// 	tableOrderList += '<td>'+value.order_for+'</td>';
				// 	tableOrderList += '<td>'+value.name_for+'</td>';
				// 	tableOrderList += '<td>'+value.department+'</td>';
				// 	tableOrderList += '<td>'+value.section+'</td>';
				// 	tableOrderList += '<td>';
				// 	tableOrderList += '<button class="btn btn-warning" onclick="openModalEdit(\''+value.id_live+'\''+','+'\''+value.order_by+'\''+','+'\''+value.name_by+'\''+','+'\''+value.order_for+'\''+','+'\''+value.name_for+'\''+','+'\''+value.due_date+'\''+','+'\''+value.order_for+'\')" id="'+value.id_live+'"><i class="fa fa-pencil"></i></button>';
				// 	tableOrderList += '</td>';
				// 	tableOrderList += '</tr>';
				// });

				// $('#tableOrderListBody').append(tableOrderList);
				// $('#tableOrderList').DataTable({
				// 	'dom': 'Bfrtip',
				// 	'responsive':true,
				// 	'lengthMenu': [
				// 	[ 10, 25, 50, -1 ],
				// 	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				// 	],
				// 	'buttons': {
				// 		buttons:[
				// 		{
				// 			extend: 'pageLength',
				// 			className: 'btn btn-default',
				// 		},
				// 		{
				// 			extend: 'copy',
				// 			className: 'btn btn-success',
				// 			text: '<i class="fa fa-copy"></i> Copy',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		{
				// 			extend: 'excel',
				// 			className: 'btn btn-info',
				// 			text: '<i class="fa fa-file-excel-o"></i> Excel',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		{
				// 			extend: 'print',
				// 			className: 'btn btn-warning',
				// 			text: '<i class="fa fa-print"></i> Print',
				// 			exportOptions: {
				// 				columns: ':not(.notexport)'
				// 			}
				// 		},
				// 		]
				// 	},
				// 	'paging': true,
				// 	'lengthChange': true,
				// 	'searching': true,
				// 	'ordering': true,
				// 	'order': [],
				// 	'info': true,
				// 	'autoWidth': true,
				// 	"sPaginationType": "full_numbers",
				// 	"bJQueryUI": true,
				// 	"bAutoWidth": false,
				// 	"processing": true
				// });
				var cal = {};
				var cals = [];
				var bg = "";
				var tx = "";

				$.each(result.menus, function(key,value){
					if(value.remark == 'H'){
						cal = {
							title: 'Libur 休日',
							start: Date.parse(value.week_date),
							allDay: true,
							backgroundColor: '#ff1744',
							textColor: 'white',
							borderColor: 'black',
						}
						cals.push(cal);	
					}
					else{
						if (value.menu_name != null) {
							if (value.serving_ordered == value.serving_quota) {
								cal = {
									title: value.menu_name+" ("+value.serving_ordered+"/"+value.serving_quota+")",
									start: Date.parse(value.week_date),
									allDay: true,
									backgroundColor:  '#427bff',
									textColor: 'white',
									borderColor: 'black'
								}
								cals.push(cal);
							}else{
								cal = {
									title: value.menu_name+" ("+value.serving_ordered+"/"+value.serving_quota+")",
									start: Date.parse(value.week_date),
									allDay: true,
									backgroundColor:  'rgb(126,86,134)',
									textColor: 'white',
									borderColor: 'black'
								}
								cals.push(cal);
							}
						}
					}
				});

				$.each(result.resumes, function(key, value){
					cal = {
						title: value.name_for,
						start: Date.parse(value.due_date),
						allDay: true,
						backgroundColor: '#d2ff8a',
						textColor: 'black',
						borderColor: 'black'
					}
					cals.push(cal);
				});

				$(function () {			
					$('#calendar').fullCalendar({
						contentHeight: 600,
						header    : {
							left  : 'prev,next today',
							center: 'title',
							right : 'month,agendaWeek,agendaDay',
						},
						buttonText: {
							today: 'today',
							month: 'month',
							week : 'week',
							day  : 'day'
						},
						eventOrder: 'color,start',
						// dayClick: function(date, jsEvent, view) { 
						// 	var d = addZero(formatDate(date));
						// 	openModalCreate('new', d, '','');
						// },
						eventClick: function(info) {
							openModalCreate('detail', formatDate(info.start), info.title,info.backgroundColor);
						},
						events    : cals,
						editable  : false
					})
					$('#calendar').fullCalendar( 'removeEvents' );
					$('#calendar').fullCalendar( 'addEventSource', cals); 

					var currColor = '#3c8dbc'
					var colorChooser = $('#color-chooser-btn')
					$('#color-chooser > li > a').click(function (e) {
						e.preventDefault()
						currColor = $(this).css('color')
						$('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
					})
					$('#add-new-event').click(function (e) {
						e.preventDefault()
						var val = $('#new-event').val()
						if (val.length == 0) {
							return
						}

						var event = $('<div />')
						event.css({
							'background-color': currColor,
							'border-color'    : currColor,
							'color'           : '#fff'
						}).addClass('external-event')
						event.html(val)
						$('#external-events').prepend(event)

						init_events(event)

						$('#new-event').val('')
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
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

	function openModalEdit(id,employee_id,due_date) {
		$('#editEmployee').val(employee_id).trigger('change.select2');
		$('#editDate').val(due_date).datepicker("setDate", new Date(due_date) );
		$('#editID').val(id);
		$('#divEdit').show();
		$('#tableDetail').hide();
		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();
	}

	function updateOrder() {
		$('#loading').show();
		var editID = $('#editID').val();
		var editEmployee = $('#editEmployee').val();
		var editDate = $('#editDate').val();

		var data = {
			id:editID,
			status:'edit',
			order_by:editEmployee,
			order_for:editEmployee,
			due_date:editDate
		}
		$.post('{{ url("edit/ga_control/live_cooking_order") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				openSuccessGritter('Success',result.message);
				$('#modalCreate').modal('hide');
				$('#editID').val("");
				$('#editEmployee').val("").trigger('change.select2');
				$('#editDate').val("");
				$('#modalEdit').modal('hide');
				cancelEdit();
				fetchOrderList();
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;				
			}
		});
	}

	function deleteOrder(){
		var id = $('#editID').val();
		var data = {
			id:id,
			status:'delete',
		}
		if(confirm("Apakah Anda yakin akan menghapus data?")){
			$.post('{{ url("edit/ga_control/live_cooking_order") }}',data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter('Success',result.message);
					$('#modalEdit').modal('hide');
					$('#editID').val("");
					$('#editDate').val("");
					$('#editEmployee').val("").trigger('change.select2');
					cancelEdit();
					fetchOrderList();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;				
				}
			});
		}
		else{
			return false;
		}
	}

	function cancelEdit() {
		$('#editEmployee').val("").trigger('change.select2');
		$('#editDate').val("");
		$('#editID').val("");
		$('#divEdit').hide();
		$('#tableDetail').show();
		fetchDetail($('#due_date').val());
	}

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#modalDetail')
		});
	});

	function modalReport() {
		fetchReport();
		$('#modalReport').modal('show');
	}

	function fetchReport() {
		$('#loading').show();
		$("#bodyTableReport").html("");
		var bodyDetail = "";

		$('#tableReport').DataTable().clear();
		$('#tableReport').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto
		}
		$.get('{{ url("report/ga_control/live_cooking") }}',data, function(result, status, xhr){
			if(result.status){
				var index = 1;
				$.each(result.datas, function(key, value){
					if (value.attend_date == null) {
						var color = '#ffc7c7';
						var hadir = 'Tidak Hadir';
					}else{
						var color = '#c7ffde';
						var hadir = 'Hadir';
					}
					bodyDetail += '<tr style="background-color:'+color+'">';
					bodyDetail += '<td>'+index+'</td>';
					bodyDetail += '<td>'+value.employee_id+'</td>';
					bodyDetail += '<td>'+value.name+'</td>';
					bodyDetail += '<td>'+(value.department || "")+'</td>';
					bodyDetail += '<td>'+(value.section || "")+'</td>';
					bodyDetail += '<td>'+(value.group || "")+'</td>';
					bodyDetail += '<td>'+value.due_date+'</td>';
					bodyDetail += '<td>'+value.menu_name+'</td>';
					bodyDetail += '<td>'+hadir+'</td>';
					bodyDetail += '<td>'+(value.attend_date || "")+'</td>';
					bodyDetail += '</tr>';
					index++;
				});
				$('#bodyTableReport').append(bodyDetail);
				$('#tableReport').DataTable({
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
				$('#tableReport').show();
				$('#loading').hide();
			}else{
				$('#loading').hide();
				$('#modalReport').modal('hide');
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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