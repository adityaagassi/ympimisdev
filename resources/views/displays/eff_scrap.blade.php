@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div id="period_title" class="col-xs-9" style="background-color: rgba(248,161,63,0.9);"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center></div>
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" onchange="fetchChart()">
			</div>
		</div>
		<div class="col-xs-12" id="scrap_wscr"></div>
		<div class="col-xs-12" id="detail_scrap_wscr">

		</div>
		<div class="col-xs-12" id="scrap_mscr"></div>
		<div class="col-xs-12" id="detail_scrap_mscr">

		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 1%;">Quantity</th>
								<th style="width: 1%;">Total Amount</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot id="tableDetailFoot">
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

	jQuery(document).ready(function() {
		fetchChart();
		setInterval(fetchChart, 1000*60*60);
		$('#datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
	});

	var detail_mscr = "";
	var detail_wscr = "";

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

	function fetchDetail(a,b){
		// $('#loading').show();
		var loc = a.split("_");
		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();
		$('#tableDetailBody').html("");
		$('#tableDetailFoot').html("");
		$('#modalDetailTitle').text(a+' '+b);
		var detailData = "";
		var detailDataFoot = "";

		if(loc[0] == 'MSCR'){
			if(loc[1] == 'ALL'){

				var items = {}, base, key;
				$.each(detail_mscr, function(key, value){
					if($.date(value.posting_date) <= $.date(b)){
						key = value.material_number+'_'+value.material_description+'_'+value.std_price;
						if (!items[key]) {
							items[key] = 0;
						}
						if(value.movement_type == '102' || value.movement_type == '9S2'){
							items[key] -= value.quantity;
						}
						else{
							items[key] += value.quantity;
						}
					}
				});

				var outputArr = [];
				var total = 0;

				$.each(items, function(key, val) {
					if(val != 0){
						var a = key.split('_');

						detailData += '<tr>';
						detailData += '<td>'+a[0]+'</td>';
						detailData += '<td>'+a[1]+'</td>';
						detailData += '<td>'+val+'</td>';
						detailData += '<td>'+(val*a[2]).toFixed(1)+'</td>';
						detailData += '</tr>';

						total += val*a[2];
					}
				});
				detailDataFoot += '<tr>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td><b>'+total.toFixed(4)+'</b></td>';
				detailDataFoot += '</tr>';

				$('#tableDetailBody').append(detailData);
				$('#tableDetailFoot').append(detailDataFoot);
				$('#modalDetail').modal('show');

				$('#tableDetail').DataTable({
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
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [3],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{

				var items = {}, base, key;
				$.each(detail_mscr, function(key, value){
					if($.date(value.posting_date) <= $.date(b) && loc[1] == value.reason){
						key = value.material_number+'_'+value.material_description+'_'+value.std_price;
						if (!items[key]) {
							items[key] = 0;
						}
						if(value.movement_type == '102' || value.movement_type == '9S2'){
							items[key] -= value.quantity;
						}
						else{
							items[key] += value.quantity;
						}
					}
				});

				var outputArr = [];
				var total = 0;

				$.each(items, function(key, val) {
					if(val != 0){
						var a = key.split('_');

						detailData += '<tr>';
						detailData += '<td>'+a[0]+'</td>';
						detailData += '<td>'+a[1]+'</td>';
						detailData += '<td>'+val+'</td>';
						detailData += '<td>'+(val*a[2]).toFixed(1)+'</td>';
						detailData += '</tr>';

						total += val*a[2];
					}
				});
				detailDataFoot += '<tr>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td><b>'+total.toFixed(4)+'</b></td>';
				detailDataFoot += '</tr>';

				$('#tableDetailBody').append(detailData);
				$('#tableDetailFoot').append(detailDataFoot);
				$('#modalDetail').modal('show');

				$('#tableDetail').DataTable({
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
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [3],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
		}

		if(loc[0] == 'WSCR'){
			if(loc[1] == 'ALL'){

				var items = {}, base, key;
				$.each(detail_wscr, function(key, value){
					if($.date(value.posting_date) <= $.date(b)){
						key = value.material_number+'_'+value.material_description+'_'+value.std_price;
						if (!items[key]) {
							items[key] = 0;
						}
						if(value.movement_type == '102' || value.movement_type == '9S2'){
							items[key] -= value.quantity;
						}
						else{
							items[key] += value.quantity;
						}
					}
				});

				var outputArr = [];
				var total = 0;

				$.each(items, function(key, val) {
					if(val != 0){
						var a = key.split('_');

						detailData += '<tr>';
						detailData += '<td>'+a[0]+'</td>';
						detailData += '<td>'+a[1]+'</td>';
						detailData += '<td>'+val+'</td>';
						detailData += '<td>'+(val*a[2]).toFixed(1)+'</td>';
						detailData += '</tr>';

						total += val*a[2];
					}
				});
				detailDataFoot += '<tr>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td><b>'+total.toFixed(4)+'</b></td>';
				detailDataFoot += '</tr>';

				$('#tableDetailBody').append(detailData);
				$('#tableDetailFoot').append(detailDataFoot);
				$('#modalDetail').modal('show');

				$('#tableDetail').DataTable({
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
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [3],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{

				var items = {}, base, key;
				$.each(detail_wscr, function(key, value){
					if($.date(value.posting_date) <= $.date(b) && loc[1] == value.reason){
						key = value.material_number+'_'+value.material_description+'_'+value.std_price;
						if (!items[key]) {
							items[key] = 0;
						}
						if(value.movement_type == '102' || value.movement_type == '9S2'){
							items[key] -= value.quantity;
						}
						else{
							items[key] += value.quantity;
						}
					}
				});

				var outputArr = [];
				var total = 0;

				$.each(items, function(key, val) {
					if(val != 0){
						var a = key.split('_');

						detailData += '<tr>';
						detailData += '<td>'+a[0]+'</td>';
						detailData += '<td>'+a[1]+'</td>';
						detailData += '<td>'+val+'</td>';
						detailData += '<td>'+(val*a[2]).toFixed(1)+'</td>';
						detailData += '</tr>';

						total += val*a[2];
					}
				});
				detailDataFoot += '<tr>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td></td>';
				detailDataFoot += '<td><b>'+total.toFixed(4)+'</b></td>';
				detailDataFoot += '</tr>';

				$('#tableDetailBody').append(detailData);
				$('#tableDetailFoot').append(detailDataFoot);
				$('#modalDetail').modal('show');

				$('#tableDetail').DataTable({
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
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [3],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

			}
		}
	}

	function fetchChart(){
		$('#loading').show();
		var period = $('#datepicker').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/display/eff_scrap") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#title_text').text('Periode '+result.first+' - '+result.last);
				var h = $('#period_title').height();
				$('#datepicker').css('height', h);
				var target_mscr = [];
				var target_wscr = [];
				var actual_mscr = [];
				var actual_wscr = [];
				detail_mscr = result.actual_mscr;
				detail_wscr = result.actual_wscr;

				$.each(result.targets, function(key, value){
					if(value.remark == 'mscr'){
						target_mscr.push([Date.parse(value.due_date), (parseFloat(value.target) || 0)]);
					}
					if(value.remark == 'wscr'){
						target_wscr.push([Date.parse(value.due_date), (parseFloat(value.target) || 0)]);
					}
				});

				var result_mscr = [];
				var result_wscr = [];

				detail_mscr.reduce(function (res, value) {
					if (!res[value.posting_date]) {
						res[value.posting_date] = {
							amount: 0,
							posting_date: value.posting_date
						};
						result_mscr.push(res[value.posting_date])
					}
					res[value.posting_date].amount += value.amount
					return res;
				}, {});

				detail_wscr.reduce(function (res, value) {
					if (!res[value.posting_date]) {
						res[value.posting_date] = {
							amount: 0,
							posting_date: value.posting_date
						};
						result_wscr.push(res[value.posting_date])
					}
					res[value.posting_date].amount += value.amount
					return res;
				}, {});

				var sum_mscr = [];
				var sum_wscr = [];

				var amount_mscr = 0;
				var amount_wscr = 0;

				$.each(result_mscr, function(key, value){
					amount_mscr += value.amount;
					sum_mscr.push([Date.parse(value.posting_date), parseFloat(amount_mscr)]);
				});
				$.each(result_wscr, function(key, value){
					amount_wscr += value.amount;
					sum_wscr.push([Date.parse(value.posting_date), parseFloat(amount_wscr)]);
				});

				var now = new Date();

				Highcharts.chart('scrap_mscr', {
					chart: {
						type: 'area',
						backgroundColor	: null
					},
					title: {
						text: 'MSCR'
					},
					xAxis: {
						type: 'datetime',
						tickInterval: 24 * 3600 * 1000,
						plotLines: [{
							color: '#b71c1c',
							width: 2,
							value: now,
							dashStyle: 'shortdash'
						}]
					},
					yAxis: {
						title: {
							text: 'USD'
						},
						labels: {
							formatter: function () {
								return this.value/1000+'K';
							}
						},
						tickInterval: 1000
					},
					tooltip: {
						formatter: function () {
							return 'Date: '+Highcharts.dateFormat('%e - %b - %Y',
								new Date(this.x))+'<br>'+this.series.name +': USD '+(this.y/1000).toFixed(3)+'K';
						}
					},
					plotOptions: {
						line: {
							dataLabels: {
								enabled: true,
								formatter: function () {
									return (this.y/1000).toFixed(3)+'K';
								}
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.series.name, $.date(this.category));
									}
								}
							}
						},
						area: {
							pointStart: 1940,
							marker: {
								enabled: false,
								symbol: 'circle',
								radius: 2,
								states: {
									hover: {
										enabled: true
									}
								}
							}
						}
					},
					credits:{
						enabled:false
					},
					series: [{
						name: 'Budget',
						type: 'area',
						step: 'left',
						zIndex: 0,
						fillOpacity: 0.3,
						color: 'rgb(248,161,63)',
						data: target_mscr
					},{
						name: 'MSCR_ALL',
						type: 'line',
						lineWidth: 1,
						zIndex: 1,
						// dashStyle: 'longdash',
						marker: {
							symbol: 'circle',
							radius: 3
						},
						color: '#ccff90',
						data: sum_mscr
					}]
				});

				Highcharts.chart('scrap_wscr', {
					chart: {
						type: 'area',
						backgroundColor	: null
					},
					title: {
						text: 'WSCR'
					},
					xAxis: {
						type: 'datetime',
						tickInterval: 24 * 3600 * 1000,
						plotLines: [{
							color: '#b71c1c',
							width: 2,
							value: now,
							dashStyle: 'shortdash'
						}]
					},
					yAxis: {
						title: {
							text: 'USD'
						},
						labels: {
							formatter: function () {
								return this.value/1000+'K';
							}
						},
						tickInterval: 100
					},
					tooltip: {
						formatter: function () {
							return 'Date: '+Highcharts.dateFormat('%e - %b - %Y',
								new Date(this.x))+'<br>'+this.series.name +': USD '+(this.y/1000).toFixed(3)+'K';
						}
					},
					plotOptions: {
						line: {
							dataLabels: {
								enabled: true,
								formatter: function () {
									return (this.y/1000).toFixed(3)+'K';
								}
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.series.name, $.date(this.category));
									}
								}
							}
						},
						area: {
							pointStart: 1940,
							marker: {
								enabled: false,
								symbol: 'circle',
								radius: 2,
								states: {
									hover: {
										enabled: true
									}
								}
							}
						}
					},
					credits:{
						enabled:false
					},
					series: [{
						name: 'Budget',
						type: 'area',
						step: 'left',
						fillOpacity: 0.3,
						zIndex: 0,
						color: 'rgb(248,161,63)',
						data: target_wscr
					},{
						name: 'WSCR_ALL',
						type: 'line',
						lineWidth: 1,
						zIndex: 1,
						// dashStyle: 'longdash',
						marker: {
							symbol: 'circle',
							radius: 3
						},
						color: '#ccff90',
						data: sum_wscr
					}]
				});

				var reason_mscr = [];
				var reason_wscr = [];
				var div_detail = "";
				$('#detail_scrap_wscr').html("");
				$('#detail_scrap_mscr').html("");

				$.each(result.categories, function(key, value){
					if(value.receive_location == 'WSCR'){
						div_detail = '<div style="height:190px;" class="col-xs-3" id="'+value.receive_location+'_'+value.reason+'"></div>';
						$('#detail_scrap_wscr').append(div_detail);
					}
					else{
						div_detail = '<div style="height:190px;" class="col-xs-3" id="'+value.receive_location+'_'+value.reason+'"></div>';
						$('#detail_scrap_mscr').append(div_detail);						
					}

				});

				$.each(result.actual_wscr, function(key, value){
					if(value.reason != null){
						if(reason_wscr.indexOf(value.reason) === -1){
							reason_wscr.push(value.reason);
							var reason = value.reason;
							var id_div = 'WSCR_'+value.reason;

							var new_detail_reason = [];
							var new_target = 0;

							$.each(result.targets, function(key, value){
								if(value.remark == id_div){
									new_target = value.target;
								}
							});

							var new_target_reason = [];

							for(var i = 0; i < result.actual_wscr.length; i++){
								if(result.actual_wscr[i].reason == reason || result.actual_wscr[i].reason == null){
									new_detail_reason.push({
										"posting_date" : result.actual_wscr[i].posting_date,
										"amount" : result.actual_wscr[i].amount
									});

									new_target_reason.push([Date.parse(result.actual_wscr[i].posting_date), parseFloat(new_target)]);

								}
							}

							var new_group = [];

							new_detail_reason.reduce(function (res, value) {
								if (!res[value.posting_date]) {
									res[value.posting_date] = {
										amount: 0,
										posting_date: value.posting_date
									};
									new_group.push(res[value.posting_date])
								}
								res[value.posting_date].amount += value.amount
								return res;
							}, {});

							var new_sum = [];
							var new_amount = 0;

							for(var i = 0; i < new_group.length; i++){
								new_amount += new_group[i].amount;
								new_sum.push([Date.parse(new_group[i].posting_date), parseFloat(new_amount)]);
							}

							Highcharts.chart(id_div, {
								chart: {
									backgroundColor: null
								},
								title: {
									text:  'WSCR '+value.reason,
									style: {
										fontSize: '14px'
									}
								},
								subtitle: {
									text: value.reason_name,
									style: {
										fontSize: '10px'
									}
								},
								yAxis: {
									title: {
										text: null
									}
								},
								credits:{
									enabled:false
								},
								xAxis: {
									type: 'datetime',
									tickInterval: 24 * 3600 * 1000,
									labels: {
										enabled:false
									},
									plotLines: [{
										color: '#b71c1c',
										width: 2,
										value: now,
										dashStyle: 'shortdash'
									}]
								},
								legend: {
									enabled: false
								},
								plotOptions: {
									series: {
										label: {
											connectorAllowed: false
										},
										pointStart: 2010,
										cursor: 'pointer',
										point: {
											events: {
												click: function () {
													fetchDetail(this.series.name, $.date(this.category));
												}
											}
										}
									}
								},
								series: [{
									name: id_div,
									marker:{
										enabled:false
									},
									color:'#ccff90',
									data: new_sum,
									dataLabels: {
										enabled: true,
										formatter: function () {
											return (this.y).toFixed(1);
										}
									}
								},{
									name: 'Target',
									type: 'line',
									marker:{
										enabled:false
									},
									color: 'rgb(248,161,63)',
									data: new_target_reason,
									dashStyle: 'shortdash'
								}]
							});
						}
					}
				});

				$.each(result.actual_mscr, function(key, value){
					if(value.reason != null){
						if(reason_mscr.indexOf(value.reason) === -1){
							reason_mscr.push(value.reason);
							var reason = value.reason;
							var id_div = 'MSCR_'+value.reason;

							var new_detail_reason = [];
							var new_target = 0;

							$.each(result.targets, function(key, value){
								if(value.remark == id_div){
									new_target = value.target;
								}
							});

							var new_target_reason = [];

							for(var i = 0; i < result.actual_mscr.length; i++){
								if(result.actual_mscr[i].reason == reason || result.actual_mscr[i].reason == null){
									new_detail_reason.push({
										"posting_date" : result.actual_mscr[i].posting_date,
										"amount" : result.actual_mscr[i].amount
									});

									new_target_reason.push([Date.parse(result.actual_mscr[i].posting_date), parseFloat(new_target)]);

								}
							}

							var new_group = [];

							new_detail_reason.reduce(function (res, value) {
								if (!res[value.posting_date]) {
									res[value.posting_date] = {
										amount: 0,
										posting_date: value.posting_date
									};
									new_group.push(res[value.posting_date])
								}
								res[value.posting_date].amount += value.amount
								return res;
							}, {});

							var new_sum = [];
							var new_amount = 0;

							for(var i = 0; i < new_group.length; i++){
								new_amount += new_group[i].amount;
								new_sum.push([Date.parse(new_group[i].posting_date), parseFloat(new_amount)]);
							}

							Highcharts.chart(id_div, {
								chart: {
									backgroundColor: null
								},
								title: {
									text: 'MSCR '+value.reason,
									style: {
										fontSize: '14px'
									}
								},
								subtitle: {
									text: value.reason_name,
									style: {
										fontSize: '10px'
									}
								},
								yAxis: {
									title: {
										text: null
									}
								},
								credits:{
									enabled:false
								},
								xAxis: {
									type: 'datetime',
									tickInterval: 24 * 3600 * 1000,
									labels: {
										enabled:false
									},
									plotLines: [{
										color: '#b71c1c',
										width: 2,
										value: now,
										dashStyle: 'shortdash'
									}]
								},
								legend: {
									enabled: false
								},
								plotOptions: {
									series: {
										label: {
											connectorAllowed: false
										},
										pointStart: 2010,
										cursor: 'pointer',
										point: {
											events: {
												click: function () {
													fetchDetail(this.series.name, $.date(this.category));
												}
											}
										}
									}
								},
								series: [{
									name: id_div,
									marker:{
										enabled:false
									},
									color:'#ccff90',
									data: new_sum,
									dataLabels: {
										enabled: true,
										formatter: function () {
											return (this.y).toFixed(1);
										}
									}
								},{
									name: 'Target',
									type: 'line',
									marker:{
										enabled:false
									},
									color: 'rgb(248,161,63)',
									data: new_target_reason,
									dashStyle: 'shortdash'
								}]
							});

						}
					}
				});

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
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