@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
	margin:0; padding:0;
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
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
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
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-8">
			<div id="container" style="width:100%; height:550px;"></div>
		</div>
		<div class="col-xs-4">
			<select class="form-control select2" name="hpl" id='hpl' data-placeholder="HPL" style="width: 74%;">
				<option value="all">All</option>
				@foreach($origin_groups as $origin_group)
				<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
				@endforeach
			</select>
			<button id="search" onClick="fillChart()" class="btn btn-primary" style="width: 24%;"><span class="fa fa-search"></span></button>
			<br><br>
		</div>
		<div class="col-xs-4">
			{{-- <div class="box box-widget"> --}}
				{{-- <div class="box-body"> --}}
					<table id="tableActual" class="table table-hover table-bordered" style="width: 100%;">
						{{-- <div class="scroll-container"> --}}
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 40%">Model</th>
									<th style="width: 15%">MTD (H-1)</th>
									<th style="width: 15%">Plan</th>
									<th style="width: 15%">Actual</th>
									<th style="width: 15%">Diff</th>
								</tr>
							</thead>
							<tbody id="tableBody"></tbody>
							<tfoot></tfoot>
						{{-- </div> --}}
					</table>
				{{-- </div> --}}
			{{-- </div> --}}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-widget">
				<div class="box-footer">
					<div class="row" id="resume"></div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
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
		$('.select2').select2();
		fillChart();
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
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
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
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Month to Date of Production Result<br><span style="color:rgba(96,92,168);">過去一ヶ月間の生産結果</span>'
						},

						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: 'Set(s)'
							},
							type:'logarithmic'
						},
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
								minPointLength: 10,
								pointPadding: 0,
								groupPadding: 0,
								animation:{
									duration:0
								}
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0,
							}
						},
						series: [{
							name: 'Plan',
							data: planCount,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: actualCount,
							pointPadding: 0.2
						}]
					});

					$('#tableActual').DataTable().destroy();
					$('#tableBody').html("");
					var tableData = '';
					$.each(result.tableData, function(key, value) {
						var caret = '';
						var diff = '';
						diff = value.actual-(value.plan+(-value.debt));
						if(value.plan+(-value.debt) == value.actual){
							caret = '<span style="font-weight: bold;" class="text-green">&nbsp;'+ diff +'&nbsp;</span>';
						}
						if(value.plan+(-value.debt) > value.actual){
							caret = '<span style="font-weight: bold;" class="text-red">&nbsp;'+ diff +'&nbsp;</span>';
						}
						if(value.plan+(-value.debt) < value.actual){
							caret = '<span style="font-weight: bold;" class="text-yellow">&nbsp;+'+ diff +'&nbsp;</span>';
						}
						tableData += '<tr>';
						tableData += '<td style="width: 40%">'+ value.model +'</td>';
						tableData += '<td style="width: 15%">'+ value.debt +'</td>';
						tableData += '<td style="width: 15%">'+ value.plan +'</td>';
						tableData += '<td style="width: 15%">'+ value.actual +'</td>';
						tableData += '<td style="width: 15%">'+ caret +'</td>';
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);
					$('#tableActual').DataTable({
						"scrollY": "440px",
						// 	"scrollCollapse": true,
						"paging": false,
						// 	'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
					});
					var totalPlan = 0;
					var totalActual = 0;
					$.each(result.chartData, function(key, value) {
						totalPlan += value.plan;
						totalActual += value.actual;
					});

					if(totalActual-totalPlan < 0){
						totalCaret = '<span class="text-red"><i class="fa fa-caret-down"></i>';
					}
					if(totalActual-totalPlan > 0){
						totalCaret = '<span class="text-yellow"><i class="fa fa-caret-up"></i>';
					}
					if(totalActual-totalPlan == 0){
						totalCaret = '<span class="text-green">&#9679;';
					}

					$('#resume').html("");
					var resumeData = '';
					resumeData += '<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-blue">'+ totalPlan.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Plan<br><span class="text-purple">計画の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-purple">'+ totalActual.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Actual<br><span class="text-purple">実績の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ totalCaret + '' +Math.abs(totalActual-totalPlan).toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Difference<br><span class="text-purple">差異</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					$('#resume').append(resumeData);
					setTimeout(fillChart, 1000);
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
