@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 0;
		padding-right: 0;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
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
				<div class="box-header">
					<h3 class="box-title">Employee Resume Data Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold;">Month From</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="monthfrom">
							</div>
						</div>
						<div class="col-xs-4">
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

					<div class="row" style="padding-top: 10px;">
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">BELUM DI KONFIRMASI (<span id="periode1"></span>)</span>
							</div>
							<table id="confirmTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Tanggal</th>
										<th style="width: 1%">ID</th>
										<th style="width: 5%">Nama</th>
										<th style="width: 3%">Departemen</th>
										<th style="width: 3%">Bagian</th>
										<th style="width: 1%">Dokter</th>
										<th style="width: 10%">Diagonsa</th>
										<th style="width: 1%">Dari</th>
										<th style="width: 1%">Sampai</th>
										<th style="width: 5%">Pengaju</th>
										<th style="width: 1%">Attachment</th>
										<th style="width: 1%">Reject</th>
										<th style="width: 1%">Confirm</th>
									</tr>
								</thead>
								<tbody id="confirmTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: yellow; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">CONFIRMED (<span id="periode3"></span>)</span>
							</div>
							<table id="detailTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Tanggal</th>
										<th style="width: 1%">ID</th>
										<th style="width: 5%">Nama</th>
										<th style="width: 3%">Departemen</th>
										<th style="width: 3%">Bagian</th>
										<th style="width: 1%">Dokter</th>
										<th style="width: 10%">Diagonsa</th>
										<th style="width: 1%">Dari</th>
										<th style="width: 1%">Sampai</th>
										<th style="width: 5%">Pengaju</th>
										<th style="width: 1%">Attachment</th>
										<th style="width: 1%">Status</th>
									</tr>
								</thead>
								<tbody id="detailTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
									</tr>
								</tfoot>
							</table>
						</div>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		$('#monthfrom').val("");
		$('#monthfrom').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true

		});
		$('.select2').select2();
		fetchTable();
	});

	function confirmRecord(id, status){
		$('#loading').show();
		var data = {
			id:id,
			status:status
		}
		$.post('{{ url("confirm/general/surat_dokter_report") }}', data, function(result, status, xhr){
			if(result.status){
				$('#confirm_'+id).remove();
				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function fetchTable(){
		$('#loading').show();
		var month_from = $('#monthfrom').val();
		var data = {
			month_from:month_from
		}
		$.get('{{ url("fetch/general/report_surat_dokter") }}', data, function(result, status, xhr){
			if(result.status){
				$('#periode1').text(result.period);
				$('#periode3').text(result.period);
				$('#confirmTable').DataTable().clear();
				$('#confirmTable').DataTable().destroy();
				$('#detailTable').DataTable().clear();
				$('#detailTable').DataTable().destroy();

				var confirmTable = "";
				var detailTable = "";

				$('#confirmTableBody').html("");
				$('#detailTableBody').html("");

				$.each(result.doctors, function(key, value){
					if(value.remark == 0){
						confirmTable += '<tr id="confirm_'+value.id+'">';
						confirmTable += '<td>'+value.tanggal_pengajuan+'</td>';
						confirmTable += '<td>'+value.employee_id+'</td>';
						confirmTable += '<td>'+value.name+'</td>';
						confirmTable += '<td>'+value.department+'</td>';
						confirmTable += '<td>'+value.section+'</td>';
						confirmTable += '<td>'+value.doctor_name+'</td>';
						confirmTable += '<td>'+value.diagnose+'</td>';
						confirmTable += '<td>'+value.date_from+'</td>';
						confirmTable += '<td>'+value.date_to+'</td>';
						confirmTable += '<td>'+value.created_name+'</td>';
						confirmTable += '<td><a href="javascript:void(0)" id="'+ value.attachment_file +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> SD</a></td>';
						confirmTable += '<td><button class="btn btn-danger btn-xs" onclick="confirmRecord(\''+value.id+'\''+','+'\''+2+'\')">Reject</button></td>';
						confirmTable += '<td><button padding-bottom: 3px;" class="btn btn-success btn-xs" onclick="confirmRecord(\''+value.id+'\''+','+'\''+1+'\')">Confirm</button></td>';
						confirmTable += '</tr>';
					}
					else{
						detailTable += '<tr id="confirm_'+value.id+'">';
						detailTable += '<td>'+value.tanggal_pengajuan+'</td>';
						detailTable += '<td>'+value.employee_id+'</td>';
						detailTable += '<td>'+value.name+'</td>';
						detailTable += '<td>'+value.department+'</td>';
						detailTable += '<td>'+value.section+'</td>';
						detailTable += '<td>'+value.doctor_name+'</td>';
						detailTable += '<td>'+value.diagnose+'</td>';
						detailTable += '<td>'+value.date_from+'</td>';
						detailTable += '<td>'+value.date_to+'</td>';
						detailTable += '<td>'+value.created_name+'</td>';
						detailTable += '<td><a href="javascript:void(0)" id="'+ value.attachment_file +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> SD</a></td>';
						if(value.remark == 1){
							detailTable += '<td style="background-color: RGB(204,255,255);">Confirmed</td>';
						}
						else if(value.remark == 2){
							detailTable += '<td style="background-color: RGB(255,204,255);">Rejected</td>';
						}
						detailTable += '</tr>';
					}
				});

				$('#confirmTableBody').append(confirmTable);
				$('#detailTableBody').append(detailTable);

				$('#confirmTable tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var table = $('#confirmTable').DataTable({
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

				$('#confirmTable tfoot tr').appendTo('#confirmTable thead');

				$('#detailTable tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var table2 = $('#detailTable').DataTable({
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

				table2.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#detailTable tfoot tr').appendTo('#detailTable thead');

				$('#loading').hide();
			}
			else{
				$('#loading').hide();				
				openErrorGritter('Error', result.message);
			}
		});
}

function downloadAtt(id){
	window.open(id, '_blank');
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