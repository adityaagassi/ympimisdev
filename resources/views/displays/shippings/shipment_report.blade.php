@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	input {
		line-height: 22px;
	}
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" name="dateHidden" value="{{ date('Y-m-d') }}" id="dateHidden">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group date">
						<div class="input-group-addon" style="border: none; background-color: #605ca8; color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2">
					<button class="btn" style="background-color: #605ca8; color: white;" onClick="searchDate()"><i class="fa fa-search"></i> Search</button>
				</div>
				<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div id="container3" style="width:100%; height:530px;"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableResult">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Material</th>
								<th>Description</th>
								<th>Quantity</th>
							</tr>
						</thead>
						<tbody id="modalResultBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th id="modalResultTotal"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


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
		$('#tanggal').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		fetchChart($('#dateHidden').val());
		setInterval(function(){
			fetchChart($('#dateHidden').val());
		}, 10000);
	});

	function searchDate(){
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


		var date = $.date($('#tanggal').val());

		if($('#tanggal').val() != 0){
			fetchChart(date);
		}
	}

	function fetchChart(id){
		if(id != 0){
			$('#dateHidden').val(id);
		}
		var data = {
			date : id
		}
		$.get('{{ url("fetch/display/shipment_report") }}', data, function(result, status, xhr){
			if(result.status){
				var data3 = result.chartResult3;
				var xAxis3 = []
				, planBLCount = []
				, actualBLCount = []

				for (i = 0; i < data3.length; i++) {
					xAxis3.push(data3[i].hpl);
					planBLCount.push(data3[i].prc_plan);
					actualBLCount.push(data3[i].prc_actual);
				}
				var yAxisLabels = [0,25,50,75,110];	

				Highcharts.chart('container3', {
					colors: ['rgba(255, 0, 0, 0.15)','rgba(255, 69, 0, 0.70)'],
					chart: {
						type: 'column',
						backgroundColor: null
					},
					legend: {
						enabled:true,
						itemStyle: {
							fontSize:'20px',
							font: '20pt Trebuchet MS, Verdana, sans-serif',
							color: '#000000'
						}
					},
					credits: {
						enabled: false
					},
					title: {
						text: '<span style="font-size: 3vw;">Weekly Shipment ETD SUB</span><br><span style="color: rgba(96, 92, 168);">'+ result.weekTitle +'</span>',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: xAxis3,
						labels: {
							style: {
								color: 'rgba(75, 30, 120)',
								fontSize: '30px',
								fontWeight: 'bold'
							}
						}
					},
					yAxis: {
						tickPositioner: function() {
							return yAxisLabels;
						},
						labels: {
							enabled:false
						},
						min: 0,
						title: {
							text: ''
						}
							// stackLabels: {
							// 	format: 'Total: {total:,.0f}set(s)',
							// 	enabled: true,
							// 	style: {
							// 		fontWeight: 'bold',
							// 		color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							// 	}
							// }
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								}
							},
							series: {
								animation:{
									duration:0
								},
								// pointPadding: 0.95,
								groupPadding: -0.2,
								// borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalDetail(this.category , this.series.name, result.weekTitle, result.now);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planBLCount
						}, {
							name: 'Actual',
							data: actualBLCount
						}]
					});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function modalDetail(hpl, name, week, date){
		$('#modalResult').modal('show');
		$('#loading').show();
		$('#modalResultTitle').hide();
		$('#tableResult').hide();
		var data = {
			hpl:hpl,
			name:name,
			week:'W'+week.substring(5),
			date:date,
		}
		$.get('{{ url("fetch/display/shipment_report_detail") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modalResultTitle').html('');
					$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
					$('#modalResultBody').html('');
					var blData = '';
					var blTotal = 0;
					$.each(result.blData, function(key, value) {
						blData += '<tr>';
						blData += '<td>'+ value.material_number +'</td>';
						blData += '<td>'+ value.material_description +'</td>';
						blData += '<td>'+ value.quantity.toLocaleString() +'</td>';
						blData += '</tr>';
						blTotal += value.quantity;
					});
					$('#modalResultBody').append(blData);
					$('#modalResultTotal').html('');
					$('#modalResultTotal').append(blTotal.toLocaleString());

					$('#loading').hide();
					$('#modalResultTitle').show();
					$('#tableResult').show();
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