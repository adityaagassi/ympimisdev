@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Daily Production Result <span class="text-purple">日常生産実績</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-12" id="weekResult">
		</div>
		<div class="col-md-12">
		</div>
		<div class="col-md-12" id="dateResult">
		</div>
		<div class="col-md-12">
			<br>
		</div>
		<div class="col-md-12">
			<div id="container" style="width:100%; height:450px;"></div>			
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

	jQuery(document).ready(function() {
		fillWeek();
		fillDate();
		fillChart();
	});

	function fillWeek(){
		$.get('{{ url("fetch/daily_production_result_week") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#weekResult').html('');
					var weekData = '';
					$.each(result.weekData, function(key, value) {
						weekData += '<button type="button" class="btn bg-purple" id="' + value.week_name + '" onClick="fillDate(id)">' + value.week + '</button>&nbsp;';
					});
					$('#weekResult').append(weekData);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

	function fillDate(id){
		var data = {
			week:id,
		}
		$.get('{{ url("fetch/daily_production_result_date") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#dateResult').html('');
					var dateData = '';
					$.each(result.dateData, function(key, value) {
						dateData += '<button type="button" class="btn bg-olive btn-xs" id="' + value.week_date + '" onClick="fillChart(id)">' + value.week_date_name + '</button>&nbsp;';
					});
					$('#dateResult').append(dateData);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}			
		});		
	}

	function fillChart(id){
		var data = {
			date:id,
		};
		$.get('{{ url("fetch/daily_production_result") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var yAxisLabels = [0,25,50,75,100,110];
					Highcharts.chart('container', {
						colors: ['rgba(255, 255, 255, 0.20)','rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'column'
						},
						credits: {
							enabled: false
						},
						title: {
							text: 'Stacked column chart'
						},
						xAxis: {
							categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: 'Total fruit consumption'
							}
						},
						tooltip: {
							pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
							shared: true
						},
						plotOptions: {
							series: {
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalContainerDeparture(this.category);
										}
									}
								}
							},
						},
						series: [{
							name: 'Target',
							data: [5, 3, 4, 7, 2]
						}, {
							name: 'Actual',
							data: [2, 2, 3, 2, 1]
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}			
		});	
	}

</script>
@endsection