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

	  .select2-search__field {
	  	color: black;
	  }
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading. Please Wait. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
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
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
						@foreach($vendors as $vendor)
						<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
						@endforeach
					</select>
					<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="form-group">
					<select class="form-control select3" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
						@foreach($materials as $material)
						<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
						@endforeach
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<button class="btn btn-success pull-left" onclick="fetchLotStatus()" style="font-weight: bold;">
					Search
				</button>
			</div>
			<div class="col-xs-2" style="padding-left: 0px">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 12.5px"></div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-left: 0px;padding-top: 5px">
			<div id="container" style="width: 100%;height: 500px"></div>
		</div>
	</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center><h3 style="font-weight: bold;color:black ;font-size: 20px" id="judul_detail"></h3></center>
					<div class="col-md-12" id="bodyDetail">
			          <table class="table table-bordered table-striped" style="font-size:15px" id="tableDetail">
			          	<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:15px">
			          		<tr>
			          			<th>Date</th>
			          			<th>Location</th>
			          			<th>Material</th>
			          			<th>Vendor</th>
			          			<th>Invoice</th>
			          			<th>Inspection Level</th>
			          			<th>Qty Rec</th>
			          			<th>Qty Check</th>
			          			<th>Qty Repair</th>
			          			<th>Qty Return</th>
			          			<th>Total NG</th>
			          			<th>Status</th>
			          		</tr>
			          	</thead>
			          	<tbody id="bodyTableDetail">
			          		
			          	</tbody>
			          </table>
			        </div>
				</div>
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
		</div>
	</div>
</div>
</section>
@endsection
@section('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{ url("js/pareto.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		$('.select3').select2();
		fetchLotStatus();
		setInterval(fetchLotStatus, 5000);


	});

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/ng_rate") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<span><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</span>');

					var categories = [];
					var checkes = [];
					var returnes = [];
					var repaires = [];
					var persen = [];

					$.each(result.ng_rate, function(key,value){
						categories.push(value.months);
						checkes.push(parseInt(value.checkes));
						returnes.push(parseInt(value.returnes));
						repaires.push(parseInt(value.repaires));
						persen.push(parseFloat(value.persen));
					});

					Highcharts.chart('container', {
					    chart: {
					        zoomType: 'xy'
					    },
					    title: {
					        text: 'NG Rate Incoming Check QA'
					    },
					    subtitle: {
					        text: ''
					    },
					    xAxis: [{
					        categories: categories,
					        crosshair: true
					    }],
					    yAxis: [{ 
					        labels: {
					            format: '{value}',
					            style: {
					                color: '#fff'
					            }
					        },
					        title: {
					            text: 'Qty',
					            style: {
					                color: '#fff'
					            }
					        }
					    }, { 
					        title: {
					            text: 'NG Rate',
					            style: {
					                color: '#fff'
					            }
					        },
					        labels: {
					            format: '{value}%',
					            style: {
					                color: '#fff'
					            }
					        },
					        opposite: true
					    }],
					    tooltip: {
					        shared: true
					    },
					    legend: {
					        enabled:true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
							},
							spline:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}%',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
							},
						},
					    series: [
					    {
					        name: 'Repair',
					        type: 'column',
					        data: repaires,
					        color: '#a5a5a5'

					    },{
					        name: 'Return',
					        type: 'column',
					        data: returnes,
					        color: '#f46fbb'

					    },{
					        name: 'Total Check',
					        type: 'column',
					        data: checkes,
					        color: '#2c5394'

					    }, {
					        name: 'NG Rate',
					        type: 'spline',
					        data: persen,
					        color: '#ed151d',
					        yAxis: 1,
					        tooltip: {
					            valueSuffix: '%'
					        }
					    }]
					});
				}
			}
		});
	}

	function showModalDetail(categories) {
		$('#loading').show();
		$('#judul_detail').html("");
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
			categories:categories
		}

		$.get('{{ url("fetch/qa/display/incoming/ng_rate/detail") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#judul_detail').html("Detail NG Rate Incoming Check QA on "+categories);
					$('#bodyTableDetail').html("");
					var bodyDetail = "";
					var total_ng = 0;
					var total_check= 0;
					$.each(result.detail, function(key,value){
						if (value.location == 'wi1') {
				  			var loc = 'Woodwind Instrument (WI) 1';
				  		}else if (value.location == 'wi2') {
				  			var loc = 'Woodwind Instrument (WI) 2';
				  		}else if(value.location == 'ei'){
				  			var loc = 'Educational Instrument (EI)';
				  		}else if (value.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						bodyDetail += '<tr>';
						bodyDetail += '<td>'+value.created+'</td>';
						bodyDetail += '<td>'+loc+'</td>';
						bodyDetail += '<td>'+value.material_number+' - '+value.material_description+'</td>';
						bodyDetail += '<td>'+value.vendor+'</td>';
						bodyDetail += '<td>'+value.invoice+'</td>';
						bodyDetail += '<td>'+value.inspection_level+'</td>';
						bodyDetail += '<td>'+value.qty_rec+'</td>';
						bodyDetail += '<td>'+value.qty_check+'</td>';
						bodyDetail += '<td>'+value.repair+'</td>';
						bodyDetail += '<td>'+value.return+'</td>';
						bodyDetail += '<td>'+value.total_ng+'</td>';
						bodyDetail += '<td>'+value.status_lot+'</td>';
						bodyDetail += '</tr>';

						total_ng = total_ng + parseInt(value.total_ng);
						total_check = total_check + parseInt(value.qty_check);
					});

					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL NG</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black">'+total_ng+'</td>';
					bodyDetail += '</tr>';
					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL CHECK</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black">'+total_check+'</td>';
					bodyDetail += '</tr>';
					bodyDetail += '</tr>';
					bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					bodyDetail += '<td colspan="10" style="color:black;text-align:right">NG RATE</td>';
					bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+Math.round((total_ng / total_check) * 100)+' %</td>';
					bodyDetail += '</tr>';

					$('#bodyTableDetail').append(bodyDetail);

					$('#modalDetail').modal('show');
					$('#loading').hide();
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