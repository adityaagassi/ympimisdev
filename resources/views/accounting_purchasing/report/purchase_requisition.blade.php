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
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-sm bg-purple" style="color:white">Create {{ $page }}</a>
		</li>
	</ol>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">PR Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-3 col-md-offset-1">
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
						<div class="col-md-3">
							<div class="form-group">
								<label>Department</label>
								<select class="form-control select2" multiple="multiple" name="department" id='department' data-placeholder="Select Department" style="width: 100%;">
									<option value=""></option>
								</select>
							</div>
						</div>	
					</div>
					<div class="row">

					</div>
					
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<table id="prTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">No PR</th>
										<th style="width: 1%">Department</th>
										<th style="width: 5%">Group</th>
										<th style="width: 2%">Submission Date</th>
										<th style="width: 1%">User</th>
										<th style="width: 1%">Receive Date</th>
										<th style="width: 1%">No Budget</th>
										<th style="width: 1%">Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<form id ="importForm" name="importForm" method="post" action="{{ url('create/daily_report') }}" enctype="multipart/form-data">
	<input type="hidden" value="{{csrf_token()}}" name="_token" />
	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-lg" style="width: 1200px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Create Purchase Requisition</h4>
					<br>
					<div class="nav-tabs-custom tab-danger">
						<ul class="nav nav-tabs">
							<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">PR Informations</a></li>
							<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">PR Item Details</a></li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label>Identity<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="identity" name="identity">
										</div>
										<div class="form-group">
											<label>Department<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="department" name="department">
										</div>
										<div class="form-group">
											<label>Attachment</label>
											<input type="file" id="reportAttachment" name="reportAttachment[]" multiple="">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Submission Date<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="datebegin" name="datebegin">
											</div>
										</div>
										<div class="form-group">
											<label>Note</label>
											<textarea class="form-control pull-right" id="datefinished" name="datefinished"></textarea>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<a class="btn btn-primary btnNext pull-right">Next</a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_2">
							<div class="row">
								<div class="col-md-12" style="margin-bottom : 5px">
									<input type="text" name="lop" id="lop" value="1" hidden>
									<div class="col-xs-8" style="padding:0;">
										<input type="text" class="form-control" id="description1" name="description1" placeholder="Enter Description" required>
									</div>
									<div class="col-xs-2" style="padding:0;">
										<input type="text" id="duration" name="duration1" class="form-control timepicker" value="01:00">
									</div>
									<div class="col-xs-2" style="padding:0;">
										<button class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
									</div>	
								</div>
								<div id="tambah"></div>
								<div class="col-md-12">
									<br>
									<button class="btn btn-success pull-right" onclick="$('[name=importForm]').submit();">Confirm</button>
									<span class="pull-right">&nbsp;</span>
									<a class="btn btn-primary btnPrevious pull-right">Previous</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

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
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();

		$('.btnNext').click(function(){
			var identity = $('#identity').val();
			var department = $('#department').val();
			if(identity == '' ){
				alert('All field must be filled');	
			}
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});
		$('.btnPrevious').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#prTable').DataTable().clear();
		$('#prTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
		}

		// $.get('{{ url("fetch/report/attendance_data") }}', data, function(result, status, xhr){

		// });

		var table = $('#prTable').DataTable({
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
				"url" : "{{ url("fetch/purchase_requisition") }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_pr" },
			{ "data": "department" },
			{ "data": "group" },
			{ "data": "submission_date" },
			{ "data": "user" },
			{ "data": "receive_date" },
			{ "data": "note" },
			{ "data": "no_budget" }
			],
		});

		$('#prTable tfoot th').each( function () {
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
		$('#prTable tfoot tr').appendTo('#prTable thead');
	}

	function openModalCreate(){
		$('#modalCreate').modal('show');
	}

</script>

@endsection