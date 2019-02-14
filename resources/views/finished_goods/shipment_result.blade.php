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
	padding: 0;
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
		<small>Based on ETD YMPI <span class="text-purple">???</span></small>
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
				<div class="box-body">
					<div class="col-md-3 col-md-offset-3">
						<div class="form-group">
							<label>Export Date From</label>
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
							<label>Export Date To</label>
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

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding">
					<table class="table table-hover table-bordered table-striped" id="tableModal">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 8%;">Material</th>
								<th style="width: 40%;">Description</th>
								<th style="width: 8%;">Dest.</th>
								<th style="width: 12%;">Plan</th>
								<th style="width: 12%;">Actual</th>
								<th style="width: 12%;">Diff</th>
							</tr>
						</thead>
						<tbody id="modalResultBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th id="modalResultTotal1"></th>
							<th id="modalResultTotal2"></th>
							<th id="modalResultTotal3"></th>
						</tfoot>
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

					var yAxisLabels = [0,25,50,75,101];
					Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: null
						},
						xAxis: {
							categories: xCategories,
							type: 'category',
							gridLineWidth: 5,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								// rotation: -40,
								style: {
									color: 'rgba(75, 30, 120)'
									// fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							min: 0,
							title: {
								enabled:false,
							},
							tickPositioner: function() {
								return yAxisLabels;
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
								borderWidth: 0
							},
							series:{
								pointPadding: 0.96,
								groupPadding: 0.96,
								borderWidth: 0.96,
								shadow: false,
								color: 'rgba(126,86,134,.7)',
								borderColor: '#303030',
								cursor: 'pointer',
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
										color: 'black',
										textOutline: false
									}
								},
								point: {
									events: {
										click: function () {
											fillModal(this.category, this.series.name);
										}
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
						series: seriesData
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

	function fillModal(date, hpl){
		var data = {
			date:date,
			hpl:hpl
		};
		$.get('{{ url("fetch/tb_shipment_result") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#tableModal').DataTable().destroy();
					$('#modalResultTitle').html('');
					$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
					$('#modalResultBody').html('');
					var resultData = '';
					var resultTotal1 = 0;
					var resultTotal2 = 0;
					var resultTotal3 = 0;
					$.each(result.shipment_results, function(key, value) {
						resultData += '<tr>';
						resultData += '<td>'+ value.material_number +'</td>';
						resultData += '<td>'+ value.material_description +'</td>';
						resultData += '<td>'+ value.destination_shortname +'</td>';
						resultData += '<td>'+ value.plan.toLocaleString() +'</td>';
						resultData += '<td>'+ value.actual.toLocaleString() +'</td>';
						resultData += '<td style="font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal1 += value.plan;
						resultTotal2 += value.actual;
						resultTotal3 += value.diff;
					});
					$('#modalResultBody').append(resultData);
					$('#modalResultTotal1').html('');
					$('#modalResultTotal1').append(resultTotal1.toLocaleString());
					$('#modalResultTotal2').html('');
					$('#modalResultTotal2').append(resultTotal2.toLocaleString());
					$('#modalResultTotal3').html('');
					$('#modalResultTotal3').append(resultTotal3.toLocaleString());
					$('#tableModal').DataTable({
						"paging": false,
						'searching': false,
						'order':[],
						'info': false,
						"columnDefs": [{
							"targets": 5,
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
					$('#modalResult').modal('show');
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