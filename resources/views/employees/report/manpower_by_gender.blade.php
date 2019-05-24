@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Gender <span class="text-purple"> japanese</span>
		{{-- <small>Based on ETD YMPI <span class="text-purple">YMPIのETDベース</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<div id="gender_chart" style="width: 100%"></div>
				</div>
			</div>
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
		$('body').toggleClass("sidebar-collapse");
		drawChart();
	});

	function drawChart(){
		$.get('{{ url("fetch/report/gender") }}', function(result, status, xhr){
			if(xhr.status == 200){

				if(result.status){
					var xCategories = [];
					var seriesLaki = [];
					var seriesPerempuan = [];
					var cat;

					for(var i = 0; i < result.manpower_by_gender.length; i++){
						cat = result.manpower_by_gender[i].mon;

						if(result.manpower_by_gender[i].jk == 'L')
							seriesLaki.push(result.manpower_by_gender[i].tot_karyawan);
						else
							seriesPerempuan.push(result.manpower_by_gender[i].tot_karyawan);

						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}

					console.log(seriesLaki);


					Highcharts.chart('gender_chart', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Total Manpower by Gender <br> Fiscal 196'
						},
						xAxis: {
							categories: xCategories
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total Manpower'
							}
						},
						tooltip: {
							useHTML: true
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column: {
								dataLabels: {
									enabled: true,
									crop: false,
									overflow: 'none'
								}
							}
						},
						series: [{
							name: 'Laki - laki',
							data: seriesLaki

						}, {
							name: 'Perempuan',
							data: seriesPerempuan

						}]
					});

				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
		})
	}
</script>
@endsection