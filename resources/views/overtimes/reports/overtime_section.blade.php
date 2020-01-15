@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
     <div class="row">
          <div class="col-xs-12">
               <div class="row">
                    <div class="col-xs-2">
                         <select class="form-control select2" data-placeholder="Pilih Bagian" id="cost_center" style="width: 100% height: 35px; font-size: 15px;" required>
                              <option value=""></option>
                              @foreach($cost_centers as $cost_center)
                              <option value="{{ $cost_center->cost_center }}">{{ $cost_center->cost_center }} - {{ strtoupper($cost_center->cost_center_name) }}</option>
                              @endforeach
                         </select>
                    </div>
                    <div class="col-xs-2">
                         <div class="input-group date">
                              <div class="input-group-addon bg-purple" style="border: none;">
                                   <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control datepicker" id="month_from" placeholder="Bulan Mulai">
                         </div>
                    </div>
                    <div class="col-xs-2">
                         <div class="input-group date">
                              <div class="input-group-addon bg-purple" style="border: none;">
                                   <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control datepicker" id="month_to" placeholder="Bulan Sampai">
                         </div>
                    </div>
                    <button class="btn btn-primary" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
               </div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
               <div id="chartOvertime" style="width: 100%; height: 700px;"></div>
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
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function() {
          $('.datepicker').datepicker({
               <?php $tgl_max = date('m-Y') ?>
               format: "mm-yyyy",
               startView: "months", 
               minViewMode: "months",
               autoclose: true,
               endDate: '<?php echo $tgl_max ?>'

          });
          $('.select2').select2();
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

     function fetchChart(){
          var month_from = $('#month_from').val();
          var month_to = $('#month_to').val();
          var cost_center = $('#cost_center').val();
          var data = {
               month_from:month_from,
               month_to:month_to,
               cost_center:cost_center
          }
          $.get('{{ url("fetch/report/overtime_section") }}', data, function(result, status, xhr){
               if(result.status){
                    var data = result.overtimes;
                    var seriesData = [];
                    var xCategories = [];
                    var i, cat;                    

                    for(i = 0; i < data.length; i++){
                         cat = data[i].period;
                         if(xCategories.indexOf(cat) === -1){
                              xCategories[xCategories.length] = cat;
                         }
                    }
                    for(i = 0; i < data.length; i++){
                         var ot = parseFloat(data[i].ot);
                         var full_name = data[i].employee_id+"-"+data[i].name;
                         if(seriesData){
                              var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == full_name.toUpperCase();});
                              if(currSeries.length === 0){
                                   seriesData[seriesData.length] = currSeries = {name: full_name.toUpperCase(), data: []};
                              } else {
                                   currSeries = currSeries[0];
                              }
                              var index = currSeries.data.length;
                              currSeries.data[index] = parseFloat(ot.toFixed(1));
                         } else {
                              seriesData[0] = {name: full_name.toUpperCase(), data: [parseFloat(ot.toFixed(1))]}
                         }
                    }

                    $('#chartOvertime').highcharts({
                         chart: {
                              type: 'spline'
                         },
                         title: {
                              text: 'Total Overtime Person'
                         },
                         xAxis: {
                              categories: xCategories
                         },
                         yAxis: {
                              title: {
                                   text: 'Total Jam'
                              }
                         },
                         legend: {
                              enabled: false
                         },
                         tooltip: {
                              formatter: function () {
                                   return this.series.name +
                                   ' : ' + this.y + 'hour(s)';
                              }
                         },
                         plotOptions: {
                              line: {
                                   dataLabels: {
                                        enabled: false
                                   },
                                   enableMouseTracking: true
                              },
                              series: {
                                   marker: {
                                        enabled: false
                                   },
                                   lineWidth: 1
                              }
                         },
                         credits:{
                              enabled:false
                         },
                         series: seriesData
                    });
               }
               else{
                    alert(result.message);
               }
          });
     }


</script>
@endsection