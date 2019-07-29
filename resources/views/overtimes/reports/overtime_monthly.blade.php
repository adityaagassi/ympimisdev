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
		<div class="col-xs-12">
			<table class="table table-responsive table-bordered">
				<thead>
					<tr>
						<th>Period</th>
						<th>Employee Id</th>
						<th>Name</th>
						<th>Department</th>
						<th>Section</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {
		var data = {
			ctg:'{{$title}}'
		};

		$.get('{{ url("fetch/report/stat") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var ctg = [], series = [];

					$.each(result.datas, function(key, value) {
						ctg.push(value.status);
						series.push(value.jml);
					})

					$('#chart').highcharts({
						chart: {
							type: 'column'
						},
						title: {
							text: '{{$title}}'
						},
						xAxis: {
							type: 'category',
							categories: ctg
						},
						yAxis: {
							type: 'logarithmic',
							title: {
								text: 'Total Employee'
							}
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category);
										}
									}
								},
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									format: '{point.y}'
								}
							}
						},
						credits: {
							enabled: false
						},

						tooltip: {
							formatter:function(){
								return this.key + ' : ' + this.y;
							}
						},

						"series": [
						{
							"name": "By Status",
							"colorByPoint": true,
							"data": series
						}
						]
					})
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