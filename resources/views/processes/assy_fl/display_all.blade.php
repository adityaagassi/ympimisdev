@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
	padding: 1px;
}
table.table-bordered{
	border:1px solid black;
	/*margin-top:20px;*/
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid black;
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Display WIP Flute<span class="text-purple"> </span>
		<small>Daily WIP <span class="text-purple">  </span></small>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-8">
			<div id="container" style="width: 100%; height: 250; margin: 0 auto;"></div>
		</div>
		<div class="col-md-4">
			<div id="container2" style="width: 100%; margin: 0 auto;"></div>
		</div>
		<div class="col-md-12">
			<table id="tableStock" class="table table-bordered">
				<thead id="tableHead">
				</thead>
				<tbody id="tableBody">
				</tbody>
				<tfoot id="tableFoot" style="background-color: RGB(252, 248, 227);">
				</tfoot>
			</table>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>

<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchTableStock();
		fetchChartStock();
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

	function fetchChartStock(){
		$.get('{{ url("fetch/wipflallchart") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.efficiencyData;
					var stockCat = [];
					var stockPlan = [];
					var stockActual = [];

					for (i = 0; i < data.length; i++) {
						stockCat.push(data[i].model);
						stockPlan.push(data[i].plan);
						stockActual.push(data[i].stock);
					}

					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'WIP Stock Accuracy <br><span style="color:rgba(96,92,168);"> ??? </span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: stockCat,
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
							data: stockPlan,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: stockActual,
							pointPadding: 0.2
						}]
					});

					Highcharts.chart('container2', {
						colors: ['#f45b5b', '#8085e9', '#8d4654', '#7798BF', '#aaeeee',
						'#ff0066', '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
						chart: {
							backgroundColor: null,
							type: 'pie'
						},
						title: {
							text: null
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true
							}
						},
						credits:{
							enabled: false
						},
						series: [{
							name: 'Brands',
							colorByPoint: true,
							data: [{
								name: 'Chrome',
								y: 61.41
							}, {
								name: 'Internet Explorer',
								y: 11.84
							}, {
								name: 'Firefox',
								y: 10.85
							}, {
								name: 'Edge',
								y: 4.67
							}, {
								name: 'Safari',
								y: 4.18
							}, {
								name: 'Other',
								y: 7.05
							}]
						}]
					});
					setTimeout(fetchChartStock, 1000);
				}
				else{
					alert('Attempt to retrieve data failed')
				}
			}
			else{
				alert('Disconnected from server')
			}
		});
		
	}

	function fetchTableStock(){
		$.get('{{ url("fetch/wipflallstock") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#tableHead').html("");
					var tableHead = '';
					var tableFoot = '';
					var heads = [];
					tableHead += '<tr>';
					tableFoot += '<tr>';
					tableHead += '<th style="width:10%; background-color: rgba(126,86,134,.7); text-align: center; font-size: 18px;">Process/Model</th>';
					tableFoot += '<th style="text-align: center; width: 10%; font-size: 2vw;">Total</th>';
					totalHead = 0;
					$.each(result.inventory, function(index, value) {
						if ($.inArray(value.model, heads)==-1) {
							heads.push(value.model);
							tableHead += '<th style="width:4.5%; background-color: rgba(126,86,134,.9); text-align: center; font-size: 18px;">'+value.model.substring(3)+'</th>';
							tableFoot += '<th style="text-align: center; width: 4.5%; font-size: 2vw;"></th>';
							totalHead += 1;
						}
					});
					tableHead += '</tr>';
					tableFoot += '</tr>';
					$('#tableHead').append(tableHead);
					$('#tableFoot').append(tableFoot);

					$('#tableBody').html("");
					var tableBody = '';

					tableBody += '<tr>';
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold;">Stamping-Kariawase</td>';
					$.each(result.inventory, function(index, value){
						if(value.process_code == 1){
							tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+value.quantity+'</td>'
						}
					})
					tableBody += '</tr>';
					tableBody += '<tr>';
					tableBody += '<td style="background-color: rgb(255,255,255); text-align: center; color: black; font-size: 16px; font-weight: bold;">Tanpo awase</td>';
					$.each(result.inventory, function(index, value){
						if(value.process_code == 2){
							tableBody += '<td style="background-color: rgb(255,255,255); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+value.quantity+'</td>'
						}
					})
					tableBody += '</tr>';
					tableBody += '<tr>';
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold;">Seasoning-Kanggou</td>';
					$.each(result.inventory, function(index, value){
						if(value.process_code == 3){
							tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+value.quantity+'</td>'
						}
					})
					tableBody += '</tr>';
					tableBody += '<tr>';
					tableBody += '<td style="background-color: rgb(255,255,255); text-align: center; color: black; font-size: 16px; font-weight: bold;">Chousei</td>';
					$.each(result.inventory, function(index, value){
						if(value.process_code == 4){
							tableBody += '<td style="background-color: rgb(255,255,255); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+value.quantity+'</td>'
						}
					})
					tableBody += '</tr>';
					$('#tableBody').append(tableBody);

					$('#tableStock').DataTable({
						// 'scrollX': true,
						'responsive':true,
						// 'dom': 'Bfrtip',
						'paging': false,
						'lengthChange': true,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
						'autoWidth': true,
						"bJQueryUI": true,
						"bAutoWidth": false,
						"footerCallback": function (tfoot, data, start, end, display) {
							var intVal = function ( i ) {
								return typeof i === 'string' ?
								i.replace(/[\$,]/g, '')*1 :
								typeof i === 'number' ?
								i : 0;
							};
							var api = this.api();
							for(x = 1; x <= totalHead; x++){
								var total = api.column(x).data().reduce(function (a, b) {
									return intVal(a)+intVal(b);
								}, 0)
								$(api.column(x).footer()).html(total.toLocaleString());
							}
						}
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