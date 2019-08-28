@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
		/*background-color: white;*/
		color:white;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 1px solid black;
		font-size: 1vw;
		font-weight: bold;
	}
	.table > tbody > tr > th {
		padding: 2px;
		text-align: center;
		color: black;
		background-color: white;

	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<form method="GET" action="{{ action('AssyProcessController@indexDisplayAssy') }}">
				<div class="col-xs-2" style="line-height: 1">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" name="date" placeholder="Select Date" style="border-color: #00a65a" <?php if (isset($_GET['date'])): ?>
						<?php echo "value=".$_GET['date']; endif ?>>
					</div>
					<br>
				</div>
				<div class="col-xs-2">
					<select class="form-control select2" multiple="multiple" id="key" onchange="change()">
						@foreach($keys as $key)
						<option value="{{ $key->key }}">{{ $key->key }}</option>
						@endforeach
					</select>
					<input type="text" name="key2" id="dd" hidden>
				</div>
				<div class="col-xs-2">
					<select class="form-control select2" name="surface">
						<option value="">Select Surface</option>
						<option value="PLT" <?php if (isset($_GET['surface']) && $_GET['surface'] == "PLT"): echo "selected"; endif ?>>Plating</option>
						<option value="LCQ" <?php if (isset($_GET['surface']) && $_GET['surface'] == "LCQ"): echo "selected"; endif ?>>Lacquering</option>
					</select>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" type="submit">Cari</button>
				</div>
			</form>
		</div>
		<div class="col-xs-12">
			<table id="assyTable" class="table table-bordered" style="padding: 0px; width: 100%">
				<tr id="model">
				</tr>
				<tr id="plan">
					<!-- <th>Total Plan</th> -->
				</tr>
				<tr id="picking">
					<!-- <th>Picking</th> -->
				</tr>
				<tr id="diff">
					<!-- <th>Diff</th> -->
				</tr>
			</table>
		</div>
		<div class="col-xs-12">
			<div id="picking_chart" style="width: 100%"></div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fill_table();

		setInterval(fill_table, 18000);

		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

	function change() {
		$("#dd").val($("#key").val());
	}

	function fill_table() {
		var data = {
			tanggal:"{{$_GET['date']}}",
			key:"{{$_GET['key2']}}",
			surface:"{{$_GET['surface']}}"
		}

		$.get('{{ url("fetch/display/sub_assy") }}', data, function(result, status, xhr){
			if(result.status){
				$("#model").empty();
				$("#plan").empty();
				$("#picking").empty();
				$("#diff").empty();

				model = "<th style='width:50px'>#</th>";
				totplan = "<th>Plan</th>";
				picking = "<th>Pick</th>";
				diff = "<th>Diff</th>";
				var style = "";

				$.each(result.picking, function(index, value){
					if ((value.picking - value.total_plan) < 0) {
						style = "style='background-color:#f24b4b';";
					} else {
						style = "style='background-color:#7fde62'; color:black";
					}

					model += "<th>"+value.model+" "+value.key+" "+value.surface+"</th>";
					totplan += "<td>"+value.total_plan.toLocaleString()+"</td>";
					picking += "<td>"+value.picking.toLocaleString()+"</td>";
					diff += "<td "+style+">"+(value.picking - value.total_plan).toLocaleString()+"</td>";
				})

				$("#model").append(model);
				$("#plan").append(totplan);
				$("#picking").append(picking);
				$("#diff").append(diff);

				fill_chart();
			}
		})
	}

	function fill_chart() {
		var data = {
			tanggal:"{{$_GET['date']}}",
			key:"{{$_GET['key2']}}",
			surface:"{{$_GET['surface']}}"
		}

		$.get('{{ url("fetch/chart/sub_assy") }}', data, function(result, status, xhr){
			if(result.status){
				var stockroom = [];
				var middle = [];
				var welding = [];

				var categories = [];

				$.each(result.picking, function(index, value){
					middle.push(parseInt(value.middle));
					stockroom.push(parseInt(value.stockroom));
					welding.push(parseInt(value.welding));

					categories.push(value.model+" "+value.key+" "+value.surface);
				})

				Highcharts.chart('picking_chart', {
					chart: {
						type: 'column'
					},
					title: {
						text: null
					},
					xAxis: {
						categories: categories
					},
					yAxis: {
						min: 0,
						title: {
							enabled: false
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
							}
						},
						labels: {
							useHTML:true,
							style:{
								width:'10px',
								whiteSpace:'normal'
							},
						},
						tickInterval: 10
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
							},
							animation: false
						},
						series: {
							pointPadding: -0.2
						}
					},
					credits :{
						enabled: false
					},
					series: [{
						name: 'Welding',
						data: welding
					}, {
						name: 'Middle',
						data: middle
					}, {
						name: 'Stockroom',
						data: stockroom
					}]
				});

				
			}
		})
	}

</script>
@endsection