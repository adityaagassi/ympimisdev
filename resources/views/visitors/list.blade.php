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
		border:none;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.content{
		color: white;
		font-weight: bold;
	}
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
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-2">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date" onchange="filllist()">
			</div>
		</div>
		<div class="col-xs-12">
			<div id="container" style="min-width: 300px; height: 200px; margin: 0 auto"></div>
		</div>

		<div class="col-xs-12">
			<div class="table-responsive">
				<table id="visitorlist" class="table table-bordered">
					<thead style="background-color: #fff;">
						<tr id="total">
							{{-- <th colspan="7"><b id="totalvi"></b></th> --}}
							<th style="background-color: rgba(220,105,0,.75);" colspan="6">Visitor</th>
							<th style="background-color: rgba(126,86,134);" colspan="2">Employee</th>
							<th style="background-color: rgba(85,85,85);" colspan="4">Action</th>
						</tr>
						<tr>
							<th style="background-color: rgba(220,105,0,.75)" >Date</th>
							{{-- <th >Id</th> --}}
							<th style="background-color: rgba(220,105,0,.75)" >Company</th>
							<th style="background-color: rgba(220,105,0,.75)" >Full Name</th>
							<th style="background-color: rgba(220,105,0,.75)" >Total</th>
							<th style="background-color: rgba(220,105,0,.75)" >Purpose</th>
							<th style="background-color: rgba(220,105,0,.75)" >Status</th>
							<th style="background-color: rgba(126,86,134);" >Name</th>
							<th style="background-color: rgba(126,86,134);" >Department</th>
							<th style="background-color: rgba(85,85,85);" >IN</th>
							<th style="background-color: rgba(85,85,85);" >OUT</th>
							<th style="background-color: rgba(85,85,85);" >Meet</th>
							<th style="background-color: rgba(85,85,85);" >Reason	</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script><script >

	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() { 
		filllist();
		drawChart();

		setInterval(filllist, 60000);
		setInterval(drawChart, 60000);


		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});

	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
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


	function drawChart(){
		var data = {					
			ktp:'ktp',			                  
		}

		$.get('{{ url("visitor_getchart") }}', data, function(result, status, xhr){
			// console.log(status);
			// console.log(result);
			// console.log(xhr);
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
							type: 'spline'
						},
						title: {
							text: 'Visitor Chart'
						},						
						xAxis: {
							categories: tgl,
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
	}


	function filllist(){
		var tanggal = $('#tanggal').val();

		var data = {
			date:tanggal
		}
		
		$('#visitorlist').DataTable().destroy();

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
				}
				]
			},
			'paging'        : true,
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
				"data" : data,
			},				
			"columnDefs": [ {
				"targets": [10],
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData == null || cellData == 'Unconfirmed') {
						$(td).css('background-color', 'rgba(242, 75, 75, 0.6)')
					}
					else{
						$(td).css('background-color', 'rgba(100, 149, 237, 0.6)')
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
				// $('#totalvi').html("Visitor ( "+total_diff.toLocaleString()+" )");
			},

			"columns": [
			{ "data": "tgl"},
			// { "data": "id"},
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

	}

	function reloadtable() {
		$('#visitorlist').DataTable().ajax.reload();
		$('#modal-default').modal('hide');
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
				[0, '#3c3c3c']
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