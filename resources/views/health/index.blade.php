@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
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

	.buttonclass {
	  top: 0;
	  left: 0;
	  transition: all 0.15s linear 0s;
	  position: relative;
	  display: inline-block;
	  padding: 15px 25px;
	  background-color: #ffe800;
	  text-transform: uppercase;
	  color: #404040;
	  font-family: arial;
	  letter-spacing: 1px;
	  box-shadow: -6px 6px 0 #404040;
	  text-decoration: none;
	  cursor: pointer;
	}
	.buttonclass:hover {
	  top: 3px;
	  left: -3px;
	  box-shadow: -3px 3px 0 #404040;
	  color: white
	}
	.buttonclass:hover::after {
	  top: 1px;
	  left: -2px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass:hover::before {
	  bottom: -2px;
	  right: 1px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass::after {
	  transition: all 0.15s linear 0s;
	  content: "";
	  position: absolute;
	  top: 2px;
	  left: -4px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg);
	  z-index: -1;
	}
	.buttonclass::before {
	  transition: all 0.15s linear 0s !important;
	  content: "";
	  position: absolute;
	  bottom: -4px;
	  right: 2px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg) !important;
	  z-index: -1 !important;
	}

	a.buttonclass {
	  position: relative;
	}

	a:active.buttonclass {
	  top: 6px;
	  left: -6px;
	  box-shadow: none;
	}
	a:active.buttonclass:before {
	  bottom: 1px;
	  right: 1px;
	}
	a:active.buttonclass:after {
	  top: 1px;
	  left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} Health Indicator <span class="text-purple text-sm">{{$title_jp}}</span>
		<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#importExcel">
			<i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;
			Upload File XML
		</button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<h4>Filter</h4>
						<div class="row">
							<div class="col-md-2 col-md-offset-4">
								<span style="font-weight: bold;">Date From</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<span style="font-weight: bold;">Date To</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_to"name="tanggal_to" placeholder="Select Date To" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-md-offset-4">
								<span style="font-weight: bold;">Type</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-thermometer-empty"></i>
										</div>
										<select class="form-control select2" name="type" id="type" style="width: 100%" data-placeholder="Choose Type . . .">
											<option value=""></option>
											<option value="Body Mass">Body Mass</option>
											<option value="Body Height">Body Height</option>
											<option value="Heart Rate">Heart Rate</option>
											<option value="Oxygen Rate">Oxygen Rate</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="{{ url('index/temperature') }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/temperature/minmoe') }}" class="btn btn-danger">Clear</a>
										<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
									</div>
								</div>
							</div>
						</div>
						<div class="row" id="divTable">
							<table id="tableHealth" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">Employee ID</th>
										<th width="2%">Name</th>
										<th width="1%">Type</th>
										<th width="1%">Type ID</th>
										<th width="1%">Source</th>
										<th width="1%">Value</th>
										<th width="1%">Unit</th>
										<th width="1%">At</th>
									</tr>
								</thead>
								<tbody id="bodyTableHealth">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Upload XML</h4>
				</div>
				<div class="modal-body">
					Upload Excel XML Here:<span class="text-red">*</span>
					<input type="file" name="file" id="file" required>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success">Upload</button>
				</div>
			</div>
		</form>
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
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();

		$('#tanggal_from').val('');
		$('#tanggal_to').val('');
		$('#type').val('').trigger('change');

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	$("form#importForm").submit(function(e) {
		if ($('#file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("upload/health") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.message){
					$("#loading").hide();
					$("#file").val('');
					$('#importExcel').modal('hide');
					fillList();
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function fillList(){
		var data = {
			date_from:$('#tanggal_from').val(),
			date_to:$('#tanggal_to').val(),
			type:$('#type').val(),
		}
		$.get('{{ url("fetch/health") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHealth').DataTable().clear();
				$('#tableHealth').DataTable().destroy();
				$('#bodyTableHealth').html("");
				var tableData = "";
				$.each(result.health, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.username +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.type +'</td>';
					tableData += '<td>'+ value.type_id +'</td>';
					tableData += '<td>'+ value.source_name +'</td>';
					tableData += '<td>'+ value.value +'</td>';
					tableData += '<td>'+ value.unit +'</td>';
					tableData += '<td>'+ value.time_at +'</td>';
					tableData += '</tr>';
				});
				$('#bodyTableHealth').append(tableData);
				
				var table = $('#tableHealth').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
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
				alert('Attempt to retrieve data failed');
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
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection