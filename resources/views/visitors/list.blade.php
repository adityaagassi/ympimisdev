@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
@endsection


@section('header')
<section class="content-header">
	<br>
</section>
@endsection

@section('content')
@if (session('status'))
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
	{{ session('status') }}
</div>   
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-ban"></i> Error!</h4>
	{{ session('error') }}
</div>   
@endif
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Visitor Chart</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					{{-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> --}}
				</div>
			</div>
			<div class="box-body">
				<div class="chart">
					<div id="container" style="min-width: 300px; height: 200px; margin: 0 auto"></div>
				</div>
			</div>
			<!-- /.box-body -->
		</div>
		<div class="box">
			<div class="box-body">
				<div class="table-responsive">
					<table id="visitorlist" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr id="total">
								<th colspan="7"><b id="totalvi"></b></th>
								<th colspan="2">Employee</th>
								<th colspan="4">Action</th>
							</tr>
							<tr>
								<th >Date</th>
								<th >Id</th>
								<th >Company</th>
								<th >Full Name</th>
								<th >Total</th>
								<th >Purpose</th>
								<th >Status</th>
								<th >Name</th>
								<th >Department</th>
								<th >In Time</th>
								<th >Out Time</th>
								<th >Meet</th>
								<th >Reason Unconfirmed</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr>
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

	</div>

</div>

@endsection
@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script><script >

	$(function () {


		var data = {					
			ktp:'ktp',			                  
		}
		$.get('{{ url("visitor_getchart") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var tgl = []
					, vendor = []
					, visitor = [];
					for (i = 0; i < result.ops.length; i++) {
						tgl.push(result.ops[i].tglok);
						vendor.push(parseInt(result.ops[i].vendor));
						visitor.push(parseInt(result.ops[i].visitor));
					}
					

					Highcharts.chart('container', {
						chart: {
							type: 'areaspline'
						},
						title: {
							text: ''
						},						
						xAxis: {
							categories: tgl,
							// plotBands: [{ 
							// 	from: 4.5,
							// 	to: 6.5,
							// 	color: 'rgba(68, 170, 213, .2)'
							// }]
						},
						yAxis: {
							title: {
								text: 'Visitors'
							}
						},
						tooltip: {
							shared: true,
							valueSuffix: ' Visitors'
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							areaspline: {
								fillOpacity: 0.5,
								  dataLabels: {
					                	enabled: true
					            },
					            enableMouseTracking: true
							}
						},
						series: 
						[{
							name: 'Vendor',
							data: vendor
						}, {
							name: 'Visitor',
							data: visitor
						}]
					});					


				}
			}
			else{
				alert("Disconnected from server");
			}
		});





	})
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() { 
		filllist();

		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});

	});


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

	function filllist(){

		$('#visitorlist tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});

		var table = $('#visitorlist').DataTable({
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
			'paging'        : false,
			'lengthChange'  : false,
			'searching'     : true,
			'ordering'      : true,
			'info'        : true,
			'order'       : [],
			'autoWidth'   : true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("visitor_filldisplay") }}/display",
			},				
			"columnDefs": [ {
				"targets": [11],
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData =='Unconfirmed' ) {
						$(td).css('background-color', 'RGB(255,204,255)')
					}
					else
					{
						$(td).css('background-color', 'RGB(204,255,255)')
					}
				}
			}],

			"footerCallback": function (tfoot, data, start, end, display) {
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				var api = this.api(), data;

				var total_diff = api.column(4).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0);
				$('#totalvi').html("Visitor ( "+total_diff.toLocaleString()+" )");
			},

			"columns": [
			{ "data": "tgl"},
			{ "data": "id"},
			{ "data": "company"},
			{ "data": "full_name"},
			{ "data": "total"},
			{ "data": "purpose"},
			{ "data": "status"},
			{ "data": "name"},
			{ "data": "department"},
			{ "data": "in_time"},
			{ "data": "out_time"},
			{ "data": "remark"},
			{ "data": "reason"},


			// { "data": "action"}
			]
		});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		});

		$('#visitorlist tfoot tr').appendTo('#visitorlist thead');
	}





	function reloadtable() {
		$('#visitorlist').DataTable().ajax.reload();
		$('#modal-default').modal('hide');
	}



</script>
@endsection