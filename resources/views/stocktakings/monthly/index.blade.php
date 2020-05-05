@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	table{
		padding: 0px;
		color: black;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Monthly Stock Taking<span class="text-purple"> 表面処理</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url("index/stocktaking/summary_of_counting") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Summary of Counting</a>
			<a href="{{ secure_url("index/stocktaking/count") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Input No Use</a>
			<a href="{{ secure_url("index/stocktaking/count") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Input PI</a>
			<a href="{{ url("index/stocktaking/audit/"."1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 1</a>
			<a href="{{ url("index/stocktaking/audit/"."2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 2</a>
			<a href="javascript:void(0)" onClick="countPI()" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Breakdown PI</a>
			<a href="{{ url("index/stocktaking/unmatch") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Unmatch</a>
			<a href="{{ url("index/stocktaking/revise") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Revise</a>


			<br>

			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<form method="GET" action="{{ url("export/stocktaking/inquiry") }}">
				<button type="submit" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple; margin-top: 5px;"> Inquiry</button>
			</form>
			<form method="GET" action="{{ url("export/stocktaking/variance") }}">
				<button type="submit" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple; margin-top: 5px;"> Variance</button>
			</form>

		</div>
		<div class="col-xs-9" style="text-align: center; color: red;">
			<span style="font-size: 30px; "><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<br>

			<div class="col-xs-6">
				<table class="table" id="store_table">
					<tbody id="store_body"></tbody>
				</table>
			</div>

			<div class="col-xs-12">
				<div id="container1"></div>
			</div>

			<div class="col-xs-12">
				<div id="container2"></div>
			</div>

		</div>

	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Material</th>
									<th>Description</th>
									<th>Loc</th>
									<th>PI</th>
									<th>Book</th>
									<th>Diff</th>
									<th>Diff Abs</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th>Total</th>
								<th></th>
								<th></th>
								<th id="modalDetailTotal1"></th>
								<th id="modalDetailTotal2"></th>
								<th id="modalDetailTotal3"></th>
								<th id="modalDetailTotal4"></th>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
		$('body').toggleClass("sidebar-collapse");

		percentage();

		filledList();
		variance();
	});


	function countPI() {
		$("#loading").show();

		$.get('{{ url("index/stocktaking/count_pi") }}', function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Success', result.message);
			}

		});
	}

	function percentage() {

		$.get('{{ url("fetch/stocktaking/percentage_location") }}', function(result, status, xhr){
			if(result.status){

				$("#store_body").empty();
				var body = '';
				for (var i = 0; i < result.location.length; i++) {

					var active = '';
					if(parseInt(result.location[i].persen) < 100){
						active = ' active';
					}

					body += '<tr>';
					body += '<td style="width: 15%;">'+result.location[i].location+'</td>';
					
					body += '<td style="width: 75%;">';
					body += '<div class="progress-group">';
					body += '<div class="progress" style="height: 20px; margin: 0px;">';
					body += '<div class="progress-bar progress-bar-success progress-bar-striped'+ active +'" id="progress-bar" style="width: '+Math.ceil(result.location[i].persen)+'%;"></div>';
					body += '</div>';
					body += '</div>';
					body += '</td>';

					body += '<td style="width: 10%;">'+Math.ceil(result.location[i].persen)+'%</td>';
					body += '</tr>';
				}

				$("#store_body").append(body);
			}
		});
	}

	function filledList() {
		$.get('{{ url("fetch/stocktaking/filled_list") }}', function(result, status, xhr){
			if(result.status){

				var location = [];
				var fill = [];
				var empty = [];

				for (var i = 0; i < result.location.length; i++) {
					location.push(result.location[i].location);
					fill.push(parseInt(result.location[i].qty));
					empty.push(parseInt(result.location[i].empty));
				}

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						backgroundColor: {
							linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
							stops: []
						},
					},
					title: {
						text: 'Input',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},	
					legend:{
						enabled: false
					},
					credits:{	
						enabled:false
					},
					xAxis: {
						categories: location,
						type: 'category',
						gridLineWidth: 5,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '20px'
							}
						},
					},
					yAxis: {
						title: {
							enabled:false,
						},
						labels: {
							enabled:false
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y;
								},
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillVarianceModal(this.category);
									}
								}
							}
						}
					},
					series: [{
						name: 'Empty',
						data: empty,
						color: 'rgb(255,116,116)'
					}, {
						name: 'Filled',
						data: fill,
						color: 'rgb(144,238,126)'
					}]
				});


			}
		});
	}

	function variance() {
		$.get('{{ url("fetch/stocktaking/variance") }}', function(result, status, xhr){
			if(result.status){

				var location = [];
				var variance = [];
				var ok = [];

				for (var i = 0; i < result.variance.length; i++) {
					location.push(result.variance[i].location);
					variance.push(parseInt(result.variance[i].variance));
					ok.push(parseInt(result.variance[i].ok));
				}

				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						backgroundColor: {
							linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
							stops: []
						},
					},
					title: {
						text: 'Variance',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					legend:{
						enabled: false
					},
					credits:{	
						enabled:false
					},
					xAxis: {
						categories: location,
						type: 'category',
						gridLineWidth: 5,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '20px'
							}
						},
					},
					yAxis: {
						title: {
							enabled:false,
						},
						labels: {
							enabled:false
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y;
								},
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillVarianceModal(this.category);
									}
								}
							}
						}
					},
					series: [{
						name: 'Variance',
						data: variance,
						color: 'rgb(255,116,116)'
					}, {
						name: 'OK',
						data: ok,
						color: 'rgb(144,238,126)'
					}]
				});


			}
		});
	}

	function fillVarianceModal(location) {

		$('#loading').show();
		$('#tableVariance').hide();
		
		var data = {
			location : location
		}

		$.get('{{ url("fetch/stocktaking/variance_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyVariance').html('');
				$('#loading').hide();

				var body = '';
				var resultTotal1 = 0;
				var resultTotal2 = 0;
				var resultTotal3 = 0;
				var resultTotal4 = 0;
				for (var i = 0; i < result.variance_detail.length; i++) {
					var color = ''
					if(result.variance_detail[i].diff_abs > 0){
						color = 'style="background-color: rgb(255, 204, 255)"';
					}else{
						color += 'style="background-color: rgb(204, 255, 255);"'			
					}

					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.variance_detail[i].material_number +'</td>';
					body += '<td style="width: 10%">'+ (result.variance_detail[i].material_description || '-') +'</td>';
					body += '<td style="width: 1%">'+ result.variance_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.variance_detail[i].pi.toLocaleString() +'</td>';
					body += '<td style="width: 1%">'+ result.variance_detail[i].book.toLocaleString() +'</td>';
					body += '<td style="width: 1%; font-weight: bold;">'+ result.variance_detail[i].diff.toLocaleString() +'</td>';
					body += '<td style="width: 1%; font-weight: bold;">'+ result.variance_detail[i].diff_abs.toLocaleString() +'</td>';
					body += '</tr>';

					resultTotal1 += result.variance_detail[i].pi;
					resultTotal2 += result.variance_detail[i].book;
					resultTotal3 += result.variance_detail[i].diff;	
					resultTotal4 += result.variance_detail[i].diff_abs;
				}

				$('#bodyVariance').append(body);
				$('#modalDetailTotal1').html('');
				$('#modalDetailTotal1').append(resultTotal1.toLocaleString());
				$('#modalDetailTotal2').html('');
				$('#modalDetailTotal2').append(resultTotal2.toLocaleString());
				$('#modalDetailTotal3').html('');
				$('#modalDetailTotal3').append(resultTotal3.toLocaleString());
				$('#modalDetailTotal4').html('');
				$('#modalDetailTotal4').append(resultTotal4.toLocaleString());

				$('#modalVariance').modal('show');
				$('#tableVariance').show();


			}
		});
	}

	function exportInquiry() {
		$.get('{{ url("export/stocktaking/inquiry") }}', function(result, status, xhr){});
	}

	function exportVariance() {
		$.get('{{ url("export/stocktaking/variance") }}', function(result, status, xhr){});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}



</script>
@endsection