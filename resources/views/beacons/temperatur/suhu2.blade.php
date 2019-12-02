@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">


</style>


@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
       <div id="chart" style="height:700px" ></div>
     </div>

   </div>
 </div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
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

		drawChart();
    setInterval(drawChart, 10000);
  });

// $.ajax({
//   type: "GET",
//   dataType: "json",
//   url: "172.17.128.253",
//   data: {


//     data},
//   success: success
// });

// $.ajax({
//        type: 'GET', // GET
//        dataType: 'json',
//           url: 'humidity.py',
//           success: function(data){
//                console.log(data);
//                obj = JSON.parse(data);



//                },

//             }).done(function(){

//                 console.log(obj)
//             })

function drawChart() {
    var week_date = $('#week_date').val();
    
    var data = {
      week_date: week_date
    };
    $.get('{{ url("index/log_suhu_2") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var month = result.monthTitle;
          console.log(result.datas);
          
          var week_date = [];
          var suhu_akhir = [];

          $.each(result.datas, function(key, value) {
            week_date.push(value.time);
            suhu_akhir.push(parseFloat(value.temperature));
          })

          // $('#chart').highcharts({
          //   title: {
          //     text: 'Suhu of '+month
          //   },
          //   xAxis: {
          //     type: 'category',
          //     categories: week_date
          //   },
          //   yAxis: [{
          //     // type: 'linear',
          //     title: {
          //       text: 'Suhu Celcius'
          //     },
          //     stackLabels: {
          //         enabled: true,
          //         style: {
          //             fontWeight: 'bold',
          //             color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
          //         }
          //     }
          //   },
          //   { // Secondary yAxis
          //       title: {
          //           text: 'Report',
          //           style: {
          //               color: Highcharts.getOptions().colors[0]
          //           }
          //       },
          //       labels: {
          //           format: '{value}',
          //           style: {
          //               color: Highcharts.getOptions().colors[0]
          //           }
          //       },
          //       opposite: true
          //     }
          //   ],
          //   legend: {
          //     align: 'right',
          //     x: -30,
          //     verticalAlign: 'top',
          //     y: 25,
          //     floating: true,
          //     backgroundColor:
          //         Highcharts.defaultOptions.legend.backgroundColor || 'white',
          //     borderColor: '#CCC',
          //     borderWidth: 1,
          //     shadow: false
          //   },
          //   plotOptions: {
          //     series: {
          //       cursor: 'pointer',
          //       point: {
          //         events: {
          //           click: function () {
          //             ShowModal(this.category);
          //           }
          //         }
          //       },
          //       borderWidth: 0,
          //       dataLabels: {
          //         enabled: true,
          //         format: '{point.y}'
          //       }
          //     }
          //   },
          //   credits: {
          //     enabled: false
          //   },

          //   tooltip: {
          //     formatter:function(){
          //       return this.series.name+' di waktu '+this.key + ' : ' + '<b>'+this.y+'</b>';
          //     }
          //   },
          //   series: [
          //   {
          //     type: 'line',
          //     name: 'Suhu',
          //     color: '#a9ff97',
          //     data: suhu_akhir
          //   }
          //   ]
          // })

// Data retrieved from http://vikjavev.no/ver/index.php?spenn=2d&sluttid=16.06.2015.

Highcharts.chart('chart', {
    chart: {
        type: 'spline',
        scrollablePlotArea: {
            minWidth: 600,
            scrollPositionX: 1
        }
    },
    title: {
        text: 'Monitoring Sensor Suhu Office (事務所温度センサーの監視)',
        align: 'center'
    },
    subtitle: {
        text: '',
        align: 'left'
    },
    xAxis: {
        type: 'category',
       categories: week_date
    },
    yAxis: {
        title: {
            text: ' °Celcius'
        },
        tickInterval: 1,  
        minorGridLineWidth: 0,
        gridLineWidth: 0,
        alternateGridColor: null,
        plotBands: [{ // Light air
            from: 0.3,
            to: 1.5,
            color: 'rgba(68, 170, 213, 0.1)',
            label: {
                text: 'Light air',
                style: {
                    color: '#606060'
                }
            }
        }, { // Light breeze
            from: 1.5,
            to: 3.3,
            color: 'rgba(0, 0, 0, 0)',
            label: {
                text: 'Light breeze',
                style: {
                    color: '#606060'
                }
            }
        }, { // Gentle breeze
            from: 3.3,
            to: 5.5,
            color: 'rgba(68, 170, 213, 0.1)',
            label: {
                text: 'Gentle breeze',
                style: {
                    color: '#606060'
                }
            }
        }, { // Moderate breeze
            from: 5.5,
            to: 8,
            color: 'rgba(0, 0, 0, 0)',
            label: {
                text: 'Moderate breeze',
                style: {
                    color: '#606060'
                }
            }
        }, { // Fresh breeze
            from: 8,
            to: 11,
            color: 'rgba(68, 170, 213, 0.1)',
            label: {
                text: 'Fresh breeze',
                style: {
                    color: '#606060'
                }
            }
        }, { // Strong breeze
            from: 11,
            to: 14,
            color: 'rgba(0, 0, 0, 0)',
            label: {
                text: 'Strong breeze',
                style: {
                    color: '#606060'
                }
            }
        }, { // High wind
            from: 14,
            to: 15,
            color: 'rgba(68, 170, 213, 0.1)',
            label: {
                text: 'High wind',
                style: {
                    color: '#606060'
                }
            }
        }]
    },
    tooltip: {
        valueSuffix: '°C'
    },
    plotOptions: {
        spline: {
            lineWidth: 4,
            states: {
                hover: {
                    lineWidth: 5
                }
            },
            marker: {
                enabled: false
            },
        }
    },
    series: [{
        name: 'Suhu',
        marker: {
            symbol: 'diamond'
        },
        data: suhu_akhir

    }],
    navigation: {
        menuItemStyle: {
            fontSize: '10px'
        }
    }
});




          //--------------
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }
</script>

<!-- <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
setInterval(function(){
$("#screen").load('.php')
}, 2000);
});
</script>

 -->



@endsection

