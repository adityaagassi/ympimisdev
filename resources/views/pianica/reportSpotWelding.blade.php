@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
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
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<!-- <form method="GET" action="{{ action('InjectionsController@getDailyStock') }}"> -->
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
						</div>
					</div>

					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
						</div>
					</div>
					
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="button" onclick="fillTable()">Update Chart</button>
						</div>
					</div>
				<!-- </form> -->
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container" style="height: 690px;"></div>
			</div>
		</div>
	</div>

 <div class="modal fade" id="modalProgress">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalProgressTitle"style="color: black"></h4>
         <h4 class="modal-title" id="modalProgressTitle2" style="color: black"></h4>
         <h4 class="modal-title" id="modalProgressTitle3"style="color: black"></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <center>
            <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
          </center>
          <table class="table table-hover table-bordered table-striped" id="tableModal">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>NG Name</th>
                <th>Total</th>              
              </tr>
            </thead>
            <tbody id="modalProgressBody">
            </tbody>
            <tfoot style="background-color: RGB(252, 248, 227);">
              <th style="color: black;font-size:12pt">Total</th>
              <th id="totalP" style="color: black;font-size:12pt"></th>
                           
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@stop

@section('scripts')
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
		$('.select2').select2();
		fillTable();
		// setInterval(fillTable, 30000);
	});

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

	

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {
		var tgl1 = $('#tanggal').val();
		var tgl2 = $('#tanggal2').val();
		var data = {
			tgl:tgl1,
			from:tgl2
		}

		$.get('{{ url("fetch/reportSpotWeldingData") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var tgl_all = [];
					var mesin1 = [];
					var mesin2 = [];
					var mesin3 = [];
					var mesin4 = [];
					var mesin5 = [];
					var mesin6 = [];

				

					for(i = 0; i < result.tgl.length; i++){
						tgl_all.push(result.tgl[i].date_a);		
						
					}


					for(i = 0; i < result.ng.length; i++){
						if ( result.ng[i].ng == "H1") {
						mesin1.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "H2") {
						mesin2.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "H3") {
						mesin3.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M1") {
						mesin4.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M2") {
						mesin5.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M3") {
						mesin6.push(parseInt(result.ng[i].ng_all));
						}
						
					}

					
					Highcharts.chart('container', {
				    chart: {
				        type: 'spline'
				    },
				    title: {
				        text: 'Daily Total NG Spot Welding'
				    },
				    subtitle: {
				        text: 'Last Update: '+getActualFullDate(),
				    },
				    xAxis: {
				        categories: tgl_all
				    },
				    yAxis: {
				        title: {
				            text: 'Total'
				        }
				    },
				    plotOptions: {
				        line: {
				            dataLabels: {
				                enabled: true
				            },
				            enableMouseTracking: false
				        }
				    },
				    series: [{

      					animation: false,
				        name: 'Mesin 1',
				        data: mesin1,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }, {

      					animation: false,
				        name: 'Mesin 2',
				        data: mesin2,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }, {

      					animation: false,
				        name: 'Mesin 3',
				        data: mesin3,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }
				    , {

      					animation: false,
				        name: 'Mesin 4',
				        data: mesin4,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }
				    , {

      					animation: false,
				        name: 'Mesin 5',
				        data: mesin5,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }, {

      					animation: false,
				        name: 'Mesin 6',
				        data: mesin6,
				        point: {
			                events: {
			                  click: function () {
			                    fillModal(this.category, this.series.name);
			                  }
			                }
			              }
				    }
				    ]
				});
				}
			}
		});
	}


  function fillModal(tgl, mesin){
    $('#modalProgress').modal('show');
    $('#loading').show();
    $('#modalProgressTitle').hide();
    $('#tableModal').hide();

    var data = {
      tgl:tgl,
      mesin:mesin
    }
    $.get('{{ url("fetch/reportSpotWeldingDataDetail") }}', data, function(result, status, xhr){
      if(result.status){
        $('#modalProgressBody').html('');
        var resultData = '';
        var total = 0;
        
        $.each(result.ng, function(key, value) {         
          resultData += '<tr >';
          resultData += '<td style="width: 40%; color:black; font-size:12pt">'+ value.ng +'</td>';
          resultData += '<td style="width: 40% ;color:black; font-size:12pt">'+ value.total +'</td>';                    
          resultData += '</tr>';   
          total += value.total;       
        });
        
        $('#loading').hide();
        $('#modalProgressBody').append(resultData);
        $('#totalP').text(total);
        $('#modalProgressTitle2').text(mesin);
        $('#modalProgressTitle3').text(tgl);  
        
        // $('#modalProgressTitle').show();
        $('#tableModal').show();
      }
      else{
        alert('Attempt to retrieve data failed');
      }
    });
  }

</script>
@stop