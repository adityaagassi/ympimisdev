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
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" id='color' data-placeholder="Select Color" style="width: 100%;">
						<option value=""></option>
						<option value="All">All</option>
						@foreach($color as $color)
						<option value="{{$color->color}}">{{$color->color}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
			</div>
			<!-- <div class="col-xs-12" style="margin-top: 5px;background-color: #313132;">
				<center><span style="font-size: 30px;font-weight: bold;"></span></center>
			</div> -->
			<div class="col-xs-8" style="margin-top: 5px;">
				<!-- <div class="row"> -->
					<div id="container1" style="width: 100%;height: 600px;"></div>
				<!-- </div> -->
			</div>
			<div class="col-xs-4" style="margin-top: 5px;padding-left: 0px">
				<!-- <div class="row"> -->
					<div id="container2" style="width: 100%;height: 600px;"></div>
				<!-- </div> -->
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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
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

	function fillChart() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		var data = {
			color:$('#color').val()
		}
		$.get('{{ url("fetch/injection/stock_monitoring") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Chart Skeleton
					var part_skeleton = [];
					var jml_skeleton = [];
					var jml_assy_skeleton = [];
					var series_skeleton = [];
					var series2_skeleton = [];
					var series3_skeleton = [];
					var colors_skeleton = [];
					var colors2_skeleton = [];
					var plan_skeleton = [];

					for (var i = 0; i < result.datas_skeleton.length; i++) {
						part_skeleton.push(result.datas_skeleton[i].part);
						jml_skeleton.push(parseInt(result.datas_skeleton[i].stock));
						jml_assy_skeleton.push(parseInt(result.datas_skeleton[i].stock_assy));
						var color1_skeleton = result.datas_skeleton[i].part.split(' ');
						var color2_skeleton = color1_skeleton.slice(-1);
						if (result.datas_skeleton[i].color == 'BLUE)') {
							colors_skeleton.push('#4287f5');
							colors2_skeleton.push('#a6c8ff');
						}else if(result.datas_skeleton[i].color == 'PINK)'){
							colors_skeleton.push('#f542dd');
							colors2_skeleton.push('#ffa3f3');
						}else if(result.datas_skeleton[i].color == 'GREEN)'){
							colors_skeleton.push('#7bff63');
							colors2_skeleton.push('#adff9e');
						}else if(result.datas_skeleton[i].color == 'RED)'){
							colors_skeleton.push('#ff4a4a');
							colors2_skeleton.push('#ff8787');
						}else if(result.datas_skeleton[i].color == 'IVORY)'){
							colors_skeleton.push('#fff5a6');
							colors2_skeleton.push('#fffce6');
						}else if(result.datas_skeleton[i].color == 'BROWN)'){
							colors_skeleton.push('#856111');
							colors2_skeleton.push('#ccae6c');
						}else if(result.datas_skeleton[i].color == 'BEIGE)'){
							colors_skeleton.push('#e0b146');
							colors2_skeleton.push('#e8c066');
						}else{
							colors_skeleton.push('#000');
							colors2_skeleton.push('#000');
						}
						series_skeleton.push({y: jml_skeleton[i],name:part_skeleton[i], color: colors_skeleton[i], key: 'RC11'});
						series3_skeleton.push({y: jml_assy_skeleton[i],name:part_skeleton[i], color: colors2_skeleton[i], key: 'RC91'});
						plan_skeleton.push(result.datas_skeleton[i].plan);
						series2_skeleton.push({y: plan_skeleton[i],name:part_skeleton[i], key: 'Plan'});
					}

					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'STOCK AFTER INJECTION TRANSLUCENT',
							style:{
								fontSize: '20px'
							}
						},
						subtitle: {
							text: '',
						},
						xAxis: {
							categories: part_skeleton,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Stock',
								style: {
			                        color: '#eee',
			                        fontSize: '15px',
			                        fontWeight: 'bold',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"13px"
								}
					        },
							type: 'linear'
						},
					    ],
						tooltip: {
							headerFormat: '<span>Part</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br> on {point.key}',
						},
						legend: {
							enabled:false
							// layout: 'horizontal',
							// align: 'right',
							// verticalAlign: 'top',
							// x: -40,
							// y: 20,
							// floating: true,
							// borderWidth: 1,
							// backgroundColor:
							// Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							// shadow: true,
							// itemStyle: {
				   //              fontSize:'16px',
				   //          },
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '<center>{point.key}<br>{point.y}</center>',
									style:{
										fontSize: '11px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								borderWidth: 1.3,
							},
						},credits: {
							enabled: false
						},
						series: [
						{
							type: 'column',
							data: series3_skeleton,
							name: 'Stock Assy',
							colorByPoint: false,
							stacking:true
						},{
							type: 'column',
							data: series_skeleton,
							name: 'Stock Store',
							colorByPoint: false,
							stacking:true
						},
						{
						    animation: false,
					    	type: 'spline',
					        name: 'Plan',
					        data: series2_skeleton,
					        colorByPoint:false,
					        color:'#ff0066',
					    }
						]
					});

					// Chart Ivory
					var part_ivory = [];
					var jml_ivory = [];
					var jml_assy_ivory = [];
					var series_ivory = [];
					var series2_ivory = [];
					var series3_ivory = [];
					var colors_ivory = [];
					var colors2_ivory = [];
					var plan_ivory = [];

					for (var i = 0; i < result.datas_ivory.length; i++) {
						part_ivory.push(result.datas_ivory[i].part);
						jml_ivory.push(parseInt(result.datas_ivory[i].stock));
						jml_assy_ivory.push(parseInt(result.datas_ivory[i].stock_assy));
						var color1_ivory = result.datas_ivory[i].part.split(' ');
						var color2_ivory = color1_ivory.slice(-1);
						if (result.datas_ivory[i].color == 'BLUE)') {
							colors_ivory.push('#4287f5');
							colors2_ivory.push('#a6c8ff');
						}else if(result.datas_ivory[i].color == 'PINK)'){
							colors_ivory.push('#f542dd');
							colors2_ivory.push('#ffa3f3');
						}else if(result.datas_ivory[i].color == 'GREEN)'){
							colors_ivory.push('#7bff63');
							colors2_ivory.push('#adff9e');
						}else if(result.datas_ivory[i].color == 'RED)'){
							colors_ivory.push('#ff4a4a');
							colors2_ivory.push('#ff8787');
						}else if(result.datas_ivory[i].color == 'IVORY)'){
							colors_ivory.push('#fff5a6');
							colors2_ivory.push('#fffce6');
						}else if(result.datas_ivory[i].color == 'BROWN)'){
							colors_ivory.push('#856111');
							colors2_ivory.push('#ccae6c');
						}else if(result.datas_ivory[i].color == 'BEIGE)'){
							colors_ivory.push('#e0b146');
							colors2_ivory.push('#e8c066');
						}else{
							colors_ivory.push('#000');
							colors2_ivory.push('#000');
						}
						series_ivory.push({y: jml_ivory[i],name:part_ivory[i], color: colors_ivory[i], key: 'RC11'});
						series3_ivory.push({y: jml_assy_ivory[i],name:part_ivory[i], color: colors2_ivory[i], key: 'RC91'});
						plan_ivory.push(result.datas_ivory[i].plan);
						series2_ivory.push({y: plan_ivory[i],name:part_ivory[i], key: 'Plan'});
					}

					Highcharts.chart('container2', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'STOCK AFTER INJECTION IVORY',
							style:{
								fontSize: '20px'
							}
						},
						subtitle: {
							text: '',
						},
						xAxis: {
							categories: part_ivory,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Stock',
								style: {
			                        color: '#eee',
			                        fontSize: '15px',
			                        fontWeight: 'bold',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"13px"
								}
					        },
							type: 'linear'
						},
					    ],
						tooltip: {
							headerFormat: '<span>Part</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br> on {point.key}',
						},
						legend: {
							enabled:false
							// layout: 'horizontal',
							// align: 'right',
							// verticalAlign: 'top',
							// x: -40,
							// y: 20,
							// floating: true,
							// borderWidth: 1,
							// backgroundColor:
							// Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							// shadow: true,
							// itemStyle: {
				   //              fontSize:'16px',
				   //          },
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '<center>{point.key}<br>{point.y}</center>',
									style:{
										fontSize: '11px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								borderWidth: 1.3,
							},
						},credits: {
							enabled: false
						},
						series: [
						{
							type: 'column',
							data: series3_ivory,
							name: 'Stock Assy',
							colorByPoint: false,
							stacking:true
						},{
							type: 'column',
							data: series_ivory,
							name: 'Stock Store',
							colorByPoint: false,
							stacking:true
						},
						{
						    animation: false,
					    	type: 'spline',
					        name: 'Plan',
					        data: series2_ivory,
					        colorByPoint:false,
					        color:'#ff0066',
					    }
						]
					});
				}
			}
		});

	}

	function ShowModal(mesin,tanggal) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/press/detail_press") }}",
          "data" : {
            mesin : mesin,
            tanggal : tanggal
          }
        },
      "columns": [
          { "data": "date" },
          { "data": "name" },
          { "data": "machine" },
          { "data": "material_number" },
          { "data": "data_ok" }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                ''+pageTotal +' ('+ total +' total)'
            );
        }     
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center>Perolehan di <b>'+mesin+' Pada '+tanggal+'</center></b>');
    
  }

  function ShowModalpic(pic,tanggal) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/press/detail_pic") }}",
          "data" : {
            pic : pic,
            tanggal : tanggal
          }
        },
      "columns": [
          { "data": "date" },
          { "data": "name" },
          { "data": "machine" },
          { "data": "material_number" },
          { "data": "data_ok" }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                ''+pageTotal +' ('+ total +' total)'
            );
        }   
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center>Perolehan <b>'+pic+' Pada '+tanggal+'</center></b>');
    
  }


</script>
@endsection