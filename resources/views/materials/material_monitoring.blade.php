@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div id="period_title" class="col-xs-7" style="background-color: #64b5f6;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
			</div>
			<div class="col-xs-1">
				<a data-toggle="modal" data-target="#uploadModal" id="btnUpload" class="btn btn-info" style="width: 100%;"><i class="fa fa-upload" style="font-size: 2vw;"></i></a>
			</div>
			<div class="col-xs-2 pull-right" style="padding-right: 0;">
				<div class="input-group date">
					<div class="input-group-addon" style="background-color: #64b5f6;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="period" name="datepicker" onchange="fetchChart()">
				</div>
			</div>
		</div>
		<div class="col-xs-12" id="material_monitoring">
		</div>
	</div>
</section>

<div class="modal fade" id="uploadModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('material')">MONITORED MATERIALS</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('policy')">STOCK POLICY</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('usage')">MRP USAGE</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('delivery')">DELIVERY PLAN</button>
					<button class="btn btn-info" style="width: 100%; margin-bottom: 5px; font-weight: bold; color: black;" onclick="modalOpen('inout')">MATERIAL IN/OUT</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="materialModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Monitored Material</h4>
				<span>Format Upload: [GMC][DESKRIPSI][KODE VENDOR][NAMA VENDOR][KATEGORI][PIC][REMARK]</span>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<textarea id="materialData" style="height: 100px; width: 100%;"></textarea>
				</div>
				<button class="btn btn-success pull-right" onclick="uploadData('material');">Upload</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearData();
		fetchChart();
		// setInterval(fetchChart, 1000*60*60);
		$('#period').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});

	});

	function clearData(){

	}

	function uploadData(id){
		if(id == 'material'){
			var material = $('#materialData').val();
			var data = {
				id:id,
				material:material
			}	
		}
		else if(id == 'policy'){
			$('#policyModal').modal('show');			
		}
		else if(id == 'usage'){
			$('#usageModal').modal('show');			
		}
		else if(id == 'delivery'){
			$('#deliveryModal').modal('show');			
		}
		else if(id == 'inout'){
			$('#inoutModal').modal('show');			
		}
		else{
			alert('Unidentified Error');
		}

		$.post('{{ url("fetch/material/material_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

			}
			else{
				
			}
		});
	}

	function modalOpen(id){
		$('#uploadModal').modal('hide');
		if(id == 'material'){
			$('#materialModal').modal('show');			
		}
		else if(id == 'policy'){
			$('#policyModal').modal('show');			
		}
		else if(id == 'usage'){
			$('#usageModal').modal('show');			
		}
		else if(id == 'delivery'){
			$('#deliveryModal').modal('show');			
		}
		else if(id == 'inout'){
			$('#inoutModal').modal('show');			
		}
		else{
			alert('Unidentified Error');
		}
	}

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function fetchChart(id){
		// $('#loading').show();
		var period = $('#period').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/material/material_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#title_text').text('Stock Condition on '+result.period+' ('+result.count_item+' item(s) <75%)');
				var h = $('#period_title').height();
				$('#period').css('height', h);
				$('#btnUpload').css('height', h);

				var count_material = 0;
				var div_chart = "";
				$('#material_monitoring').html("");

				var now = new Date();

				$.each(result.material_percentages, function(key, value){
					count_material += 1;
					div_chart += '<div class="col-xs-6" style="padding: 0 5px 0 5px;">';
					div_chart += '<div class="box box-solid" style="margin-bottom: 10px;">';
					div_chart += '<div class="box-header">';
					div_chart += '<span style="font-weight: bold; font-size: 1.2vw;">'+count_material+') '+value.material_number+' '+value.material_description+'</span>';
					div_chart += '<span style="font-weight: bold; font-size: 1.2vw; color:red;" class="pull-right">('+value.percentage+'%)</span>';	
					div_chart += '<div class="box-body" style="padding: 10px 0 10px 0;">';
					div_chart += '<div style="height: 350px;" id="chart_'+value.material_number+'"></div>';
					div_chart += '</div>';
					div_chart += '</div>';
					div_chart += '</div>';
					div_chart += '</div>';
					$('#material_monitoring').append(div_chart);
					div_chart = "";

					var material_number = value.material_number;
					var stock_total = [];
					var stock_wip = [];
					var stock_mstk = [];
					var plan_usage = [];
					var plan_delivery = [];
					var plan_stock = [];
					var actual_usage = [];
					var actual_delivery = [];
					var stock_policy = [];
					var policy = value.policy;
					var percentage = 0;
					var stock_percentage = [];

					for(var i = 0; i < result.results.length; i++){
						if(result.results[i].material_number == material_number){
							stock_total.push(parseFloat(result.results[i].stock_total));
							stock_mstk.push(parseFloat(result.results[i].stock_mstk));
							stock_wip.push(parseFloat(result.results[i].stock_wip));
							stock_policy.push(parseFloat(policy));
							plan_usage.push(parseFloat(result.results[i].plan_usage));
							plan_delivery.push(parseFloat(result.results[i].plan_delivery));
							actual_usage.push(parseFloat(result.results[i].actual_usage));
							actual_delivery.push(parseFloat(result.results[i].actual_delivery));
							plan_stock.push(parseFloat(result.results[i].plan_stock));
							if(result.results[i].stock_total > 0){
								percentage = (parseFloat(result.results[i].stock_total)/parseFloat(policy))*100;
							}
							else{
								percentage = (parseFloat(result.results[i].plan_stock)/parseFloat(policy))*100;
							}
							stock_percentage.push((parseFloat(percentage)).toFixed(2));
						}
					}

					var chart_name = 'chart_'+value.material_number;

					Highcharts.chart(chart_name, {
						chart: {
							backgroundColor	: null
						},
						title: {
							text: null
						},
						credits: {
							enabled: false
						},
						xAxis: {
							tickInterval: 1,
							gridLineWidth: 1,
							categories: result.categories,
							crosshair: true,
							plotBands:[{
								from: result.count_now-1.5,
								to: result.count_now-0.5,
								color: 'rgba(68, 170, 213, .2)',
								label: {
									text: 'Today',
									style: {
										color: '#999999'
									},
									y: 20
								}
							}]
						},
						yAxis: [{
							title: {
								text: null
							}
						}],
						legend: {
							align: 'right',
							verticalAlign: 'top',
							layout: 'vertical',
							x: 0,
							y: 100,
							symbolRadius: 1,
							borderWidth: 1
						},
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							column: {
								stacking: 'normal',
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.8,
								borderColor: '#212121'
							}
						},
						series: [{
							name: 'Stock Policy',
							type: 'area',
							marker:{
								enabled:false
							},
							lineColor: 'red',
							color: 'RGBA(255,0,0,0.05)',
							data: stock_policy,
							dashStyle: 'shortdash'
						}, {
							name: 'Plan Delivery',
							type: 'column',
							data: plan_delivery,
							color: '#64b5f6'
						}, {
							name: 'Plan Stock',
							type: 'column',
							data: plan_stock,
							color: '#757575'
						}, {
							name: 'Actual Delivery',
							type: 'column',
							stack: 'Stock',
							data: actual_delivery,
							color: '#fff176'

						}, {
							name: 'Actual WIP',
							type: 'column',
							stack: 'Stock',
							data: stock_wip,
							color: '#dcedc8'

						}, {
							name: 'Actual MSTK',
							type: 'column',
							stack: 'Stock',
							data: stock_mstk,
							color: '#4caf50'

						}, {
							name: 'Plan Usage',
							type: 'spline',
							data: plan_usage,
							dashStyle: 'shortdash',
							color: '#212121'
						}, {
							name: 'Actual Usage',
							type: 'spline',
							data: actual_usage,
							color: '#f57f17'
						}]
					});
				});
}
});
}

</script>
@endsection