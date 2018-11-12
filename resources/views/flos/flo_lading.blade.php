@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Lading <span class="text-purple">??????</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">FLO Lading <span class="text-purple">??????</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">

					<div class="col-md-12 col-md-offset-4">
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Invoice Number" name="invoice_number" id="invoice_number" style="width: 100%;" required>
									<option></option>
									@foreach($invoices as $invoice)
									<option value="{{ $invoice->invoice_number }}">{{ $invoice->invoice_number }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>BL Date</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="bl_date" nama="bl_date" required>
								</div>
							</div>
							<div class="form-group pull-right">
								<button href="javascript:void(0)" id="confirm" onClick="inputBlDate()" class="btn btn-primary">Confirm</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<table id="flo_invoice_table" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="font-size: 14">Invoice Number</th>
										<th style="font-size: 14">Ship. Date</th>
										<th style="font-size: 14">Dest.</th>
										<th style="font-size: 14">Dest. Name</th>
										<th style="font-size: 14">Plan BL Date</th>
										<th style="font-size: 14">Actual BL Date</th>
										<th style="font-size: 14" class="notexport">Action</th>
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

<div class="modal fade" id="editModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Edit BL Date</h4>
				</div>
				<div class="modal-body">
					<input type="text" style="text-align: center;" class="form-control" name="modal_invoice_number" id="modal_invoice_number" disabled>
					<br>
					<label>BL Date</label>
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control pull-right" id="modal_bl_date" nama="modal_bl_date" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Confirm</button>
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
	<script src="{{ url("js/pdfmake.min.js")}}"></script>
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
			$('#invoice_number').val('').change();
			$('#bl_date').val('');
			$('#bl_date').datepicker({
				autoclose: true
			});
			$('#modal_bl_date').datepicker({
				autoclose: true
			});
			$('.select2').select2({
				language : {
					noResults : function(params) {
						return "There is no invoice with empty BL Date";
					}
				}
			});
			fillInvoiceTable();
		});

		function inputBlDate(){
			if($('#invoice_number').val() != '' && $('#bl_date').val() != ''){
				var invoice_number = $('#invoice_number').val();
				var bl_date = $('#bl_date').val();
				var data = {
					invoice_number : invoice_number,
					bl_date : bl_date,
				}
				$.post('{{ url("input/flo_lading") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){
							$('#flo_invoice_table').DataTable().ajax.reload();
							$('#invoice_number').val('').change();
							$('#bl_date').val('');
							openSuccessGritter('Success!', result.message);
						}
					}
					else{
						openErrorGritter('Error!', 'Disconnected from server');
						audio_error.play();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Invoice number and bl date required');
				audio_error.play();
			}
		}

		function editConfirmation(id){
			var data = {
				id:id,
			}
			$.get('{{ url("fetch/flo_lading") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#modal_invoice_number').val(result.invoice_number);
						$('#modal_bl_date').val(result.bl_date);
						$('#editModal').modal('show');
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}

		function fillInvoiceTable(){
			$('#flo_invoice_table').DataTable().destroy();
			$('#flo_invoice_table').DataTable({
				'dom': 'Bfrtip',
				'buttons': {
					dom: {
						button: {
							tag:'button',
							className:''
						}
					},
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
					"type" : "post",
					"url" : "{{ url("index/flo_invoice") }}",
				},
				"columns": [
				{ "data": "invoice_number" },
				{ "data": "st_date" },
				{ "data": "destination_code" },
				{ "data": "destination_name" },
				{ "data": "plan_bl" },
				{ "data": "actual_bl" },
				{ "data": "action" }
				]
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

		function openInfoGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-info',
				image: '{{ url("images/image-unregistered.png") }}',
				sticky: false,
				time: '2000'
			});
		}
	</script>
	@endsection