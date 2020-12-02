@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
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
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box">
		<div class="box-header">
			<h3 class="box-title">Agreement Lists<span class="text-purple"> ??</span></span></h3>
			<button class="btn btn-success pull-right" onclick="newData('new')">Create New Agreement</button>
		</div>
		<div class="box-body">
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Terminated</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">In Use</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Safe</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">&#8804; 90 Days</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">&#8804; 30 Days</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Expired</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="count_all" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_terminated" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_inuse" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_ok" style="background-color: #aee571; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_90" style="background-color: #ffeb3b; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_30" style="background-color: #f9a825; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_expired" style="background-color: #e53935; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
					</tr>
				</tbody>
			</table>
			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="">#</th>
						<th style="">Dept</th>
						<th style="">Vendor</th>
						<th style="">Description</th>
						<th style="">Valid From</th>
						<th style="">Valid To</th>
						<th style="">Status</th>
						<th style="">Remark</th>
						<th style="">Validity</th>
						<th style="">Att</th>
						<th style="">Created By</th>
						<th style="">Created At</th>
						<th style="">Last Update</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
				<div class="row">
					<input type="hidden" id="newId">
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newDepartment" class="col-sm-3 control-label">Department<span class="text-red">*</span></label>
						<div class="col-sm-7">
							<select class="form-control select2" name="newDepartment" id="newDepartment" data-placeholder="Select Department" style="width: 100%;">
								<option value=""></option>
								@php
								$department = array();
								@endphp
								@foreach($employees as $employee)
								@if(!in_array($employee->department, $department))
								<option value="{{ $employee->department }}">{{ $employee->department }}</option>
								@php
								array_push($department, $employee->department);
								@endphp
								@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newVendor" class="col-sm-3 control-label">Vendor Name<span class="text-red">*</span></label>
						<div class="col-sm-7">
							<input type="text" style="width: 100%" class="form-control" id="newVendor" name="newVendor" placeholder="Enter Vendor Name">
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newDescription" class="col-sm-3 control-label">Description<span class="text-red">*</span></label>
						<div class="col-sm-8">
							<textarea class="form-control" id="newDescription" name="newDescription" placeholder="Enter Description"></textarea>
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newValidFrom" class="col-sm-3 control-label">Valid From<span class="text-red">*</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control pull-right" id="newValidFrom" name="newValidFrom">							
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newValidTo" class="col-sm-3 control-label">Valid To</label>
						<div class="col-sm-5">
							<input type="text" class="form-control pull-right" id="newValidTo" name="newValidTo">
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 5px;">
						<label for="newStatus" class="col-sm-3 control-label">Status<span class="text-red">*</span></label>
						<div class="col-sm-7">
							<select class="form-control select2" name="newStatus" id="newStatus" data-placeholder="Select Status" style="width: 100%;">
								@foreach($agreement_statuses as $agreement_status)
								<option value="{{ $agreement_status }}">{{ $agreement_status }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 15px;">
						<label for="newRemark" class="col-sm-3 control-label">Remark</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="newRemark" name="newRemark" placeholder="Enter Remark"></textarea>
						</div>
					</div>
					<div class="col-md-12" style="margin-bottom: 15px;">
						<label for="newAttachment" class="col-sm-3 control-label">Attachment<span class="text-red">*</span></label>
						<div class="col-sm-6">
							<input type="file" name="newAttachment" id="newAttachment"  multiple="">
						</div>
					</div>
					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="newAgreement('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="newAgreement('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDownload">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Download Attachment</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="downloadId">
				<center>
					<div class="form-group">
						<label>Select File(s) to Download</label>
						<select multiple class="form-control" style="height: 180px;" id="selectDownload">
						</select>
					</div>
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="downloadAtt()">Download</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#newValidFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('#newValidTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('.select2').select2();
		fetchTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){
		if(id == 'new'){
			$('#modalNewTitle').text('Create New Agreement');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newAttachment').val('');
			$('#newButton').hide();
			$('#updateButton').show();
			var data = {
				id:id
			}
			$.get('{{ url("fetch/general/agreement_detail") }}', data, function(result, status, xhr){
				if(result.status){

					$('#newDepartment').html('');
					$('#newStatus').html('');

					var newDepartment = "";
					var newStatus = "";

					$.each(result.employees, function(key, value){
						if(value.department == result.agreement.department){
							newDepartment += '<option value="'+value.department+'" selected>'+value.department+'</option>';
						}
						else{
							newDepartment += '<option value="'+value.department+'">'+value.department+'</option>';
						}
					});
					$('#newDepartment').append(newDepartment);
					$('#newVendor').val(result.agreement.vendor);
					$('#newDescription').val(result.agreement.description);
					$('#newValidFrom').val(result.agreement.valid_from);
					$('#newValidTo').val(result.agreement.valid_to);
					$.each(result.agreement_statuses, function(key, value){
						if(value == result.agreement.status){
							newStatus += '<option value="'+value+'" selected>'+value+'</option>';
						}
						else{
							newStatus += '<option value="'+value+'">'+value+'</option>';
						}
					});
					$('#newStatus').append(newStatus);
					$('#newRemark').val(result.agreement.remark);
					$('#newId').val(result.agreement.id);

					$('#modalNewTitle').text('Update Agreement');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

	function newAgreement(id){
		$('#loading').show();
		if(id == 'new'){
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == ""){
				$('#loading').modal('hide');
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachment  = $('#newAttachment').prop('files')[0];
			var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachment);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('create/general/agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == ""){
				$('#loading').modal('hide');
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachment  = $('#newAttachment').prop('files')[0];
			var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newId', $("#newId").val());
			formData.append('newAttachment', newAttachment);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('edit/general/agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
	}

	function clearNew(){
		$("#newDepartment").prop('selectedIndex', 0).change();
		$('#newVendor').val('');
		$('#newDescription').val('');
		$('#newValidFrom').val("");
		$('#newValidTo').val("");
		$('#newAttachment').val('');
		$("#newStatus").prop('selectedIndex', 0).change();
		$('#newRemark').val('');
		$('#newId').val('');
		$('#downloadId').val('');
	}

	function downloadAtt(){
		var file_name = $('#selectDownload').val();
		var data = {
			file_name:file_name
		}

		$.get('{{ url("download/general/agreement") }}', data, function(result, status, xhr){
			if(result.status){
				download_files(result.file_paths);
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function download_files(files) {
		function download_next(i) {
			if (i >= files.length) {
				return;
			}
			var a = document.createElement('a');
			a.href = files[i].download;
			a.target = '_parent';
			if ('download' in a) {
				a.download = files[i].filename;
			}
			(document.body || document.documentElement).appendChild(a);
			if (a.click) {
				a.click();
			} else {
				$(a).click();
			}
			a.parentNode.removeChild(a);
			setTimeout(function() {
				download_next(i + 1);
			}, 500);
		}
		download_next(0);
	}

	function modalDownload(id){
		var data = {
			id:id
		}
		$.get('{{ url("fetch/general/agreement_download") }}', data, function(result, status, xhr){
			if(result.status){

				$('#selectDownload').html('');
				var optionData = '';
				$.each(result.files, function(key, value) {
					optionData += '<option value="' + value.file_name + '">' + value.file_name + '</option>';
				});
				$('#selectDownload').append(optionData);
				$('#modalDownload').modal('show');

			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/general/agreement") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;
				var count_terminated = 0;
				var count_inuse = 0;
				var count_ok = 0;
				var count_90 = 0;
				var count_30 = 0;
				var count_expired = 0;

				$.each(result.agreements, function(key, value){
					if(value.status == 'In Use'){
						listTableBody += '<tr>';
					}
					if(value.status == 'Terminated'){
						listTableBody += '<tr style="background-color: RGB(255,204,255);">';
					}
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.department_shortname+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:3%;">'+value.vendor+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:8%;">'+value.description+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.valid_from+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.valid_to+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.status+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:5%;">'+value.remark+'</td>';
					count_all += 1;
					if(value.status == "In Use"){
						count_inuse += 1;
					}
					if(value.status == "Terminated"){
						count_terminated += 1;
					}
					if(value.validity <= 0){
						if(value.status == "In Use"){
							count_expired += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #e53935;">'+value.validity+' Day(s)</td>';			
					}
					else if(value.validity <= 30){
						if(value.status == "In Use"){
							count_30 += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #f9a825;">'+value.validity+' Day(s)</td>';			
					}
					else if(value.validity <= 90){
						if(value.status == "In Use"){
							count_90 += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #ffeb3b;">'+value.validity+' Day(s)</td>';			
					}
					else
					{
						if(value.status == "In Use"){
							count_ok += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #aee571;">'+value.validity+' Day(s)</td>';			
					}
					listTableBody += '<td style="width:1%;" onclick="modalDownload(\''+value.id+'\')"><a href="javascript:void(0)" onclick="modalDownload(\''+value.id+'\')">Att('+value.att+')</a></td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%;">'+value.created_by+'<br>'+value.name+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.created_at+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.updated_at+'</td>';
					listTableBody += '</tr>';
				});

				$('#count_all').text(count_all);
				$('#count_terminated').text(count_terminated);
				$('#count_inuse').text(count_inuse);
				$('#count_ok').text(count_ok);
				$('#count_30').text(count_30);
				$('#count_90').text(count_90);
				$('#count_expired').text(count_expired);
				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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
					'pageLength': 20,
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

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
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

