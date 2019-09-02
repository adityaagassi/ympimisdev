@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">	
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="col-md-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-md-12">
				<div class="nav-tabs-custom">
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div id="chart" style="width: 99%;"></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
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
		$('body').toggleClass("sidebar-collapse");

		drawChart();
	});

	$('.datepicker').datepicker({
		// <?php $tgl_max = date('m-Y') ?>
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		
		// endDate: '<?php echo $tgl_max ?>'

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {
		var tgl = $('#tgl').val();
		var data = {
			ctg:'{{$title}}',
			tgl: tgl
		};

		$.get('{{ url("fetch/report/gender2") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var seriesL, seriesP;
					var month = result.monthTitle;

					$.each(result.manpower_by_gender, function(key, value) {
						if (value.gender == "L") {
							seriesL = value.jml;
						} else if (value.gender == "P") {
							seriesP = value.jml;
						}
					})

					Highcharts.chart('chart', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie'
						},
						title: {
							text: '{{$title}} in '+month
						},
						tooltip: {
							pointFormat: '<b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true
							},
							series: {
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.y} Manpower',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
									}
								}
							}
						},
						series: [{
							name: 'Gender',
							colorByPoint: true,
							data: [{
								name: 'Male',
								y: seriesL
							}, {
								name: 'Female',
								y: seriesP
							}]
						}]
					});
				} else{
					alert('Attempt to retrieve data failed');
				}
			}
		})
	}

	function ShowModal(name) {
		$("#myModal").modal("show");
		// alert(name);
	}

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