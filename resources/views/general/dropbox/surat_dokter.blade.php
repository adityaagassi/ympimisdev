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
								<label>Tanggal Pengajuan Mulai</label>
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
								<label>Tanggal Pengajuan Sampai</label>
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
						<center>
							<button class="btn btn-success" style="font-weight: bold; width: 50%; font-size: 1.5vw; margin-bottom: 10px;" onclick="openModalCreate()">
								<i class="fa fa-pencil-square-o"></i> Tambah Data Surat Dokter <i class="fa fa-pencil-square-o"></i>
							</button>
						</center>
					</div>
					<div class="col-xs-12">
						<span class="pull-right" style="font-weight: bold; font-style: italic; color: purple;">Verifikasi: 0=Belum Diverifikasi; 1=Sudah Diverifikasi HR; 2=Ditolak HR;</span>
					</div>
					<div class="col-xs-12">
						<table id="recordTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">Submit Date</th>
									<th style="width: 1%">ID</th>
									<th style="width: 6%">Nama</th>
									<th style="width: 2%">Dokter</th>
									<th style="width: 7%">Diagnosa</th>
									<th style="width: 1%">Dari</th>
									<th style="width: 1%">Sampai</th>
									<th style="width: 1%">Attachment</th>
									<th style="width: 1%">Verified</th>
									<th style="width: 1%">Submitted By</th>
									<th style="width: 1%">Action</th>
								</tr>
							</thead>
							<tbody id="recordTableBody">
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
								</tr>
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
					<div class="col-md-12 col-md-offset-1">
						<form class="form-horizontal">
							<div class="form-group">
								<label for="newEmployee" class="col-sm-3 control-label">Karyawan Sakit<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<select class="form-control select2" name="newEmployee" id="newEmployee" data-placeholder="Pilih Karyawan" style="width: 100%;">
										<option value=""></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} {{ $employee->name }}</option>
										@endforeach	
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="newDoctorName" class="col-sm-3 control-label">Nama Dokter<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="newDoctorName" name="newDoctorName" placeholder="Masukkan Nama Dokter">
								</div>
							</div>
							<div class="form-group">
								<label for="newDiagnose" class="col-sm-3 control-label">Diagnosa<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<textarea style="width: 100%" class="form-control" id="newDiagnose" name="newDiagnose" placeholder="Masukkan Diagnosa">
									</textarea>
								</div>
							</div>

							<div class="form-group">
								<label for="newDateFrom" class="col-sm-3 control-label">Tanggal<span class="text-red">*</span></label>
								<div class="col-sm-3" style="padding-right: 0;">
									<input type="text" class="form-control pull-right" id="newDateFrom" name="newDateFrom">
								</div>
								<div class="col-sm-1" style="padding: 0;">
									<center>
										<b>s/d</b>
									</center>
								</div>
								<div class="col-sm-3" style="padding-left: 0;">
									<input type="text" class="form-control pull-right" id="newDateTo" name="newDateTo">
								</div>
							</div>

							<div class="form-group">
								<label for="newAttachment" class="col-sm-3 control-label">Foto Surat Dokter<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="file" onchange="readURL(this);" id="newAttachment">
									<img width="150px" id="blah" src="" style="display: none" alt="your image" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<i class="pull-left" style="font-weight: bold; color:red;">Masukkan surat dokter asli sesuai gambar di dropbox area klinik</i>
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
		$('#newDateFrom').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#newDateTo').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2();
		fetchRecordTable();
	});

	function addRecord(){
		$('#loading').show();

		var employee_id = $('#newEmployee').val();
		var doctor_name = $('#newDoctorName').val();
		var diagnose = $('#newDiagnose').val();
		var date_from = $('#newDateFrom').val();
		var date_to = $('#newDateTo').val();
		var attachment = $('#newAttachment').prop('files')[0];

		if(employee_id != "" && doctor_name != "" && diagnose != "" && date_from != "" && date_to != "" && attachment != ""){
			var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			var formData = new FormData();
			formData.append('employee_id', employee_id);
			formData.append('doctor_name', doctor_name);
			formData.append('diagnose', diagnose);
			formData.append('date_from', date_from);
			formData.append('date_to', date_to);
			formData.append('attachment', attachment);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('input/general/surat_dokter') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					openSuccessGritter('Success','Input Data Berhasil');
					$('#loading').hide();
					$('#modalRecord').modal('hide');
					fetchRecordTable();
				}
			})

		}
		else{
			openErrorGritter('Error', 'Isikan semua data terlebih dahulu');
		}
	}


	function downloadAtt(id){
		window.open(id, '_blank');
	}

	function openModalCreate(){
		$('#newEmployee').prop('selectedIndex', 0).change();
		$('#newDoctorName').val('');
		$('#newDiagnose').val('');
		$('#newDateFrom').val('');
		$('#newDateTo').val('');
		$('#newAttachment').val('');
		$('#modalRecordTitle').text('Tambah Data Surat Dokter Karyawan');
		$('#modalRecord').modal('show');
	}

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

	function fetchRecordTable(){
		$('#loading').show();
		var date_from = $('#datefrom').val();
		var date_to = $('#dateto').val();
		var data = {
			date_from:date_from,
			date_to:date_to
		}
		$.get('{{ url("fetch/general/surat_dokter") }}', data, function(result, status, xhr){
			if(result.status){
				$('#recordTable').DataTable().clear();
				$('#recordTable').DataTable().destroy();
				$('#recordTableBody').html('');
				var recordTable = '';

				$.each(result.general_doctors, function(key, value){
					if(value.remark	== 1){
						recordTable += '<tr id="record_'+value.id+'" style="background-color: RGB(204,255,255);">';
					}
					else if(value.remark == 2){
						recordTable += '<tr id="record_'+value.id+'" style="background-color: RGB(255,204,255);">';	
					}
					else{
						recordTable += '<tr id="record_'+value.id+'">';						
					}
					recordTable += '<td>'+value.created_at+'</td>';
					recordTable += '<td>'+value.employee_id+'</td>';
					recordTable += '<td>'+value.name+'</td>';
					recordTable += '<td>'+value.doctor_name+'</td>';
					recordTable += '<td>'+value.diagnose+'</td>';
					recordTable += '<td>'+value.date_from+'</td>';
					recordTable += '<td>'+value.date_to+'</td>';
					recordTable += '<td><a href="javascript:void(0)" id="'+ value.attachment_file +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> Download</a></td>';
					recordTable += '<td>'+value.remark+'</td>';
					recordTable += '<td>'+value.created_by+'</td>';
					if(value.created_by == "{{ Auth::user()->username }}"){
						if(value.remark == 1 || value.remark == 2){
							recordTable += '<td></td>';
						}
						else{
							recordTable += '<td><button class="btn btn-danger btn-xs" onclick="deleteRecord(\''+value.id+'\')">Hapus</button></td>';
						}
					}
					else{
						recordTable += '<td></td>';
					}
					recordTable += '</tr>';
				});

				$('#recordTableBody').append(recordTable);


				$('#recordTable tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var table = $('#recordTable').DataTable({
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

				$('#recordTable tfoot tr').appendTo('#recordTable thead');

				$('#loading').hide();
			}
			else{
				openErrorGritter('Error', result.message);
			}
		});
	}

	function deleteRecord(id){
		$('#loading').show();
		var data = {
			id:id
		}

		if(confirm("Apakah anda yakin akan menghapus data ini?")){
			$.post('{{ url("delete/general/surat_dokter") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					$('#record_'+id).remove();
					$('#loading').hide();
				}
				else{
					openErrorGritter('Erorr', result.message);
					$('#loading').hide();
				}
			});
		}
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