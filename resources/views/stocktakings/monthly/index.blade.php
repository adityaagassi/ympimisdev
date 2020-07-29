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
		<div class="col-xs-9">
			<h2 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h2>
		</div>
		<div class="col-xs-3">
			<h3 style="margin: 0px;" class="pull-right" id="month_text"></h3>
		</div>
	</div>
</section>
@stop
@section('content')
<section class="content">

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
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>

			<a id="manage_store" href="{{ url("index/stocktaking/manage_store") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Manage Store</a>

			@if(in_array('S36', $navs))	
			<a id="summary_of_counting" href="{{ url("index/stocktaking/summary_of_counting") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Summary of Counting</a>
			@endif
			

			<a id="no_use" href="{{ secure_url("index/stocktaking/no_use") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Input No Use</a>
			<a id="input_pi" href="{{ secure_url("index/stocktaking/count") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Input PI</a>
			<a id="audit1" href="{{ secure_url("index/stocktaking/audit/"."1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 1</a>

			@if(in_array('S36', $navs))	
			{{-- <a id="audit2" href="{{ url("index/stocktaking/audit/"."2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 2</a> --}}
			<a id="breakdown" data-toggle="modal" data-target="#modalBreakdown" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Breakdown PI</a>
			<a id="revise" href="{{ secure_url("index/stocktaking/revise") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Revise</a>
			
			@endif
			<a id="unmatch" onclick="unmatch()" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Unmatch</a>

			<br>

			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>

			<form method="GET" action="{{ url("export/stocktaking/inquiry") }}">
				<input type="text" name="month_inquiry" id="month_inquiry" placeholder="Select Month" hidden>
				<button id="inquiry" type="submit" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple; margin-top: 5px;">Inquiry</button>
			</form>
			<form method="GET" action="{{ url("export/stocktaking/variance") }}">
				<input type="text" name="month_variance" id="month_variance" placeholder="Select Month" hidden>
				<button id="variance" type="submit" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple; margin-top: 5px;">Variance</button>
			</form>

			@if(in_array('S36', $navs))
			<form method="GET" action="{{ url("export/stocktaking/official_variance") }}" target="_blank">
				<input type="text" name="month_official_variance" id="month_official_variance" placeholder="Select Month" hidden>
				<button id="variance" type="submit" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple; margin-top: 5px;">Official Variance</button>
			</form>

			<br>

			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Final <i class="fa fa-angle-double-down"></i></span>
			<a id="upload_sap" onclick="uploadSap()" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Upload SAP</a>
			<a id="export_log" onclick="exportLog()" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Export to Log</a>

			@endif

		</div>
		<div class="col-xs-9" style="text-align: center; color: red;">
			<div class="pull-right" id="last_update" style="color: black; margin: 0px; padding-top: 0px; padding-right: 0px; font-size: 1vw;"></div>

			<div class="col-xs-12">
				<div id="container"></div>
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
									<th>Status</th>
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

	<div class="modal fade" id="modalInput">
		<div class="modal-dialog modal-lg" style="width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableInput">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
									<th>Category</th>
									<th>Material</th>
									<th>Description</th>
									<th>Qty</th>
									<th>Audit 1</th>
									{{-- <th>Audit 2</th> --}}
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

	<div class="modal fade" id="modalMonth">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Month</label>
							<div class="input-group date">
								<div class="input-group-addon bg-green">
									<i class="fa fa-calendar"></i>
								</div>
								<input style="text-align: center;" type="text" class="form-control datepicker" onchange="monthChange()" name="month" id="month" placeholder="Select Month" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalBreakdown">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h2 style="text-align: center; margin: 2%; font-weight: bold;">Breakdown PI</h2>
					</div>

					<div class="col-xs-12" style="margin-top: 3%;">
						<div class="form-group">
							<label>Select Group</label><br>

							<label><input type="checkbox" class="minimal" id="ASSEMBLY">&nbsp;&nbsp;Assembly</label><br>
							<label><input type="checkbox" class="minimal" id="ST">&nbsp;&nbsp;Surface Treatment</label><br>
							<label><input type="checkbox" class="minimal" id="WELDING">&nbsp;&nbsp;Welding</label><br>
							<label><input type="checkbox" class="minimal" id="PP">&nbsp;&nbsp;Parts Process</label><br>
							<label><input type="checkbox" class="minimal" id="EI">&nbsp;&nbsp;Educational Instrument</label><br>
							<label><input type="checkbox" class="minimal" id="WAREHOUSE">&nbsp;&nbsp;Warehouse</label><br>
							<label><input type="checkbox" class="minimal" id="FG">&nbsp;&nbsp;Finished Goods</label>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="countPI()"><i class="fa fa-play"></i> 	Start</button>
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

		var error = $('#msg_error').val();
		console.log(error);

		if(error){
			var error_message = error.split('(ime)');
			var month = error_message[0];
			var message = error_message[1];
			

			$('#month').val(month);
			monthChange();
			filledList();
			auditedList();
			variance();

			console.log(error_message);
			console.log(month);

			openErrorGritter('Error', message);
		}else{
			$('#modalMonth').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		
		$('#month').blur();
		$('#month').val('');

		filledList();
		auditedList();

		setInterval(filledList, 20000);
		setInterval(auditedList, 20000);

	});


	function uploadSap() {
		$("#loading").show();

		var month = $('#month').val();

		var data = {
			month : month
		}

		$.get('{{ url("export/stocktaking/upload_sap") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Export Log Success');
			}else{
				$("#loading").hide();
				openErrorGritter('Error', 'Export Log Failed');
			}

		});
	}

	function exportLog() {
		$("#loading").show();

		var month = $('#month').val();

		var data = {
			month : month
		}

		$.get('{{ url("export/stocktaking/log") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				monthChange();
				openSuccessGritter('Success', 'Export Log Success');
			}else{
				$("#loading").hide();
				openErrorGritter('Error', 'Export Log Failed');
			}

		});
	}

	function unmatch(){

		var month = $('#month').val();
		window.open('{{ url("index/stocktaking/unmatch/") }}'+'/'+month, '_blank');

	}

	function monthChange(){
		var month = $('#month').val();

		$('#month_inquiry').val(month);
		$('#month_variance').val(month);
		$('#month_official_variance').val(month);

		var data = {
			month : month
		}

		$('#month_text').text(bulanText(month));
		$('#modalMonth').modal('hide');

		$.get('{{ url("fetch/stocktaking/check_month") }}', data, function(result, status, xhr){
			if(result.status){
				$('#inquiry').removeClass('disabled');
				$('#variance').removeClass('disabled');

				if(result.data.status == 'finished'){
					$('#manage_store').addClass('disabled');
					$('#summary_of_counting').addClass('disabled');
					$('#no_use').addClass('disabled');
					$('#input_pi').addClass('disabled');
					$('#audit1').addClass('disabled');
					$('#audit2').addClass('disabled');
					$('#breakdown').addClass('disabled');
					$('#unmatch').addClass('disabled');
					$('#revise').addClass('disabled');
					$('#upload_sap').addClass('disabled');
					$('#export_log').addClass('disabled');
				}else{
					$('#manage_store').removeClass('disabled');
					$('#summary_of_counting').removeClass('disabled');
					$('#no_use').removeClass('disabled');
					$('#input_pi').removeClass('disabled');
					$('#audit1').removeClass('disabled');
					$('#audit2').removeClass('disabled');
					$('#breakdown').removeClass('disabled');
					$('#unmatch').removeClass('disabled');
					$('#revise').removeClass('disabled');
					$('#upload_sap').removeClass('disabled');
					$('#export_log').removeClass('disabled');
				}

				filledList();
				auditedList();
				variance();

				$('#month_text').text(bulanText(month));
				$('#modalMonth').modal('hide');

			}else{
				$('#month_text').text(bulanText(month));
				$('#modalMonth').modal('hide');
				openErrorGritter('Error', result.message);

				$('#manage_store').addClass('disabled');
				$('#summary_of_counting').addClass('disabled');
				$('#no_use').addClass('disabled');
				$('#input_pi').addClass('disabled');
				$('#audit1').addClass('disabled');
				$('#audit2').addClass('disabled');
				$('#breakdown').addClass('disabled');
				$('#unmatch').addClass('disabled');
				$('#revise').addClass('disabled');
				$('#upload_sap').addClass('disabled');
				$('#export_log').addClass('disabled');
				$('#inquiry').addClass('disabled');
				$('#variance').addClass('disabled');
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

	$("#modalBreakdown").on("hidden.bs.modal", function () {
		$('#ASSEMBLY').iCheck('uncheck');
		$('#ST').iCheck('uncheck');
		$('#WELDING').iCheck('uncheck');
		$('#PP').iCheck('uncheck');
		$('#EI').iCheck('uncheck');
		$('#WAREHOUSE').iCheck('uncheck');
		$('#FG').iCheck('uncheck');
	});


	function countPI() {
		$("#loading").show();
		var group = [];

		if($('#ASSEMBLY').is(":checked")){
			group.push('ASSEMBLY');
		}
		if($('#ST').is(":checked")){
			group.push('ST');
		}
		if($('#WELDING').is(":checked")){
			group.push('WELDING');
		}
		if($('#PP').is(":checked")){
			group.push('PP');
		}
		if($('#EI').is(":checked")){
			group.push('EI');
		}
		if($('#WAREHOUSE').is(":checked")){
			group.push('WAREHOUSE');
		}
		if($('#FG').is(":checked")){
			group.push('FINISHED GOODS');
		}


		if(group.length > 0){
			var data = {
				group : group
			}

			$.post('{{ url("index/stocktaking/count_pi") }}', data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					$("#modalBreakdown").modal('hide');

					$('#ASSEMBLY').iCheck('uncheck');
					$('#ST').iCheck('uncheck');
					$('#WELDING').iCheck('uncheck');
					$('#PP').iCheck('uncheck');
					$('#EI').iCheck('uncheck');
					$('#WAREHOUSE').iCheck('uncheck');
					$('#FG').iCheck('uncheck');

					variance();
					openSuccessGritter('Success', result.message);
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}

			});
		}else{
			$("#loading").hide();
			openErrorGritter('Error', 'Select Group');
		}		
	}


	function filledList() {

		var month = $('#month').val();

		if(month != ''){
			var data = {
				month : month
			}

			$.get('{{ url("fetch/stocktaking/filled_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

					var location = [];
					var fill = [];
					var empty = [];

					for (var i = 0; i < result.data.length; i++) {
						location.push(result.data[i].location);
						fill.push(parseInt(result.data[i].qty));
						empty.push(parseInt(result.data[i].empty));
					}

					Highcharts.chart('container', {
						chart: {
							height: 225,
							type: 'column',
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
								stops: []
							},
						},
						title: {
							text: 'Progress Input PI Stocktaking',
							style: {
								fontSize: '1.5vw',
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
									fontSize: '1vw'
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
							name: 'Inputted',
							data: fill,
							color: 'rgb(144,238,126)'
						}]
					});
				}
			});
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

				var color = ''
				if(series == "Empty"){
					color = 'style="background-color: rgb(255,116,116);"';
				}else{
					color = 'style="background-color: rgb(144,238,126);"';	
				}

				var body = '';
				for (var i = 0; i < result.input_detail.length; i++) {
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


	function auditedList() {
		var month = $('#month').val();

		if(month != ''){
			var data = {
				month : month
			}

			$.get('{{ url("fetch/stocktaking/audited_list") }}', data, function(result, status, xhr){
				if(result.status){

					var location = [];
					var audited = [];
					var notyet = [];

					for (var i = 0; i < result.data.length; i++) {
						location.push(result.data[i].location);
						audited.push(parseInt(result.data[i].audited));
						notyet.push(parseInt(result.data[i].not_audited));
					}

					Highcharts.chart('container1', {
						chart: {
							height: 225,
							type: 'column',
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
								stops: []
							},
						},
						title: {
							text: 'Progress Audit 1 Stocktaking',
							style: {
								fontSize: '1.5vw',
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
									fontSize: '1vw'
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
										fontSize:'18px',
										fontWeight: 'bold',
									}
								},
								point: {
									events: {
										click: function () {
											fillAuditModal(this.category, this.series.name);
										}
									}
								}
							}
						},
						series: [{
							name: 'Not yet',
							data: notyet,
							color: 'rgb(255,116,116)'
						}, {
							name: 'Audited',
							data: audited,
							color: 'rgb(144,238,126)'
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

				var color = ''
				if(series == "Not yet"){
					color = 'style="background-color: rgb(255,116,116);"';
				}else{
					color = 'style="background-color: rgb(144,238,126);"'			
				}

				var body = '';
				for (var i = 0; i < result.audit_detail.length; i++) {
					body += '<tr '+ color +'">';
					body += '<td style="width: 1%">'+ result.audit_detail[i].area +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].location +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].store +'</td>';
					body += '<td style="width: 1%">'+ result.audit_detail[i].audit_status +'</td>';
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
							type: 'column',
							backgroundColor: {
								linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
								stops: []
							},
						},
						title: {
							text: 'Quick Count Variance',
							style: {
								fontSize: '1.5vw',
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
									fontSize: '1vw'
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
										fontSize:'18px',
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
							color: 'rgb(255,116,116)'
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