@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
	font-size: 30px;
}
tbody>tr>td{
	text-align:center;
	font-size: 30px;
}
tfoot>tr>th{
	text-align:center;
	font-size: 30px;
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
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">
	
	<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Flute NG <i class="fa fa-angle-double-down"></i></span>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
	
		<div class="col-xs-12" style="text-align: center;">
			<div class="input-group col-md-12 ">
				<div class="box box-danger">
					<div class="box-body">
						<table id="planTable" name="planTable" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th rowspan="2">Model</th>
										<th rowspan="2">MTD (H-1)</th>
										<th rowspan="2">Target Packing</th>
										<th rowspan="2">Act Packing</th>
										<th colspan="2" width="15%">Stock</th>
										<th rowspan="2">Target SubAssy (H)</th>
										<th rowspan="2">Stamping</th>
										<th rowspan="2">Target SubAssy (H+1 Full)</th>
										<!-- <th>Diff</th> -->
									</tr>
									<tr>
										<th>WIP</th>
										<th>NG</th>
									</tr>
								</thead>
								<tbody id="planTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
									</tr>
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
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status = 200){
				if(result.status){
					$('#planTable').DataTable().destroy();
					$('#planTableBody').html("");
					var planData = '';
					var totalTarget = '';
					var totalSubassy = '';
					
					$.each(result.planData, function(key, value) {
						// alert(value.planh2 );
						
						totalTarget = value.plan;
						totalSubassy = (((totalTarget + (-value.debt)) - value.actual) - (value.total_return - value.total_ng)) ;
						var h2 = Math.round(value.planh2);
						if (totalSubassy < 0) {
						totalSubassy = 0;
						h2 = Math.round(value.planh2) - (value.total_stamp - value.actual);
						}
						if (h2 < 0) {
							h2 = 0;
						}
						planData += '<tr>';
						planData += '<td>'+ value.model3 +'</td>';
						planData += '<td>'+ value.debt +'</td>';						
						planData += '<td>'+ totalTarget +'</td>';
						planData += '<td>'+ value.actual +'</td>';
						planData += '<td>'+ value.total_return +'</td>';
						planData += '<td>'+ value.total_ng +'</td>';
						planData += '<td>'+ totalSubassy +'</td>';
						planData += '<td>'+ value.total_stamp +'</td>';
						planData += '<td>'+ h2 +'</td>';
						planData += '</tr>';
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
							
							var total_actual = api.column(7).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(7).footer()).html(total_actual.toLocaleString());

						},
						"columnDefs": [  {
							"targets": 6,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[7]) < parseInt(rowData[6])  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						},
						{
							"targets": 8,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[6]) >= 0  && parseInt(rowData[8]) > 0) {
										if (parseInt(rowData[6]) <= 0) {
											$(td).css('background-color', 'RGB(255,204,255)')
										}

									
								}
								else
								{
										// $(td).css('background-color', 'RGB(204,255,255)')
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