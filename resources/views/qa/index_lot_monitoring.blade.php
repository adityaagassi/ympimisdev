@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
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

	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
	    width: 350px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px">
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<button class="btn btn-success pull-left" onclick="fetchLotStatus()" style="font-weight: bold;">
					Search
				</button>
			</div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
		</div>
		@foreach($location as $location)
		<?php $locs = explode("_", $location) ?>
		<div class="gambar" style="margin-top:0px" id="container_{{$locs[0]}}">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 25px">{{$locs[1]}}
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 25px;width: 50%;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 25px;width: 50%;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 100px;" id="lot_ok_td_{{$locs[0]}}"><span id="lot_ok_{{$locs[0]}}">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 100px;" id="lot_out_td_{{$locs[0]}}"><span id="lot_out_{{$locs[0]}}">0</span>
					</td>
				</tr>
			</table>
		</div>
		@endforeach
		<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
			<div class="box-body">
				<div class="col-xs-12" style="background-color: rgb(126,86,134)">
					<span style="font-size: 40px;color: white;width: 100%;">LOT OUT DETAILS</span>
				</div>
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 20px">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="border: 1px solid black;padding: 0px;width: 1%">Date</th>
								<th style="border: 1px solid black;padding: 0px;width: 2%">Loc</th>
								<th style="border: 1px solid black;padding: 0px;width: 3%">Material</th>
								<th style="border: 1px solid black;padding: 0px;width: 3%">Vendor</th>
								<th style="border: 1px solid black;padding: 0px;width: 1%">Invoice</th>
								<th style="border: 1px solid black;padding: 0px;width: 1%">Qty Check</th>
								<th style="border: 1px solid black;padding: 0px;width: 1%">Qty NG</th>
								<th style="border: 1px solid black;padding: 0px;width: 1%">NG Ratio (%)</th>
								<th style="border: 1px solid black;padding: 0px;width: 2%">Defect</th>
							</tr>
						</thead>
						<tbody id="body_table_lot" style="text-align:center;">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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

	jQuery(document).ready(function(){
		$('.select2').select2();
		fetchLotStatus();
		setInterval(fetchLotStatus, 5000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/lot_status") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$.each(result.lot_count, function(key,value){
						$('#lot_ok_'+value.location).html(value.lot_ok);
						$('#lot_out_'+value.location).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.location).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.location).css("color","white",'important');
						}else{
							$('#lot_ok_td_'+value.location).css("background-color","white",'important');
							$('#lot_ok_td_'+value.location).css("color","black",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.location).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.location).css("color","white",'important');
						}else{
							$('#lot_out_td_'+value.location).css("background-color","white",'important');
							$('#lot_out_td_'+value.location).css("color","black",'important');
						}
					});

					$('#body_table_lot').html("");
					var body_lot = "";

					$.each(result.lot_detail, function(key2,value2){
						if (value2.location == 'wi1') {
				  			var loc = 'Woodwind Instrument (WI) 1';
				  		}else if (value2.location == 'wi2') {
				  			var loc = 'Woodwind Instrument (WI) 2';
				  		}else if(value2.location == 'ei'){
				  			var loc = 'Educational Instrument (EI)';
				  		}else if (value2.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value2.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						body_lot += '<tr>';
						body_lot += '<td>'+value2.date_lot+'</td>';
						body_lot += '<td>'+loc+'</td>';
						body_lot += '<td>'+value2.material_number+' - '+value2.material_description+'</td>';
						body_lot += '<td>'+value2.vendor+'</td>';
						body_lot += '<td>'+value2.invoice+'</td>';
						body_lot += '<td>'+value2.qty_check+'</td>';
						body_lot += '<td>'+value2.total_ng+'</td>';
						body_lot += '<td>'+value2.ng_ratio+'</td>';
						body_lot += '<td>'+value2.ng_name+'</td>';
						body_lot += '</tr>';
					});

					$('#body_table_lot').append(body_lot);
				}
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
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection