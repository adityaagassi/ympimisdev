@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="col-md-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-md-12">
				<div class="nav-tabs-custom">
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div id="chart" style="width: 99%;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
					<br><h4 class="modal-title" id="judul_table"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Employee ID</th>
										<th>Employee Name</th>
										<th>Division</th>
										<th>Department</th>
										<th>Section</th>
										<th>Sub Section</th>
										<th>Entry Date</th>
										<th>Employee Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
		$('body').toggleClass("sidebar-collapse");

		$('#myModal').on('hidden.bs.modal', function () {
			$('#example2').DataTable().clear();
		});

		drawChart();
	});

	$('.datepicker').datepicker({
		// <?php $tgl_max = date('m-Y') ?>
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		
		// endDate: '<?php echo $tgl_max ?>'

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {
		var tgl = $('#tgl').val();

		var data = {
			ctg:'{{$title}}',
			tgl: tgl
		};

		$.get('{{ url("fetch/report/stat") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var ctg = [], series = [];
					var month = result.monthTitle;

					$.each(result.datas, function(key, value) {
						ctg.push(value.status);
						series.push(value.jml);
					})

					$('#chart').highcharts({
						chart: {
							type: 'column'
						},
						title: {
							text: '{{$title}} in '+month
						},
						xAxis: {
							type: 'category',
							categories: ctg
						},
						yAxis: {
							type: 'logarithmic',
							title: {
								text: 'Total Employee'
							}
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category, result.ctg);
										}
									}
								},
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									format: '{point.y}'
								}
							}
						},
						credits: {
							enabled: false
						},

						tooltip: {
							formatter:function(){
								return this.key + ' : ' + this.y;
							}
						},

						"series": [
						{
							"name": "By Status",
							"colorByPoint": true,
							"data": series
						}
						]
					})
				} else{
					alert('Attempt to retrieve data failed');
				}
			}
		})
	}

	function ShowModal(kondisi, by) {
		tabel = $('#example2').DataTable();
		tabel.destroy();

		$("#myModal").modal("show");

		var table = $('#example2').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/report/detail_stat") }}",
				"data" : {
					kondisi : kondisi,
					by : by
				}
			},
			"columns": [
			{ "data": "employee_id" },
			{ "data": "name" },
			{ "data": "division" },
			{ "data": "department" },
			{ "data": "section" },
			{ "data": "sub_section" },
			{ "data": "hire_date" },
			{ "data": "status"}
			]
		});

		$('#judul_table').append().empty();
		$('#judul_table').append('<center>'+by+' '+kondisi+'<center>');
		
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}	
</script>
@endsection