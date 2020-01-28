@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	/*table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-condensed > thead > tr > th{   
  color: black;
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
  wid
  : 100%;
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
table > thead > tr > th {
	border:1px solid white !important;
	text-align: center;
}
table > tbody > tr > td {
	border:1px solid white !important;
	text-align: center;
}
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

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="bulan" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<center>
				<center>
					<span style="font-weight: bold;font-size: 40px">Recorder Process Monitoring</span><br>
					<span style="font-weight: bold;font-size: 20px">Last 7 Days</span>
				</center>
			</center>
			<div class="col-xs-12" style="width: 100%;height: 300px;margin-top: 5px;background-color: none;">
				<table style="height: 100%;width: 100%;padding-top: 10px">
					<thead id="headResult">
					</thead>
					<tbody id="bodyResult">
					</tbody>
				</table>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 10000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

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

	function fillChart() {
		// var proses = $('#process').val();
		var bulan = $('#bulan').val();
		
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var remark = '{{ $remark }}';

		var data = {
			bulan:bulan,
		}

		$.get('{{ url("fetch/recorder/push_block_check_monitoring/".$remark) }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Chart Machine Report
					// var jumlah_cek = [];
					// var jumlah_ng_push_pull = [];
					// var jumlah_ng_height = [];
					// var pic = [];

					// for (var i = 0; i < result.datas.length; i++) {
					// 	pic.push(result.datas[i].pic_check);
					// 	jumlah_cek.push(parseInt(result.datas[i].jumlah_cek));
					// 	jumlah_ng_push_pull.push(parseInt(result.datas[i].jumlah_ng_push_pull));
					// 	jumlah_ng_height.push(parseInt(result.datas[i].jumlah_ng_height));
					// 	// series.push([machine[i], jml[i]]);
					// }
					$('#headResult').html("");
					$('#bodyResult').html("");

					var bodyData = "";
					var headData = "";

					var tgl = [];

					headData += '<tr>';
					headData += '<th style="padding-left: 10px;padding-right: 10px;font-size: 30px">Recorder Process</th>';
					headData += '<th colspan="3" style="padding-left: 10px;padding-right: 10px;font-size: 30px">First Shot Approval</th>';
					headData += '<th colspan="3" style="padding-left: 10px;padding-right: 10px;font-size: 30px">After Injection</th>';
					headData += '<th colspan="2" style="padding-left: 10px;padding-right: 10px;font-size: 30px">Push Pull Assembly</th>';
					headData += '<th colspan="2" style="padding-left: 10px;padding-right: 10px;font-size: 30px">Camera Kango</th>';
					// for(var i = 0; i < result.date7days.length; i++){
					// 	headData += '<th style="padding-left: 10px;padding-right: 10px;font-size: 20px">'+result.date7days[i].week_date+'</th>';
					// 	tgl.push(result.date7days[i].week_date);
					// }
					headData += '</tr>';

					$('#headResult').append(headData);

					$.each(result.datas, function(key, value) {

						var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
						var d = new Date(value.week_date);

						bodyData += '<tr>';
						bodyData += '<td rowspan="2" style="padding-left: 10px;padding-right: 10px;font-size: 30px">'+d.getDate()+' '+months[d.getMonth()]+' '+d.getFullYear()+'</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">Total Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Push Block :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Height Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">Total Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Push Block :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Height Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">Total Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Push Pull :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">Total Check :</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 10px">NG Camera Kango :</td>';

						if (value.countfsappng > 0) {
							var color1 = '#ff6363';
						}else{
							var color1 = 'none';
						}

						if(value.countfsahng > 0){
							var color2 = '#ff6363';
						}else{
							var color2 = 'none';
						}

						if(value.countaippng > 0){
							var color3 = '#ff6363';
						}else{
							var color3 = 'none';
						}

						if(value.countaihng > 0){
							var color4 = '#ff6363';
						}else{
							var color4 = 'none';
						}

						if(value.countppassyng > 0){
							var color5 = '#ff6363';
						}else{
							var color5 = 'none';
						}

						if(value.countckng > 0){
							var color6 = '#ff6363';
						}else{
							var color6 = 'none';
						}
						
						bodyData += '</tr>';
						bodyData += '<tr>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countfsa+'</td>';
						bodyData += '<td style="background-color:'+color1+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countfsappng+'</td>';
						bodyData += '<td style="background-color:'+color2+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countfsahng+'</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countai+'</td>';
						bodyData += '<td style="background-color:'+color3+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countaippng+'</td>';
						bodyData += '<td style="background-color:'+color4+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countaihng+'</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countppassy+'</td>';
						bodyData += '<td style="background-color:'+color5+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countppassyng+'</td>';
						bodyData += '<td style="padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countck+'</td>';
						bodyData += '<td style="background-color:'+color6+';padding-left: 10px;padding-right: 10px;font-size: 30px">'+value.countckng+'</td>';
						bodyData += '</tr>';
					});

					$('#bodyResult').append(bodyData);


					// Highcharts.chart('container1', {
					// 	chart: {
					// 		type: 'column'
					// 	},
					// 	title: {
					// 		text: 'Recorder Push Pull & Height Check Monitoring By PIC - '+remark,
					// 		style: {
					// 			fontSize: '20px',
					// 			fontWeight: 'bold'
					// 		}
					// 	},
					// 	subtitle: {
					// 		text: 'on '+result.date,
					// 		style: {
					// 			fontSize: '1vw',
					// 			fontWeight: 'bold'
					// 		}
					// 	},
					// 	xAxis: {
					// 		categories: pic,
					// 		type: 'category',
					// 		// gridLineWidth: 1,
					// 		gridLineColor: 'RGB(204,255,255)',
					// 		lineWidth:2,
					// 		lineColor:'#9e9e9e',
					// 		labels: {
					// 			style: {
					// 				fontSize: '15px'
					// 			}
					// 		},
					// 	},
					// 	yAxis: {
					// 		title: {
					// 			text: 'Total Push Block & Height Check',
					// 			style: {
			  //                       color: '#eee',
			  //                       fontSize: '20px',
			  //                       fontWeight: 'bold',
			  //                       fill: '#6d869f'
			  //                   }
					// 		},
					// 		labels:{
					//         	style:{
					// 				fontSize:"15px"
					// 			}
					//         },
					// 		type: 'linear'
					// 	},
					// 	tooltip: {
					// 		headerFormat: '<span>Total Check</span><br/>',
					// 		pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					// 	},
					// 	legend: {
					// 		layout: 'horizontal',
					// 		align: 'right',
					// 		verticalAlign: 'top',
					// 		x: -90,
					// 		y: 30,
					// 		floating: true,
					// 		borderWidth: 1,
					// 		backgroundColor:
					// 		Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
					// 		shadow: true,
					// 		itemStyle: {
				 //                fontSize:'12px',
				 //            },
					// 	},	
					// 	plotOptions: {
					// 		series:{
					// 			cursor: 'pointer',
				 //                point: {
				 //                  events: {
				 //                    click: function () {
				 //                      ShowModal(this.category,result.remark);
				 //                    }
				 //                  }
				 //                },
					// 			dataLabels: {
					// 				enabled: true,
					// 				format: '{point.y}',
					// 				style:{
					// 					fontSize: '1.5vw'
					// 				}
					// 			},
					// 			animation: false,
					// 			pointPadding: 0.93,
					// 			groupPadding: 0.93,
					// 			borderWidth: 0.93,
					// 			cursor: 'pointer'
					// 		},
					// 	},credits: {
					// 		enabled: false
					// 	},
					// 	series: [{
					// 		type: 'column',
					// 		data: jumlah_cek,
					// 		name: 'Jumlah Cek',
					// 		colorByPoint: false,
					// 		color: "#218380",
					// 		key:'OK'
					// 	},{
					// 		type: 'column',
					// 		data: jumlah_ng_push_pull,
					// 		name: 'Jumlah NG Push Pull Check',
					// 		// stacking:'normal',
					// 		colorByPoint: false,
					// 		color:'#d81159',
					// 		key:'NG'
					// 	},{
					// 		type: 'column',
					// 		data: jumlah_ng_height,
					// 		name: 'Jumlah NG Height Check',
					// 		// stacking:'normal',
					// 		colorByPoint: false,
					// 		color:'#8f2d56',
					// 		key:'NG'
					// 	},
					// 	]
					// });
				}
			}
		});

		// $.get('{{ url("fetch/recorder/height_check_monitoring/".$remark) }}',data, function(result, status, xhr) {
		// 	if(xhr.status == 200){
		// 		if(result.status){

		// 			//Chart Machine Report
		// 			var jumlah_cek = [];
		// 			var jumlah_ng_height = [];
		// 			var pic = [];

		// 			for (var i = 0; i < result.datas.length; i++) {
		// 				pic.push(result.datas[i].pic_check);
		// 				jumlah_cek.push(parseInt(result.datas[i].jumlah_cek));
		// 				jumlah_ng_height.push(parseInt(result.datas[i].jumlah_ng_height));
		// 				// series.push([machine[i], jml[i]]);
		// 			}


		// 			Highcharts.chart('container2', {
		// 				chart: {
		// 					type: 'column'
		// 				},
		// 				title: {
		// 					text: 'Height Gauge Check Monitoring By PIC - '+remark,
		// 					style: {
		// 						fontSize: '20px',
		// 						fontWeight: 'bold'
		// 					}
		// 				},
		// 				subtitle: {
		// 					text: 'on '+result.date,
		// 					style: {
		// 						fontSize: '1vw',
		// 						fontWeight: 'bold'
		// 					}
		// 				},
		// 				xAxis: {
		// 					categories: pic,
		// 					type: 'category',
		// 					// gridLineWidth: 1,
		// 					gridLineColor: 'RGB(204,255,255)',
		// 					lineWidth:2,
		// 					lineColor:'#9e9e9e',
		// 					labels: {
		// 						style: {
		// 							fontSize: '15px'
		// 						}
		// 					},
		// 				},
		// 				yAxis: {
		// 					title: {
		// 						text: 'Total Height Gauge Block Check',
		// 						style: {
		// 	                        color: '#eee',
		// 	                        fontSize: '20px',
		// 	                        fontWeight: 'bold',
		// 	                        fill: '#6d869f'
		// 	                    }
		// 					},
		// 					labels:{
		// 			        	style:{
		// 							fontSize:"15px"
		// 						}
		// 			        },
		// 					type: 'linear'
		// 				},
		// 				tooltip: {
		// 					headerFormat: '<span>Production Result</span><br/>',
		// 					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
		// 				},
		// 				legend: {
		// 					layout: 'horizontal',
		// 					align: 'right',
		// 					verticalAlign: 'top',
		// 					x: -90,
		// 					y: 30,
		// 					floating: true,
		// 					borderWidth: 1,
		// 					backgroundColor:
		// 					Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
		// 					shadow: true,
		// 					itemStyle: {
		// 		                fontSize:'12px',
		// 		            },
		// 				},	
		// 				plotOptions: {
		// 					series:{
		// 						cursor: 'pointer',
		// 		                point: {
		// 		                  events: {
		// 		                    click: function () {
		// 		                      ShowModal2(this.category,this.series.name,result.remark);
		// 		                    }
		// 		                  }
		// 		                },
		// 						dataLabels: {
		// 							enabled: true,
		// 							format: '{point.y}',
		// 							style:{
		// 								fontSize: '1vw'
		// 							}
		// 						},
		// 						animation: false,
		// 						pointPadding: 0.93,
		// 						groupPadding: 0.93,
		// 						borderWidth: 0.93,
		// 						cursor: 'pointer'
		// 					},
		// 				},credits: {
		// 					enabled: false
		// 				},
		// 				series: [{
		// 					type: 'column',
		// 					data: jumlah_cek,
		// 					name: 'Jumlah OK',
		// 					colorByPoint: false,
		// 					color: "#92cf11",
		// 					key:'OK'
		// 				},{
		// 					type: 'line',
		// 					data: jumlah_ng_height,
		// 					name: 'Jumlah NG',
		// 					stacking: 'normal',
		// 					colorByPoint: false,
		// 					color:'#dc3939',
		// 					key:'NG'
		// 				},
		// 				]
		// 			});
		// 		}
		// 	}
		// });

	}

	function ShowModal(pic,remark) {

	    $("#myModal").modal("show");

	    var tanggal = $('#tanggal').val();

	    var data = {
			tanggal:tanggal,
			pic:pic,
	    	remark:remark
		}
	    $.get('{{ url("index/recorder/detail_monitoring") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableResult').DataTable().clear();
				$('#tableResult').DataTable().destroy();
				$('#tableBodyResult').html("");
				var tableData = "";
				var count = 1;
				var push_ng = [];
				var height_ng = [];
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.check_date +'</td>';
					tableData += '<td>'+ value.injection_date +'</td>';
					tableData += '<td>'+ value.product_type +'</td>';
					tableData += '<td>'+ value.head +'</td>';
					tableData += '<td>'+ value.block +'</td>';
					if(value.push_pull_ng_name != 'OK'){
						push_pull_ng_name = value.push_pull_ng_name.split(',');
						push_pull_ng_value = value.push_pull_ng_value.split(',');
						for (var i=0; i < push_pull_ng_name.length; i++) { 
							push_ng.push("Head-Block = "+push_pull_ng_name[i]+" Memiliki Nilai NG = <label class='label label-danger' readonly>"+push_pull_ng_value[i]+"</label><br>")
						}
						tableData += '<td>'+ push_ng.join("") +'</td>';
					}else{
						tableData += '<td><label class="label label-success">'+ value.push_pull_ng_name +'</label></td>';
					}

					if(value.height_ng_name != 'OK'){
						height_ng_name = value.height_ng_name.split(',');
						height_ng_value = value.height_ng_value.split(',');
						for (var i=0; i < height_ng_name.length; i++) { 
							height_ng.push("Head-Block = "+height_ng_name[i]+" Memiliki Nilai NG = <label class='label label-danger' readonly>"+height_ng_value[i]+"</label><br>")
						}
						tableData += '<td>'+ height_ng.join("") +'</td>';
					}else{
						tableData += '<td><label class="label label-success">'+ value.height_ng_name +'</label></td>';
					}
					tableData += '<td>'+ value.jumlah_cek +'</td>';
					tableData += '<td>'+ value.pic_check +'</td>';
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
	    $('#judul_table').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan oleh <b>'+pic+'</b> (<b>'+remark+'</b>)</center>');
	    
	  }

	  function ShowModal2(tanggal,judgement,remark) {
	    tabel = $('#example2').DataTable();
	    tabel.destroy();

	    $("#myModal2").modal("show");

	    var data = {
			tanggal:tanggal,
	    	judgement:judgement,
	    	remark:remark
		}
		var jdgm = '';
		if (judgement == 'Jumlah OK') {
			jdgm = 'OK';
		}
		else{
			jdgm = 'NG';
		}

	    $.get('{{ url("index/recorder/detail_monitoring2") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableResult2').DataTable().clear();
				$('#tableResult2').DataTable().destroy();
				$('#tableBodyResult2').html("");
				var tableData = "";
				var count = 1;
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.check_date +'</td>';
					tableData += '<td>'+ value.injection_date +'</td>';
					tableData += '<td>'+ value.head +'</td>';
					tableData += '<td>'+ value.block +'</td>';
					tableData += '<td>'+ value.ketinggian +'</td>';
					tableData += '<td>'+ value.judgement2 +'</td>';
					tableData += '<td>'+ value.pic_check +'</td>';
					tableData += '</tr>';
					count += 1;
				});
				$('#tableBodyResult2').append(tableData);
				$('#tableResult2').DataTable({
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

	    $('#judul_table2').append().empty();
	    $('#judul_table2').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan Judgement <b>'+jdgm+'</b> (<b>'+remark+'</b>)</center>');
	    
	  }


</script>
@endsection