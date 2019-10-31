@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}

	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
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

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0; color:black;">
					<select class="form-control select2" multiple="multiple" id='origin_group' data-placeholder="Select Products" style="width: 100%;">
						@foreach($origin_groups as $origin_group)
						<option value="{{ $origin_group->origin_group_code }}-{{ $origin_group->origin_group_name }}">{{ $origin_group->origin_group_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div class="col-xs-4" style="padding-left: 0px;">
					<div id="container1_shift3" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4">
					<div id="container1_shift1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4" style="padding-right: 0px;">
					<div id="container1_shift2" style="width: 100%;"></div>
				</div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container2" style="width: 100%;"></div>
			</div>
		</div>
	</div>

	<!-- start modal -->
	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
					<br><h4 class="modal-title" id="judul_table"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="tabel_detail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Kensa at</th>
										<th>Nama</th>
										<th>Model</th>
										<th>Key</th>
										<th>NG Name</th>
										<th>Quantity</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- end modal -->


</section>
@endsection
@section('scripts')
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

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 10000);
		
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);

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
		return year + "-" + month + "-" + day + " (" + h + ":" + m + ":" + s +")";
	}

	function fillChart() {
		var hpl = $('#origin_group').val();
		var tanggal = $('#tanggal').val();

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		
		var data = {
			tanggal:tanggal,
			code:hpl
		}

		$.get('{{ url("fetch/middle/buffing_op_ng") }}', data, function(result, status, xhr) {
			if(result.status){

				var date = result.date; 

				// SHIFT 3
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 's3'){

						var name_temp = result.ng_rate[i].name.split(" ");
						var in_name = '';
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.'){
							in_name = name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							if(name_temp[1].length > 7){
								in_name = name_temp[0]+'. '+name_temp[1].charAt(0);
							}else{
								in_name = result.ng_rate[i].name;
							}
						}
						op_name.push(in_name);
						rate.push(result.ng_rate[i].rate);						
					}
				}

				var chart = Highcharts.chart('container1_shift3', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 3 on '+date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -25,
							style: {
								fontSize: '1vw'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(68,169,168)',
						data: rate,
						showInLegend: false
					}]
				});

				

				// SHIFT 1
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 's1'){
						var name_temp = result.ng_rate[i].name.split(" ");
						var in_name = '';
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.'){
							in_name = name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							if(name_temp[1].length > 7){
								in_name = name_temp[0]+'. '+name_temp[1].charAt(0);
							}else{
								in_name = result.ng_rate[i].name;
							}
						}
						op_name.push(in_name);
						rate.push(result.ng_rate[i].rate);					
					}
				}

				var chart = Highcharts.chart('container1_shift1', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 1 on '+date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -25,
							style: {
								fontSize: '1vw'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(169,255,151)',
						data: rate,
						showInLegend: false
					}]
				});


				// SHIFT 2
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 's2'){
						var name_temp = result.ng_rate[i].name.split(" ");
						var in_name = '';
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.'){
							in_name = name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							if(name_temp[1].length > 7){
								in_name = name_temp[0]+'. '+name_temp[1].charAt(0);
							}else{
								in_name = result.ng_rate[i].name;
							}
						}
						op_name.push(in_name);
						rate.push(result.ng_rate[i].rate);			
					}
				}

				var chart = Highcharts.chart('container1_shift2', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 2 on '+date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -25,
							style: {
								fontSize: '1vw'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(255,116,116)',
						data: rate,
						showInLegend: false
					}]
				});
			}
		});

$.get('{{ url("fetch/middle/buffing_daily_op_ng_rate") }}', function(result, status, xhr) {
	if(result.status){

		var seriesData = [];
		var data = [];


		for (var i = 0; i < result.op.length; i++) {
			data = [];
			for (var j = 0; j < result.ng_rate.length; j++) {
				if(result.op[i].operator_id == result.ng_rate[j].operator_id){
					if(Date.parse(result.ng_rate[j].week_date) > Date.parse('2019-10-01')){
						if(result.ng_rate[j].ng_rate == 0){
							data.push([Date.parse(result.ng_rate[j].week_date), null]);
						}else{
							data.push([Date.parse(result.ng_rate[j].week_date), result.ng_rate[j].ng_rate]);
						}
					}else{
						data.push([Date.parse(result.ng_rate[j].week_date), null]);

					}
				}
			}
			seriesData.push({name : result.op[i].name, data: data});
		}

		var chart = Highcharts.stockChart('container2', {
			chart:{
				type:'spline',
			},
			rangeSelector: {
				selected: 0
			},
			scrollbar:{
				enabled:false
			},
			navigator:{
				enabled:false
			},
			title: {
				text: 'Daily NG Rate By Operators',
				style: {
					fontSize: '30px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Last Update: '+getActualFullDate(),
				style: {
					fontSize: '18px',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				plotLines: [{
					color: '#FFFFFF',
					width: 2,
					value: 0,
					dashStyles: 'longdashdot'
				}]
			},
			xAxis: {
				categories: 'datetime',
				tickInterval: 24 * 3600 * 1000 
			},
			tooltip: {
				pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
				split: false,
			},
			legend : {
				enabled:false
			},
			credits: {
				enabled:false
			},
			plotOptions: {
				series: {
					dataLabels: {
						enabled: true,
						format: '{point.y:,.1f}%',
					},
					connectNulls: true,
					shadow: {
						width: 3,
						opacity: 0.4
					},
					label: {
						connectorAllowed: false
					},
					cursor: 'pointer',
				}
			},
			series: seriesData,
			responsive: {
				rules: [{
					condition: {
						maxWidth: 500
					},
					chartOptions: {
						legend: {
							layout: 'horizontal',
							align: 'center',
							verticalAlign: 'bottom'
						}
					}
				}]
			}
		});
	}
});


}


function showDetail(tgl, nama) {
	tabel = $('#tabel_detail').DataTable();
	tabel.destroy();

	$('#myModal').modal('show');

	var table = $('#tabel_detail').DataTable({
		'dom': 'Bfrtip',
		'responsive': true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/middle/buffing_detail_op_ng") }}",
				"data" : {
					tgl : tgl,
					nama : nama
				}
			},
			"columns": [
			{ "data": "created_at" },
			{ "data": "name" },
			{ "data": "model"},
			{ "data": "key"},
			{ "data": "ng_name"},
			{ "data": "quantity"},
			]
		});

}



</script>
@endsection