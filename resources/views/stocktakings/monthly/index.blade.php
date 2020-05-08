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
	<div class="row">
		<div class="col-xs-9">
			<h2 style="margin-top: 0px;">Monthly Stock Taking<span class="text-purple"> 表面処理</span></h2>
		</div>
		<div class="col-xs-3">
			<div class="col-xs-10 pull-right" style="padding: 0px;">
				<div class="input-group date">
					<div class="input-group-addon bg-green">
						<i class="fa fa-calendar"></i>
					</div>
					<input style="text-align: center;" type="text" class="form-control datepicker" name="month" id="month" placeholder="Select Month" readonly>
				</div>
			</div>

			<div class="pull-right" id="last_update" style="color: black; margin: 0px; padding-top: 0px; padding-right: 0px; font-size: 1vw;"></div>

		</div>
	</div>
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
			<a href="{{ secure_url("index/stocktaking/no_use") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Input No Use</a>
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

	<div class="modal fade" id="modalInput">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<table class="table table-hover table-bordered table-striped" id="tableInput">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
									<th>Material</th>
									<th>Description</th>
									<th>Qty</th>
									<th>Audit 1</th>
									<th>Audit 2</th>
									<th>PI</th>
								</tr>
							</thead>
							<tbody id="bodyInput">
							</tbody>
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

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'

		});
		$('#month').blur();

		filledList();
		variance();

		setInterval(filledList, 300000);
		setInterval(variance, 300000);

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


	function filledList() {
		$.get('{{ url("fetch/stocktaking/filled_list") }}', function(result, status, xhr){
			if(result.status){
				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

				var area = [];
				var fill = [];
				var empty = [];

				for (var i = 0; i < result.data.length; i++) {
					area.push(result.data[i].area);
					fill.push(parseInt(result.data[i].qty));
					empty.push(parseInt(result.data[i].empty));
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
						categories: area,
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
										fillInputModal(this.category, this.series.name);
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

	function fillInputModal(group, series) {

		$('#loading').show();
		$('#tableInput').hide();
		
		var data = {
			group : group,
			series : series
		}

		$.get('{{ url("fetch/stocktaking/filled_list_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyInput').html('');
				$('#loading').hide();

				var color = ''
				if(series == "Empty"){
					color = 'style="background-color: rgb(255,116,116);"';
				}else{
					color = 'style="background-color: rgb(144,238,126);"'			
				}

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.input_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].material_number +'</td>';
					body += '<td style="width: 10%">'+ (result.input_detail[i].material_description || '-') +'</td>';
					
					if(result.input_detail[i].quantity != null){
						body += '<td style="width: 1%;">'+ result.input_detail[i].quantity.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}

					if(result.input_detail[i].audit1 != null){
						body += '<td style="width: 1%;">'+ result.input_detail[i].audit1.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}

					if(result.input_detail[i].audit2 != null){
						body += '<td style="width: 1%;">'+ result.input_detail[i].audit2.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}

					if(result.input_detail[i].final_count != null){
						body += '<td style="width: 1%; font-weight: bold;">'+ result.input_detail[i].final_count.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}
					

					body += '</tr>';
				}

				$('#bodyInput').append(body);
				$('#modalInput').modal('show');
				$('#tableInput').show();


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