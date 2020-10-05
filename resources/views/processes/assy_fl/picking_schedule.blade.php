@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
		font-size: 20px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">
	
	<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Flute NG <i class="fa fa-angle-double-down"></i></span>
</section>
@stop
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">

		<div class="col-xs-12" style="text-align: center;">
			<div class="input-group col-md-12 ">
				<table id="planTable" name="planTable" class="table table-bordered">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th rowspan="2" style="padding:0; width:1%;">Model</th>
							<th rowspan="2" style="padding:0; width:1%;">H-1</th>
							<th rowspan="2" style="padding:0; width:1%;">Plan Packing</th>
							<th rowspan="2" style="padding:0; width:1%;">Act Packing</th>
							<th colspan="2" style="padding:0; width:1%;">Stock</th>
							<th rowspan="2" style="padding:0; width:1%;">Target Sub-Assy</th>
							<th rowspan="2" style="padding:0; width:1%;">Sisa Sub-Assy</th>
							<th rowspan="2" style="padding:0; width:1%;">Stamp FG</th>
							<th rowspan="2" style="padding:0; width:1%;">Stamp KD</th>
							<th rowspan="2" style="padding:0; width:1%;">Target Sub-Assy (H+1)</th>
							<th rowspan="2" style="padding:0; width:1%;">Sisa Sub-Assy (H+1)</th>
							<th rowspan="2" style="padding:0; width:1%;">Target Sub-Assy (H+2)</th>
							<th rowspan="2" style="padding:0; width:1%;">Sisa Sub-Assy (H+2)</th>
							<!-- <th>Diff</th> -->
						</tr>
						<tr>
							<th style="padding:0; width:1%;">WIP</th>
							<th style="padding:0; width:1%;">NG</th>
						</tr>
					</thead>
					<tbody id="planTableBody">
					</tbody>
					<tfoot style="background-color: RGB(252, 248, 227); color: black;">
						<tr>
							<th>Total</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fillPlannew();

		setInterval(fillPlannew, 1000);
	});

	function fillPlannew(){
		$.get('{{ url("fetch/fetchResultFlnew") }}', function(result, status, xhr){
			if(xhr.status = 200){
				if(result.status){
					$('#planTable').DataTable().destroy();
					$('#planTableBody').html("");
					var planData = '';
					var totalTarget = '';
					var totalSubassy = '';
					var sisaToday = 0;
					var sisaH1 = 0;
					var sisaH2 = 0;
					var no = 1;

					console.log(result.nextday);
					console.log(result.nextdayplus1);

					$.each(result.planData, function(key, value) {
						// alert(value.planh2 );
						
						// totalTarget = value.plan;
						// totalSubassy = (((totalTarget + (-value.debt)) - value.actual) - (value.wip - value.ng)) ;
						// var h1 = Math.round(value.h1);
						// if (totalSubassy < 0) {
						// 	totalSubassy = 0;
						// 	h1 = Math.round(value.h1) - (value.stamp - value.actual);
						// }
						// if (h1 < 0) {
						// 	h1 = 0;
						// }

						// if (value.stamp <= 0 && (value.wip - value.ng) >= Math.round(value.h1)) {
						// 	h1 = 0;
						// }

						// if (value.stamp <= 0 && (value.wip - value.ng) <= Math.round(value.h1)) {
						// 	h1 = Math.round(value.h1) - (value.wip - value.ng);
						// }

						// var h2 = Math.round(value.h2);
						// if (totalSubassy < 0) {
						// 	totalSubassy = 0;
						// 	h2 = Math.round(value.h2) - (value.stamp - value.actual);
						// }
						// if (h2 < 0) {
						// 	h2 = 0;
						// }

						// if (value.stamp <= 0 && (value.wip - value.ng) >= Math.round(value.h2)) {
						// 	h2 = 0;
						// }

						// if (value.stamp <= 0 && (value.wip - value.ng) <= Math.round(value.h2)) {
						// 	h2 = Math.round(value.h2) - (value.wip - value.ng);
						// }

						if (value.sisaToday >= 0) {
							sisaToday = value.sisaToday;
						}
						else if(value.sisaToday < 0){
							sisaToday = 0;
						}

						if (value.sisaH1 >= 0) {
							sisaH1 = value.sisaH1;
						}
						else if(value.sisaH1 < 0){
							sisaH1 = 0;
						}

						if (value.sisaH2 >= 0) {
							sisaH2 = value.sisaH2;
						}
						else if(value.sisaH2 < 0){
							sisaH2 = 0;
						}

						if (no % 2 === 0 ) {
							color = 'style="background-color: rgb(60,60,60)"';
						} else {
							color = 'style="background-color: rgb(100,100,100)"';
						}
						
						planData += '<tr '+color+'>';
						planData += '<td style="width: 1%">'+ value.model +'</td>';
						planData += '<td style="width: 1%">'+ value.debt +'</td>';						
						planData += '<td style="width: 1%">'+ value.plan +'</td>';
						planData += '<td style="width: 1%">'+ value.actual +'</td>';
						planData += '<td style="width: 1%">'+ value.wip +'</td>';
						planData += '<td style="width: 1%">'+ value.ng +'</td>';
						planData += '<td style="width: 1%">'+ value.targetToday +'</td>';
						planData += '<td style="width: 1%">'+ sisaToday +'</td>';
						planData += '<td style="width: 1%">'+ value.stamp +'</td>';
						planData += '<td style="width: 1%">'+ value.stamp_kd +'</td>';
						planData += '<td style="width: 1%">'+ value.h1 +'</td>';
						planData += '<td style="width: 1%">'+ sisaH1 +'</td>';
						planData += '<td style="width: 1%">'+ value.h2 +'</td>';
						planData += '<td style="width: 1%">'+ sisaH2 +'</td>';
						planData += '</tr>';

						no += 1;
					});
					$('#planTableBody').append(planData);
					$('#listModel').html("");
					$.unique(result.model.map(function (d) {
						$('#listModel').append('<button type="button" class="btn bg-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 1vw" id="'+d.model+'" onclick="model(id)">'+d.model+'</button>');
					}));
					$('#planTable').DataTable({
						'paging': false,
						'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
						'autoWidth': true,
						"footerCallback": function (tfoot, data, start, end, display) {
							var intVal = function ( i ) {
								return typeof i === 'string' ?
								i.replace(/[\$,]/g, '')*1 :
								typeof i === 'number' ?
								i : 0;
							};
							var api = this.api();
							
							var sisa_sub_assy = api.column(7).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(7).footer()).html(sisa_sub_assy.toLocaleString());

							var mtd = api.column(1).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(1).footer()).html(mtd.toLocaleString());

							var Packing = api.column(2).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(2).footer()).html(Packing.toLocaleString());

							var act = api.column(3).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(3).footer()).html(act.toLocaleString());

							var wip = api.column(4).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(4).footer()).html(wip.toLocaleString());

							var ng = api.column(5).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(5).footer()).html(ng.toLocaleString());

							var h = api.column(6).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(6).footer()).html(h.toLocaleString());

							var stamp = api.column(8).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(8).footer()).html(stamp.toLocaleString());

							var stamp_kd = api.column(9).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(9).footer()).html(stamp_kd.toLocaleString());

							var h1 = api.column(10).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(10).footer()).html(h1.toLocaleString());

							var sisaH1 = api.column(11).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(11).footer()).html(sisaH1.toLocaleString());

							var h2 = api.column(12).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(12).footer()).html(h2.toLocaleString());

							var sisaH2 = api.column(13).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(13).footer()).html(sisaH2.toLocaleString());


						},
						"columnDefs": [  {
							"targets": 7,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[7]) != 0  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
									$(td).css('color', 'black')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
									$(td).css('color', 'black')
								}
							}
						},
						{
							"targets": 11,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[11]) != 0  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
									$(td).css('color', 'black')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
									$(td).css('color', 'black')
								}
							}
						},
						{
							"targets": 13,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[13]) != 0  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
									$(td).css('color', 'black')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
									$(td).css('color', 'black')
								}
							}
						}]
						});
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
}
</script>
@endsection