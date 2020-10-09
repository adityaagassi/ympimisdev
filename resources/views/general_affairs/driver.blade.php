@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
	}
	tfoot>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
		{{-- <p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading</span>
		</p> --}}
	</div>
	<div class="row">
		<div class="col-xs-1 pull-right" style="padding-left: 0;">
			<button class="btn btn-success pull-right" style="margin-left: 5px; width: 100%;" onclick="modalCreate();">Request Driver</button>
			{{-- <button class="btn btn-success pull-right" style="margin-left: 5px;"><i class="fa fa-plus"></i> </button> --}}
		</div>
		<div class="col-xs-12">
			<div id="container"></div>
		</div>
		<div class="col-xs-5">
			<table class="table table-hover table-bordered table-striped" id="tableRequest">
				<thead style="background-color: rgba(126,86,134); color: white;">
					<tr>
						<th colspan="7">REQUEST LIST (ドライバー予約の一覧)</th>
					</tr>
					<tr>
						<th style="width: 1%;">ID</th>
						<th style="width: 5%;">Purpose</th>
						<th style="width: 1%;">City</th>
						<th style="width: 1%;">From</th>
						<th style="width: 1%;">To</th>
						<th style="width: 2%;">By</th>
						<th style="width: 1%;">#</th>
					</tr>
				</thead>
				<tbody id="tableRequestBody">

				</tbody>
			</table>
		</div>
		<div class="col-xs-6">
			<table class="table table-hover table-bordered table-striped" id="tableDuty">
				<thead style="background-color: #ff851b; color: white;">
					<tr>
						<th colspan="7">DRIVER ON DUTY (使用中のドライバー)</th>
					</tr>
					<tr>
						{{-- <th style="width: 1%;">ID</th> --}}
						<th style="width: 2%;">Name</th>
						<th style="width: 3%;">Purpose</th>
						<th style="width: 1%;">City</th>
						<th style="width: 1%;">From</th>
						<th style="width: 1%;">To</th>
						<th style="width: 2%;">By</th>
					</tr>
				</thead>
				<tbody id="tableDutyBody">
				</tbody>
			</table>
		</div>
		<div class="col-xs-1" style="padding-left: 0;">
			<button data-toggle="modal" class="btn btn-primary" style="width: 100%; margin-bottom: 5px;" onclick="modalCreate('ga');">Add Duty</button>
			<button data-toggle="modal" data-target="#modalImport" class="btn btn-primary" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-upload"></i> Upload Duty</button>
			<a href="{{ url('index/ga_control/driver_log') }}" class="btn btn-info" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-file-text-o"></i> Driver Log</a>
			<button class="btn btn-success" onclick="fetchEditDriver('new')" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-plus"></i> Driver Add</button>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Request Driver</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12" style="padding-bottom: 5px;">
							<div class="form-group" id="createDv">
								<label for="createDriver" class="col-sm-2 control-label">Pilih Driver<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<select class="form-control select4" name="createDriver" id="createDriver" data-placeholder="Pilih Driver" style="width: 50%;">
										<option></option>
										@foreach($driver_lists as $driver_list)
										<option value="{{ $driver_list->driver_id }}">{{ $driver_list->driver_id }} - {{ $driver_list->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="createPurpose" class="col-sm-2 control-label">Keperluan<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 100%" class="form-control" name="createPurpose" id="createPurpose" placeholder="Masukkan keperluan">
								</div>
							</div>
							<div class="form-group">
								<label for="createDestination" class="col-sm-2 control-label">Kota Tujuan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" name="createDestination" id="createDestination" placeholder="Masukkan kota yang akan dituju">
								</div>
							</div>
							<div class="form-group">
								<label for="createStart" class="col-sm-2 control-label">Waktu Berangkat<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="createStart" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">		
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="createStartTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="createEnd" class="col-sm-2 control-label">Waktu Kembali<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="createEnd" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">								
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="createEndTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 1vw;">Tambah Penumpang<span class="text-red">*</span></span>
								<table class="table table-hover table-bordered table-striped" id="tablePassenger">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 4%;">Employee ID</th>
											<th style="width: 1%;">#</th>
										</tr>
									</thead>
									<tbody id="tablePassengerBody">
									</tbody>
								</table>
								<select class="form-control select4" name="addEmployee" id="addEmployee" data-placeholder="Pilih Penumpang" style="width: 70%;">
									<option></option>
									@foreach($employees as $employee)
									<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
									@endforeach
								</select>
								<a class="btn btn-success" style="width: 28%;" onclick="addPassenger()">Tambahkan</a>
							</div>
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 1vw;">Tambah Destinasi<span class="text-red">*</span></span>
								<table class="table table-hover table-bordered table-striped" id="tableDestination">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 7%;">Nama Destinasi</th>
											<th style="width: 1%;">#</th>
										</tr>
									</thead>
									<tbody id="tableDestinationBody">
									</tbody>
								</table>
								<div class="col-xs-8" style="padding-right: 3px;padding-left: 0px">
									<input type="text" class="form-control" id="addDestination" placeholder="Masukkan Destinasi" style="width: 100%">
								</div>
								<div class="col-xs-4" style="padding-left: 0px">
									<a class="btn btn-success" style="width: 110%;" onclick="addDestination();">Tambahkan</a>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-primary pull-right" onclick="createRequest()" style="font-size: 1.5vw; width: 100%; font-weight: bold;" id="btnRequest">CONFIRM</a>
						<a class="btn btn-primary pull-right" onclick="createRequest('ga')" style="font-size: 1.5vw; width: 100%; font-weight: bold;" id="btnRequestGA">CONFIRM</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Driver Request Detail</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="detailID" class="col-sm-2 control-label">ID Request<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 15%" class="form-control" name="detailID" id="detailID" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="detailPurpose" class="col-sm-2 control-label">Keperluan<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 100%" class="form-control" name="detailPurpose" id="detailPurpose" placeholder="Masukkan keperluan">
								</div>
							</div>
							<div class="form-group">
								<label for="detailDestination" class="col-sm-2 control-label">Kota Tujuan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 50%" class="form-control" name="detailDestination" id="detailDestination" placeholder="Masukkan kota yang akan dituju">
								</div>
							</div>
							<div class="form-group">
								<label for="detailRequestedBy" class="col-sm-2 control-label">Diajukan Oleh<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 50%" class="form-control" name="detailRequestedBy" id="detailRequestedBy" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="detailApprovedBy" class="col-sm-2 control-label">Disetujui Oleh<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 50%" class="form-control" name="detailApprovedBy" id="detailApprovedBy" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="detailStart" class="col-sm-2 control-label">Waktu Berangkat<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="detailStart" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">		
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="detailStartTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="detailEnd" class="col-sm-2 control-label">Waktu Kembali<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="detailEnd" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">								
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="detailEndTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="detailDriver" class="col-sm-2 control-label">Pilih Driver<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<select class="form-control select3" name="detailDriver" id="detailDriver" data-placeholder="Pilih Driver" style="width: 50%;">
										
									</select>
								</div>
							</div>
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 1vw;">Penumpang<span class="text-red">*</span></span>
								<table class="table table-hover table-bordered table-striped" id="tableDetailPassenger">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 4%;">Name</th>
										</tr>
									</thead>
									<tbody id="tableDetailPassengerBody">
									</tbody>
								</table>
							</div>
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 1vw;">Destinasi<span class="text-red">*</span></span>
								<table class="table table-hover table-bordered table-striped" id="tableDetailDestination">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 7%;">Nama Destinasi</th>
										</tr>
									</thead>
									<tbody id="tableDetailDestinationBody">
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-success pull-right" onclick="acceptRequest('accept')" style="font-size: 1.2vw; font-weight: bold;" id="acceptButton">Terima</a>
						<a class="btn btn-success pull-right" onclick="acceptRequest('accept')" style="font-size: 1.2vw; font-weight: bold;" id="saveButton">Simpan</a>
						<a class="btn btn-danger pull-left" onclick="acceptRequest('reject')" style="font-size: 1.2vw; font-weight: bold;" id="rejectButton">Tolak</a>
						<a class="btn btn-danger pull-left" onclick="acceptRequest('close')" style="font-size: 1.2vw; font-weight: bold;" id="closeButton">Hapus</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Driver Edit</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="editID" class="col-sm-2 control-label">Driver ID<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 15%" class="form-control" name="editID" id="editID" placeholder="Masukkan ID driver">
								</div>
							</div>
							<div class="form-group">
								<label for="editName" class="col-sm-2 control-label">Nama<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 70%" class="form-control" name="editName" id="editName" placeholder="Masukkan nama driver">
								</div>
							</div>
							<div class="form-group">
								<label for="editNo" class="col-sm-2 control-label">No Telepon<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 40%" class="form-control" name="editNo" id="editNo" placeholder="Masukkan nomor telp">
								</div>
							</div>
							<div class="form-group">
								<label for="editPlat" class="col-sm-2 control-label">No Plat<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 20%" class="form-control" name="editPlat" id="editPlat" placeholder="Masukkan nomor plat">
								</div>
							</div>
							<div class="form-group">
								<label for="editCar" class="col-sm-2 control-label">Kendaraan<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 40%" class="form-control" name="editCar" id="editCar" placeholder="Masukkan jenis kendaraan">
								</div>
							</div>
							<div class="form-group">
								<label for="editCategory" class="col-sm-2 control-label">Category<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 40%" class="form-control" name="editCategory" id="editCategory" placeholder="Masukkan category">
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-success pull-right" onclick="editDriver('new')" style="font-size: 1.2vw; font-weight: bold;" id="newDriverButton">Tambahkan</a>
						<a class="btn btn-success pull-right" onclick="editDriver('save')" style="font-size: 1.2vw; font-weight: bold;" id="saveDriverButton">Simpan</a>
						<a class="btn btn-danger pull-left" onclick="editDriver('delete')" style="font-size: 1.2vw; font-weight: bold;" id="deleteDriverButton">Nonaktifkan Driver</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalImport">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Import Duty</span>
				</center>
				Format: [NIK Driver][Purpose][Destination City][From][To]<br>
				Sample: <a href="{{ url('download/manual/import_duty.txt') }}">import_duty.txt</a> Code: #Add
				<hr>
				<form class="form-horizontal" id="importForm" method="post" action="{{ url('import/ga_control/driver_duty') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body table-responsive no-padding" style="min-height: 50px;">
						<center><input type="file" name="duty" id="InputFile" accept="text/plain"></center>
					</div>
					<div class="box-footer">
						<div class="col-xs-12">
							<button id="modalImportButton" type="submit" class="btn btn-success pull-right">Import</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearAll();
		fetchDriver();
		fetchRequest();
		fetchDriverDuty();
		$('#dateFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true	
		});
		setInterval(fetchRequest, 30000);
		setInterval(fetchDriverDuty, 30000);
		setInterval(fetchDriver, 30000);
	});
	
	$('.timepicker').timepicker({
		use24hours: true,
		showInputs: false,
		showMeridian: false,
		minuteStep: 30,
		defaultTime: '00:00',
		timeFormat: 'h:mm'
	});

	var passenger = [];
	var destination = [];

	function clearAll(){
		passenger = [];
		destination = [];
		$('#tablePassengerBody').html('');
		$('#tableDestinationBody').html('');
		$('#createDriver').prop('selectedIndex', 0).change();
		$('#createPurpose').val('');
		$('#createDestination').val('');
		$('#createStart').val('');
		$('#createEnd').val('');
		$('#createStartTime').timepicker({defaultTime: '00:00'});
		$('#createEndTime').timepicker({defaultTime: '00:00'});
		$("#addEmployee").prop('selectedIndex', 0).change();

		$('#tableDetailPassengerBody').html('');
		$('#tableDetailDestinationBody').html('');
		$('#detailID').val('');
		$('#detailPurpose').val('');
		$('#detailDestination').val('');
		$('#detailStart').val('');
		$('#detailEnd').val('');
		$('#detailStartTime').timepicker({defaultTime: '00:00'});
		$('#detailEndTime').timepicker({defaultTime: '00:00'});
		$('#detailRequetedBy').val('');
		$('#detailApprovedBy').val('');
		$("#detailDriver").prop('selectedIndex', 0).change();

		$('#editID').val('');
		$('#editName').val('');
		$('#editNo').val('');
		$('#editPlat').val('');
		$('#editCar').val('');
		$('#editCategory').val('');
	}

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#modalCreate')
		});
	})

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#modalDetail')
		});
	})

	function modalCreate(id){
		clearAll();
		if(id == 'ga'){
			$('#createDv').show();
			$('#btnRequest').hide();
			$('#btnRequestGA').show();
		}
		else{
			$('#createDv').hide();
			$('#btnRequest').show();
			$('#btnRequestGA').hide();			
		}
		$('#modalCreate').modal('show');
	}

	function addPassenger(){
		if($('#addEmployee').val() != ""){
			var employee_id = $('#addEmployee').val();
			tableData = "";

			tableData += "<tr id='rowPassenger"+employee_id+"'>";
			tableData += '<td>'+employee_id+'</td>';
			tableData += "<td><a href='javascript:void(0)' onclick='remPassenger(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableData += '</tr>';
			passenger.push(employee_id);

			$('#tablePassengerBody').append(tableData);

			$("#addEmployee").prop('selectedIndex', 0).change();			
		}
		else{
			openErrorGritter('Error!', 'Pilih penumpang terlebih dahulu');	
		}
	}

	function addDestination(){
		if($('#addDestination').val() != ""){
			var destination_id = $('#addDestination').val();
			tableData = "";

			tableData += "<tr id='rowDestination"+destination_id+"'>";
			tableData += '<td>'+destination_id+'</td>';
			tableData += "<td><a href='javascript:void(0)' onclick='remDestination(id)' id='"+destination_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableData += '</tr>';
			destination.push(destination_id);

			$('#tableDestinationBody').append(tableData);
			$('#addDestination').val('');
		}
		else{
			openErrorGritter('Error!', 'Masukkan destinasi terlebih dahulu');
		}
	}

	function editDriver(cat){
		$('#loading').show();
		var id = $('#editID').val();
		var name = $('#editName').val();
		var no = $('#editNo').val();
		var plat = $('#editPlat').val();
		var car = $('#editCar').val();
		var category = $('#editCategory').val();
		var data = {
			id:id,
			name:name,
			no:no,
			plat:plat,
			car:car,
			category:category,
			cat:cat
		}
		if(id != '' && name != '' && no != "" && plat != "" && car != "" && category != ""){
			$.post('{{ url("edit/ga_control/driver_edit") }}', data, function(result, status, xhr){
				if(result.status){
					$('#modalEdit').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchDriver();
					fetchRequest();
					clearAll();
					$('#loading').hide();
				}
				else{
					openErrorGritter('Error!', result.message);
					$('#loading').hide();
				}
			});
		}
		else{
			openErrorGritter('Error!', 'Data harus lengkap tidak boleh ada yang kosong');
			$('#loading').hide();		
		}

	}

	function fetchDriverDuty(){
		$.get('{{ url("fetch/ga_control/driver_duty") }}', function(result, status, xhr){
			tableData = "";
			$('#tableDutyBody').html('');
			var color = "";
			var no = 1;

			$.each(result.drivers, function(key, value){
				if (no % 2 === 0 ) {
					color = 'style="background-color: #ffd8b7"';
				} else {
					color = 'style="background-color: #fffcb7"';
				}
				tableData += '<tr '+color+'>';
				tableData += '<td style="padding:0;">'+value.driver_name+'</td>';
				tableData += '<td style="padding:0;">'+value.purpose+'</td>';
				tableData += '<td style="padding:0;">'+value.destination_city+'</td>';
				tableData += '<td style="padding:0;">'+value.date_from+'</td>';
				tableData += '<td style="padding:0;">'+value.date_to+'</td>';
				tableData += '<td style="padding:0;">'+value.name+'</td>';
				tableData += '</tr>';
				no ++;
			});

			$('#tableDutyBody').append(tableData);
		});
	}

	function createRequest(id){
		$('#loading').show();
		var purpose = $('#createPurpose').val();
		var destination_city = $('#createDestination').val();
		var start_time = $('#createStart').val()+' '+$('#createStartTime').val();
		var end_time = $('#createEnd').val()+' '+$('#createEndTime').val();
		var driver_id = $('#createDriver').val();

		var data = {
			driver_id:driver_id,
			purpose:purpose,
			destination_city:destination_city,
			start_time:start_time,
			end_time:end_time,
			passenger:passenger,
			destination:destination
		}

		if(id != 'ga'){
			if(purpose != '' && destination_city != '' && $('#createStart').val() != '' && $('#createEnd').val() != '' && passenger.length > 0 && destination.length > 0){
				$.post('{{ url("create/ga_control/driver_request") }}', data, function(result, status, xhr){
					if(result.status){
						$('#modalCreate').modal('hide');
						openSuccessGritter('Success!', result.message);
						fetchDriver();
						fetchRequest();
						clearAll();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!', result.message);
					}
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Semua point form harus diisi');
			}			
		}
		else{
			$.post('{{ url("create/ga_control/driver_duty") }}', data, function(result, status, xhr){
				if(result.status){
					$('#modalCreate').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchDriver();
					fetchRequest();
					fetchDriverDuty();
					clearAll();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function acceptRequest(cat){
		$('#loading').show();
		var id = $('#detailID').val();
		var purpose = $('#detailPurpose').val();
		var destination_city = $('#detailDestination').val();
		var driver_id = $('#detailDriver').val();
		var start_time = $('#detailStart').val()+' '+$('#detailStartTime').val();
		var end_time = $('#detailEnd').val()+' '+$('#detailEndTime').val();
		var data = {
			cat:cat,
			id:id,
			purpose:purpose,
			destination_city:destination_city,
			driver_id:driver_id,
			start_time:start_time,
			end_time:end_time
		}

		if(driver_id == '' && cat == 'accept'){
			openErrorGritter('Error!', 'Pilih driver terlebih dahulu')
		}

		$.post('{{ url("accept/ga_control/driver_request") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				$('#loading').hide();
				$('#modalDetail').modal('hide');
				fetchDriver();
				fetchRequest();
				clearAll();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchRequest(){
		$.get('{{ url("fetch/ga_control/driver_request") }}', function(result, status, xhr){
			if(result.status){
				var tableData = "";
				$('#tableRequestBody').html('');
				var color = "";
				var no = 1;

				$.each(result.requests, function(key, value){
					if (no % 2 === 0 ) {
						color = 'style="background-color: #ffd8b7"';
					} else {
						color = 'style="background-color: #fffcb7"';
					}
					tableData += '<tr '+color+'>';
					tableData += '<td style="padding:0;">'+value.id+'</td>';
					tableData += '<td style="padding:0;">'+value.purpose+'</td>';
					tableData += '<td style="padding:0;">'+value.destination_city+'</td>';
					tableData += '<td style="padding:0;">'+value.date_from+'</td>';
					tableData += '<td style="padding:0;">'+value.date_to+'</td>';
					tableData += '<td style="padding:0;">'+value.requested_by+'</td>';
					tableData += '<td style="padding:0;"><button class="btn btn-success btn-sm" onclick="fetchDetail(\''+value.id+'\',\'new\')"><i class="fa fa-list"></i></button></td>';
					tableData += '</tr>';
					no ++;
				});

				$('#tableRequestBody').append(tableData);
			}
			else{
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}
		});
	}

	function fetchDriver(){
		$.get('{{ url("fetch/ga_control/driver") }}', function(result, status, xhr){
			if(result.status){
				var today = new Date();
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var series = [];
				var drivers = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();

				for (var i = 0; i < result.driver_lists.length; i++) {
					var deal = [];
					var unfilled = true;
					for (var j = 0; j < result.drivers.length; j++) {
						if(result.driver_lists[i].driver_id == result.drivers[j].driver_id){
							unfilled = false;
							if(result.drivers[j].remark == 'requested'){
								var re = 'Menunggu persetujuan atasan';
								var color = 'rgb(255,204,255)';
							}
							else if(result.drivers[j].remark == 'accepted'){
								var re = 'Menunggu persetujuan GA';
								var color = '#ff851b';
							}
							else if(result.drivers[j].remark == 'rejected'){
								var re = 'Ditolak GA';
								var color = 'red';
							}
							else{
								var re = 'Telah dikonfirmasi GA';
								var color = '#00a65a';
							}

							deal.push({
								request_id : result.drivers[j].id,
								destination : result.drivers[j].destination_city,
								requested_by : result.drivers[j].name,
								remark : re,
								color : color,
								from : Date.parse(result.drivers[j].date_from),
								to : Date.parse(result.drivers[j].date_to)
							});
						}
					}
					if(unfilled){
						deal.push({
							request_id : 0,
							destination : 0,
							requested_by : 0
						});
					}

					drivers.push({
						name: result.driver_lists[i].name,
						driver_id: result.driver_lists[i].driver_id,
						phone_no: result.driver_lists[i].phone_no,
						plat_no: result.driver_lists[i].plat_no,
						current: 0,
						deals: deal
					});
				}

				series = drivers.map(function(driver, i) {
					var data = driver.deals.map(function(deal) {
						return {
							id: 'deal-' + i,
							request_id: deal.request_id,
							destination: deal.destination,
							requested_by: deal.requested_by,
							start: deal.from,
							end: deal.to,
							status:deal.remark,
							color:deal.color,
							y: i
						};
					});
					return {
						driver_id: driver.driver_id,
						name: driver.name,
						phone_no: driver.phone_no,
						plat_no: driver.plat_no,
						data: data,
						current: driver.deals[driver.current]
					};
				});

				var chart = Highcharts.ganttChart('container', {
					series: series,
					chart: {
						backgroundColor: null
					},
					title: {
						text: null,
					},
					tooltip: {
						pointFormat: '<span>ID: {point.request_id}</span><br/><span>By: {point.requested_by}</span><br/><span>Dest.: {point.destination}</span><br/><span>From: {point.start:%e %b %Y, %H:%M}</span><br/><span>To: {point.end:%e %b %Y, %H:%M}</span><br/><span>Status: {point.status}</span>'
					},
					xAxis:
					[{
						tickInterval: 1000 * 60 * 60,
						min: today,
						max: today + 1 * day,
						currentDateIndicator:{
							enabled: true,
							color : '#fff',
							label: {
								style: {
									fontSize: '14px',
									color: '#FFB300',
									fontWeight: 'bold'
								}
							}
						},
						scrollbar: {
							enabled: true,
							barBackgroundColor: 'gray',
							barBorderRadius: 7,
							barBorderWidth: 0,
							buttonBackgroundColor: 'gray',
							buttonBorderWidth: 0,
							buttonArrowColor: 'white',
							buttonBorderRadius: 7,
							rifleColor: 'white',
							trackBackgroundColor: '#3C3C3C',
							trackBorderWidth: 1,
							trackBorderColor: 'silver',
							trackBorderRadius: 7
						}
					},{
						tickInterval: 1000 * 60 * 60 * 24
					}],
					yAxis: {
						type: 'category',
						grid: {
							columns: [{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.driver_id;
								})
							},{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.name;
								})
							},{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.phone_no;
								}),
							},{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.plat_no;
								}),
							}]
						}
					},
					plotOptions: {
						gantt: {
							animation: false,
						},
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.request_id);
									}
								}
							}
						}
					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					}
				});

				$('.highcharts-yaxis-labels text').on('click', function () {
					fetchEditDriver(this.textContent);
				});

				$.each(chart.yAxis[0].ticks, function(i, tick) {
					$('.highcharts-yaxis-labels text').hover(function () {
						$(this).css('fill', '#33c570');
						$(this).css('cursor', 'pointer');
					},
					function () {
						$(this).css('cursor', 'pointer');
						$(this).css('fill', 'white');
					});
				});

			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
}

function fetchEditDriver(id){
	$('#loading').show();
	if(id == 'new'){
		clearAll();
		$('#loading').hide();
		$("#editID").prop('disabled', false);
		$("#newDriverButton").show();
		$("#saveDriverButton").hide();
		$("#deleteDriverButton").hide();
		$('#modalEdit').modal('show');
		return false;
	}
	var data = {
		id:id
	}
	$.get('{{ url("fetch/ga_control/driver_edit") }}', data, function(result, status, xhr){
		if(result.status){
			$("#newDriverButton").hide();
			$("#saveDriverButton").show();
			$("#deleteDriverButton").show();
			$("#editID").prop('disabled', true);
			$('#editID').val(result.driver_list.driver_id);
			$('#editName').val(result.driver_list.name);
			$('#editNo').val(result.driver_list.phone_no);
			$('#editPlat').val(result.driver_list.plat_no);
			$('#editCar').val(result.driver_list.car);
			$('#editCategory').val(result.driver_list.category);
			$('#modalEdit').modal('show');
			$('#loading').hide();
		}
		else{
			openErrorGritter('Error!', result.message);
			$('#loading').hide();
		}
	});
}

function fetchDetail(id,cat){
	if(cat == 'new'){
		$('#saveButton').hide();
		$('#closeButton').hide();
		$('#acceptButton').show();
		$('#rejectButton').show();
	}
	else{
		$('#saveButton').show();
		$('#closeButton').show();
		$('#acceptButton').hide();
		$('#rejectButton').hide();	
	}
	$('#loading').show();
	var data = {
		id:id
	}
	$.get('{{ url("fetch/ga_control/driver_detail") }}', data, function(result, status, xhr){
		if(result.status){
			var start_time = result.driver.date_from.split(" ");
			var end_time = result.driver.date_to.split(" ");
			$('#detailID').val(result.driver.id);
			$('#detailPurpose').val(result.driver.purpose);
			$('#detailDestination').val(result.driver.destination_city);
			$('#detailRequestedBy').val(result.driver.request_id+' - '+result.driver.request_name);
			if(result.driver.approve_id != null){
				$('#detailApprovedBy').val(result.driver.approve_id+' - '+result.driver.approve_name);
			}
			else{
				$('#detailApprovedBy').val('(Waiting For Approval)');				
			}
			$('#detailStart').val(start_time[0]);
			$('#detailStartTime').val(start_time[1]);
			$('#detailEnd').val(end_time[0]);
			$('#detailEndTime').val(end_time[1]);

			var driverData = "";
			$('#detailDriver').html('');

			$.each(result.driver_lists, function(key, value){
				driverData += '<option></option>';
				if(value.driver_id == result.driver.driver_id){
					driverData += '<option value="'+value.driver_id+'" selected>'+value.driver_id+' - '+value.name+'</option>';
				}
				else{
					driverData += '<option value="'+value.driver_id+'">'+value.driver_id+' - '+value.name+'</option>';					
				}
			});

			$('#detailDriver').append(driverData);

			$('#modalDetail').modal('show');
			$('#loading').hide();

			var passengerData = "";
			var destinationData = "";

			$('#tableDetailPassengerBody').html("");
			$('#tableDetailDestinationBody').html("");

			$.each(result.passenger_detail, function(key, value){
				passengerData += '<tr>';
				passengerData += '<td>'+value.employee_id+'</td>';
				passengerData += '<td>'+value.name+'</td>';
				passengerData += '</tr>';
			});

			$.each(result.destination_detail, function(key, value){
				destinationData += '<tr>';
				destinationData += '<td>'+value.remark+'</td>';
				destinationData += '</tr>';
			});

			$('#tableDetailPassengerBody').append(passengerData);
			$('#tableDetailDestinationBody').append(destinationData);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});

}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
			]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);

Highcharts.setOptions({
	global: {
		useUTC: true,
		timezoneOffset: -420
	}
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
</script>
@endsection