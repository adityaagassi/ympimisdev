@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Finished Goods Stock <span class="text-purple">?????????????????</span>
		<small>By Each Location <span class="text-purple">??????</span></small>
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
				<div class="box-header with-border">
					<i class="fa fa-info"></i>
					<h4 class="box-title" id="boxTitle"></h4>
				</div>
				<div class="box-body">
					<div id="container" style="width:100%; height:450px;"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalStock">
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
								<th style="font-size: 14">Material</th>
								<th style="font-size: 14">Description</th>
								<th style="font-size: 14">Quantity</th>
								{{-- <th style="font-size: 14">m&sup3;</th> --}}
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

		fillChart();
	});

	function fillChart(){
		$.get('{{ url("fetch/fg_stock") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#boxTitle').html('Total Stock: <b>'+  + '</b>pc(s) ()');
					var data = result.jsonData;
					var seriesData = [];
					var xCategories = [];
					var i, cat;
					for(i = 0; i < data.length; i++){
						cat = data[i].destination;
						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}
					for(i = 0; i < data.length; i++){
						if(seriesData){
							var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].location;});
							if(currSeries.length === 0){
								seriesData[seriesData.length] = currSeries = {name: data[i].location, data: []};
							} else {
								currSeries = currSeries[0];
							}
							var index = currSeries.data.length;
							currSeries.data[index] = data[i].actual;
						} else {
							seriesData[0] = {name: data[i].location, data: [data[i].actual]}
						}
					}

					var chart;
					$(document).ready(function() {
						chart = new Highcharts.Chart({
							chart: {
								renderTo: 'container',
								type: 'column'
							},
							title: {
								text: 'Finished Goods Stock By Location Chart'
							},
							xAxis: {
								categories: xCategories,
								gridLineWidth: 1,
								scrollbar: {
									enabled: true
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: 'Total Finished Goods'
								},
								stackLabels: {
									enabled: true,
									style: {
										fontWeight: 'bold',
										color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
									}
								}
							},
							credits: {
								enabled: false
							},
							plotOptions: {
								series: {
									cursor: 'pointer',
									stacking: 'normal',
									point: {
										events: {
											click: function () {
												// alert('Destinasi: ' + this.category + ', Location: ' + this.series.name +', qty: ' + this.y);
												modalStock(this.category , this.series.name);
											}
										}
									}
								}
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

	function modalStock(destination, location){
		if(location == 'Production'){
			var status = 0;
		}
		if(location == 'InTransit'){
			var status = 1;
		}
		if(location == 'FSTK'){
			var status = 2;
		}
		var data = {
			status:status,
			destination:destination
		}

		$.get('{{ url("fetch/tb_stock") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#tableBody').html("");
					$('.modal-title').html("");
					$('.modal-title').html('Location <b>' + result.location+ '</b> for Destination <b>' +result.title+'</b>');
					var tableData = '';
					$.each(result.table, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.actual +'</td>';
						tableData += '</tr>';

					});
					$('#tableBody').append(tableData);
					$('#modalStock').modal('show');
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