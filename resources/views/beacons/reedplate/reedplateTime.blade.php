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
   overflow:hidden;
   padding: 3px;
 }
 tbody>tr>td{
   text-align:center;
 }
 tfoot>tr>th{
   text-align:center;
 }
 th:hover {
   overflow: visible;
 }
 td:hover {
   overflow: visible;
 }
 table.table-bordered{
   border:1px solid black;
 }
 table.table-bordered > thead > tr > th{
   border:1px solid black;
 }
 table.table-bordered > tbody > tr > td{
   border:1px solid black;
   vertical-align: middle;
   padding:0;
 }
 table.table-bordered > tfoot > tr > th{
   border:1px solid black;
   padding:0;
 }
 td{
   overflow:hidden;
   text-overflow: ellipsis;
 }
 .dataTable > thead > tr > th[class*="sort"]:after{
   content: "" !important;
 }
 #queueTable.dataTable {
   margin-top: 0px!important;
 }
 #loading, #error { display: none; }

 #parent { 
   position: relative; 
   /*width: 720px; 
   height:500px;*/
   margin-right: auto;
   margin-left: auto; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k1 { 
   position: absolute; 
   right: 142px; 
   top: 305px; 
   width: 60px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k2 { 
   position: absolute; 
   right: 142px; 
   top: 320px; 
   width: 60px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k3 { 
   position: absolute; 
   right: 52px; 
   top: 320px; 
   width: 60px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k4 { 
   position: absolute; 
   right: 52px; 
   top: 375px; 
   width: 60px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k5 { 
   position: absolute; 
   right: 142px; 
   top: 145px; 
   width: 60px;
   height: 60px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k6 { 
   position: absolute; 
   right: 142px; 
   top: 80px; 
   width: 60px;
   height: 60px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding_k7 { 
   position: absolute; 
   right: 49px; 
   top: 51px; 
   width: 60px;
   height: 60px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #benkuri_1 { 
   position: absolute; 
   right: 247px; 
   top: 133px; 
   width: 80px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #benkuri_2 { 
   position: absolute; 
   right: 340px; 
   top: 133px; 
   width: 50px;
   height: 50px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #bennuki_1 { 
   position: absolute; 
   right: 265px; 
   top: 337px; 
   width: 45px;
   height: 45px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }
 #bennuki_2{ 
   position: absolute; 
   right: 265px; 
   top: 381px; 
   width: 45px;
   height: 45px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }
 #bennuki_3 { 
   position: absolute; 
   right: 342px; 
   top: 337px; 
   width: 45px;
   height: 45px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }
 #bennuki_4{ 
   position: absolute; 
   right: 342px; 
   top: 381px; 
   width: 45px;
   height: 45px; 
   border: solid 1px red; 
   font-size: 24px; 
   text-align: center; 
 }

 /*#bennuki_5 { 
   position: absolute; 
   right: 305px; 
   top: 405px; 
   width: 45px;
   height: 45px; 
   border: solid 0px green; 
   font-size: 24px; 
   text-align: center; 
   }*/

   #pressReedplate_1 { 
     position: absolute; 
     right: 428px; 
     top: 353px; 
     width: 45px;
     height: 45px; 
     border: solid 1px red; 
     font-size: 24px; 
     text-align: center; 
   }
   #pressReedplate_2 { 
     position: absolute; 
     right: 428px; 
     top: 405px; 
     width: 45px;
     height: 45px; 
     border: solid 1px red; 
     font-size: 24px; 
     text-align: center; 
   }
   #pressReedplate_3 { 
     position: absolute; 
     right: 555px; 
     top: 353px; 
     width: 45px;
     height: 45px; 
     border: solid 1px red; 
     font-size: 24px; 
     text-align: center; 
   }
   #pressReedplate_4 { 
     position: absolute; 
     right: 589px; 
     top: 405px; 
     width: 45px;
     height: 45px; 
     border: solid 1px red; 
     font-size: 24px; 
     text-align: center; 
   }

   #spotWelding_k1 > div,
   #spotWelding_k2 > div, 
   #spotWelding_k3 > div,
   #spotWelding_k4 > div,
   #spotWelding_k5 > div,
   #spotWelding_k6 > div,
   #spotWelding_k7 > div,
   #benkuri_1 > div,
   #benkuri_2 > div,
   #bennuki_1 > div,
   #bennuki_2 > div,
   #bennuki_3 > div,
   #bennuki_4 > div,
   /*#bennuki_5 > div,*/
   #pressReedplate_1 > div,
   #pressReedplate_2 > div,
   #pressReedplate_3 > div,
   #pressReedplate_4 > div {
    border-radius: 20%;
  }

  .square {
    opacity: 0.8;
  }

  .squarex {
    border-radius: 4px;
    overflow: auto;
    border: 1px solid 
    white;
    font-size: 0.75em;
    width: 25px;
    letter-spacing: 1.1px;
}


</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top:0px">
 <div class="row">
  <div class="col-xs-12" style="margin-top: 0px;">
    <div class="col-xs-2" style="padding: 0">
      <div class="input-group date">
        <div class="input-group-addon bg-green" style="border: none;">
          <i class="fa fa-calendar"></i>
        </div>

        <input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
      </div>
    </div>
    <div class="col-xs-1">
      <button class="btn btn-success" onclick="drawChart()">Update Chart</button>
    </div>
    <div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;color: white"></div>
    <br><br>

    <div class="col-xs-12" style="padding: 0">
    <!-- <h2 style="margin:0;margin-bottom: 20px">
      <center><a style="font-size: 30px; font-weight: bold;" class="text-Lime"> Working Time / Day</a><a style="font-size: 20px; font-weight: bold;" class="text-yellow"> (日次作業時間)</a></center>
    </h2> -->
      <div id="container" style="width: 100%"></div><br>
      <div id="containermesin" style="width: 100%;"></div>
    </div>
  </div>
</div>
    </section>
    @endsection
    @section('scripts')
    <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
    <script src="{{ url("js/highcharts.js")}}"></script>
    <script src="{{ url("js/exporting.js")}}"></script>
    <script src="{{ url("js/export-data.js")}}"></script>
    <script>

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

      jQuery(document).ready(function() {
        drawChart();
        setInterval(drawChart, 10000);
      })

      $('.datepicker').datepicker({
        <?php $tgl_max = date('d-m-Y') ?>
        autoclose : true,
        format : "dd-mm-yyyy",
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

  function drawChart() {
    var week_date = $('#week_date').val();
    var tanggal = $('#tanggal').val();

    $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

    var data = {
      week_date: week_date,
      tanggal: tanggal
    };
    $.get('{{ url("fetch/reedplate/log") }}', data, function(result, status, xhr) {
      if (xhr.status == 200) {
        if (result.status) {
          var month = result.monthTitle;
          var name = [];
          var jam_kerja = [];
          var all_datas = [];
          var z = 0;
          var d = [];

        for (var i = 0; i < result.data.length; i++) {
                  // if (result.data[i].jam_kerja <= 353) {
                  //   var color = '#e91e63';             
                  // }else{
                  //   var color = '#000';
                  // }


                  if (typeof result.data[i + 1] == 'undefined') {
                    d.push(parseFloat(result.data[i].jam_kerja));
                    all_datas.push({
                      name: result.data[i].lokasi + '   ' + '(' + 'Reader : ' + result.data[i].reader + ')',
                      data: d,
                            // color: color
                          });
                  } else {
                    if (result.data[i].lokasi != result.data[i + 1].lokasi) {
                      d.push(parseFloat(result.data[i].jam_kerja));
                      
                      all_datas.push({
                        name: result.data[i].lokasi + '   ' + '(' + 'Reader : ' + result.data[i].reader + ')',
                        data: d,
                          // color: color
                        });
                      d = [];
                    } else {
                      d.push(parseFloat(result.data[i].jam_kerja));
                    }
                  }
                  if (jQuery.inArray(result.data[i].name, name) != -1) {

                  } else {
                    name.push(result.data[i].name + '   ' + '(' + result.data[i].kode + ')');
                  }
                }

                // console.log(all_datas);
                // console.table(all_datas);

                Highcharts.chart('container', {
                  chart: {
                    type: 'column',
                    animation: false,
                    events: {
                      load: function() {
                        var check = $('#container').highcharts();
                        var min = check.yAxis[0].min;
                        var max = check.yAxis[0].max;
                        var pLine = check.yAxis[0].chart.options.yAxis[0].plotLines[0].value;
                        if (pLine > max) {
                          check.yAxis[0].setExtremes(null, pLine);
                        }
                        if (pLine < min) {
                          check.yAxis[0].setExtremes(pLine, null);
                        }
                      }
                    }
                  },
                  title: {
                    text: 'Working Time Operator / Day',
                    style: {
                      fontSize: '25px',
                      fontWeight: 'bold',
                      color: '#f39c12'
                    }
                    
                  },
                  subtitle: {
                    text: 'on '+result.date,
                    style: {
                      fontSize: '1.2vw',
                      fontWeight: 'bold'
                    }
                  },
                  xAxis: {
                    type: 'category',
                    categories: name,
                  },
                  yAxis: {
                    lineWidth:2,
                    lineColor:'#9e9e9e',
                    type: 'linear',
                    min: 0,
                    title: {
                     text: 'Working Time (Minute)',
                     style: {
                      fontSize: '18px',
                      fontWeight: 'bold',
                      fill: '#6d869f'
                    }
                  },

                        // tickInterval: 1,
                        stackLabels: {
                          enabled: true,
                          style: {
                            fontWeight: 'bold',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                          }
                        },
                        plotLines: [{
                          color: '#FF0000',
                          value: 460,
                          dashStyle: 'shortdash',
                          width: 2,
                          zIndex: 5,
                          label: {
                            align:'right',
                            text: 'Target Per Hari',
                            x:-7,
                            style: {
                              fontSize: '12px',
                              color: '#FF0000',
                              fontWeight: 'bold'
                            }
                          }
                        }],
                      },
                      credits: {
                        enabled: false
                      },
                      legend: {
                        reversed: true
                      },
                      plotOptions: {
                        series: {
                          stacking: 'normal',
                          cursor: 'pointer',
                          borderWidth: 0,
                          dataLabels: {
                            enabled: false,
                            format: '{point.y}'
                          }
                        },
                        column: {
                          color:  Highcharts.ColorString,
                          borderRadius: 1,
                          dataLabels: {
                            enabled: true
                          }
                        }
                      },
                      series: all_datas
                    });



                var machine = [];
                var all_datas2 = [];
                var e = [];

                for (var i = 0; i < result.data_mesin.length; i++) {
                  e.push(parseFloat(result.data_mesin[i].jam_kerja));
                  machine.push(result.data_mesin[i].lokasi);

                  all_datas2.push({
                    name: result.data_mesin[i].lokasi,
                    data: e
                  });
                }

                Highcharts.chart('containermesin', {
                  chart: {
                    type: 'column',
                    animation: false
                  },
                  title: {
                    text: 'Working Time Machine / Day',
                    style: {
                      fontSize: '25px',
                      fontWeight: 'bold',
                      color: '#3c8dbc'
                    }
                    
                  },
                  subtitle: {
                    text: 'on '+result.date,
                    style: {
                      fontSize: '1.2vw',
                      fontWeight: 'bold'
                    }
                  },
                  xAxis: {
                    type: 'category',
                    categories: machine,
                  },
                  yAxis: {
                    lineWidth:2,
                    lineColor:'#9e9e9e',
                    type: 'linear',
                    min: 0,
                    title: {
                     text: 'Working Time (Minute)',
                     style: {
                      fontSize: '18px',
                      fontWeight: 'bold',
                      fill: '#6d869f'
                    }
                  },

                        // tickInterval: 1,
                        stackLabels: {
                          enabled: true,
                          style: {
                            fontWeight: 'bold',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                          }
                        }
                      },
                      credits: {
                        enabled: false
                      },
                      legend: {
                        // reversed: true
                        enabled:false
                      },
                      plotOptions: {
                        series: {
                            // stacking: 'normal',
                            cursor: 'pointer',
                            borderWidth: 0,
                            dataLabels: {
                              enabled: false,
                              format: '{point.y}'
                            }
                          },
                          column: {
                            color:  Highcharts.ColorString,
                            borderRadius: 1,
                            dataLabels: {
                              enabled: true
                            }
                          }
                        },
                        series: [{
                          name: 'Working Time',
                          data: e,
                          colorByPoint: true
                        }]
                      });
              } else {
                alert('Attempt to retrieve data failed');
              }
            }
          })
}


Highcharts.createElement('link', {
  href: '{{ url("fonts/UnicaOne.css")}}',
  rel: 'stylesheet',
  type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
  colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
  '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'
  ],
  chart: {
    backgroundColor: {
      linearGradient: {
        x1: 0,
        y1: 0,
        x2: 1,
        y2: 1
      },
      stops: [
      [0, '#212121'],
      [1, '#212121']
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