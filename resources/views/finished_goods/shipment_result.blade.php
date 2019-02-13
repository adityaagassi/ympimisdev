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
		Shipment Result <span class="text-purple">出荷結果</span>
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
					asdasd
				</div>
				<div class="box-body">
					<div class="col-md-3 col-md-offset-3">
						<div class="form-group">
							<label>Ship. Date From</label>
							<div class="input-group date">
								<div class="input-group-addon" style="background-color: rgba(126,86,134,.7);">
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
								<div class="input-group-addon" style="background-color: rgba(126,86,134,.7);">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group pull-right">
								<button id="search" onClick="fillChart()" class="btn btn-primary bg-purple">Update Chart</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div id="container" style="width:100%; height:450px;"></div>
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

	function getActualFullDate(){
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
		$.get('{{ url("fetch/fg_shipment_result") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.shipment_results;
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
						cat = data[i].st_date;
						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}

					for(i = 0; i < data.length; i++){
						if(seriesData){
							var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].hpl;});
							if(currSeries.length === 0){
								seriesData[seriesData.length] = currSeries = {name: data[i].hpl, data: []};
							} else {
								currSeries = currSeries[0];
							}
							var index = currSeries.data.length;
							currSeries.data[index] = data[i].actual;
						} else {
							seriesData[0] = {name: data[i].hpl, data: [data[i].actual]}
						}
					}

					var yAxisLabels = [0,25,50,75,110];
					Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Column chart with negative values'
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
							tickPositioner: function() {
								return yAxisLabels;
							},
							stackLabels: {
								style: {
									color: 'black'
								},
								enabled: true,
								formatter: function() {

									return 'asdasd';

								}
							},
							labels: {
								enabled:false
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column:{
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								},
								dataLabels:{
									enabled:true,
									formatter: function() {
										return this.y+'%';
									}

								}
							},
							series:{
								pointPadding: 0.98,
								groupPadding: 0.98,
								borderWidth: 0.98,
								shadow: false,
								dataLabels: {
									enabled: true,
									rotation: -90,
									align: 'right',
									formatter: function() {
										return this.series.name +': '+ this.y +'%';
									},
									y: 10,
									style: {
										fontSize: '1vw',
										color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
									}
								}

							}
						},
						tooltip: {
							formatter: function() {
								return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y +'%';
							}
						},
						series: seriesData,
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