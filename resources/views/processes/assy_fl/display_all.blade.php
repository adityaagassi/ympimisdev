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

	/*.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
		}*/
	</style>
	@stop
	@section('header')
	<section class="content-header">
		<h1>
			Display WIP Flute<span class="text-purple"> FL仕掛品表示</span>
			<small>Daily WIP <span class="text-purple"> 本日の仕掛品</span></small>
		</h1>
		<ol class="breadcrumb" id="last_update">
		</ol>
	</section>
	@stop
	@section('content')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<div class="row">
			<div class="col-md-7">
				<div id="container" style="width: 100%; height: 300; margin: 0 auto;"></div>
			</div>
			<div class="col-md-5">
				<div id="container2" style="width: 100%; margin: 0 auto;"></div>
			</div>
			<div class="col-md-12">

                <div class="table-responsive">
				<table id="tableStock" class="table table-bordered">
				</table>
			</div>
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
			var data = {
				originGroupCode : '041'
			}
			$.get('{{ url("fetch/wipflallchart") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
						var data = result.efficiencyData;
						var stockCat = [];
						var stockPlan = [];
						var stockActual = [];
						var maxPlan = [];

						for (i = 0; i < data.length; i++) {
							stockCat.push(data[i].model);
							stockPlan.push(data[i].plan);
							stockActual.push(parseInt(data[i].stock));
							maxPlan.push(data[i].max_plan);
						}

						var stockData = result.stockData;
						var stock1 = 0;
						var stock2 = 0;
						var stock3 = 0;
						var stock4 = 0;
						var stock5 = 0;
						var stock6 = 0;
						var stock7 = 0;
						var stock8 = 0;
						var stock9 = 0;
						var stock10 = 0;


						for (x = 0; x < stockData.length; x++) {
							if(stockData[x].process_code == 1 || stockData[x].process_code == 2){
								stock1 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 3 || stockData[x].process_code == 4 || stockData[x].process_code == 5 || stockData[x].process_code == 6){
								stock2 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 7 || stockData[x].process_code == 8){
								stock3 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 9 || stockData[x].process_code == 10 || stockData[x].process_code == 11){
								stock4 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 12){
								stock5 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 13 || stockData[x].process_code == 14){
								stock6 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 15 || stockData[x].process_code == 16){
								stock7 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 17 || stockData[x].process_code == 20){
								stock8 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 18 || stockData[x].process_code == 19 || stockData[x].process_code == 22){
								stock9 += parseInt(stockData[x].qty);
							}
							if(stockData[x].process_code == 21 || stockData[x].process_code == 23){
								stock10 += parseInt(stockData[x].qty);
							}
						}

						Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
							return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
						};

						Highcharts.chart('container', {
							colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)','rgba(255,0,0,1)'],
							chart: {
								type: 'column',
								backgroundColor: null,
								spacingTop: 0,
								spacingLeft: 0,
								spacingRight: 0,
								spacingBottom: 0
							},
							title: {
								text: '<span>Current WIP Stock '+result.currStock+' Day(s)</span>',
								style: {
									fontSize: '30px',
									fontWeight: 'bold'
								}
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
							// enabled:true
						},
						plotOptions: {
							series:{
								minPointLength: 3,
								pointPadding: 0,
								groupPadding: 0,
								animation:{
									duration:false
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
						}, {
							name: 'MaxPlan',
							marker: {
								symbol: 'c-rect',
								lineWidth:3,
								lineColor: 'rgb(255,0,0)',
								radius: 10,
							},
							type: 'scatter',
							data: maxPlan
						}]
					});

						// Highcharts.chart('container2', {
						// 	colors: ['rgb(241,92,128)','rgb(128,133,233)','rgb(247,163,92)','rgb(144,237,125)'],
						// 	chart: {
						// 		backgroundColor: null,
						// 		type: 'pie',
						// 		spacingTop: 0,
						// 		spacingLeft: 0,
						// 		spacingRight: 0,
						// 		spacingBottom: 0
						// 	},
						// 	exporting: { enabled: false },
						// 	title: {
						// 		text: null
						// 	},
						// 	tooltip: {
						// 		pointFormat: '{series.name}: <b>{point.y}</b>'
						// 	},
						// 	legend:{
						// 		enabled:false
						// 	},
						// 	plotOptions: {
						// 		pie: {
						// 			allowPointSelect: true,
						// 			cursor: 'pointer',
						// 			borderColor: 'rgb(126,86,134)',
						// 			dataLabels: {
						// 				enabled: true,
						// 				format: '<b>{point.name}<br/>{point.y} sets</b>',
						// 				distance: -50,
						// 				style:{
						// 					fontSize:'16px',
						// 					textOutline:0
						// 				},
						// 				color:'black',
						// 			},
						// 			showInLegend: true
						// 		},
						// 		series:{
						// 			animation:{
						// 				duration:false
						// 			}
						// 		}
						// 	},
						// 	credits:{
						// 		enabled: false
						// 	},
						// 	series: [{
						// 		data: [{
						// 			name: 'Stamp - Perakitan',
						// 			y: stock1
						// 		},
						// 		{
						// 			name: 'Kariawase',
						// 			y: stock2
						// 		},
						// 		{
						// 			name: 'Tanpoire - Perakitan Ulang',
						// 			y: stock3
						// 		},
						// 		{
						// 			name: 'Tanpo Awase',
						// 			y: stock4
						// 		},
						// 		{
						// 			name: 'Yuge',
						// 			y: stock5
						// 		},
						// 		{
						// 			name: 'Kango',
						// 			y: stock6
						// 		},
						// 		{
						// 			name: 'Renraku',
						// 			y: stock7
						// 		},
						// 		{
						// 			name: 'QA Fungsi - Repair',
						// 			y: stock8
						// 		},
						// 		{
						// 			name: 'Fukiage 1 & 2',
						// 			y: stock9
						// 		},
						// 		{
						// 			name: 'QA Visual 1 & 2',
						// 			y: stock10
						// 		}
						// 		]
						// 	}]
						// });
						Highcharts.chart('container2', {
						    colors: ['rgb(241,92,128)','rgb(128,133,233)','rgb(247,163,92)','rgb(144,237,125)'],
							chart: {
								backgroundColor: null,
								type: 'pie',
								spacingTop: 0,
								spacingLeft: 0,
								spacingRight: 0,
								spacingBottom: 0
							},
						    exporting: { enabled: false },
							title: {
								text: null
							},
						    tooltip: {
						        pointFormat: '{series.name}: <b>{point.y}</b>'
						    },
						    accessibility: {
						        // point: {
						        //     valueSuffix: '%'
						        // }
						    },
						    plotOptions: {
						        pie: {
						            allowPointSelect: true,
						            cursor: 'pointer',
						            dataLabels: {
						                enabled: true,
						                format: '<b>{point.name}<br/>{point.y} sets</b>',
						                style: {
						                    fontSize: '14px'
						                }
						            },
						            animation: false,
						        }
						    },credits: {
								enabled: false
							},
						    series: [{
						        data: [{
									name: 'Stamp - Perakitan',
									y: stock1
								},
								{
									name: 'Kariawase',
									y: stock2
								},
								{
									name: 'Tanpoire - Perakitan Ulang',
									y: stock3
								},
								{
									name: 'Tanpo Awase',
									y: stock4
								},
								{
									name: 'Yuge',
									y: stock5
								},
								{
									name: 'Kango',
									y: stock6
								},
								{
									name: 'Renraku',
									y: stock7
								},
								{
									name: 'QA Fungsi - Repair',
									y: stock8
								},
								{
									name: 'Fukiage 1 & 2',
									y: stock9
								},
								{
									name: 'QA Visual 1 & 2',
									y: stock10
								}
								]
						    }]
						});
						setTimeout(fetchChartStock, 60000);
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
		if(xhr.status == 200){
			if(result.status){
				$('#tableStock').html("");
				$('#tableHead').html("");
				$('#tableFoot').html("");
				$('#tableBody').html("");
				var tableStock = '';
				var tableHead = '';
				var tableFoot = '';
				var tableBody = '';
				var totalFoot = 0;
				var heads = [];

				tableStock += '<thead id="tableHead">';
				tableStock += '</thead>';
				tableStock += '<tbody id="tableBody">';
				tableStock += '</tbody>';
				tableStock += '<tfoot id="tableFoot" style="background-color: RGB(252, 248, 227);">';
				tableStock += '</tfoot>';
				$('#tableStock').append(tableStock);

				tableHead += '<tr>';
				tableFoot += '<tr>';
				tableHead += '<th style="width:10%; background-color: rgba(126,86,134,.7); text-align: center; font-size: 18px;">Process/Model</th>';
				tableFoot += '<th style="text-align: center; width: 10%; font-size: 2vw;">Total</th>';
				totalHead = 0;
				$.each(result.stampperakitan, function(index, value) {
					if ($.inArray(value.model, heads)==-1) {
						heads.push(value.model);
						tableHead += '<th style="width:4.5%; background-color: rgba(126,86,134,.7); text-align: center; font-size: 18px;">'+value.model.substring(3)+'</th>';
						tableFoot += '<th style="text-align: center; width: 4.5%; font-size: 2vw;"></th>';
						totalHead += 1;
					}
				});
				tableHead += '<th style="width:4.5%; background-color: rgba(126,86,134,.7); text-align: center; font-size: 18px;">Total</th>';
				tableHead += '</tr>';

				tableHead += '<tr>';
				tableHead += '<th style="width:10%; background-color: rgba(248,161,63,1); text-align: center; font-size: 18px;">Stock Plan 3Days</th>';
				totalPlan = 0;
				$.each(result.plan, function(index, value){
					tableHead += '<th style="width:4.5%; background-color: rgba(248,161,63,1); text-align: center; font-size: 18px;">'+value.plan+'</th>';
					totalPlan += value.plan;
				})
				tableHead += '<th style="width:4.5%; background-color: rgba(248,161,63,1); text-align: center; font-size: 18px;">'+totalPlan+'</th>';
				tableHead += '</tr>';
				$('#tableHead').append(tableHead);

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Stamp - Perakitan</td>';
				total1 = 0;
				$.each(result.stampperakitan, function(index, value){
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total1 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total1+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Kariawase</td>';
				total2 = 0;
				$.each(result.kariawase, function(index, value){
					tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total2 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total2+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Tanpoire - Perakitan Ulang</td>';
				total3 = 0;
				$.each(result.tanpoireperakitan, function(index, value){
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total3 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total3+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Tanpo Awase</td>';
				total4 = 0;
				$.each(result.tanpoawase, function(index, value){
					tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total4 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total4+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Seasoning - Kango</td>';
				total5 = 0;
				$.each(result.seasoningkango, function(index, value){
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total5 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total5+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Renraku</td>';
				total6 = 0;
				$.each(result.renraku, function(index, value){
					tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total6 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total6+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">QA Fungsi</td>';
				total7 = 0;
				$.each(result.qafungsi, function(index, value){
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total7 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total7+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Fukiage 1 & 2- Repair</td>';
				total8 = 0;
				$.each(result.fukiagerepair, function(index, value){
					tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total8 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(245, 245, 245); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total8+'</td>';
				tableBody += '</tr>';

				tableBody += '<tr>';
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 16px; font-weight: bold; width:50%;">Qa Visual 1 & 2</td>';
				total9 = 0;
				$.each(result.qavisual, function(index, value){
					tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+parseInt(value.quantity)+'</td>';
					total9 += parseInt(value.quantity);
				})
				tableBody += '<td style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 24px; font-weight: bold;">'+total9+'</td>';
				tableBody += '</tr>';

				$('#tableBody').append(tableBody);
				totalFoot = total1+total2+total3+total4+total5+total6+total7+total8+total9;
				tableFoot += '<th style="text-align: center; width: 4.5%; font-size: 2vw; background-color: RGB(255,204,255);">'+totalFoot+'</th>';
				tableFoot += '</tr>';
				$('#tableFoot').append(tableFoot);

				$('#tableStock').DataTable().clear();
				$('#tableStock').DataTable().destroy();
				$('#tableStock').DataTable({
						// 'scrollX': true,
						'responsive':false,
						// 'dom': 'Bfrtip',
						'paging': false,
						'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
						'autoWidth': false,
						"bJQueryUI": false,
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
				$('#tableStock').find("thead th").removeClass("sorting_asc");
				setTimeout(fetchTableStock, 60000);
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