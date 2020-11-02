@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	/*Start CSS Numpad*/
	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	/*End CSS Numpad*/


	#recordTableBody > tr:hover {
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 3px;
		padding-bottom: 3px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.93vw;
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
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
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchRecordTable()" class="btn btn-primary">Search</button>
						</div>
					</div>
					<div class="col-xs-12">
						<table id="employeeTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="font-weight: bold; width: 2%;">ID</th>
									<th style="font-weight: bold; width: 4%;">Nama</th>
									<th style="font-weight: bold; width: 0.5%;">Grade</th>
									<th style="font-weight: bold; width: 4%;">Department</th>
									<th style="font-weight: bold; width: 2%;">Section</th>
									<th style="font-weight: bold; width: 6%;">Alamat</th>
									<th style="font-weight: bold; width: 0.5%;">Zona</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="employee_id" value="{{ $employee->employee_id }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="employee_name" value="{{ $employee->name }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="grade_code" value="{{ $employee->grade_code }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="department" value="{{ $employee->department }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="section" value="{{ $employee->section }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="address" value="{{ $employee->domicile_address }}" readonly></td>
									<td style="padding-top: 10px; padding-bottom: 10px;"><input type="text" style="text-align: center; border: 0; font-weight: bold; width: 100%; font-size: 0.9vw;" id="zona" value="{{ $employee->zona }}" readonly></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-xs-12">
						<center>
							<button class="btn btn-success" style="font-weight: bold; width: 50%; font-size: 1.5vw; margin-bottom: 10px;" onclick="openModalCreate()">
								<i class="fa fa-pencil-square-o"></i> Tambah Laporan <i class="fa fa-pencil-square-o"></i>
							</button>
						</center>
					</div>
					<div class="col-xs-12">
						<span class="pull-right" style="font-weight: bold; font-style: italic; color: purple;">Verifikasi: (1) Belum Diverifikasi; (2) Sudah Diverifikasi HR;</span>
					</div>
					<div class="col-xs-12">
						<table id="recordTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">Tanggal</th>
									<th style="width: 1%">Kehadiran</th>
									<th style="width: 1%">Kendaraan</th>
									<th style="width: 1%">Asal</th>
									<th style="width: 1%">Tujuan</th>
									<th style="width: 1%">Tol (IDR)</th>
									<th style="width: 1%">Jarak (Km)</th>
									<th style="width: 1%">Bensin</th>
									<th style="width: 1%">Total</th>
									<th style="width: 2%">Lampiran</th>
									<th style="width: 1%">Verifikasi</th>
									<th style="width: 1.5%">Hapus</th>
								</tr>
							</thead>
							<tbody id="recordTableBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);" id="recordTableFoot">
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalRecord">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px;" id="modalRecordTitle"></h3></center>
				<div class="row">
					<div class="col-md-11 col-md-offset-2">
						<form class="form-horizontal">
							<input type="hidden" id="newId">
							<div class="form-group">
								<label for="newAttend" class="col-sm-2 control-label">Kehadiran<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<select class="form-control select2" name="newAttend" id="newAttend" data-placeholder="Pilih Kehadiran" style="width: 100%;" onchange="selectAttend(value)">
										<option value=""></option>
										<option value="in">Masuk</option>
										<option value="out">Pulang</option>
										<option value="cuti">Cuti</option>
										<option value="izin">Izin</option>
										<option value="sakit">Sakit</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="newDate" class="col-sm-2 control-label">Tanggal<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control pull-right" id="newDate" name="newDate" value="{{date('Y-m-d')}}">
								</div>
							</div>
							<div class="form-group">
								<label for="newVehicle" class="col-sm-2 control-label">Kendaraan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<select class="form-control select2" name="newVehicle" id="newVehicle" data-placeholder="Pilih Kehadiran" style="width: 100%;" onchange="selectVehicle(value)">
										<option value=""></option>
										<option value="car">Mobil</option>
										<option value="shuttle">Shuttle</option>
										<option value="lainnya">Lainnya</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="newOrigin" class="col-sm-2 control-label">Asal<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="newOrigin" name="newOrigin" placeholder="Asal">
								</div>
							</div>
							<div class="form-group">
								<label for="newDestination" class="col-sm-2 control-label">Tujuan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="newDestination" name="newDestination" placeholder="Tujuan">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Jarak<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="number" style="width: 100%" class="form-control" id="newDistance" name="newDistance" placeholder="Jarak Tempuh">
										<div class="input-group-addon">
											Km
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="newHighwayAmount" class="col-sm-2 control-label">Biaya Tol<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="newHighwayAmount" name="newHighwayAmount" placeholder="Biaya Tol">
								</div>
							</div>
							<div class="form-group">
								<label for="lampiran" class="col-sm-2 control-label">Lampiran</label>
								<div class="col-sm-6">
									<input type="file" onchange="readURL(this);" id="newAttachment">
									{{-- <button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage('#fileData')">Photo</button> --}}
									<img width="150px" id="blah" src="" style="display: none" alt="your image" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-success pull-right" onclick="addRecord()" id="newButton">Tambah</a>
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
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('select').select2({
			minimumResultsForSearch: -1
		});
		$('.timepicker').timepicker({
			use24hours: true,
			defaultTime: '00:00',
			showMeridian: false,
			showInputs: false
		});
		$('#datefrom').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#newDate').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});

		fetchRecordTable();
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').show();
				$('#blah')
				.attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	function buttonImage(idfile) {
		$(idfile).click();
	}

	function deleteRecord(id, check_date, attend_code,vehicle,origin,destination,highway_amount,distance){
		if(confirm("Apakah anda yakin akan menghapus data '"+attend_code.toUpperCase()+"' pada tanggal '"+check_date+"'")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/general/online_transportation") }}', data, function(result, status, xhr){
				if(result.status){
					fetchRecordTable();
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			return false;
		}
	}

	function addRecord(){

		if($('#newAttend').val() != "" && ($('#newAttend').val() == 'in' || $('#newAttend').val() == 'out')){
			var employee_id = $('#employee_id').val();
			var grade = $('#grade_code').val();
			var zona = $('#zona').val();
			var newDate = $('#newDate').val();
			var newAttend = $('#newAttend').val();
			var newDistance = $('#newDistance').val();
			var newOrigin = $('#newOrigin').val();
			var newVehicle = $('#newVehicle').val();
			var newDestination = $('#newDestination').val();
			// var newHighwayBill = $('#newHighwayBill').val();
			var newHighwayAmount = $('#newHighwayAmount').val();
			var newAttachment  = $('#newAttachment').prop('files')[0];
		}
		else if($('#newAttend').val() != ""){
			var employee_id = $('#employee_id').val();
			var grade = $('#grade_code').val();
			var zona = $('#zona').val();
			var newDate = $('#newDate').val();
			var newAttend = $('#newAttend').val();
			var newVehicle = $('#newVehicle').val();
			var newDistance = 0;
			var newOrigin = $('#newOrigin').val();
			var newDestination = $('#newDestination').val();
			// var newHighwayBill = 0;
			var newHighwayAmount = 0;
			var newAttachment  = "";
		}


		var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		var formData = new FormData();
		formData.append('newAttachment', newAttachment);
		formData.append('employee_id', employee_id);
		formData.append('grade', grade);
		formData.append('zona', zona);
		formData.append('newDate', newDate);
		formData.append('newVehicle', newVehicle);
		formData.append('newAttend', newAttend);
		formData.append('newDistance', newDistance);
		formData.append('newOrigin', newOrigin);
		formData.append('newDestination', newDestination);
		// formData.append('newHighwayBill', newHighwayBill);
		formData.append('newHighwayAmount', newHighwayAmount);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/general/online_transportation') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				openSuccessGritter('Success','Input Data Transportation');
				$('#loading').hide();
				$('#modalRecord').modal('hide');
				clearModal();
				fetchRecordTable();
			}
		})
	}

	function downloadAtt(id){
		window.open(id, '_blank');
	}

	function selectAttend(id){
		if(id != "" || id != 'in' || id != 'out'){
			$('#newDate').prop("disabled", false);
			$('#newTime').prop("disabled", true);
			$("#newAttend").prop("disabled", false);
			$("#newOrigin").prop("disabled", true);
			$('#newDestination').prop("disabled", true);
			$('#newVehicle').prop("disabled", true);
			$('#newHighwayAmount').prop("disabled", true);
			$('#newDistance').prop("disabled", true);
			$('#newAttachment').prop("disabled", false);

			$('#newId').val("");
			$('#newDate').val("{{date('Y-m-d')}}");
			$("#newOrigin").val('');
			$('#newDestination').val("");
			$('#newHighwayAmount').val("");
			$('#newDistance').val("");

		}

		if( id != "" && (id == 'in' || id == 'out')){
			$('#newDate').prop("disabled", false);
			$('#newTime').prop("disabled", false);
			$("#newAttend").prop("disabled", false);
			$("#newOrigin").prop("disabled", false);
			$("#newVehicle").prop("disabled", false);
			$('#newDestination').prop("disabled", false);
			$('#newHighwayAmount').prop("disabled", false);
			$('#newDistance').prop("disabled", false);
			$('#newAttachment').prop("disabled", false);
		}

		var data = {
			employee_id:$('#employee_id').val(),
			attend_code:id
		}

		$.get('{{ url("fetch/general/online_transportation_data") }}', data, function(result, status, xhr){
			if (result.status) {
					$('#newVehicle').val(result.datas.vehicle).trigger('change');
					$('#newOrigin').val(result.datas.origin);
					$('#newDestination').val(result.datas.destination);
					$('#newDistance').val(result.datas.distance);
					$('#newHighwayAmount').val(result.datas.highway_amount);
			}else{
				openErrorGritter('Error!','Data Tidak Tersedia');
			}
		});
	}

	function selectVehicle(id){
		if(id !== "" || id !== "car"){
			$('#newOrigin').prop("disabled", true);
			$('#newDestination').prop("disabled", true);
			$('#newHighwayAmount').prop("disabled", true);
			$('#newDistance').prop("disabled", true);
			$('#newAttachment').prop("disabled", false);

			$('#newOrigin').val("");
			$('#newDestination').val("");
			$('#newHighwayAmount').val("");
			$('#newDistance').val("");
		}

		if(id !== "" && (id === 'car' || id === 'lainnya')){
			$('#newVehicle').prop("disabled", false);
			$('#newOrigin').prop("disabled", false);
			$('#newDestination').prop("disabled", false);
			$('#newHighwayAmount').prop("disabled", false);
			$('#newDistance').prop("disabled", false);
			$('#newAttachment').prop("disabled", false);
		}
	}

	function clearModal(){
		$('#newId').val("");
		$('#newDate').val("{{date('Y-m-d')}}");
		$('#newTime').timepicker({defaultTime: '00:00'});
		$("#newAttend").prop('selectedIndex', 0).change();
		$("#newVehicle").prop('selectedIndex', 0).change();
		// $('#newHighwayBill').val("");
		$('#newHighwayAmount').val("");
		$('#newOrigin').val("");
		$('#newDestination').val("");
		$('#newDistance').val("");
		$('#newAttachment').val("");
	}

	function openModalCreate(){
		clearModal();

		$('#newId').prop("disabled", true);
		$('#newDate').prop("disabled", true);
		$('#newTime').prop("disabled", true);
		$("#newAttend").prop("disabled", false);
		$("#newOrigin").prop("disabled", true);
		$('#newDestination').prop("disabled", true);
		// $('#newHighwayBill').prop("disabled", true);
		$('#newHighwayAmount').prop("disabled", true);
		$('#newDistance').prop("disabled", true);
		$('#newAttachment').prop("disabled", true);

		$('#modalRecordTitle').text('Tambah Record');
		$('#editButton').hide();
		$('#deleteButton').hide();
		$('#modalRecord').modal('show');
	}

	function fetchRecordTable(){
		$('#loading').show();
		var date_from = $('#datefrom').val();
		var date_to = $('#dateto').val();
		var data = {
			date_from:date_from,
			date_to:date_to
		}
		$.get('{{ url("fetch/general/online_transportation") }}', data, function(result, status, xhr){
			if(result.status){
				$('#recordTable').DataTable().clear();
				$('#recordTable').DataTable().destroy();
				$('#recordTableBody').html('');
				var recordTable = '';
				$('#recordTableFoot').html('');
				var recordTableFoot = '';
				var grand_hadir = 0;
				var grand_highway = 0;
				var grand_distance = 0;
				var grand_fuel = 0;
				var grand_total = 0;

				$.each(result.transportations, function(key, value){
					var fuel = 0;
					var divider = 0;
					var multiplier = 0;

					var grade = "";

					if(value.grade != null){
						grade = value.grade;
					}

					if(grade.substring(0,1) == 'M'){
						divider = 5;
						multiplier = 7650;
					}
					else if(grade.substring(0,1) == 'L'){
						divider = 7;
						multiplier = 7650;
					}
					else{
						divider = value.distance_total;
						multiplier = 0;
					}

					if(value.vehicle == 'car'){
						if(value.distance_total <= 150){
							fuel = (value.distance_total/divider)*multiplier;
						}
						else{
							fuel = (150/divider)*multiplier;						
						}
					}

					if(value.vehicle == 'lainnya'){
						if($('#zona').val() == '1'){
							fuel = 11000;
						}
						else if($('#zona').val() == '2'){
							fuel = 12400;
						}
						else{
							fuel = 17000;
						}
					}


					var total_amount = fuel+value.highway_amount_total;

					var remark = "";

					if(value.remark == null){
						remark = '(0)';						
					}
					if(value.remark == 0){
						remark = '(1)';
					}
					if(value.remark == 1){
						remark = '(2)';
					}

					var bgcolor = "";

					if(value.h == "H"){
						bgcolor = "background-color: #352c2c; color: white;"
					}

					recordTable += '<tr style="text-align: center; '+bgcolor+'">';	
					recordTable += '<td style="width: 0.1%;">'+value.check_date+'</td>';
					recordTable += '<td style="width: 0.1%;">'+value.attend_code.toUpperCase()+'</td>';
					if (value.vehicle == 'car') {
						recordTable += '<td style="width: 1%;">Mobil</td>';
					}else if(value.vehicle == 'shuttle'){
						recordTable += '<td style="width: 1%;">Shuttle</td>';
					}else if(value.vehicle == 'lainnya'){
						recordTable += '<td style="width: 1%;">Lainnya</td>';
					}else{
						recordTable += '<td style="width: 1%;">'+value.vehicle+'</td>';
					}
					recordTable += '<td style="width: 1%;">('+value.origin_in+') - ('+value.origin_out+')</td>';
					recordTable += '<td style="width: 1%;">('+value.destination_in+') - ('+value.destination_out+')</td>';
					// recordTable += '<td style="width: 2%;">'+value.highway_bill_in+'; '+value.highway_bill_out+';</td>';
					recordTable += '<td style="width: 1%;">'+value.highway_amount_total.toLocaleString()+'</td>';
					recordTable += '<td style="width: 1%;">'+value.distance_total+'</td>';
					recordTable += '<td style="width: 1%;">'+fuel.toLocaleString()+'</td>';
					recordTable += '<td style="width: 1%;">'+total_amount.toLocaleString()+'</td>';
					recordTable += '<td style="width: 2%";>';
					if(value.att_in != '{{ url("files/general_transportation/0") }}'){
						recordTable += '<a href="javascript:void(0)" id="'+ value.att_in +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> in</a>';
					}
					if(value.att_out != '{{ url("files/general_transportation/0") }}'){
						recordTable += '&nbsp;<a href="javascript:void(0)" id="'+ value.att_out +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> out</a>';
					}
					recordTable += '</td>';
					recordTable += '<td style="width: 0.3%;">'+remark+'</td>';
					recordTable += '<td style="width: 1.5%">';
					if(value.attend_code == 'hadir' && value.id_in > 0 && value.remark_in == 0){
						recordTable += '<button onclick="deleteRecord(\''+value.id_in+'\''+','+'\''+value.check_date+'\''+','+'\''+value.attend_code+'\''+','+'\''+value.vehicle+'\''+','+'\''+value.origin_in+'\''+','+'\''+value.destination_in+'\''+','+'\''+value.highway_amount_in+'\''+','+'\''+value.distance_in+'\')" style="width: 45%; padding: 0; margin-right:5px;" class="btn btn-danger">In</button>';
					}
					if(value.attend_code == 'izin' && value.id_in > 0 && value.id_out == 0 && value.remark_in == 0){
						recordTable += '<button onclick="deleteRecord(\''+value.id_in+'\''+','+'\''+value.check_date+'\''+','+'\''+value.attend_code+'\''+','+'\''+value.vehicle+'\''+','+'\''+value.origin_in+'\''+','+'\''+value.destination_in+'\''+','+'\''+value.highway_amount_in+'\''+','+'\''+value.distance_in+'\')" style="width: 45%; padding: 0; margin-right:5px;" class="btn btn-danger">In</button>';
					}
					if(value.attend_code == 'hadir' && value.id_out > 0 && value.remark_out == 0){
						recordTable += '<button onclick="deleteRecord(\''+value.id_out+'\''+','+'\''+value.check_date+'\''+','+'\''+value.attend_code+'\''+','+'\''+value.vehicle+'\''+','+'\''+value.origin_out+'\''+','+'\''+value.destination_out+'\''+','+'\''+value.highway_amount_out+'\''+','+'\''+value.distance_out+'\')" style="width: 45%; padding: 0;" class="btn btn-danger">Out</button>';
					}
					recordTable += '</td>';
					recordTable += '</tr>';

					grand_highway += value.highway_amount_total;
					grand_distance += value.distance_total;
					grand_fuel += fuel;
					grand_total += total_amount;
					if(value.attend_code == 'hadir'){
						grand_hadir += 1;
					}

				});

recordTableFoot += '<tr>';
recordTableFoot += '<th>Grand Total</th>';
recordTableFoot += '<th>'+grand_hadir+' Hari</th>';
recordTableFoot += '<th></th>';
recordTableFoot += '<th></th>';
recordTableFoot += '<th></th>';
recordTableFoot += '<th>'+grand_highway.toLocaleString()+'</th>';
recordTableFoot += '<th>'+grand_distance.toLocaleString()+'</th>';
recordTableFoot += '<th>'+grand_fuel.toLocaleString()+'</th>';
recordTableFoot += '<th>'+grand_total.toLocaleString()+'</th>';
recordTableFoot += '<th></th>';
recordTableFoot += '<th></th>';
recordTableFoot += '<th></th>';
recordTableFoot += '</tr>';

$('#recordTableBody').append(recordTable);
$('#recordTableFoot').append(recordTableFoot);

$('#recordTable').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
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
	openErrorGritter('Error', result.message);
}
});	
}

function clearConfirmation(){
	location.reload(true);		
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