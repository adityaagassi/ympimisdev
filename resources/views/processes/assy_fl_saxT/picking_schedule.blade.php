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
						<table id="planTablenew" name="planTablenew" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th rowspan="2">Model</th>
										<th rowspan="2">Target Packing</th>
										<th rowspan="2">Act Packing</th>
										<th colspan="2" width="15%">Stock</th>
										<th rowspan="2">Target AssySax (H)</th>
										<th rowspan="2">Picking</th>
										<th rowspan="2">Target AssySax (H+1/2)</th>
										<!-- <th>Diff</th> -->
									</tr>
									<tr>
										<th>WIP</th>
										<th>NG</th>
									</tr>
								</thead>
								<tbody id="planTableBodynew">
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
		$.get('{{ url("fetch/fetchResultSaxnew") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status = 200){
				if(result.status){
					$('#planTablenew').DataTable().destroy();
					$('#planTableBodynew').html("");
					var planData = '';
					$.each(result.tableData, function(key, value) {
						var totalTarget = '';
						var totalSubassy = '';
						var diff = '';
						var h2 = Math.round(value.planh2 / 2);
						totalTarget = value.plan+(-value.debt);
						totalSubassy = ((totalTarget - value.actual) - (value.total_return - value.total_ng)) - value.total_perolehan;
						if (totalSubassy < 0) {
							totalSubassy = 0;
							// h2 = Math.round(value.planh2 / 2) - (value.total_perolehan - value.actual);
							if ((value.total_perolehan - value.actual ) < 0) {
							h2 = Math.round(value.planh2 / 2) - 0;
						}
						else{
							h2 = Math.round(value.planh2 / 2) - (value.total_perolehan - value.actual );
						}
						}
						if (h2 < 0) {
							h2 = 0;
						}
						diff = totalSubassy - value.total_perolehan;
						planData += '<tr>';
						planData += '<td>'+ value.model2 +'</td>';
						planData += '<td>'+ totalTarget +'</td>';
						planData += '<td>'+ value.actual +'</td>';
						planData += '<td>'+ value.total_return +'</td>';
						planData += '<td>'+ value.total_ng +'</td>';
						planData += '<td>'+ totalSubassy +'</td>';
						planData += '<td>'+ value.total_perolehan +'</td>';
						planData += '<td>'+ h2 +'</td>';
							// planData += '<td>'+ diff +'</td>';

							planData += '</tr>';
						});
					$('#planTableBodynew').append(planData);										
					$('#planTablenew').DataTable({
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

							var total_diff = api.column(6).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(6).footer()).html(total_diff.toLocaleString());

						},
						"columnDefs": [ {
							"targets": 5,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[6]) < parseInt(rowData[5])  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						},
						{
							"targets": 7,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[5]) >= 0  && parseInt(rowData[7]) > 0) {
									if (parseInt(rowData[5]) <= 0) {
											$(td).css('background-color', 'RGB(255,204,255)')
										}

									
								}
								else
								{
										// $(td).css('background-color', 'RGB(204,255,255)')
									}
								}
							},
							{
							"targets": 0,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( rowData[0].indexOf("YAS") != -1) {								

									$(td).css('background-color', 'rgb(157, 255, 105)')
								}
								else
								{
										$(td).css('background-color', '#ffff66')
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