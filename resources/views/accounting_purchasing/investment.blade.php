@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
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
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url('investment/create')}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create {{ $page }}</a>
		</li>
	</ol>
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="col-xs-12">
						<div class="col-md-3">
							<div class="form-group">
								<label>Submission Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Submission Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>

						<?php if(Auth::user()->role_code == "MIS" || $employee->department == "Accounting") { ?>
						<div class="col-md-3">
							<div class="form-group">
								<label>Department</label>
								<select class="form-control select2" multiple="multiple" name="department" id='department' data-placeholder="Select Department" style="width: 100%;">
									<option value=""></option>
									@foreach($dept as $dept)
									<option value="{{ $dept }}">{{ $dept }}</option>
									@endforeach
								</select>
							</div>
						</div>	
						<?php } ?>

						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fillTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearConfirmation()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-header">
						</div>
						<div class="box-body" style="padding-top: 0;">
							<table id="invTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Submission Date</th>
										<th style="width: 1%">Department</th>
										<th style="width: 1%">Applicant</th>
										<th style="width: 1%">Category</th>
										<th style="width: 1%">Subject</th>
										<th style="width: 1%">Type</th>
										<th style="width: 3%">Vendor</th>
										<th style="width: 1%">File</th>
										<th style="width: 2%">Status</th>
										<th style="width: 3%">Action</th>
									</tr>
								</thead>
								<tbody>
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
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="upload_adagio">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off" action="{{ url('investment/adagio') }}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Informasi: <br><b>Upload Bukti Approval Adagio yang telah Di disetujui Sebagai Bukti Investment Telah Disetujui Semua.</b>
					</div>
					<div class="modal-body">
						Upload PDF file here:<span class="text-red">*</span>
						<input type="file" name="file" id="file">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<input type="hidden" id="id_edit" name="id_edit">
						<button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload File</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
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
		$('body').toggleClass("sidebar-collapse");
		fillTable();
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#invTable').DataTable().clear();
		$('#invTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
		}

		var table = $('#invTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/investment") }}",
				"data" : data
			},
			"columns": [
			{ "data": "submission_date" },
			{ "data": "applicant_department" },
			{ "data": "applicant_name" },
			{ "data": "category" },
			{ "data": "subject" },
			{ "data": "type" },
			{ "data": "supplier_code" },
			{ "data": "file" },
			{ "data": "status" },
			{ "data": "action" },
			],
		});

		$('#invTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
		});

		table.columns().every( function () {
			var that = this;
			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#invTable tfoot tr').appendTo('#invTable thead');
	}

	function uploadBukti(id){
    	$("#id_edit").val(id);
    	$('#upload_adagio').modal("show");
    }

	$('.select2').select2();

</script>

@endsection