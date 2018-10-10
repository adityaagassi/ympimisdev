@extends('layouts.master')
@section('stylesheets')
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
									@foreach($flos as $flo)
									<option value="{{ $flo->shipmentschedule->material->origin_group_code }}">{{ $flo->shipmentschedule->material->origingroup->origin_group_name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Material Number" name="material_number" id="material_number" style="width: 100%;">
									<option value=""></option>
									@foreach($flos as $flo)
									<option value="{{ $flo->shipmentschedule->material_number }}">{{ $flo->shipmentschedule->material->material_number }} - {{ $flo->shipmentschedule->material->material_description }}</option>
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
										<th style="font-size: 14">Quantity</th>
										<th style="font-size: 14">Created At</th>
										<th style="font-size: 14">Action</th>
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
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			clearConfirmation();

			$('#datefrom').datepicker({
				autoclose: true
			});
			$('#dateto').datepicker({
				autoclose: true
			});
			$('.select2').select2()
		});

		function clearConfirmation(){
			$('#flo_detail_table').DataTable().clear();
			$('#flo_detail_table').DataTable().destroy();
			$('#datefrom').val('');
			$('#dateto').val('');
			$('#origin_group').val('').change();
			$('#material_number').val('').change();
			$('#flo_number').val('').change();
		}

		function fillFloDetail(){
			$('#flo_detail_table').DataTable().destroy();
			var datefrom = $('#datefrom').val();
			var dateto = $('#dateto').val();
			var origin_group = $('#origin_group').val();
			var material_number = $('#material_number').val();
			var flo_number = $('#flo_number').val();
			var data = {
				datefrom:datefrom,
				dateto:dateto,
				origin_group:origin_group,
				material_number:material_number,
				flo_number:flo_number,
			}
			$('#flo_detail_table').DataTable({
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
				{ "data": "action" }
				]
			});

		}
	</script>
	@endsection