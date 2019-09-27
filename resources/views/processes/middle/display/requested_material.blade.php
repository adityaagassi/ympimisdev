@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('MiddleProcessController@indexDisplayPicking') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-2">
						<select class="form-control select2" multiple="multiple" id="surfaceSelect" data-placeholder="Select Surface" onchange="change_surface()">
							<option value="">Select Surface</option>
							<option value="PLT" <?php if (isset($_GET['surface']) && $_GET['surface'] == "PLT"): echo "selected"; endif ?>>Plating</option>
							<option value="LCQ" <?php if (isset($_GET['surface']) && $_GET['surface'] == "LCQ"): echo "selected"; endif ?>>Lacquering</option>
						</select>
						<input type="text" name="surface" id="surface" hidden>
					</div>
					<div class="col-xs-2">
						<select class="form-control select2" multiple="multiple" id="modelSelect" data-placeholder="Select Model" onchange="change_model()">
							@foreach($models as $model)
							<option value="{{ $model->model }}">{{ $model->model }}</option>
							@endforeach
						</select>
						<input type="text" name="model" id="model" hidden>
					</div>
					<div class="col-xs-2">
						<select class="form-control select2" multiple="multiple" id="keySelect" data-placeholder="Select Key" onchange="change_key()">
							@foreach($keys as $key)
							<option value="{{ $key->key }}">{{ $key->key }}</option>
							@endforeach
						</select>
						<input type="text" name="key" id="key" hidden>
					</div>
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="submit">Search</button>
						</div>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<table id="s3" class="table table-bordered" style="margin:0">
					<thead id="head">
					</thead>
					<tbody id="body">
					</tbody>
				</table>
			</div>
		</div>
	</div>

</section>


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fillTable();
		// setInterval(fillTable, 60000);
	});

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function change_key() {
		$("#key").val($("#keySelect").val());
	}

	function change_model() {
		$("#model").val($("#modelSelect").val());
	}

	function change_surface() {
		$("#surface").val($("#surfaceSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {
		var data = {
			tgl:"{{$_GET['tanggal']}}",
			surface:"{{$_GET['surface']}}",
			model:"{{$_GET['model']}}",
			key:"{{$_GET['key']}}"
		}

		$.get('{{ url("fetch/middle/display_picking") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){


				}
			}


		});
	}

</script>
@stop