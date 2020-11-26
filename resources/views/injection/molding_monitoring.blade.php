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
		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #42d4f5;color: black">MOLDING TERPASANG</h4></center>
				<div id="cont"></div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #69f542;color: black">MOLDING READY</h4></center>
				<div id="cont3"></div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #f59042;color: black">MOLDING PERIODIK</h4></center>
				<div id="cont2"></div>
			</div>
		</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 10000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

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

		$.get('{{ url("fetch/molding_monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					$('#cont').empty();
					$('#cont2').empty();
					$('#cont3').empty();

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
						last_counter.push(parseInt((parseInt(result.query_pasang[i].last_counter) / parseInt(result.query_pasang[i].qty_shot)).toFixed(0)));
						ng_count.push(parseInt(result.query_pasang[i].ng_count));
						if (result.query_pasang[i].status_mesin == null) {
							status_mesin.push('STORAGE');
						}else{
							status_mesin.push(result.query_pasang[i].status_mesin);
						}
						data.push([parseInt((parseInt(result.query_pasang[i].last_counter) / parseInt(result.query_pasang[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body += '<div class="gambar" id="container'+a+'"></div>';
					}
					$('#cont').append(body);

					for (var j = 0; j < part.length; j++) {
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

						var parta = '<div style="font-size:25px;color:#fff;text-decoration: none">'+part[j]+'</div>';

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
						            format:
						            	tabel
						        },
						        animation: false,
						        tooltip: {
						            valueSuffix: ' Shot'
						        }
						    }]

						}));
					}

					var part_maintenance = [];
					var product_maintenance = [];
					var last_counter_maintenance = [];
					var ng_count_maintenance = [];
					var color_maintenance = [];
					var status_mesin_maintenance = [];
					var data_maintenance = [];
					var body_maintenance = '';

					for (var i = 0; i < result.query_not_ready.length; i++) {
						part_maintenance.push(result.query_not_ready[i].part);
						product_maintenance.push(result.query_not_ready[i].product);
						last_counter_maintenance.push(parseInt((parseInt(result.query_not_ready[i].last_counter) / parseInt(result.query_not_ready[i].qty_shot)).toFixed(0)));
						ng_count_maintenance.push(parseInt(result.query_not_ready[i].ng_count));
						if (result.query_not_ready[i].status_mesin == null && result.query_not_ready[i].status == "LEPAS") {
							status_mesin_maintenance.push('STORAGE');
						}else if(result.query_not_ready[i].status_mesin == null && result.query_not_ready[i].status == "DIPERBAIKI"){
							status_mesin_maintenance.push('PERIODIK');
						}else{
							status_mesin_maintenance.push(result.query_not_ready[i].status_mesin);
						}
						data_maintenance.push([parseInt((parseInt(result.query_not_ready[i].last_counter) / parseInt(result.query_not_ready[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body_maintenance += '<div class="gambar" id="container2'+a+'"></div>';
					}
					$('#cont2').append(body_maintenance);

					for (var k = 0; k < part_maintenance.length; k++) {
						var gaugeOptions2 = {
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

						var a = k+1;
						var container = 'container2'+a;
						var b = data_maintenance[k];
						var tabel = 
						'<table style="text-align:center;margin-right:-30px;"><tr><td style="width:200px;border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px;text-align:left">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_maintenance[k]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_maintenance[k]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin_maintenance[k]+'</td></tr>' +
		            	'</table>';

						var parta = '<div style="font-size:25px;color:#fff;text-decoration: none">'+part_maintenance[k]+'</div>';

						var chartSpeed2 = Highcharts.chart(container, Highcharts.merge(gaugeOptions2, {
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
						            format:
						            	tabel
						        },
						        animation: false,
						        tooltip: {
						            valueSuffix: ' Shot'
						        }
						    }]

						}));
					}

					var part_ready = [];
					var product_ready = [];
					var last_counter_ready = [];
					var ng_count_ready = [];
					var color_ready = [];
					var status_mesin_ready = [];
					var data_ready = [];
					var body_ready = '';

					for (var i = 0; i < result.query_ready.length; i++) {
						part_ready.push(result.query_ready[i].part);
						product_ready.push(result.query_ready[i].product);
						last_counter_ready.push(parseInt((parseInt(result.query_ready[i].last_counter) / parseInt(result.query_ready[i].qty_shot)).toFixed(0)));
						ng_count_ready.push(parseInt(result.query_ready[i].ng_count));
						if (result.query_ready[i].status_mesin == null && result.query_ready[i].status == "LEPAS") {
							status_mesin_ready.push('STORAGE');
						}else if(result.query_ready[i].status_mesin == null && result.query_ready[i].status == "DIPERBAIKI"){
							status_mesin_ready.push('PERIODIK');
						}else{
							status_mesin_ready.push(result.query_ready[i].status_mesin);
						}
						data_ready.push([parseInt((parseInt(result.query_ready[i].last_counter) / parseInt(result.query_ready[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body_ready += '<div class="gambar" id="container3'+a+'"></div>';
					}
					$('#cont3').append(body_ready);

					for (var k = 0; k < part_ready.length; k++) {
						var gaugeOptions2 = {
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

						var a = k+1;
						var container = 'container3'+a;
						var b = data_ready[k];
						var tabel = 
						'<table style="text-align:center;margin-right:-30px;"><tr><td style="width:200px;border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px;text-align:left">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_ready[k]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_ready[k]+'</td></tr>' +

		            	'<tr><td style="border: 1px solid #fff !important;padding-left:40px;padding-right:40px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin_ready[k]+'</td></tr>' +
		            	'</table>';

						var parta = '<div style="font-size:25px;color:#fff;text-decoration: none">'+part_ready[k]+'</div>';

						var chartSpeed2 = Highcharts.chart(container, Highcharts.merge(gaugeOptions2, {
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
						            format:
						            	tabel
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


</script>
@endsection