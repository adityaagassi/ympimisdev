@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.morecontent span {
		display: none;
	}
	.morelink {
		display: block;
	}

	thead>tr>th{
		text-align:center;
		color:white;
		font-weight: bold;
		font-size: 11pt;
	}
	tbody>tr>td{
		text-align:center;
		color:white;
		border-top: 1px solid #333333 !important;
		border-left: 1px solid #363b38;
		border-right: 1px solid #363b38;
		font-size: 0.90vw;
	}
	tfoot>tr>th{
		text-align:center;
		color:white;
	}
	td:hover {
		overflow: visible;
	}
	table {
		background-color: #212121;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="col-md-12" style="padding: 1px !important">
		<div class="col-md-2">
			<div class="input-group date">
				<div class="input-group-addon bg-green">
					<i class="fa fa-calendar"></i>
				</div>
				<select class="form-control" id="status_cari" placeholder="Pilih Status" onchange="get_data('')" style="width: 100%;">
					<option></option>
					<option value="Finished">Finished</option>
				</select>
			</div>
		</div>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div id="chart_result" style="width: 100%; height: 300px;"></div>
			<table id="masterTable" class="table">
				<thead>
					<tr>
						<th rowspan='2' style="width: 3%">ORDER NO.</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 5%">Department</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 1%">Target Date</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; vertical-align: middle">Permasalahan</th>
						<th style="border-left: 3px solid #f44336;"></th>
						<th colspan="2" style="border-left: 3px solid #f44336; width: 1%">Verification</th>
						<th colspan="2" style="border-left: 3px solid #f44336; width: 25%">Progress</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; vertical-align: middle">Penanganan</th>
					</tr>

					<tr>
						<th style="border-left: 3px solid #f44336; width: 1%">Requester</th>
						<th style="border-left: 3px solid #f44336; width: 1%">Request</th>
						<th style="border-left: 1px solid #363b38; width: 1%">Receive</th>
						<th style="border-left: 3px solid #f44336; width: 1%">PIC</th>
						<th style="width: 1%">Status</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>
</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		get_data('');

		setInterval( function() { get_data(''); }, 10000 );
	})

	function get_data(mon) {
		var data = {
			mon:mon
		}

		var statuses = $("#status_cari option:selected").val();

		var showChar = 200;
		var ellipsestext = "...";
		var moretext = "Show more >";
		var lesstext = "< Show less";

		$.get('{{ url("fetch/maintenance/spk/monitoring") }}', data, function(result, status, xhr){

			$('#tableBody').html("");
			var tableData = "";

			var colors = ["default", "info", "warning", "primary", "danger", "success"];

			$.each(result.datas, function(index, value){

				var st = ["Requested", "Received", "Listed", "InProgress", "Pending"];

				if (statuses == value.process_name || (statuses == '' && st.indexOf(value.process_name) > 0)) {
					var stat = 0;
					var progress = "0%";
					var cls_prog = "progress-bar-success";

					$.each(result.progress, function(index2, value2){
						if (value.order_no == value2.order_no) {
							stat = 1;

							tmp = (value2.act_time / value2.plan_time * 100).toFixed(0);

							if (tmp == 'Infinity') {
								progress = "500%";
								cls_prog = "progress-bar-danger";
							} else {
								progress = tmp+"%";
								cls_prog = "progress-bar-success";
							}
						}
					})

					tableData += '<tr>';
					tableData += '<td>'+ value.order_no +'</td>';
					tableData += '<td style="border-left: 3px solid #f44336">'+ value.bagian +'</td>';

					// if(value.priority == 'Urgent'){
					// 	var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					// }else{
					// 	var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					// }

					tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-success">'+ value.target_date +'</span></td>';

					tableData += '<td style="border-left: 3px solid #f44336"><span class="more">'+ value.description +'</span></td>';
					tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-success">'+ value.requester +'</span></td>';

					if (value.pic) {
						tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-success">Maintenance</span></td>';

						tableData += '<td><span class="label label-success">maintenance</span></td>';

						picc = value.pic.split(',');
						if (value.inprogress){

							tableData += '<td style="border-left: 3px solid #f44336">';
							for (var i = 0; i < picc.length; i++) {
								tableData += '<span class="label label-success">'+picc[i]+'</span><br>';
							}

							tableData += '</td>';
						}
						else {
							tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-danger">'+value.pic+'</span></td>';
						}
					}
					else {
						if (value.priority == "Urgent") {
							if (value.process_name == "Requested")
								tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-danger">Maintenance</span></td>';
							else {
								tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-success">Maintenance</span></td>';
								tableData += '<td><span class="label label-danger">Maintenance</span></td>';
							}
						} else {
							tableData += '<td style="border-left: 3px solid #f44336"></td>';
							tableData += '<td><span class="label label-danger">Maintenance</span></td>';
						}

						tableData += '<td style="border-left: 3px solid #f44336"></td>';
					}


					if (value.process_name == "Pending") 
						var status_pending = '<br><span class="label label-danger">'+value.status+'</span>';
					else
						var status_pending = "";

					tableData += '<td style="border-left: 1px solid #f44336"><span class="label label-'+colors[st.indexOf(value.process_name)]+'">'+value.process_name+'</span>'+status_pending+'</td>';

					if (value.process_name == "Pending" || value.process_name == "Finished") 

						tableData += '<td style="border-left: 3px solid #f44336"><span class="more">'+(value.handling || '' )+'</span></td>';	
					else
						tableData += '<td style="border-left: 3px solid #f44336"></td>';	


					tableData += '</tr>';	
				}
			})

			$('#tableBody').append(tableData);

			// -------------- MORE ----------	
			$('.more').each(function() {
				var content = $(this).html();

				if(content.length > showChar) {

					var c = content.substr(0, showChar);
					var h = content.substr(showChar, content.length - showChar);

					var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

					$(this).html(html);
				}

			});

			$(".morelink").click(function(){
				if($(this).hasClass("less")) {
					$(this).removeClass("less");
					$(this).html(moretext);
				} else {
					$(this).addClass("less");
					$(this).html(lesstext);
				}
				$(this).parent().prev().toggle();
				$(this).prev().toggle();
				return false;
			});

			var dt = [];
			var listed = [];
			var inprogress = [];
			var pending = [];
			var finished = [];

			$.each(result.data_bar, function(index3, value3){
				if (value3.process_name == "Listed") {
					listed.push(value3.jml);
				} else if (value3.process_name == "InProgress") {
					inprogress.push(value3.jml);
				} else if (value3.process_name == "Pending") {
					pending.push(value3.jml);
				} else if (value3.process_name == "Finished") {
					finished.push(value3.jml);
				}

				if(dt.indexOf(value3.dt) === -1){
					dt[dt.length] = value3.dt;
				}
			})

			var datas = [listed, inprogress ,pending, finished];

			drawChart(dt, datas);
		})
}

function showDetail(order_no) {
	$("#detailModal").modal("show");

	var data = {
		order_no : order_no
	}

	$.get('{{ url("fetch/maintenance/detail") }}', data,  function(result, status, xhr){
		$("#spk_detail").val(result.detail.order_no);
		$("#pengaju_detail").val(result.detail.name);
		$("#tanggal_detail").val(result.detail.date);
		$("#bagian_detail").val(result.detail.section);

		if (result.detail.priority == "Normal") {
			$("#prioritas_detail").addClass("label-default");
		} else {
			$("#prioritas_detail").addClass("label-danger");
		}
		$("#prioritas_detail").text(result.detail.priority);

		$("#workType_detail").val(result.detail.type);
		$("#kategori_detail").val(result.detail.category);
		$("#mesin_detail").val(result.detail.machine_condition);
		$("#bahaya_detail").val(result.detail.danger);
		$("#uraian_detail").val(result.detail.description);
		$("#keamanan_detail").val(result.detail.safety_note);
		$("#target_detail").val(result.detail.target_date);
		$("#status_detail").val(result.detail.process_name);
	})
}

function drawChart(ctg, datas) {
	Highcharts.chart('chart_result', {
		chart: {
			type: 'column'
		},
		title: {
			text: '<b>SPK Progress Monitoring</b>'
		},
		subtitle: {
			text: 'On ',
			style: {
				fontSize: '1vw',
				fontWeight: 'bold'
			}
		},
		xAxis: {
			categories: ctg
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Total SPK'
			},
			stackLabels: {
				enabled: true,
			}
		},
		legend: {
			align: 'right',
			x: -30,
			verticalAlign: 'top',
			y: 25,
			floating: true
		},
		tooltip: {
			headerFormat: '<b>{point.x}</b><br/>',
			pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
		},
		credits:{
			enabled:false
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				dataLabels: {
					enabled: true
				},
				animation: false
			}
		},
		series: [{
			name: 'Listed',
			data: datas[0]
		}, {
			name: 'InProgress',
			data: datas[1]
		}, {
			name: 'Pending',
			data: datas[2]
		}, {
			name: 'Finished',
			data: datas[3]
		}]
	});
}

function insert() {
	$("#tanggal").val();
	$("#bagian").val();
	$("#prioritas").val();
	$("#jenis_pekerjaan").val();
	$("#kondisi_mesin").val();
	$("#bahaya").val();
	$("#detail").val();
	$("#target").val();
	$("#safety").val();
}

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

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#ddd', '#1b7bc4', '#f45b5b', '#90ee7e', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
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