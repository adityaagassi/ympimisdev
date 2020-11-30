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
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.dataTables_filter {
		float: left !important;
	}

	.button-right{
		float: right; !important;
	}

	#loading, #error { display: none; }
	.disabled {
		pointer-events: none;
		cursor: default;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12" style="margin-bottom: 2%;">
			<table style="width: 100%; vertical-align: middle;">
				<tr>
					<td width="50%">
						<h3 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
					</td>
					<td width="50%">
						<div id="last_update" class="pull-right" style="color: black; margin: 0px; padding: 0px; font-size: 15px;"></div>
					</td>
				</tr>
				<tr>
					<td width="50%">
						<a class="btn btn-primary pull-left" href="{{ url("/index/stocktaking/menu") }}"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;&nbsp;KEMBALI</a>
						{{-- <a class="btn btn-primary pull-left" href="{{ url("/index/stocktaking/menu") }}"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;&nbsp;KEMBALI</a> --}}
						<button class="btn btn-success pull-left" style="margin-left: 1%;" onclick="monthChange()"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;REFRESH</button>
					</td>
					<td width="50%">
						<div class="input-group date pull-right col-xs-12 col-md-4 col-lg-4">
							<div class="input-group-addon bg-green">
								<i class="fa fa-calendar"></i>
							</div>
							<input style="text-align: center;" type="text" class="form-control datepicker" onchange="monthChange()" name="month" id="month" placeholder="Select Month" readonly>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</section>
@stop
@section('content')
<section class="content" style="padding-top: 0;">


	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach

	@if (session('error'))
	<input type="text" id="msg_error" value="{{ session('error') }}" hidden>
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
			<span style="font-size: 50px;">Please wait ... </span><br>
			<span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Progress Input By Location</a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Progress Audit By Location</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<div id="container0"></div>				
					</div>
					<div class="tab-pane" id="tab_2">
						<div id="container4"></div>				
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab"><a href="#tab_11" data-toggle="tab" id="tab_header_11">Quick Count Variance</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane" id="tab_11">
						<div id="container2"></div>				
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Plnt</th>
									<th>Group</th>
									<th>Location</th>
									<th>Percentage</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAudit">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableAudit">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
								</tr>
							</thead>
							<tbody id="bodyAudit">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalInputNew">
		<div class="modal-dialog modal-lg" style="width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableInputNew">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
									<th>Sub Store</th>
									<th>Category</th>
									<th>Material</th>
									<th>Description</th>
									<th>Qty</th>
									<th>Audit 1</th>
									{{-- <th>Audit 2</th> --}}
									<th>PI</th>
								</tr>
							</thead>
							<tbody id="bodyInputNew">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Plnt</th>
									<th>Group</th>
									<th>Location</th>
									<th>Percentage</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
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
<script src="{{ url("js/icheck.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#date_stock').datepicker({
			autoclose: true,
			todayHighlight: true
		});

		$('input[type="checkbox"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue'
		});

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'	
		});

		if($('#month').val() == ''){

			var now = new Date();
			var month = now.getMonth() + 1;
			var year = now.getFullYear();

			$('#month').val(year +'-'+ month);
		}

		monthChange();

	});

	function loadingPage(){
		$("#loading").show();
	}

	function monthChange(){
		var month = $('#month').val();

		$('#month_inquiry').val(month);
		$('#month_variance').val(month);
		$('#month_official_variance').val(month);

		var data = {
			month : month
		}

		$('#modalMonth').modal('hide');

		$.get('{{ url("fetch/stocktaking/check_month") }}', data, function(result, status, xhr){
			if(result.status){
			
				filledList();
				auditedList();	
				variance();

				$('#modalMonth').modal('hide');

			}else{
				$('#modalMonth').modal('hide');
				openErrorGritter('Error', result.message);
			}

		});
	}

	function bulanText(param){

		var index = param.split('-');
		var bulan = parseInt(index[1]);
		var tahun = parseInt(index[0]);
		var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		return bulanText[bulan-1]+" "+tahun;
	}

	function filledList() {

		var month = $('#month').val();

		if(month != ''){
			var data = {
				month : month
			}

			$.get('{{ url("fetch/stocktaking/filled_list_new") }}', data, function(result, status, xhr){
				if(result.status){
					$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

					var location_new = [];
					var use = [];
					var no_use = [];

					for (var i = 0; i < result.data.length; i++) {
						location_new.push(result.data[i].location);
						use.push(parseInt(result.data[i].use));
						no_use.push(parseInt(result.data[i].no_use));
					}

					Highcharts.chart('container0', {
						chart: {
							height: 225,
							type: 'column'
						},
						title: {
							text: 'Progress Input By Location'
						},	
						legend:{
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 0,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							shadow: false
						},
						credits:{	
							enabled:false
						},
						xAxis: {
							categories: location_new,
							type: 'category'
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
								'Total Item: ' + this.point.stackTotal;
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
								stacking: 'percent',
								dataLabels: {
									enabled: true,
									formatter: function() {
										return this.y;
									},
									style: {
										fontWeight: 'bold',
									}
								},
								point: {
									events: {
										click: function () {
											fillInputModalNew(this.category, this.series.name);
										}
									}
								}
							}
						},
						series: [{
							name: 'NO USE',
							data: no_use,
							color: 'rgba(255, 0, 0, 0.25)'
						}, {
							name: 'USE',
							data: use,
							color: '#00a65a'
						}]
					});
				}
			});


			// $.get('{{ url("fetch/stocktaking/filled_list_by_store") }}', data, function(result, status, xhr){
			// 	if(result.status){
			// 		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

			// 		var store = [];
			// 		var fill_new = [];
			// 		var empty_new = [];

			// 		for (var i = 0; i < result.data.length; i++) {
			// 			store.push(result.data[i].store);
			// 			fill_new.push(parseInt(result.data[i].qty));
			// 			empty_new.push(parseInt(result.data[i].empty));
			// 		}

			// 		Highcharts.chart('container6', {
			// 			chart: {
			// 				height: 225,
			// 				type: 'column'
			// 			},
			// 			title: {
			// 				text: 'Progress By Store'
			// 			},	
			// 			legend:{
			// 				enabled: false
			// 			},
			// 			credits:{	
			// 				enabled:false
			// 			},
			// 			xAxis: {
			// 				categories: store,
			// 				type: 'category'
			// 			},
			// 			yAxis: {
			// 				title: {
			// 					enabled:false,
			// 				},
			// 				labels: {
			// 					enabled:false
			// 				}
			// 			},
			// 			tooltip: {
			// 				formatter: function () {
			// 					return '<b>' + this.x + '</b><br/>' +
			// 					this.series.name + ': ' + this.y + '<br/>' +
			// 					'Total Item: ' + this.point.stackTotal;
			// 				}
			// 			},
			// 			plotOptions: {
			// 				column: {
			// 					stacking: 'percent',
			// 				},
			// 				series:{
			// 					animation: false,
			// 					pointPadding: 0.93,
			// 					groupPadding: 0.93,
			// 					borderWidth: 0.93,
			// 					cursor: 'pointer',
			// 					stacking: 'percent',
			// 					dataLabels: {
			// 						enabled: true,
			// 						formatter: function() {
			// 							return this.y;
			// 						},
			// 						style: {
			// 							fontWeight: 'bold',
			// 						}
			// 					},
			// 					point: {
			// 						events: {
			// 							click: function () {
			// 								fillInputModalByStore(this.category, this.series.name);
			// 							}
			// 						}
			// 					}
			// 				}
			// 			},
			// 			series: [{
			// 				name: 'Empty',
			// 				data: empty_new,
			// 				color: 'rgba(255, 0, 0, 0.25)'
			// 			}, {
			// 				name: 'Inputted',
			// 				data: fill_new,
			// 				color: '#00a65a'
			// 			}]
			// 		});
			// 	}
			// });


			// $.get('{{ url("fetch/stocktaking/filled_list_by_substore") }}', data, function(result, status, xhr){
			// 	if(result.status){
			// 		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

			// 		var sub_store = [];
			// 		var fill_new = [];
			// 		var empty_new = [];

			// 		for (var i = 0; i < result.data.length; i++) {
			// 			sub_store.push(result.data[i].sub_store);
			// 			fill_new.push(parseInt(result.data[i].qty));
			// 			empty_new.push(parseInt(result.data[i].empty));
			// 		}

			// 		Highcharts.chart('container5', {
			// 			chart: {
			// 				height: 225,
			// 				type: 'column'
			// 			},
			// 			title: {
			// 				text: 'Progress By Sub Store'
			// 			},	
			// 			legend:{
			// 				enabled: false
			// 			},
			// 			credits:{	
			// 				enabled:false
			// 			},
			// 			xAxis: {
			// 				categories: sub_store,
			// 				type: 'category'
			// 			},
			// 			yAxis: {
			// 				title: {
			// 					enabled:false,
			// 				},
			// 				labels: {
			// 					enabled:false
			// 				}
			// 			},
			// 			tooltip: {
			// 				formatter: function () {
			// 					return '<b>' + this.x + '</b><br/>' +
			// 					this.series.name + ': ' + this.y + '<br/>' +
			// 					'Total Item: ' + this.point.stackTotal;
			// 				}
			// 			},
			// 			plotOptions: {
			// 				column: {
			// 					stacking: 'percent',
			// 				},
			// 				series:{
			// 					animation: false,
			// 					pointPadding: 0.93,
			// 					groupPadding: 0.93,
			// 					borderWidth: 0.93,
			// 					cursor: 'pointer',
			// 					stacking: 'percent',
			// 					dataLabels: {
			// 						enabled: true,
			// 						formatter: function() {
			// 							return this.y;
			// 						},
			// 						style: {
			// 							fontWeight: 'bold',
			// 						}
			// 					},
			// 					point: {
			// 						events: {
			// 							click: function () {
			// 								fillInputModalBySubstore(this.category, this.series.name);
			// 							}
			// 						}
			// 					}
			// 				}
			// 			},
			// 			series: [{
			// 				name: 'Empty',
			// 				data: empty_new,
			// 				color: 'rgba(255, 0, 0, 0.25)'
			// 			}, {
			// 				name: 'Inputted',
			// 				data: fill_new,
			// 				color: '#00a65a'
			// 			}]
			// 		});
			// 	}
			// });

		}
	}

	function fillInputModal(group, series) {

		$('#loading').show();
		$('#tableInput').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/filled_list_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableInput').DataTable().clear();
				$('#tableInput').DataTable().destroy();
				$('#bodyInput').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
					var color = ''

					if(result.input_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"';
					}
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.input_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].category +'</td>';
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

					// if(result.input_detail[i].audit2 != null){
					// 	body += '<td style="width: 1%;">'+ result.input_detail[i].audit2.toLocaleString() +'</td>';
					// }else{
					// 	body += '<td style="width: 1%;"></td>';
					// }

					if(result.input_detail[i].final_count != null){
						body += '<td style="width: 1%; font-weight: bold;">'+ result.input_detail[i].final_count.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}


					body += '</tr>';
				}

				$('#bodyInput').append(body);

				var table = $('#tableInput').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				$('#modalInput').modal('show');
				$('#tableInput').show();
			}
		});
	}

	function fillInputModalNew(group, series) {

		$('#loading').show();
		$('#tableInputNew').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/filled_list_detail_new") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableInputNew').DataTable().clear();
				$('#tableInputNew').DataTable().destroy();
				$('#bodyInputNew').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
					var color = ''
					if(result.input_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"';
					}
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.input_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].sub_store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].category +'</td>';
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

					// if(result.input_detail[i].audit2 != null){
					// 	body += '<td style="width: 1%;">'+ result.input_detail[i].audit2.toLocaleString() +'</td>';
					// }else{
					// 	body += '<td style="width: 1%;"></td>';
					// }

					if(result.input_detail[i].final_count != null){
						body += '<td style="width: 1%; font-weight: bold;">'+ result.input_detail[i].final_count.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}


					body += '</tr>';
				}

				$('#bodyInputNew').append(body);

				var table = $('#tableInputNew').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				// $('#modalInput').modal('show');
				$('#modalInputNew').modal('show');
				$('#tableInputNew').show();
			}
		});
	}


	function fillInputModalByStore(group, series) {

		$('#loading').show();
		$('#tableInputNew').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/filled_list_detail_by_store") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableInputNew').DataTable().clear();
				$('#tableInputNew').DataTable().destroy();
				$('#bodyInputNew').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
					var color = ''
					if(result.input_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"';
					}
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.input_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].sub_store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].category +'</td>';
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

					// if(result.input_detail[i].audit2 != null){
					// 	body += '<td style="width: 1%;">'+ result.input_detail[i].audit2.toLocaleString() +'</td>';
					// }else{
					// 	body += '<td style="width: 1%;"></td>';
					// }

					if(result.input_detail[i].final_count != null){
						body += '<td style="width: 1%; font-weight: bold;">'+ result.input_detail[i].final_count.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}


					body += '</tr>';
				}

				$('#bodyInputNew').append(body);

				var table = $('#tableInputNew').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				// $('#modalInput').modal('show');
				$('#modalInputNew').modal('show');
				$('#tableInputNew').show();
			}
		});
	}

	function fillInputModalBySubstore(group, series) {

		$('#loading').show();
		$('#tableInputNew').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/filled_list_detail_by_substore") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableInputNew').DataTable().clear();
				$('#tableInputNew').DataTable().destroy();
				$('#bodyInputNew').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
					var color = ''
					if(result.input_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"';
					}
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.input_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].sub_store +'</td>';
					body += '<td style="width: 1%">'+ result.input_detail[i].category +'</td>';
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

					// if(result.input_detail[i].audit2 != null){
					// 	body += '<td style="width: 1%;">'+ result.input_detail[i].audit2.toLocaleString() +'</td>';
					// }else{
					// 	body += '<td style="width: 1%;"></td>';
					// }

					if(result.input_detail[i].final_count != null){
						body += '<td style="width: 1%; font-weight: bold;">'+ result.input_detail[i].final_count.toLocaleString() +'</td>';
					}else{
						body += '<td style="width: 1%;"></td>';
					}


					body += '</tr>';
				}

				$('#bodyInputNew').append(body);

				var table = $('#tableInputNew').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				// $('#modalInput').modal('show');
				$('#modalInputNew').modal('show');
				$('#tableInputNew').show();
			}
		});
	}


	function auditedList() {
		var month = $('#month').val();

		if(month != ''){
			var data = {
				month : month
			}

			// $.get('{{ url("fetch/stocktaking/audited_list") }}', data, function(result, status, xhr){
			// 	if(result.status){

			// 		var location = [];
			// 		var audited = [];
			// 		var notyet = [];

			// 		for (var i = 0; i < result.data.length; i++) {
			// 			location.push(result.data[i].location);
			// 			audited.push(parseInt(result.data[i].audited));
			// 			notyet.push(parseInt(result.data[i].not_audited));
			// 		}

			// 		// Highcharts.chart('container3', {
			// 		// 	chart: {
			// 		// 		height: 225,
			// 		// 		type: 'column'
			// 		// 	},
			// 		// 	title: {
			// 		// 		text: 'Progress Audit'
			// 		// 	},	
			// 		// 	legend:{
			// 		// 		enabled: false
			// 		// 	},
			// 		// 	credits:{	
			// 		// 		enabled:false
			// 		// 	},
			// 		// 	xAxis: {
			// 		// 		categories: location,
			// 		// 		type: 'category'
			// 		// 	},
			// 		// 	yAxis: {
			// 		// 		title: {
			// 		// 			enabled:false,
			// 		// 		},
			// 		// 		labels: {
			// 		// 			enabled:false
			// 		// 		}
			// 		// 	},
			// 		// 	tooltip: {
			// 		// 		formatter: function () {
			// 		// 			return '<b>' + this.x + '</b><br/>' +
			// 		// 			this.series.name + ': ' + this.y + '<br/>' +
			// 		// 			'Total Store: ' + this.point.stackTotal;
			// 		// 		}
			// 		// 	},
			// 		// 	plotOptions: {
			// 		// 		column: {
			// 		// 			stacking: 'percent',
			// 		// 		},
			// 		// 		series:{
			// 		// 			animation: false,
			// 		// 			pointPadding: 0.93,
			// 		// 			groupPadding: 0.93,
			// 		// 			borderWidth: 0.93,
			// 		// 			cursor: 'pointer',
			// 		// 			stacking: 'percent',
			// 		// 			dataLabels: {
			// 		// 				enabled: true,
			// 		// 				formatter: function() {
			// 		// 					return this.y;
			// 		// 				},
			// 		// 				style: {
			// 		// 					fontWeight: 'bold',
			// 		// 				}
			// 		// 			},
			// 		// 			point: {
			// 		// 				events: {
			// 		// 					click: function () {
			// 		// 						fillAuditModal(this.category, this.series.name);
			// 		// 					}
			// 		// 				}
			// 		// 			}
			// 		// 		}
			// 		// 	},
			// 		// 	series: [{
			// 		// 		name: 'Not yet',
			// 		// 		data: notyet,
			// 		// 		color: 'rgba(255, 0, 0, 0.25)'
			// 		// 	}, {
			// 		// 		name: 'Audited',
			// 		// 		data: audited,
			// 		// 		color: '#00a65a'
			// 		// 	}]
			// 		// });
			// 	}
			// });


			$.get('{{ url("fetch/stocktaking/audited_list_new") }}', data, function(result, status, xhr){
				if(result.status){

					var location_new = [];
					var audited_new = [];
					var notyet_new = [];

					for (var i = 0; i < result.data.length; i++) {
						location_new.push(result.data[i].location);
						audited_new.push(parseInt(result.data[i].audited));
						notyet_new.push(parseInt(result.data[i].not_audited));
					}

					Highcharts.chart('container4', {
						chart: {
							height: 225,
							type: 'column'
						},
						title: {
							text: 'Progress Audit'
						},	
						legend:{
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 0,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							shadow: false
						},
						credits:{	
							enabled:false
						},
						xAxis: {
							categories: location_new,
							type: 'category'
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
								'Total Store: ' + this.point.stackTotal;
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
								stacking: 'percent',
								dataLabels: {
									enabled: true,
									formatter: function() {
										return this.y;
									},
									style: {
										fontWeight: 'bold',
									}
								},
								point: {
									events: {
										click: function () {
											fillAuditModalNew(this.category, this.series.name);
										}
									}
								}
							}
						},
						series: [{
							name: 'Not yet',
							data: notyet_new,
							color: 'rgba(255, 0, 0, 0.25)'
						}, {
							name: 'Audited',
							data: audited_new,
							color: '#00a65a'
						}]
					});
				}
			});
		}
	}

	function fillAuditModal(group, series){

		$('#loading').show();
		$('#tableAudit').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/audited_list_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableAudit').DataTable().clear();
				$('#tableAudit').DataTable().destroy();
				$('#bodyAudit').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.audit_detail.length; i++) {

					var color = ''
					if(result.audit_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"'			
					}

					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.audit_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].store +'</td>';
					body += '</tr>';
				}

				$('#bodyAudit').append(body);

				var table = $('#tableAudit').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				$('#modalAudit').modal('show');
				$('#tableAudit').show();
			}
		});
	}

	function fillAuditModalNew(group, series){

		$('#loading').show();
		$('#tableAudit').hide();

		var month = $('#month').val();

		var data = {
			group : group,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/audited_list_detail_new") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableAudit').DataTable().clear();
				$('#tableAudit').DataTable().destroy();
				$('#bodyAudit').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.audit_detail.length; i++) {

					var color = ''
					if(result.audit_detail[i].ord == 0){
						color = 'style="background-color: rgba(255, 0, 0, 0.25);"';
					}else{
						color = 'style="background-color: #00a65a;"'			
					}

					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.audit_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].store +'</td>';
					body += '</tr>';
				}

				$('#bodyAudit').append(body);

				var table = $('#tableAudit').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					'sPaginationType': 'full_numbers',
					'bJQueryUI': true,
					'bAutoWidth': false,
					'processing': true,
					'bPaginate': false
				});

				$('#modalAudit').modal('show');
				$('#tableAudit').show();
			}
		});
	}

	function variance() {

		var month = $('#month').val();

		if(month != ''){
			var data = {
				month : month
			}

			$.get('{{ url("fetch/stocktaking/variance") }}', data, function(result, status, xhr){
				if(result.status){

					var location = [];
					var variance = [];


					for (var i = 0; i < result.variance.length; i++) {
						location.push(result.variance[i].group);
						variance.push(parseFloat(result.variance[i].percentage));
					}

					Highcharts.chart('container2', {
						chart: {
							height: 225,
							type: 'column'
						},
						title: {
							text: 'Quick Count Variance',
							style: {
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
							type: 'category'
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
								'Variance: ' + this.y.toFixed(2) + '%';
							}
						},
						plotOptions: {
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.y:.2f}%',
									style: {
										fontWeight: 'bold',
									}
								},
								point: {
									events: {
										click: function () {
											fillVarianceModal(this.category, this.series.name);
										}
									}
								}
							}
						},
						series: [{
							name: 'Variance',
							data: variance,
							color: '#ff851b'
						}]
					});
				}else{
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function fillVarianceModal(location, series){

		$('#loading').show();
		$('#tableVariance').hide();

		var month = $('#month').val();

		var data = {
			location : location,
			series : series,
			month : month
		}

		$.get('{{ url("fetch/stocktaking/variance_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyVariance').html('');
				$('#loading').hide();

				var body = '';
				for (var i = 0; i < result.variance_detail.length; i++) {
					var color = 'style="background-color: rgb(252, 248, 227)"';

					body += '<tr '+ color +'">';
					body += '<td>'+ result.variance_detail[i].plnt +'</td>';
					body += '<td>'+ result.variance_detail[i].group +'</td>';
					body += '<td>'+ result.variance_detail[i].location +'</td>';
					body += '<td>'+ result.variance_detail[i].percentage.toFixed(2) +'%</td>';
					body += '</tr>';

				}

				$('#bodyVariance').append(body);

				$('#modalVariance').modal('show');
				$('#tableVariance').show();
			}
		});
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


</script>
@endsection