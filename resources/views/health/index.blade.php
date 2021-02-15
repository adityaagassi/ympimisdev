@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
<style type="text/css">
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.buttonclass {
	  top: 0;
	  left: 0;
	  transition: all 0.15s linear 0s;
	  position: relative;
	  display: inline-block;
	  padding: 15px 25px;
	  background-color: #ffe800;
	  text-transform: uppercase;
	  color: #404040;
	  font-family: arial;
	  letter-spacing: 1px;
	  box-shadow: -6px 6px 0 #404040;
	  text-decoration: none;
	  cursor: pointer;
	}
	.buttonclass:hover {
	  top: 3px;
	  left: -3px;
	  box-shadow: -3px 3px 0 #404040;
	  color: white
	}
	.buttonclass:hover::after {
	  top: 1px;
	  left: -2px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass:hover::before {
	  bottom: -2px;
	  right: 1px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass::after {
	  transition: all 0.15s linear 0s;
	  content: "";
	  position: absolute;
	  top: 2px;
	  left: -4px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg);
	  z-index: -1;
	}
	.buttonclass::before {
	  transition: all 0.15s linear 0s !important;
	  content: "";
	  position: absolute;
	  bottom: -4px;
	  right: 2px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg) !important;
	  z-index: -1 !important;
	}

	a.buttonclass {
	  position: relative;
	}

	a:active.buttonclass {
	  top: 6px;
	  left: -6px;
	  box-shadow: none;
	}
	a:active.buttonclass:before {
	  bottom: 1px;
	  right: 1px;
	}
	a:active.buttonclass:after {
	  top: 1px;
	  left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} Health Indicator <span class="text-purple text-sm">{{$title_jp}}</span>
		<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#importExcel">
			<i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;
			Upload File XML
		</button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div id="container"></div>
					</div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<h4>Filter</h4>
						<div class="row">
							<div class="col-md-2 col-md-offset-4">
								<span style="font-weight: bold;">Date From</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<span style="font-weight: bold;">Date To</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_to"name="tanggal_to" placeholder="Select Date To" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-md-offset-4">
								<span style="font-weight: bold;">Type</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-thermometer-empty"></i>
										</div>
										<select class="form-control select2" name="type" id="type" style="width: 100%" data-placeholder="Choose Type . . .">
											<option value=""></option>
											<option value="Heart Rate">Heart Rate</option>
											<option value="Oxygen Rate">Oxygen Rate</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="{{ url('home') }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/health/'.$loc) }}" class="btn btn-danger">Clear</a>
										<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
									</div>
								</div>
							</div>
						</div>
						<div class="row" id="divTable">
							<table id="tableHealth" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">Employee ID</th>
										<th width="2%">Name</th>
										<th width="1%">Type</th>
										<th width="1%">Type ID</th>
										<th width="1%">Source</th>
										<th width="1%">Value</th>
										<th width="1%">Unit</th>
										<th width="1%">At</th>
									</tr>
								</thead>
								<tbody id="bodyTableHealth">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Upload XML</h4>
				</div>
				<div class="modal-body">
					Upload Excel XML Here:<span class="text-red">*</span>
					<input type="file" name="file" id="file" required>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success">Upload</button>
				</div>
			</div>
		</form>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();

		$('#tanggal_from').val('');
		$('#tanggal_to').val('');
		$('#type').val('').trigger('change');

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	$("form#importForm").submit(function(e) {
		if ($('#file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("upload/health") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.message){
					$("#loading").hide();
					$("#file").val('');
					$('#importExcel').modal('hide');
					fillList();
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function fillList(){
		var data = {
			date_from:$('#tanggal_from').val(),
			date_to:$('#tanggal_to').val(),
			type:$('#type').val(),
		}
		$.get('{{ url("fetch/health") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHealth').DataTable().clear();
				$('#tableHealth').DataTable().destroy();
				$('#bodyTableHealth').html("");
				var tableData = "";
				$.each(result.health, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.type +'</td>';
					tableData += '<td>'+ value.type_id +'</td>';
					tableData += '<td>'+ value.source_name +'</td>';
					if (value.type == 'Oxygen Rate') {
						tableData += '<td>'+ parseFloat(value.value)*100 +'</td>';
					}else{
						tableData += '<td>'+ value.value+'</td>';
					}
					tableData += '<td>'+ value.unit +'</td>';
					tableData += '<td>'+ value.time_at +'</td>';
					tableData += '</tr>';
				});
				$('#bodyTableHealth').append(tableData);
				
				var table = $('#tableHealth').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				var date = [], max_heart = [], min_heart = [], max_oxy = [],min_oxy = [], series = [], series2 = [], series3 = [], series4 = [], series5 = [];

				$.each(result.chart, function(key, value){
					date.push(value.date);
					if (value.employee_id == 'PI1910002') {
						max_oxy.push(value.max_oxy_rate);
						min_oxy.push(value.min_oxy_rate);
						max_heart.push(value.max_heart_rate);
						min_heart.push(value.min_heart_rate);
						series.push([date[key],max_oxy[key]]);
						series2.push([date[key],min_oxy[key]]);
						series3.push([date[key],max_heart[key]]);
						series4.push([date[key],min_heart[key]]);
					};
					// date.push(value.date);
					// avg.push(parseFloat(value.avg));
					// series.push([week_date[key],avg[key]]);

					// week_date2.push(value.week_date);
					// highest.push(parseFloat(value.highest));
					// series2.push([week_date2[key],highest[key]]);
				});

				Highcharts.chart('container', {
					chart: {
						type: 'spline'
					},
					title: {
						text: 'Health Indicator Chart'
					},						
					xAxis: {
						categories: date,
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
						name: 'Max Oxy Rate',
						data: series
					}, {
						name: 'Min Oxy Rate',
						data: series2
					}, {
						name: 'Max Heart Rate',
						data: series3
					}, {
						name: 'Min Heart Rate',
						data: series4
					}]
				});	

			}
			else{
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
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection