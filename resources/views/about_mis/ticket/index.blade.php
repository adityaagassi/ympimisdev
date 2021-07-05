@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
#ticketTableBody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
table.table-bordered{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(150,150,150);
	vertical-align: middle;

}
.crop2 {
	overflow: hidden;
}
.crop2 img {
	height: 70px;
	margin: -20% 0 0 0 !important;
}
#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalCreate();"><i class="fa fa-pencil-square-o"></i> Create Ticket</button>
		<a href="{{ url('index/ticket/monitoring/mis') }}" class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-desktop"></i> Monitoring</a>
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
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('all')">Total</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Approval')">Approval</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Waiting')">Waiting</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('InProgress')">InProgress</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('OnHold')">OnHold</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Finished')">Finished</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Rejected')">Rejected</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="count_all" style="cursor: pointer; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('all')">0</td>
						<td id="count_approval" style="cursor: pointer; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Approval')">0</td>
						{{-- <td id="count_inuse" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td> --}}
						<td id="count_waiting" style="cursor: pointer; background-color: #ffeb3b; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Waiting')">0</td>
						<td id="count_inprogress" style="cursor: pointer; background-color: #aee571; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('InProgress')">0</td>
						<td id="count_onhold" style="cursor: pointer; background-color: #e0e0e0; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('OnHold')">0</td>
						<td id="count_finished" style="cursor: pointer; background-color: #f9a825; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Finished')">0</td>
						<td id="count_rejected" style="cursor: pointer; background-color: #e53935; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Rejected')">0</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<table id="ticketTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">ID</th>
						<th style="width: 3%;">Title</th>
						<th style="width: 7%;">Description</th>
						<th style="width: 1%;">Status</th>
						<th style="width: 1%;">#</th>
						<th style="width: 4%;">PIC</th>
						<th style="width: 7%;">Progress</th>
						<th style="width: 2%;">Estimated From</th>
						<th style="width: 2%;">Estimated To</th>
					</tr>
				</thead>
				<tbody id="ticketTableBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Create Your Ticket<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>

								<div class="col-sm-7">
									<select class="form-control select2" name="createDepartment" id="createDepartment" data-placeholder="Select Department" style="width: 100%;">
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="createCategory" id="createCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach($categories as $category)
										<option value="{{ $category }}">{{ $category }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Priority<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="createPriority" data-placeholder="Select Priority" style="width: 100%;" onchange="changePriority()">
										<option></option>
										@foreach($priorities as $priority)
										<option value="{{ $priority }}">{{ $priority }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group" id="createGroupReason">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Priority Reason<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Reason" id="createReason"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Title<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Title" id="createTitle">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="createDescription"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Condition Before<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="createBefore">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Condition After<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="createAfter">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due Date From<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="createDueFrom" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due Date To<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="createDueTo" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Attachment<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="createAttachment" multiple="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Document Number<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Document Number" id="createDocument"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Target<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<a class="btn btn-primary" style="font-weight: bold;" onclick="addCostDown()"><i class="fa fa-plus"></i> Target</a>
								</div>
							</div>
							<span style="font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> List Target</span>
							<table class="table table-bordered table-striped" id="tableCostDown">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 4%;">Category<span class="text-red">*</span></th>
										<th style="width: 8%;">Description<span class="text-red">*</span></th>
										<th style="width: 4%;">Amount (USD)<span class="text-red">*</span></th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableCostDownBody">
								</tbody>
							</table>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL </button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="createTicket()">CREATE TICKET </i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
		$('.select2').select2();
		$('#createDueFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#createDueTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		fetchTicket();
	});

	$(function () {
		$('#createCategory').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
	});

	$(function () {
		$('#createPriority').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var costdowns = [];
	var costdown_count = 0;

	function detailProject(id){
		window.open('{{ url("index/ticket/detail") }}'+'/'+id, '_blank');
	}

	function fetchTicket(stat){
		$('#loading').show();

		var stats = 'all';
		if(stat){
			stats = stat;
		}

		var data = {
			category : 'index',
			status : stats
		}

		$.get('{{ url("fetch/ticket") }}', data, function(result, status, xhr){
			if(result.status){
				clearAll();
				$('#ticketTable').DataTable().clear();
				$('#ticketTable').DataTable().destroy();
				$('#ticketTableBody').html("");
				var ticketTable = "";
				var tickets = result.tickets;
				var count_status = [];

				tickets.reduce(function (res, value) {
					if (!res[value.status]) {
						res[value.status] = {
							cnt: 0,
							status: value.status
						};
						count_status.push(res[value.status])
					}
					res[value.status].cnt += 1
					return res;
				}, {});

				var total_status = 0;
				$.each(count_status, function(key, value){
					if(value.status == 'Approval'){
						total_status += value.cnt;
						$('#count_approval').text(value.cnt);
					}
					if(value.status == 'Waiting'){
						total_status += value.cnt;
						$('#count_waiting').text(value.cnt);
					}
					if(value.status == 'InProgress'){
						total_status += value.cnt;
						$('#count_inprogress').text(value.cnt);
					}
					if(value.status == 'OnHold'){
						total_status += value.cnt;
						$('#count_onhold').text(value.cnt);
					}
					if(value.status == 'Finished'){
						total_status += value.cnt;
						$('#count_finished').text(value.cnt);
					}
					if(value.status == 'Rejected'){
						total_status += value.cnt;
						$('#count_rejected').text(value.cnt);
					}
				});
				
				if(stats == 'all'){
					$('#count_all').text(total_status);					
				}

				$.each(result.tickets, function(key, value){
					ticketTable += '<tr onclick="detailProject(\''+value.ticket_id+'\')">';
					ticketTable += '<td>'+replaceNull(value.ticket_id)+'</td>';
					ticketTable += '<td>'+replaceNull(value.case_title)+'</td>';
					ticketTable += '<td>'+replaceNull(value.case_description)+'</td>';
					ticketTable += '<td>'+replaceNull(value.status)+'</td>';
					ticketTable += '<td>';
					if(replaceNull(value.pic_id) != '-'){
						var foto = value.pic_id.toUpperCase();
						var avatar = 'images/avatar/'+foto+'.jpg';
						var url = "{{ url('') }}"+'/'+avatar;
						ticketTable += '<div class="crop2">';
						ticketTable += '<img src="'+url+'">';
						ticketTable += '</div>';
					}
					else{
						ticketTable += '-';
					}
					ticketTable += '</td>';
					ticketTable += '<td>'+replaceNull(value.pic_id)+'<br>'+truncate(replaceNull(value.pic_name), 20)+'</td>';
					ticketTable += '<td class="project_progress">';
					ticketTable += '<div class="progress progress-sm active">';
					ticketTable += '<div class="progress-bar progress-bar-success progress-bar-striped" aria-volumenow="'+value.progress+'" aria-volumemin="0" aria-volumemax="100" style="width: '+value.progress+'%">';
					ticketTable += '</div>';
					ticketTable += '</div>';
					ticketTable += '<small>'+value.progress+'% Complete</small>';
					ticketTable += '</td>';
					ticketTable += '<td>'+replaceNull(value.estimated_date_from)+'</td>';
					ticketTable += '<td>'+replaceNull(value.estimated_date_to)+'</td>';
					ticketTable += '</tr>';
				});

				$('#ticketTableBody').append(ticketTable);

				$('#ticketTable').DataTable({
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
					'paging': true,
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
				alert('Unidentified Error '+result.message);
				audio_error.play();
				return false;
			}
		});
}

function createTicket(){
	if(confirm("Apakah anda yakin akan mengajukan tiket ini?")){
		// $('#loading').show();

		var category = $('#createCategory').val();
		var department = $('#createDepartment').val();
		var priority = $('#createPriority').val();
		var reason = $('#createReason').val();
		var title = $('#createTitle').val();
		var description = $('#createDescription').val();
		var doc = $('#createDocument').val();
		var before = CKEDITOR.instances.createBefore.getData();
		var after = CKEDITOR.instances.createAfter.getData();
		var due_from = $('#createDueFrom').val();
		var due_to = $('#createDueTo').val();
		var costdown_length = costdowns.length;

		if(priority == 'High' || priority == 'Very High'){
			if(reason == ''){
				audio_error.play();
				openErrorGritter('Error!', 'Untuk prioritas tinggi harus menyertakan alasan.');
				$('#createReason').focus();
				return false;
			}
		}

		if(category == '' || priority == '' || title == '' || description == '' || before == '' || after == '' || due_from == '' || due_to == ''){
			audio_error.play();
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		if(category == 'Pembuatan Aplikasi Baru' || category == 'Pengembangan Aplikasi Lama'){
			if(costdown_length <= 0){
				audio_error.play();
				openErrorGritter('Error!', 'Harus ada costdown untuk kategori (Pembuatan & Pengembangan Aplikasi).');
				return false;
			}
		}

		var formData = new FormData();
		var attachment  = $('#createAttachment').prop('files')[0];
		var file = $('#createAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		$.each(costdowns, function(key, value){
			var costdown_category = $('#costdown_category_'+value).val();
			var costdown_description = $('#costdown_description_'+value).val();
			var costdown_amount =  $('#costdown_amount_'+value).val();
			if(costdown_category == '' || costdown_description == '' || costdown_amount == ''){
				costdown = [];
				audio_error.play();
				openErrorGritter('Error!', 'Semua kolom costdown harus terisi.');
				return false;
			}
			if(!$.isNumeric(costdown_amount)){
				costdown = [];
				audio_error.play();
				openErrorGritter('Error!', 'Amount costdown harus angka.');
				return false;
			}
			formData.append('costdown['+key+']', costdown_category+'~'+costdown_description+'~'+costdown_amount);
		});

		formData.append('doc', doc);
		formData.append('attachment', attachment);
		formData.append('department', department);
		formData.append('category', category);
		formData.append('priority', priority);
		formData.append('reason', reason);
		formData.append('title', title);
		formData.append('description', description);
		formData.append('due_from', due_from);
		formData.append('due_to', due_to);
		formData.append('before', before);
		formData.append('after', after);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/ticket') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					clearAll();
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}

			}
		});
	}
	else{
		return false;
	}

}

function modalCreate(){
	clearAll();
	$('#modalCreate').modal('show');
}

function clearAll(){
	$('#loading').hide();
	$('#modalCreate').modal('hide');
	var departments = <?php echo json_encode($departments); ?>;
	var employee = <?php echo json_encode($employee); ?>;
	var optionDepartment = "";
	$('#createDepartment').html("");

	$.each(departments, function(key, value){
		if(employee.department == value.department_name){
			optionDepartment += '<option value="'+value.department_name+'" selected>'+value.department_name+'</option>';
		}
		else{
			optionDepartment += '<option value="'+value.department_name+'">'+value.department_name+'</option>';
		}
	});
	$('#createDepartment').append(optionDepartment);

	$('#createGroupReason').hide();
	$("#createCategory").prop('selectedIndex', 0).change();
	$("#createPriority").prop('selectedIndex', 0).change();
	$('#createTitle').val("");
	$('#createDescription').val("");
	$('#createBefore').val("");
	$('#createAfter').val("");
	$('#createDueFrom').val("");
	$('#createDueTo').val("");
	$('#createAttachment').val("");
	$('#createDocument').val("");
	$('#tableCostDownBody').html("");
	costdowns = [];
	costdown_count = 0;
}

function addCostDown(){
	var costdown_list = <?php echo json_encode($costdowns); ?>;

	var tableCostDownBody = "";
	costdown_count += 1;

	tableCostDownBody += '<tr id="costdown_'+costdown_count+'">';
	tableCostDownBody += '<td>';
	tableCostDownBody += '<select style="width: 100%;" class="select2" id="costdown_category_'+costdown_count+'">';
	tableCostDownBody += '<option></option>';
	$.each(costdown_list, function(key, value){
		tableCostDownBody += '<option value="'+value+'">'+value+'</option>';
	});
	tableCostDownBody += '</select>';
	tableCostDownBody += '</td>';
	tableCostDownBody += '<td><textarea class="form-control" rows="2" id="costdown_description_'+costdown_count+'"></textarea></td>';
	tableCostDownBody += '<td><input type="text" class="form-control" id="costdown_amount_'+costdown_count+'" value="0"></td>';
	tableCostDownBody += '<td><center><a href="javascript:void(0)" onclick="remCostDown(id)" id="'+costdown_count+'" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>';
	tableCostDownBody += '</tr>';

	$('#tableCostDownBody').append(tableCostDownBody);
	costdowns.push(costdown_count);

	$('.select2').select2();
}

function remCostDown(id){
	costdowns = jQuery.grep(costdowns, function(value) {
		return value != id;
	});
	$('#costdown_'+id).remove();
}

function changePriority(){
	if($('#createPriority').val() == 'High' || $('#createPriority').val() == 'Very High'){
		$('#createGroupReason').show();
		return false;
	}
	else{
		$('#createGroupReason').hide();	
		return false;		
	}
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

function replaceNull(s) {
	return s == null ? "-" : s;
}

CKEDITOR.replace('createBefore' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('createAfter' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});
</script>

@endsection