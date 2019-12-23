@extends('layouts.display')
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
		<div class="col-xs-9">
			<div class="col-xs-12">
				<div class="progress-group" id="progress_div">
					<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;margin-bottom: 0.5%">
						<span class="progress-text" id="progress_text_production" style="font-size: 25px; padding-top: 10px;"></span>
						<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production" style="font-size: 30px; padding-top: 10px;"></div>
					</div>
				</div>
			</div>
			<div id="container" style="width:100%; height:700px;"></div>
		</div>
		<div class="col-xs-3" style="padding-left: 0;">
			<div style="padding-bottom: 10px;">
				<select class="form-control select2" name="hpl" id='hpl' data-placeholder="HPL" style="width: 100%;">
					<option value="all">All</option>
					@foreach($locations as $location)
					<option value="{{ $location->hpl }}">{{ $location->hpl }}</option>
					@endforeach
				</select><br>
			</div>
			<div class="small-box" style="background: rgb(220,220,220); height: 150px;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>PLAN <span class="text-purple">計画</span></b></h3>
					<h5 class="text-orange" style="font-size: 4vw; font-weight: bold;" id="plan"></h5>
				</div>
				<div class="icon" style="padding-top: 30px;">
					<i class="ion ion-stats-bars"></i>
				</div>
			</div>
			<div class="small-box" style="background: rgb(220,220,220); height: 150px;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL <span class="text-purple">実績</span></b></h3>
					<h5 class="text-purple" style="font-size: 4vw; font-weight: bold;" id="actual"></h5>
				</div>
				<div class="icon" style="padding-top: 30px;">
					<i class="ion ion-stats-bars"></i>
				</div>
			</div>
			<div class="small-box" style="background: rgb(220,220,220); height: 150px;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>DIFF <span class="text-purple">差異</span></b></h3>
					<h5 class="text-purple" style="font-size: 4vw; font-weight: bold;" id="diff"></h5>
				</div>
				<div class="icon" style="padding-top: 30px;">
					<i class="ion ion-stats-bars"></i>
				</div>
			</div>
			<div class="small-box" style="background: rgb(220,220,220); height: 150px;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>(%) <span class="text-purple">差異実績</span></b></h3>
					<h5 class="text-purple" style="font-size: 4vw; font-weight: bold;" id="pctg"></h5>
				</div>
				<div class="icon" style="padding-top: 30px;">
					<i class="ion ion-stats-bars"></i>
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
		$.get('{{ url("fetch/kd_daily_production_result") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					// Progres bar jam kerja/hari
					if(now.getHours() < 7){
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
						var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
						if(now.getHours() >= 7 && now_menit < total){
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
						var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
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
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
						$('#progress_bar_production').addClass('active');
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
							text: '<span style="color: white;">Daily Target 日製目標</span>'
						},
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
								formatter () {
									return `<span style="color: white;">${this.value}</span>`
								}
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: '<span style="color: white;">Pc(s)</span>',
							},
							labels:{
								formatter () {
									return `<span style="color: white;">${this.value}</span>`
								}
							}
							// ,type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: true,
							layout: 'horizontal',
							align: 'right',
							verticalAlign: 'top',
							x: -90,
							y: 20,
							floating: true,
							borderWidth: 1,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true,
							itemStyle: {
								fontSize:'16px',
								font: '16pt Trebuchet MS, Verdana, sans-serif',
								color: 'white'
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

					$('#plan').html("");
					$('#plan').append(totalPlan.toLocaleString());
					$('#actual').html("");
					$('#actual').append(totalActual.toLocaleString());
					$('#diff').html("");
					$('#diff').append(totalCaret + '' +Math.abs(totalActual-totalPlan).toLocaleString());
					$('#pctg').html("");
					$('#pctg').append(persenColor + ''+ Math.abs((totalActual/totalPlan)*100).toFixed(2));

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
