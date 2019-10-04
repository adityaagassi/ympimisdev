@extends('layouts.display')
@section('stylesheets')
@stop
@section('header')
@endsection
<style type="text/css">
	#tabelbody tr {
		color: white;
		font-size: 26px;
	}
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		color: yellow;
		font-size: 28px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0px 2px 0px 2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
</style>
@section('content')
<!-- <a href="{{ url('trialmail') }}" class="btn btn-success">asdasdasd</a> -->
<section class="content" style="padding-top: 0;">
	<!-- <button class="btn btn-danger" onclick="mesin()">Mesin</button> -->
	<?php 
	// $arr = [];
	// foreach ($dd['feed']['entry'] as $excel) {
	// 	$id = explode('/',$excel['id']['$t']);
	// 	$id = str_replace("R","_",end($id));
	// 	$id = str_replace("C","_",$id);
	// 	$ids = explode('_',$id);
	// 	$tmp=array("row"=>$ids[1], "column"=>$ids[2],"content"=>$excel['content']['$t']);
	// 	array_push($arr, $tmp);
	// }

	$arr = [];
	foreach ($dd['feed']['entry'] as $excel) {
		$id = explode('/',$excel['id']['$t']);
		$id = str_replace("R","_",end($id));
		$id = str_replace("C","_",$id);
		$ids = explode('_',$id);
		$tmp=array("row"=>$ids[1], "column"=>$ids[2],"content"=>$excel['content']['$t']);
		array_push($arr, $tmp);
	}
	
	?>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-4">
				<div class="col-xs-12" style="font-size: 22px;">
					<center>
						KARYAWAN TERDAMPAK
					</center>
				</div>
				<div class="col-xs-12" style="padding: 0;">
					<div id="pie_chart1" style="height: 360px;"></div>
				</div>
				<div class="col-xs-12" style="font-size: 22px;">
					<center>
						KELUARGA TERDAMPAK
					</center>
				</div>
				<div class="col-xs-12" style="padding: 0;">
					<div id="pie_chart2" style="height: 360px;"></div>
				</div>		
			</div>
			<div class="col-xs-8" style="padding-left:0;">
				<table class="table table-bordered table-stripped" style="display: none">
					<thead>
						<tr id="head">
						</tr>
					</thead>
					<tbody id="body" style="font-size: 26px">
					</tbody>
				</table>

				<table class="table table-bordered table-stripped">
					<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 20px;">
						<tr>
							<th>Departemen</th>
							<th width="15%">Total</th>
							<th style='background-color: rgb(255,204,255); color: black' width="15%">Terkena Dampak</th>
							<th style='background-color: rgb(204,255,255); color: black' width="15%">Tidak Terkena Dampak</th>
							<th width="15%">Belum Memberi Info</th>
						</tr>
					</thead>
					<tbody id="tabelbody">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>
@stop
@section('scripts')
<script src="{{ url("js/jquery.marquee.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

	jQuery(document).ready(function() {
		drawTable2();
		// setInterval(drawTable2, 30000);
	});

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
					color: 'black'
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

	function drawTable() {
		var arr = <?php echo json_encode($arr); ?>;

		var head = "";
		// var len = 0;
		var row = [];

		$.each(arr, function(index, value) {
			if (value.row == 1) {
				len = value.column;
				
				head += "<th>"+value.content+"</th>";
			}
		})

		$("#head").append(head);

		var num = 0;
		
		$.each(arr, function(index, value) {
			if (value.row != 1) {
				if (typeof arr[index+1] !== 'undefined') {
					if (arr[index].row != arr[index+1].row) {
						row.push({"kolom": num, "isi":value.content, "dd":arr[index].column});
						num++;
					} else {
						if ((parseInt(arr[index-1].column)+1) != arr[index].column) {
							for (var i = parseInt(arr[index-1].column)+1; i < arr[index].column; i++) {
								row.push({"kolom": num, "isi":"kosong", "dd":arr[index].column});
							}
						}

						row.push({"kolom": num, "isi":value.content, "dd":arr[index].column});

					}
				}
			}
		})

		// $("#head").append(head);
		console.log(len);

		console.table(row);
	}


	function drawTable2() {
		var arr = <?php echo json_encode($arr); ?>;
		var head = "";
		// var len = 0;
		var row = [];
		var nik = "";

		$.each(arr, function(index, value) {
			if (value.row == 1) {
				len = value.column;
				
				head += "<th>"+value.content+"</th>";
			}
		})

		//REMOVE DUPLICATE (PROTOTYPE)

		var arr_tmp = [];
		var arr2 = [];

		$.each(arr, function(index, value) {
			if (value.row != 1) {
				if (typeof arr[index+1] !== 'undefined') {
					if (arr[index].row != arr[index+1].row) {
						arr_tmp.push(value.content);
						arr2.push(arr_tmp);
						arr_tmp = [];
					} else {
						arr_tmp.push(value.content);
					}
				} else {
					arr_tmp.push(value.content);
					arr2.push(arr_tmp);
				}
			}
		})

		var temp = [];

		var temp2 = [];

		for (var i = arr2.length - 1; i >= 0; i--) {
			if (temp.includes(arr2[i][1]) == false) {
				temp2.push([arr2[i][1], arr2[i][2]]);
				temp.push(arr2[i][1]);
			}
		}

		$.each(temp2, function(index, value) {
			nik = nik+"'"+temp2[index][0]+"'";
			if (typeof temp2[index+1] !== 'undefined') {
				nik = nik+",";
			}
		})

		var data = {
			nik:nik
		}

		$.get('{{ url("fetch/employee/data") }}', data, function(result, status, xhr){
			var body2 = "";

			$.each(result.emp_bagian, function(index, value) {
				$.each(temp2, function(index2, value2) {
					if (temp2[index2][0] == value.employee_id) {
						temp2[index2].push(value.department);
					}
				})
			})

			$.each(result.emp_datas, function(index, value) {
				body2 += "<tr>";
				body2 += "<td style='text-align: left;'>"+value.department+"</td>";
				body2 += "<td style='text-align: right;'>"+value.total+"</td>";
				var num_tidak = 0;
				var num_ya = 0;

				$.each(temp2, function(index2, value2) {
					if (temp2[index2][2] == value.department && temp2[index2][1] == "Tidak") {
						num_tidak += 1;
					} else if (temp2[index2][2] == value.department && temp2[index2][1] != "Tidak") {
						num_ya += 1;
					}
				})

				body2 += "<td style='background-color:RGB(255,204,255); color: black; text-align: right;'>"+num_ya+"</td>";
				body2 += "<td style='background-color:RGB(204,255,255); color: black; text-align: right;'>"+num_tidak+"</td>";
				body2 += "<td style='text-align: right;'>"+(value.total - (num_tidak+num_ya))+"</td>";

				body2 += "</tr>";
			})

			$("#tabelbody").append(body2);
		})


		$("#head").append(head);

		var num = 1;
		var ya = [0,0];
		var tidak = [0,0];
		// console.log(arr);
		var body = "<tr>";
		var body_arr = [];
		$.each(arr, function(index, value) {
			if (value.row != 1) {
				if (value.column == 3) {
					if (value.content == "Ya (Terkena Dampak)") {
						ya[0] += 1;
					} else if (value.content == "Tidak"){
						tidak[0] += 1;
					}
				}

				if (value.column == 4) {
					if (value.content == "Ya (Terkena Dampak)") {
						ya[1] += 1;
					} else if (value.content == "Tidak"){
						tidak[1] += 1;
					}
				}

				if (typeof arr[index+1] !== 'undefined') {
					if (arr[index].row != arr[index+1].row) {
						num++;
						body += "<td>"+value.content+"</td></tr>";
						body += "<tr>";
					} else {
						body += "<td>"+value.content+"</td>";
					}
				} else {
					body += "<td>"+value.content+"</td></tr>";
				}
			}
		})

		$("#body").append(body);

		var ya1 = ya[0] / num;
		var tidak1 = tidak[0] / num;

		var ya2 = ya[1] / num;
		var tidak2 = tidak[1] / num;

		Highcharts.chart('pie_chart1', {
			chart: {
				backgroundColor: null,
				type: 'pie',
				spacingTop: 0,
				spacingLeft: 0,
				spacingRight: 0,
				spacingBottom: 0
			},
			exporting: { enabled: false },
			title: {
				text: null
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					borderColor: 'black',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}<br/>{point.percentage:.0f}%</b>',
						distance: -50,
						color: 'black',
						style:{
							fontSize:'16px',
							textOutline:0,
						},
					},
				}
			},
			credits: {
				enabled: false
			},
			series: [{
				name: 'Anda Terkena Dampak',
				colorByPoint: true,
				data: [{
					name: 'Yes',
					y: ya1,
					color:"RGB(255,204,255)"
				}, {
					name: 'No',
					y: tidak1,
					color:"RGB(204,255,255)"
				}]
			}]
		});

		Highcharts.chart('pie_chart2', {
			chart: {
				backgroundColor: null,
				type: 'pie',
				spacingTop: 0,
				spacingLeft: 0,
				spacingRight: 0,
				spacingBottom: 0
			},
			exporting: { enabled: false },
			title: {
				text: null
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					borderColor: 'black',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}<br/>{point.percentage:.0f}%</b>',
						distance: -50,
						color: 'black',
						style:{
							fontSize:'16px',
							textOutline:0,
						},
					},
				}
			},
			credits: {
				enabled: false
			},
			series: [{
				name: 'Keluarga Terkena Dampak',
				colorByPoint: true,
				data: [{
					name: 'Yes',
					y: ya2,
					color:"#bd0606"
				}, {
					name: 'No',
					y: tidak2,
					color:"#0da125"
				}]
			}]
		});
	}

</script>
@endsection