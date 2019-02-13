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
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Container Departure <span class="text-purple">コンテナー出発</span>
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
				<div class="box-header with-border" id="boxTitle">
				</div>
				<div class="box-body">
					<div class="col-md-3 col-md-offset-3">
						<div class="form-group">
							<label>Ship. Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Ship. Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group pull-right">
								<button id="search" onClick="fillChart()" class="btn btn-primary">Update Chart</button>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab_1" data-toggle="tab">By Shipment Date</a></li>
							<li><a href="#tab_2" data-toggle="tab">By Destination</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div id="container1" style="width:100%; height:450px;"></div>
							</div>
							<div class="tab-pane" id="tab_2">
								<div id="container2" style="width:100%; height:450px;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalContainerDeparture">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
				<div class="modal-body table-responsive no-padding">
					<table class="table table-hover table-striped table-bordered">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width:10%;">Cont. ID</th>
								<th style="width:5%;">Dest.</th>
								<th style="width:20%;">Container No.</th>
								<th style="width:15%;">Ship. Date</th>
								<th style="width:10%;">Evidence Att.</th>
							</tr>
						</thead>
						<tbody id="tableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
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
		$('#datefrom').datepicker({
			autoclose: true
		});
		$('#dateto').datepicker({
			autoclose: true
		});
		$('#datefrom').val("");
		$('#dateto').val("");

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
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var data = {
			datefrom:datefrom,
			dateto:dateto
		};
		$.get('{{ url("fetch/fg_container_departure") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#boxTitle').html('<i class="fa fa-info-circle"></i><h4 class="box-title">Containeer Plan: <b>'+ result.total_plan + ' unit(s)</b> Container Departed: <b>'+ result.total_actual + ' unit(s)</b>');
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.jsonData1;
					var seriesData = [];
					var xCategories = [];
					var i, cat;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					for(i = 0; i < data.length; i++){
						cat = data[i].shipment_date;
						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}
					for(i = 0; i < data.length; i++){
						if(seriesData){
							var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].status;});
							if(currSeries.length === 0){
								seriesData[seriesData.length] = currSeries = {name: data[i].status, data: []};
							} else {
								currSeries = currSeries[0];
							}
							var index = currSeries.data.length;
							currSeries.data[index] = data[i].quantity;
						} else {
							seriesData[0] = {name: data[i].status, data: [data[i].quantity]}
						}
					}
					var yAxisLabels = [0,25,50,75,100,110];

					Highcharts.chart({
						colors: ['rgba(255, 255, 255, 0.20)','rgba(75, 30, 120, 0.70)'],
						chart: {
							renderTo: 'container1',
							type: 'column'
						},
						title: {
							text: 'Containers Depart From YMPI'
						},
						xAxis: {
							categories: xCategories,
							gridLineWidth: 1,
							scrollbar: {
								enabled: true
							},
							labels: {
								rotation: -40,
								style: {
									fontSize: '13px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total Container Departed (unit)'
							},
							tickPositioner: function() {
								return yAxisLabels;
							},
							stackLabels: {
								style: {
									color: 'black'
								},
								enabled: true,
								formatter: function() {

									return this.axis.series[1].yData[this.x] + '/' + this.total;

								}
							},
							labels: {
								enabled:false
							}
						},
						credits: {
							enabled: false
						},
						legend:{
							enabled: false
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
							// column: {
							// 	stacking: 'normal',
							// 	dataLabels: {
							// 		color: 'white',
							// 		formatter: function() {
							// 			return this.y + '/' + this.total ;
							// 		}
							// 	}
							// }
						},
						tooltip: {
							formatter: function() {
								return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y +'<br/>'+
								'Total: '+ this.point.stackTotal;
							}
						},
						series: seriesData
					});

					var data = result.jsonData2;
					// data = data.reverse()
					var seriesData = [];
					var xCategories = [];
					var i, cat;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					for(i = 0; i < data.length; i++){
						cat = data[i].destination_shortname;
						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}
					for(i = 0; i < data.length; i++){
						dat = data[i].quantity;
						if(seriesData.indexOf(cat) === -1){
							seriesData[seriesData.length] = dat;
						}
					}

					Highcharts.chart('container2', {
						colors: ['rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'bar'
						},
						title: {
							text: 'Containers Depart From The Port'
						},
						credits: {
							enabled:false
						},
						xAxis: {
							categories: xCategories,
							type: 'category'
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total Container Departed (unit)'
							},
							allowDecimals:false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Departed: <b>{point.y} containers</b>'
						},
						plotOptions:{
							bar:{
								dataLabels:{
									enabled:true
								}
							},
							series: {
								borderColor: '#303030',
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalStock(this.Departed , this.series.name);
										}
									}
								}
							},
						},
						series: [{
							data: seriesData
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

function modalContainerDeparture(st_date){
	var data = {
		st_date:st_date,
	}
	$.get('{{ url("fetch/tb_container_departure") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#tableBody').html("");
				$('.modal-title').html("");
				$('.modal-title').html('Shipment Date: <b>' + result.st_date + '</b>');
				var tableData = '';
				$.each(result.table, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.container_id +'</td>';
					tableData += '<td>'+ value.destination_shortname +'</td>';
					tableData += '<td>'+ value.container_number +'</td>';
					tableData += '<td>'+ value.shipment_date +'</td>';
					if( value.att > 0 ){
						tableData += '<td><a href="javascript:void(0)" id="'+ value.container_id +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> '+ value.att +' attachment(s)</a></td>';
					}
					else
					{
						tableData += '<td><span id="'+ value.container_id +'" class="fa fa-paperclip"> '+ value.att +' attachment(s)</span></td>';
					}
					tableData += '</tr>';
				});
				$('#tableBody').append(tableData);
				$('#modalContainerDeparture').modal('show');
			}
			else
				alert('Attempt to retrieve data failed');
		}
		else{
			alert('Disconnected from server');
		}
	});
}

function downloadAtt(id){
	var data = {
		container_id:id
	}
	$.get('{{ url("download/att_container_departure") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			var file_path = result.file_path;
			var a = document.createElement('A');
			a.href = file_path;
			a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		}
		else{
			alert('Disconnected from server');
		}
	});
}
</script>
@endsection