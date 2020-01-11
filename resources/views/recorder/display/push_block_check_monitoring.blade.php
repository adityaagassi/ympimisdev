@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
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
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%;height: 520px;"></div>
			</div>
			<!-- <div class="col-xs-12" style="margin-top: 5px;">
				<div id="container2" style="width: 100%;height: 420px;"></div>
			</div> -->
		</div>
	</div>

	
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
                    <th>Check Date</th>
                    <th>Injection Date</th>
                    <th>Product</th>
                    <th>Head</th>    
                    <th>Block</th>
                    <th>Push Pull Check</th>
                    <th>Height Check</th>
                    <th>Jumlah Cek</th>
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

  <div class="modal fade" id="myModal2">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table2"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableResult2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                  	<th>Nomor</th>
                    <th>Check Date</th>
                    <th>Injection Date</th>
                    <th>Head</th>    
                    <th>Block</th>
                    <th>Height</th>
                    <th>Judgement</th>
                    <th>PIC</th>
                  </tr>
                </thead>
                <tbody id="tableBodyResult2">
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
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

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
					var jumlah_cek = [];
					var jumlah_ng_push_pull = [];
					var jumlah_ng_height = [];
					var pic = [];

					for (var i = 0; i < result.datas.length; i++) {
						pic.push(result.datas[i].pic_check);
						jumlah_cek.push(parseInt(result.datas[i].jumlah_cek));
						jumlah_ng_push_pull.push(parseInt(result.datas[i].jumlah_ng_push_pull));
						jumlah_ng_height.push(parseInt(result.datas[i].jumlah_ng_height));
						// series.push([machine[i], jml[i]]);
					}


					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Recorder Push Pull & Height Check Monitoring By PIC - '+remark,
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'on '+result.date,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: pic,
							type: 'category',
							// gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '15px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total Push Block & Height Check',
								style: {
			                        color: '#eee',
			                        fontSize: '20px',
			                        fontWeight: 'bold',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"15px"
								}
					        },
							type: 'linear'
						},
						tooltip: {
							headerFormat: '<span>Total Check</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							align: 'right',
							verticalAlign: 'top',
							x: -90,
							y: 30,
							floating: true,
							borderWidth: 1,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true,
							itemStyle: {
				                fontSize:'12px',
				            },
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                      ShowModal(this.category,result.remark);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1.5vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							type: 'column',
							data: jumlah_cek,
							name: 'Jumlah Cek',
							colorByPoint: false,
							color: "#218380",
							key:'OK'
						},{
							type: 'column',
							data: jumlah_ng_push_pull,
							name: 'Jumlah NG Push Pull Check',
							// stacking:'normal',
							colorByPoint: false,
							color:'#d81159',
							key:'NG'
						},{
							type: 'column',
							data: jumlah_ng_height,
							name: 'Jumlah NG Height Check',
							// stacking:'normal',
							colorByPoint: false,
							color:'#8f2d56',
							key:'NG'
						},
						]
					});
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