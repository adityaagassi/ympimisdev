@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Daily Upload <i class="fa fa-angle-double-down"></i></span>
			<a data-toggle="modal" data-target="#uploadCompletion" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Upload Completion</a>
			<a data-toggle="modal" data-target="#uploadScrap" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Upload Scrap</a>
			<a data-toggle="modal" data-target="#uploadStorage" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Upload Storage Loc Stock</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> On Change Upload <i class="fa fa-angle-double-down"></i></span>
			<a data-toggle="modal" data-target="#uploadStdTime" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Upload Standard Time</a>
			<a data-toggle="modal" data-target="#uploadSMBMR" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Upload SMBMR</a>
		</div>
	</div>
</section>

<div class="modal fade" id="uploadCompletion">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="formUploadCompletion" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Import Completion</h4>
				Sample: <a href="{{ url('download/manual/import_completion.xlsx') }}">import_completion.xlsx</a>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">					
					<div class="col-xs-6 col-xs-offset-3">
						<div class="form-group">
							<label align="left">Entry Month</label>
							<input type="text" class="form-control" id="date_completion" name="date_completion">
						</div>
					</div>

					<div class="col-xs-6 col-xs-offset-3">
						<div class="form-group">
							<label align="left">Cost Center Name</label>
							<select class="form-control select2" multiple="multiple" name="cost_center_name" id='cost_center_name' style="width: 100%;">
								<option value="">Select Cost Center Name</option>
								@foreach($cost_center_names as $cost_center_name)
								<option value="{{ $cost_center_name->cost_center_name }}">{{ $cost_center_name->cost_center_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<br>
					<div class="col-xs-6 col-xs-offset-3">
						<input type="file" name="completion" id="completion" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" style="text-align: right;">
					</div>

					<input type="text" name="cc" id="cc" hidden>

				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="modalImportButton" type="submit" class="btn btn-success pull-right">Import</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="uploadStorage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="importForm" method="post" action="{{ url('import/material/storage') }}" enctype="multipart/form-data">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Import Storage Location Stock</h4>
					Format: [Material Number][Material Description][SLoc][Unrestricted][Download Date][Download Time]<br>
					Sample: <a href="{{ url('download/manual/import_storage_location_stock.txt') }}">import_storage_location_stock.txt</a> Code: #Truncate
				</div>
				<div class="modal-body">
					Select Date:
					<input type="text" class="form-control" id="date_stock" name="date_stock" style="width:25%;"><br>
					<input type="file" name="storage_location_stock" id="storage_location_stock" accept="text/plain">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button id="modalImportButton" type="submit" class="btn btn-success" onclick="loadingPage()">Import</button>
				</div>
			</form>
		</div>
	</div>
</div>
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
		$('#date_stock').datepicker({
			autoclose: true,
			todayHighlight: true
		});

		$('#date_completion').datepicker({
			<?php $tgl_max = date('Y-m') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'	
		});

		$('.select2').select2();
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	$("form#formUploadCompletion").submit(function(e) {
		if ($('#completion').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		if ($('#date_completion').val() == '') {
			openErrorGritter('Error!', 'You need to select date');
			return false;
		}

		if ($('#cost_center_name').val() == '') {
			openErrorGritter('Error!', 'You need to select cost center name');
			return false;
		}

		var arr = $('#cost_center_name').val();
		$('#cc').val(arr.toString());

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("import/sap/completion") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){

					$("#completion").val('');
					$("#date_completion").val('');
					$("#cc").val('');
					$("#cost_center_name").prop('selectedIndex', 0).change();

					$('#uploadCompletion').modal('hide');
					$("#loading").hide();
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