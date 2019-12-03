@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}

	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
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
		border:1px solid white;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		color: black;
		background-color: white;
	}
	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
		background-color: white;
		color: black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid white;
		vertical-align: middle;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid white;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.color {
		-webkit-animation: colors 1s infinite;  /* Safari 4+ */
		-moz-animation: colors 1s infinite;  /* Fx 5+ */
		-o-animation: colors 1s infinite;  /* Opera 12+ */
		animation: colors 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes colors {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: #f55656;
		}
	}
	#quantity_kanban, #quantity {
		font-size: 2vw;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0; overflow-y:hidden; overflow-x:scroll;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 5px;">
			<div id="chart"></div>

			<!-- <table class="table table-bordered table-stripped" id="logs">
				<thead>
					<tr>
						<th>Material Number</th>
						<th>Model and Key</th>
						<th>Descriptions</th>
						<th>Quantity</th>
						<th>Kanban Quantity</th>
					</tr>
				</thead>
				<tbody id="tbody">
				</tbody>
			</table> -->

			<table id="assyTable" class="table table-bordered" style="padding: 0px; margin-bottom: 0px; font-size: 1vw">
				<tr id="modelAll" style="font-size: 1.8vw">
					<!-- <th>#</th> -->
				</tr>
				<tr id="quantity_kanban">
					<!-- <th>Total Qty Kanban</th> -->
				</tr>
				<tr id="quantity">
					<!-- <th>Total Quantity</th> -->
				</tr>
				<tr id="chart2">
					
				</tr>
			</table>
		</div>
	</div>
</section>
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
		drawTable();
		setInterval(drawTable, 2000);
	});

	function drawTable() {
		var filter = '{{$_GET["filter"]}}';
		var data = {
			origin_group_code:"{{$option}}",
			filter: filter
		}

		$.get('{{ url("fetch/middle/request/") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modelAll").empty();
				$("#quantity").empty();
				$("#quantity_kanban").empty();
				$("#chart2").empty();

				var material_req = [];
				var cat = [];
				var limit = [];
				var chart = "";
				var isi = 0;
				var isi2 = "";
				var kosong = 0;
				var max = 0;

				model = "<th style='width:45px'>#</th>";
				quantity = "<th>Qty</th>";
				quantity_kanban = "<th>Qty Kanban</th>";
				chart = "<th></th>";

				$.each(result.datas, function(index, value){
					// if (value.quantity >= value.lot_transfer * 2) {


						if(value.model[0] == 'A'){
							model += "<th style='background-color: #ffff66;'>"+value.model+" "+value.key+"</th>";
						}else if(value.model[0] == 'T'){
							model += "<th style='background-color: #1565C0;'>"+value.model+" "+value.key+"</th>";
						}

						quantity += "<td>"+value.quantity+"</td>";
						quantity_kanban += "<td>"+value.kanban+"</td>";
						limit.push(value.kanban);
					// }

					// if (value.quantity >= value.lot_transfer * 2) {
					// 	cat.push(value.model+" "+value.key);
					// 	material_req.push((value.quantity / value.lot_transfer));
					// 	limit.push(2);	
					// }
				})

				max = (Math.max(...limit));

				$.each(result.datas, function(index, value){
					// if (value.quantity / value.lot_transfer >= 2) {

						high = value.kanban / max * 100;

						if (value.kanban >= 4) {
							color = "#e0391f";
						} else if (value.kanban >= 2) {
							color = "#facf23";
						}
						else {
							color = "lime";
						}

						chart += "<td style='height:400px'><div style='height:"+(100 - high)+"%'></div><div style='margin: 10px 3px 0px 3px; background-color: "+color+"; height:"+high+"%; font-size:1.5vw'>"+value.kanban+"</div></td>";
					// }
					// kosong = (max - (value.quantity / value.lot_transfer)) / max * 100;
					// chart += '<div style="margin: 0px 3px 0px 3px; background-color: #3c3c3c; height: '+kosong+'%" id="kosong"></div>';

					// isi = (value.quantity / value.lot_transfer) / max * 100;

					// if ((value.quantity / value.lot_transfer) > 0) isi2 = (value.quantity / value.lot_transfer); else isi2 = '';
					// chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: #7cb5ec; height: '+isi+'%" id="welding">'+isi2+'</div>';
				})

				// chart = "<td></td>";

				// chart += "<td><div style='height:100px'>DD</div></td>";

				$("#modelAll").append(model);
				$("#quantity").append(quantity);
				$("#quantity_kanban").append(quantity_kanban);
				$("#chart2").append(chart);

				//CHART
				// Highcharts.chart('chart', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'column',
				// 	},
				// 	exporting: { enabled: false },
				// 	title: {
				// 		text: null
				// 	},
				// 	tooltip: {
				// 		pointFormat: 'Quantity: <b>{point.y} Kanban</b>'
				// 	},
				// 	xAxis: {
				// 		labels: {
				// 			style: {
				// 				color: '#9dff69',
				// 				fontSize: '12px',
				// 				fontWeight: 'bold'
				// 			}
				// 		},
				// 		categories: cat
				// 	},
				// 	yAxis: {
				// 		tickInterval: 1,
				// 		title: {
				// 			text: 'Quantity (Kanban)'
				// 		},
				// 		plotLines: [{
				// 			color: 'red',
				// 			width: 2,
				// 			value: 2,
				// 			zIndex: 5,
				// 			dashStyle: 'Dash'
				// 		}]
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			allowPointSelect: true,
				// 			borderColor: 'black',
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '<b>{point.name}<br/>{point.y}</b>',
				// 				distance: -50,
				// 				style:{
				// 					fontSize:'14px',
				// 					textOutline:0,
				// 				},
				// 			},
				// 			zones: [{
				// 				value: 2, 
				// 				color: '#46e83a' 
				// 			},{
				// 				color: '#f55656' 
				// 			}]
				// 		}, 
				// 		series: {
				// 			animation: false
				// 		}
				// 	},
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	series: [{
				// 		name: 'Material',
				// 		data: material_req
				// 	}]
				// });
			}
		})
}


	// function drawTable() {
	// 	var data = {
	// 		option:"{{$option}}"
	// 	}

	// 	$.get('{{ url("fetch/middle/request/") }}', data, function(result, status, xhr){
	// 		if(result.status){
	// 			var tableData = "";
	// 			$('#logs').DataTable().destroy();
	// 			$("#tbody").empty();
	// 			var material_req = [];
	// 			var cat = [];
	// 			var limit = [];

	// 			$.each(result.datas, function(index, value) {

	// 				if (value.quantity >= (value.lot_transfer*2)) {

	// 				}

	// 				tableData += "<tr>";
	// 				tableData += "<td>"+value.material_number+"</td>";
	// 				tableData += "<td>"+value.model+" "+value.key+"</td>";
	// 				tableData += "<td>"+value.material_description+"</td>";
	// 				tableData += "<td>"+value.quantity+"</td>";
	// 				tableData += "<td>"+(value.quantity / value.lot_transfer)+"</td>";
	// 				tableData += "</tr>";

	// 				if (value.quantity >= value.lot_transfer) {
	// 					cat.push(value.model+" "+value.key);
	// 					material_req.push((value.quantity / value.lot_transfer));
	// 					limit.push(2);	
	// 				}

	// 			})

	// 			$("#tbody").append(tableData);
	// 			$('#logs').DataTable({
	// 				"paging": true,
	// 				'searching': false,
	// 				'responsive':true,
	// 				'lengthMenu': [
	// 				[ 10, 25, 50, -1 ],
	// 				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 				],
	// 				'ordering': false,
	// 				'lengthChange': false,
	// 				'info': false,
	// 				'sPaginationType': "full_numbers",
	// 				"columnDefs": [ {
	// 					"targets": 0,
	// 					"orderable": false
	// 				} ]
	// 			});

			// }
	// 	})
	// }

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true
	});


</script>
@endsection