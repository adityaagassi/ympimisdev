@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid black;
}
/*table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}*/
	.content{
		color: white;
		font-weight: bold;
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
    width: 300px;
    height: 350px;
    background-color: none;
    border-radius: 15px;
    margin-left: 30px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }

  #table-count{
  	border: 1px solid #000 !important;
  	padding: 5px;
  }

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 0px;">
			<center><a style="font-size: 2vw; font-weight: bold;color: white"> MOLDING MAINTENANCE MONITORING </a><a style="font-size: 2vw; font-weight: bold; color: white"> (金型保全管理)</a></center>
			<div class="row" style="margin:0px;">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
		</div>
		<div class="col-xs-9">
			<div class="row">
				<div id="cont"></div>
			</div>
		</div>
		<!-- <div class="col-xs-3">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #65ff57;">
					<center><span style="font-size: 22px; font-weight: bold; color: black;">MOLDING READY</span></center>
				</div>
				<ul class="nav nav-pills nav-stacked">
					<li>
						<table class="table-responsive" style="width: 100%;color: black;text-align: center;">
							<thead>
								<tr>
									<td style="border-bottom: 2px solid #545454;border-right: 2px solid #ff0000">Molding</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Qty</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Lokasi</td>
								</tr>
							</thead>
							<tbody id="bodyMoldingReady">
								<tr>
									<td>
										
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				</ul>
			</div>
			<div class="box box-solid">
				<div class="box-header" style="background-color: #ff7878;">
					<center><span style="font-size: 15px; font-weight: bold; color: black;">MOLDING BELUM MAINTENANCE</span></center>
				</div>
				<ul class="nav nav-pills nav-stacked">
					<li>
						<table class="table-responsive" style="width: 100%;color: black;text-align: center;">
							<thead>
								<tr>
									<td style="border-bottom: 2px solid #545454;border-right: 2px solid #ff0000">Molding</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Qty</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Lokasi</td>
								</tr>
							</thead>
							<tbody id="bodyMoldingNotReady">
								<tr>
									<td>
										
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				</ul>
			</div>
			<div class="box box-solid">
				<div class="box-header" style="background-color: #ffcf30;">
					<center><span style="font-size: 15px; font-weight: bold; color: black;">MOLDING SEDANG MAINTENANCE</span></center>
				</div>
				<ul class="nav nav-pills nav-stacked">
					<li>
						<table class="table-responsive" style="width: 100%;color: black;text-align: center;">
							<thead>
								<tr>
									<td style="border-bottom: 2px solid #545454;border-right: 2px solid #ff0000">Molding</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Qty</td>
									<td style="border-bottom: 2px solid #545454;border-left: 2px solid #ff0000">Lokasi</td>
								</tr>
							</thead>
							<tbody id="bodyMoldingMaintenance">
								<tr>
									<td>
										
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				</ul>
			</div>
		</div>
	</div> -->

	
</section>

<div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableResult" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                  	<th>Nomor</th>
                    <th>Mesin</th>
                    <th>Part</th>
                    <th>Color</th>    
                    <th>Running Time</th>
                    <th>NG</th>
                    <th>PIC</th>
                  </tr>
                </thead>
                <tbody id="tableBodyResult">
                </tbody>
                <tfoot>
				</tfoot>
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
@endsection
@section('scripts')
<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
<!-- <script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<!-- <script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		// setInterval(fillChart, 10000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	// Highcharts.theme = {
	// 	colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
	// 	'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	// 	chart: {
	// 		backgroundColor: {
	// 			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
	// 			stops: [
	// 			[0, '#2a2a2b'],
	// 			[1, '#3e3e40']
	// 			]
	// 		},
	// 		style: {
	// 			fontFamily: 'sans-serif'
	// 		},
	// 		plotBorderColor: '#606063'
	// 	},
	// 	title: {
	// 		style: {
	// 			color: '#E0E0E3',
	// 			textTransform: 'uppercase',
	// 			fontSize: '20px'
	// 		}
	// 	},
	// 	subtitle: {
	// 		// style: {
	// 		// 	color: '#E0E0E3',
	// 		// 	textTransform: 'uppercase'
	// 		// }
	// 	},
	// 	xAxis: {
	// 		gridLineColor: '#707073',
	// 		labels: {
	// 			style: {
	// 				color: '#E0E0E3'
	// 			}
	// 		},
	// 		lineColor: '#707073',
	// 		minorGridLineColor: '#505053',
	// 		tickColor: '#707073',
	// 		title: {
	// 			style: {
	// 				color: '#A0A0A3'

	// 			}
	// 		}
	// 	},
	// 	yAxis: {
	// 		gridLineColor: '#707073',
	// 		labels: {
	// 			style: {
	// 				color: '#E0E0E3'
	// 			}
	// 		},
	// 		lineColor: '#707073',
	// 		minorGridLineColor: '#505053',
	// 		tickColor: '#707073',
	// 		tickWidth: 1,
	// 		title: {
	// 			style: {
	// 				color: '#A0A0A3'
	// 			}
	// 		}
	// 	},
	// 	tooltip: {
	// 		backgroundColor: 'rgba(0, 0, 0, 0.85)',
	// 		style: {
	// 			color: '#F0F0F0'
	// 		}
	// 	},
	// 	plotOptions: {
	// 		series: {
	// 			dataLabels: {
	// 				color: 'white'
	// 			},
	// 			marker: {
	// 				lineColor: '#333'
	// 			}
	// 		},
	// 		boxplot: {
	// 			fillColor: '#505053'
	// 		},
	// 		candlestick: {
	// 			lineColor: 'white'
	// 		},
	// 		errorbar: {
	// 			color: 'white'
	// 		}
	// 	},
	// 	legend: {
	// 		itemStyle: {
	// 			color: '#E0E0E3'
	// 		},
	// 		itemHoverStyle: {
	// 			color: '#FFF'
	// 		},
	// 		itemHiddenStyle: {
	// 			color: '#606063'
	// 		}
	// 	},
	// 	credits: {
	// 		style: {
	// 			color: '#666'
	// 		}
	// 	},
	// 	labels: {
	// 		style: {
	// 			color: '#707073'
	// 		}
	// 	},

	// 	drilldown: {
	// 		activeAxisLabelStyle: {
	// 			color: '#F0F0F3'
	// 		},
	// 		activeDataLabelStyle: {
	// 			color: '#F0F0F3'
	// 		}
	// 	},

	// 	navigation: {
	// 		buttonOptions: {
	// 			symbolStroke: '#DDDDDD',
	// 			theme: {
	// 				fill: '#505053'
	// 			}
	// 		}
	// 	},

	// 	rangeSelector: {
	// 		buttonTheme: {
	// 			fill: '#505053',
	// 			stroke: '#000000',
	// 			style: {
	// 				color: '#CCC'
	// 			},
	// 			states: {
	// 				hover: {
	// 					fill: '#707073',
	// 					stroke: '#000000',
	// 					style: {
	// 						color: 'white'
	// 					}
	// 				},
	// 				select: {
	// 					fill: '#000003',
	// 					stroke: '#000000',
	// 					style: {
	// 						color: 'white'
	// 					}
	// 				}
	// 			}
	// 		},
	// 		inputBoxBorderColor: '#505053',
	// 		inputStyle: {
	// 			backgroundColor: '#333',
	// 			color: 'silver'
	// 		},
	// 		labelStyle: {
	// 			color: 'silver'
	// 		}
	// 	},

	// 	navigator: {
	// 		handles: {
	// 			backgroundColor: '#666',
	// 			borderColor: '#AAA'
	// 		},
	// 		outlineColor: '#CCC',
	// 		maskFill: 'rgba(255,255,255,0.1)',
	// 		series: {
	// 			color: '#7798BF',
	// 			lineColor: '#A6C7ED'
	// 		},
	// 		xAxis: {
	// 			gridLineColor: '#505053'
	// 		}
	// 	},

	// 	scrollbar: {
	// 		barBackgroundColor: '#808083',
	// 		barBorderColor: '#808083',
	// 		buttonArrowColor: '#CCC',
	// 		buttonBackgroundColor: '#606063',
	// 		buttonBorderColor: '#606063',
	// 		rifleColor: '#FFF',
	// 		trackBackgroundColor: '#404043',
	// 		trackBorderColor: '#404043'
	// 	},

	// 	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	// 	background2: '#505053',
	// 	dataLabelsColor: '#B0B0B3',
	// 	textColor: '#C0C0C0',
	// 	contrastTextColor: '#F0F0F3',
	// 	maskColor: 'rgba(255,255,255,0.3)'
	// };
	// Highcharts.setOptions(Highcharts.theme);

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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		return day + "-" + month + "-" + year;
	}

	function fillChart() {
		// var proses = $('#process').val();
		var tgl = $('#tanggal').val();

		if (tgl == '') {
			tgl = '{{ date("Y-m-d")}}';
		}
		
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		
		var data = {
			tgl:tgl
			// proses:proses
		}

		$.get('{{ url("fetch/molding_monitoring") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#bodyMoldingReady').html("");
					$('#bodyMoldingNotReady').html("");
					$('#bodyMoldingMaintenance').html("");

					var moldingReady = "";
					var moldingNotReady = "";
					var moldingMaintenance = "";

					$.each(result.query_ready, function(key, value) {
						moldingReady += '<tr>';
						moldingReady += '<td style="border: 1px solid #545454;">'+value.part+'</td>';
						moldingReady += '<td style="border: 1px solid #545454;">'+value.last_counter+'</td>';
						if (value.status == 'LEPAS') {
							moldingReady += '<td style="border: 1px solid #545454;">STORAGE</td>';
						}
						moldingReady += '</tr>';
					});
					$('#bodyMoldingReady').append(moldingReady);

					$.each(result.query_not_ready, function(key, value) {
						moldingNotReady += '<tr>';
						moldingNotReady += '<td>'+value.part+'</td>';
						moldingNotReady += '<td>'+value.last_counter+'</td>';
						if (value.status == 'LEPAS') {
							moldingNotReady += '<td>STORAGE</td>';
						}else{
							moldingNotReady += '<td>'+status+'</td>';
						}
						moldingNotReady += '</tr>';
					});
					$('#bodyMoldingNotReady').append(moldingNotReady);

					$.each(result.query_maintenance, function(key, value) {
						moldingMaintenance += '<tr>';
						moldingMaintenance += '<td>'+value.part+'</td>';
						moldingMaintenance += '<td>'+value.last_counter+'</td>';
						if (value.status == 'LEPAS') {
							moldingMaintenance += '<td>STORAGE</td>';
						}else{
							moldingMaintenance += '<td>'+status+'</td>';
						}
						moldingMaintenance += '</tr>';
					});
					$('#bodyMoldingMaintenance').append(moldingMaintenance);

					$('#cont').empty();

					//Chart Machine Report
					// var jumlah_ok = [];
					var part = [];
					var product = [];
					var last_counter = [];
					var ng_count = [];
					var color = [];
					var status_mesin = [];
					var data = [];
					var body = '';

					for (var i = 0; i < result.query_pasang.length; i++) {
						part.push(result.query_pasang[i].part);
						product.push(result.query_pasang[i].product);
						// jumlah_ok.push(parseInt(result.query_pasang[i].jumlah_ok));
						last_counter.push(parseInt(result.query_pasang[i].last_counter));
						ng_count.push(parseInt(result.query_pasang[i].ng_count));
						if (result.query_pasang[i].status_mesin == null) {
							status_mesin.push('STORAGE');
						}else{
							status_mesin.push(result.query_pasang[i].status_mesin);
						}

						if(result.query_pasang[i].last_counter >= 15000){
							data.push([result.query_pasang[i].last_counter]);
						}else{
							data.push([result.query_pasang[i].last_counter])
						}
						var a = i+1;
						body += '<div class="gambar" id="container'+a+'"></div>';
					}
					$('#cont').append(body);

					// console.log(part.length);

					for (var j = 0; j < part.length; j++) {
						// console.log(part[j]);
						var gaugeOptions = {
						    chart: {
						        type: 'solidgauge',
						        backgroundColor:null
						    },

						    title: null,

						    pane: {
						        center: ['50%', '50%'],
						        size: '100%',
						        startAngle: -90,
						        endAngle: 90,
						        background: {
						            backgroundColor:
						                Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
						            innerRadius: '60%',
						            outerRadius: '100%',
						            shape: 'arc'
						        }
						    },

						    exporting: {
						        enabled: false
						    },

						    tooltip: {
						        enabled: false
						    },

						    // the value axis
						    yAxis: {
						        stops: [
						            [0.1, '#55BF3B'], // green
						            [0.5, '#DDDF0D'], // yellow
						            [0.7, '#DF5353'] // red
						        ],
						        lineWidth: 0,
						        tickWidth: 0,
						        minorTickInterval: null,
						        tickAmount: 2,
						        title: {
						            y: 125
						        },
						        labels: {
						            y: 30,
						            style:{
						            	color:'#fff',
						            	fontSize:'20px'
						            }
						        },
						    },

						    plotOptions: {
						        solidgauge: {
						            dataLabels: {
						                y: 182,
						                borderWidth: 0,
						                useHTML: true
						            }
						        }
						    }
						};

						var a = j+1;
						var container = 'container'+a;
						var b = data[j];
						var tabel = 
						'<table style="text-align:center;margin-right:-30px;"><tr><td style="width:200px;border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px;text-align:left">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count[j]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin[j]+'</td></tr>' +
		            	'</table>';
						// var parta = '<div style="font-size:40px;color:#fff;text-decoration: none">'+part[j]+'</div><br><div style="font-size:25px;color:#fff;text-decoration: none">SHOTS : '+last_counter[j]+'</div><br><div style="font-size:25px;color:#fff;text-decoration: none">NG : '+ng_count[j]+'</div><br><div style="font-size:25px;color:#fff;text-decoration: none">LOC : '+status_mesin[j]+'</div>';

						var parta = '<div style="font-size:25px;color:#fff;text-decoration: none">'+part[j]+'</div>';

						// The speed gauge
						var chartSpeed = Highcharts.chart(container, Highcharts.merge(gaugeOptions, {
						    yAxis: {
						        min: 0,
						        max: 15000,
						        title: {
						            text: parta,
						            style: {
											color: '#000',
											textTransform: 'uppercase',
											fontSize: '30px',
										}
									// enabled:false
						        },
						        tickPositions: [0, 15000]
						    },

						    credits: {
						        enabled: false
						    },

						    series: [{
						        name: 'Shots',
						        data: [b],
						        dataLabels: {
						        	// enabled:false
						            format:
						            	tabel
						                // '<div style="text-align:center">' +
						                // '<span style="font-size:40px;color:white">{y}</span><br/>' +
						                // '<span style="font-size:12px;color:white">Shots</span>' +
						                // '</div>'
						        },
						        animation: false,
						        tooltip: {
						            valueSuffix: ' Shot'
						        }
						    }]

						}));

						


					}
				}
			}
		});

	}

	function ShowModal(mesin,tanggal) {

	    $("#myModal").modal("show");

	    var data = {
	    	mesin:mesin,
			tanggal:tanggal
		}

	    $.get('{{ url("fetch/detailDailyNG") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableResult').DataTable().clear();
				$('#tableResult').DataTable().destroy();
				$('#tableBodyResult').html("");
				var tableData = "";
				var count = 1;
				var ng = [];
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.mesin +'</td>';
					tableData += '<td>'+ value.part_name +'</td>';
					tableData += '<td>'+ value.color +'</td>';
					var a = value.running_time.split(':');
					var minutes = (+a[0]) * 60 + (+a[1]) + ((+a[2]) / 60);
					tableData += '<td>'+ parseFloat(minutes).toFixed(2) + ' Minutes</td>';
					var ng_name = value.ng_name.split(",");
					var ng_count = value.ng_count.split(",");
					for (var i = 0; i < ng_name.length; i++) {
						ng.push('<label class="label label-danger">'+ng_name[i] + ' = ' + ng_count[i] + '</label><br>');
					}
					tableData += '<td>'+ ng.join(' ') +'</td>';
					tableData += '<td>'+ value.pic +'</td>';
					tableData += '</tr>';
					count += 1;
				});
				$('#tableBodyResult').append(tableData);
				$('#tableResult').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 5, 10, 25, -1 ],
					[ '5 rows', '10 rows', '25 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 5,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});

	    $('#judul_table').append().empty();
	    $('#judul_table').append('<center>Daily NG Tanggal <b>'+tanggal+'</b> di <b>'+mesin+'</b></center>');
	    
	  }


</script>
@endsection