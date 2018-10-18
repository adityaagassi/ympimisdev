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
		<small>Details <span class="text-purple">??????</span></small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li> --}}
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
					<h3 class="box-title">FLO Filters <span class="text-purple">FLO ----???</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Origin Group" name="origin_group" id="origin_group" style="width: 100%;">
									@foreach($origin_groups as $origin_group)
									<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Material Number" name="material_number" id="material_number" style="width: 100%;">
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select FLO Number" name="flo_number" id="flo_number" style="width: 100%;">
									<option value=""></option>
									@foreach($flos as $flo)
									<option value="{{ $flo->flo_number }}">{{ $flo->flo_number }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select FLO Status" name="status" id="status" style="width: 100%;">
									<option value=""></option>
									@foreach($statuses as $status)
									<option value="{{ $status->status_code }}">{{ $status->status_name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillFloDetail()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>			</div>
					<div class="row">
						<div class="col-md-12">
							<table id="flo_detail_table" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="font-size: 14">FLO Number</th>
										<th style="font-size: 14">Ship. Date</th>
										<th style="font-size: 14">Dest.</th>
										<th style="font-size: 14">Mat. Number</th>
										<th style="font-size: 14">Mat. Description</th>
										<th style="font-size: 14">Serial Number</th>
										<th style="font-size: 14">Qty</th>
										<th style="font-size: 14">Created At</th>
										<th style="font-size: 14">Status</th>
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
	</section>

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
			clearConfirmation();

			$('#datefrom').datepicker({
				autoclose: true
			});
			$('#dateto').datepicker({
				autoclose: true
			});
			$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no flo with status 'close'";
				}
			}
		});
		});

		function deleteConfirmation(id){
			var flo_number = $("#flo_number").val(); 
			var data = {
				id: id,
				flo_number : flo_number
			};
			if(confirm("Are you sure you want to delete this data?")){
				$.post('{{ url("destroy/serial_number") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);

					if(xhr.status == 200){
						if(result.status){
							$('#flo_detail_table').DataTable().ajax.reload();
							openSuccessGritter('Success!', result.message);
						}
						else{
							openErrorGritter('Error!', result.message);
							audio_error.play();
						}
					}
					else{
						openErrorGritter('Error!', 'Disconnected from server');
						audio_error.play();
					}
				});
			}
			else{
				return false;
			}
		}

		function clearConfirmation(){
			$('#flo_detail_table').DataTable().clear();
			$('#flo_detail_table').DataTable().destroy();
			$('#datefrom').val('');
			$('#dateto').val('');
			$('#origin_group').val('').change();
			$('#material_number').val('').change();
			$('#flo_number').val('').change();
			$('#status').val('').change();
		}

		function fillFloDetail(){
			$('#flo_detail_table').DataTable().destroy();
			var datefrom = $('#datefrom').val();
			var dateto = $('#dateto').val();
			var origin_group = $('#origin_group').val();
			var material_number = $('#material_number').val();
			var flo_number = $('#flo_number').val();
			var status = $('#status').val();
			var data = {
				datefrom:datefrom,
				dateto:dateto,
				origin_group:origin_group,
				material_number:material_number,
				flo_number:flo_number,
				status:status,
			}
			$('#flo_detail_table').DataTable({
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
					"type" : "post",
					"url" : "{{ url("filter/flo_detail") }}",
					"data" : data,
				},
				"columns": [
				{ "data": "flo_number" },
				{ "data": "st_date" },
				{ "data": "destination_shortname" },
				{ "data": "material_number" },
				{ "data": "material_description" },
				{ "data": "serial_number" },
				{ "data": "quantity" },
				{ "data": "created_at" },
				{ "data": "status_name" },
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