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
		margin:0; 
		padding:0;
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
			<select class="form-control select2" name="hpl" id='hpl' data-placeholder="HPL" style="width: 100%;">
				<option value="all">All</option>
				@foreach($origin_groups as $origin_group)
				<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
				@endforeach
			</select>
			{{-- <button id="search" onClick="fillChart()" class="btn btn-primary" style="width: 24%;"><span class="fa fa-search"></span></button>
			<br> --}}<br>
		</div>
		<div class="col-xs-4">
			{{-- <div class="box box-widget"> --}}
				{{-- <div class="box-body"> --}}
					<table id="tableActual" class="table table-hover table-bordered">
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
			<div class="progress-group" id="progress_div">
				<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;margin-bottom: 0.5%">
					<span class="progress-text" id="progress_text_production" style="font-size: 25px; padding-top: 10px;"></span>
					<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production" style="font-size: 30px; padding-top: 10px;"></div>
				</div>
			</div>
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
		var now = new Date();
		var now_tgl = addZero(now.getFullYear())+'-'+addZero(now.getMonth()+1)+'-'+addZero(now.getDate());

		var hpl = $('#hpl').val();
		var data = {
			hpl:hpl,
		}
		$.get('{{ url("fetch/dp_production_result") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					if(now.getHours() < 6){
						$('#progress_bar_production').append().empty();
						$('#progress_text_production').html("Today's Working Time : 0%");
						$('#progress_bar_production').css('width', '0%');
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
					}
					else if((now.getHours() >= 16) && (now.getDay() != 5)){
						$('#progress_text_production').append().empty();
						$('#progress_bar_production').html("Today's Working Time : 100%");
						$('#progress_bar_production').css('width', '100%');
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
						$('#progress_bar_production').removeClass('active');
					}
					else if(now.getDay() == 5){
						$('#progress_text_production').append().empty();
						var total = 570;
						var now_menit = ((now.getHours()-6)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
						if(now.getHours() >= 6 && now_menit < total){
							if(persen > 24){
								if(persen > 32){
									$('#progress_bar_production').html("Today's Working Time : "+persen.toFixed(2)+"%");
								}
								else{
									$('#progress_bar_production').html("Working Time : "+persen.toFixed(2)+"%");
								}	
							}
							else{
								$('#progress_bar_production').html(persen.toFixed(2)+"%");
							}
							$('#progress_bar_production').css('width', persen+'%');
							$('#progress_bar_production').addClass('active');

						}
						else if(now_menit >= total){
							$('#progress_bar_production').html("Today's Working Time : 100%");
							$('#progress_bar_production').css('width', '100%');
							$('#progress_bar_production').removeClass('active');

						}
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
					}
					else{
						$('#progress_text_production').append().empty();
						var total = 540;
						var now_menit = ((now.getHours()-6)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
						if(now.getHours() >= 6 && now_menit < total){
							if(persen > 24){
								if(persen > 32){
									$('#progress_bar_production').html("Today's Working Time : "+persen.toFixed(2)+"%");
								}
								else{
									$('#progress_bar_production').html("Working Time : "+persen.toFixed(2)+"%");
								}	
							}
							else{
								$('#progress_bar_production').html(persen.toFixed(2)+"%");
							}
							$('#progress_bar_production').css('width', persen+'%');
							$('#progress_bar_production').addClass('active');

						}
						else if(now_menit >= total){
							$('#progress_bar_production').html("Today's Working Time : 100%");
							$('#progress_bar_production').css('width', '100%');
							$('#progress_bar_production').removeClass('active');

						}
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
					}
					
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.tableData;
					var xAxis = []
					, planCount = []
					, actualCount = []

					for (i = 0; i < data.length; i++) {
						if(data[i].plan-data[i].debt > 0){
							xAxis.push(data[i].model);
							planCount.push(data[i].plan-data[i].debt);
							actualCount.push(data[i].actual);							
						}
					}

					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Daily Target<br><span style="color:rgba(96,92,168);"> 日製目標</span>'
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
							enabled: true,
							itemStyle: {
								fontSize:'16px',
								font: '16pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
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
						var diff = '';
						diff = value.actual-(value.plan+(-value.debt));
						tableData += '<tr>';
						tableData += '<td style="width: 40%">'+ value.model +'</td>';
						tableData += '<td style="width: 15%">'+ value.debt +'</td>';
						tableData += '<td style="width: 15%">'+ value.plan +'</td>';
						tableData += '<td style="width: 15%">'+ value.actual +'</td>';
						tableData += '<td style="width: 15%">'+ diff +'</td>';
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);
					$('#tableActual').DataTable({
						"scrollY": "440px",
						"paging": false,
						'searching': false,
						'order':[[4, "asc"]],
						'info': false,
						"columnDefs": [{
							"targets": 4,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData <  0 ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
					var totalPlan = 0;
					var totalActual = 0;
					$.each(result.tableData, function(key, value) {
						totalPlan += value.plan-value.debt;
						totalActual += value.actual;
					});

					if(totalActual-totalPlan < 0){
						totalCaret = '<span class="text-red"><i class="fa fa-caret-down"></i>';
						persenColor = '<span class="text-red">';
					}
					if(totalActual-totalPlan > 0){
						totalCaret = '<span class="text-yellow"><i class="fa fa-caret-up"></i>';
						persenColor = '<span class="text-yellow">';
					}
					if(totalActual-totalPlan == 0){
						totalCaret = '<span class="text-green">&#9679;';
						persenColor = '<span class="text-green">&#9679;';
					}

					$('#resume').html("");
					var resumeData = '';
					resumeData += '<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-blue">'+ totalPlan.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Plan<br><span class="text-purple">計画の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-purple">'+ totalActual.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Actual<br><span class="text-purple">実績の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ totalCaret + '' +Math.abs(totalActual-totalPlan).toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Difference<br><span class="text-purple">差異</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					// start add percentage
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ persenColor + ''+ Math.abs((totalActual/totalPlan)*100).toFixed(2) +'%</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Percentage(%)<br><span class="text-purple">差異実績</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					// end add percentage
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
