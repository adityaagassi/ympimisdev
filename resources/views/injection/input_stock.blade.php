@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$page}}<small><span class="text-purple">??</span></small>
		<!-- <small> <span class="text-purple">??</span></small> -->
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Input Stock
		</button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Updating, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12 pull-left">
			<table id="tableStock" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th>Material Number</th>
						<th>Material Description</th>
						<th>Location</th>
						<th>Quantity</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableStock">
				</tbody>
			</table>
		</div>
	</div>

<div class="modal modal-default fade" id="create_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Operator</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />

							<div class="form-group row" align="right">
								<label class="col-sm-4">Materials<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<select class="form-control select2" data-placeholder="Select Materials" name="material_number" id="material_number" style="width: 100%">
										<option value=""></option>
										@foreach($materials as $materials)
										<option value="{{ $materials->material_number }}">{{ $materials->material_number }} - {{ $materials->material_description }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
								<div class="col-sm-5">
									<input type="number" class="form-control numpad" id="quantity" placeholder="Input Quantity" required>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Location<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="location" placeholder="Location" required disabled value="RC91">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="addStock()"><i class="fa fa-plus"></i> Add Stock</button>
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
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
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
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('#material_number').val("").trigger('change');
		$('#quantity').val("");
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

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

	function fillList(){
		$.get('{{ url("fetch/injection/stock") }}', function(result, status, xhr){
			if(result.status){
				$('#tableStock').DataTable().clear();
				$('#tableStock').DataTable().destroy();
				$('#bodyTableStock').html("");
				var tableData = "";
				$.each(result.stock, function(key, value) {
					tableData += '<tr style="font-size:15px">';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.location +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td></td>';
					tableData += '</tr>';
				});
				$('#bodyTableStock').append(tableData);
				
				var table = $('#tableStock').DataTable({
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

	function addStock(){
		$('#loading').show();
		var material_number = $('#material_number').val();
		var quantity = $('#quantity').val();
		var location = $('#location').val();

		var data = {
			material_number:material_number,
			quantity:quantity,
			location:location,
		}
		
		$.post('{{ url("input/injection/stock") }}', data, function(result, status, xhr){
			if(result.status){
				$("#create_modal").modal('hide');
				$('#loading').hide();
				fillList();
				$('#material_number').val("").trigger('change');
				$('#quantity').val("");
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Push Pull Recorder Check Failed');
			}
		});
	}
</script>
@endsection