@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Container Departure <span class="text-purple">?????????????????</span>
		{{-- <small>By Each Location <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb">

	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				{{-- <div class="box-header with-border" id="boxTitle">
					asdasd
				</div> --}}
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
							<li><a href="#tab_2" data-toggle="tab">By BL Date</a></li>
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
					<table class="table table-hover">
						<thead>
							<tr>
								<th style="font-size: 14">Cont. ID</th>
								<th style="font-size: 14">Destination</th>
								<th style="font-size: 14">Container Number</th>
								<th style="font-size: 14">Evidence Att.</th>
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
					var data = result.jsonData1;
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

function modalContainerDeparture(){
	$('#modalContainerDeparture').modal('show');
}
</script>
@endsection