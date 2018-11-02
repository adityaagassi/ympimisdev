@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		Daily Production Result <span class="text-purple">日常生産実績</span>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-10">
			<div id="container" style="width:100%; height:450px;"></div>
		</div>
		<div class="col-xs-2">
			<select class="form-control select2" name="hpl" id='hpl' data-placeholder="HPL" style="width: 60%;">
				<option></option>
				@foreach($hpls as $hpl)
				<option value="{{ $hpl->hpl }}">{{ $hpl->hpl }}</option>
				@endforeach
			</select>
			<button id="search" onClick="fillChart()" class="btn btn-primary"><span class="fa fa-search"></span></button>
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
		$('.select2').select2();	
	});

	function fillChart(){
		var hpl = $('#hpl').val();
		var data = {
			hpl:hpl,
		}
		$.get('{{ url("fetch/dp_production_result") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){


					var data = result.chartData;

					var xAxis = []
					, planCount = []
					, actualCount = []

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].model);
						planCount.push(data[i].plan);
						actualCount.push(data[i].actual);
					}

					Highcharts.chart('container', {
						colors: ['rgba(75, 30, 120, 0.40)','rgba(75, 30, 120)'],
						chart: {
							type: 'column'
						},
						title: {
							text: 'Efficiency Optimization by Branch'
						},
						xAxis: {
							categories: xAxis,
							labels:{
								rotation: -45
							}							
						},
						yAxis: [{
							min: 0,
							title: {
								text: 'Set(s)'
							}
						}],
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						plotOptions: {
							series:{
								pointPadding: 0,
								groupPadding: 0
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0
							}
						},
						series: [{
							name: 'Plan',
							data: planCount,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: actualCount,
							pointPadding: 0.15
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
