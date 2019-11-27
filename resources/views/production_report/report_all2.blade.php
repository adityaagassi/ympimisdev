@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .gambar {
    width: 300px;
    height: 300px;
    background-color: white;
    border-radius: 15px;
    margin-left: 30px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div class="row" style="padding:0">
    <h1 style="color:white">
      <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        
      </div>
      <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        
      </div>
      <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <center>
          <b>Task Leader Monitoring Assembly (WI-A) (職長業務管理)</b>
        </center>
      </div>
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border-color: #00a65a">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker2" id="week_date" onchange="drawChart()" placeholder="Select Month"  style="border-color: #00a65a">
        </div>
      </div>
    </h1>
    <div id="container1" class="gambar" ></div>
    <div id="container2" class="gambar" ></div>
    <div id="container3" class="gambar"></div>
    <div id="container4" class="gambar"></div>
    <div id="container5" class="gambar"></div>
    <div id="container6" class="gambar"></div>
    <div id="container7" class="gambar"></div>
  </div>
  <div class="modal fade" id="myModal" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Leader Task Monitoring</b></h4>
          <h5 class="modal-title" style="text-align: center;" id="judul"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th colspan="8" style="text-align: center;" id="leader_name"></th>
                  </tr>
                  <tr>
                    <th>Activity Name</th>
                    <th>Activity Type</th>
                    <th style="width: 13%">Plan</th>
                    <th style="width: 13%">Actual</th>
                    <th style="width: 13%">Presentase</th>
                    <th style="width: 13%">Details</th>
                    {{-- <th style="width: 13%">OK</th>
                    <th style="width: 13%">NG</th> --}}
                  </tr>
                </thead>
                <tbody id="data-activity">
                  {{-- <tr>
                    <td>Audit IK - QC Koteihyo</td>
                    <td>10</td>
                    <td>7</td>
                    <td>70%</td>
                    <td>7</td>
                    <td>0</td>
                  </tr> --}}
                </tbody>
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
</section>


@endsection

@section('scripts')
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
{{-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> --}}

{{-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script> --}}
{{-- <script src="{{ url("js/buttons.flash.min.js")}}"></script> --}}
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
{{-- <script src="{{ url("js/buttons.html5.min.js")}}"></script> --}}
{{-- <script src="{{ url("js/buttons.print.min.js")}}"></script> --}}

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    drawChart();

    $('.datepicker').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy",
      startView: "years", 
      minViewMode: "years",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });
  });

  $(function () {
    $('.select2').select2()
  });

  function renderLabels() {

  var offsetTop = 5,
    offsetLeft = 5;

  if (!this.series[0].label) {
    this.series[0].label = this.renderer
      .label(this.series[0].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center',
        'borderColor': '#EBBA95'
      })
      .add(this.series[0].group);
  }

  this.series[0].label.translate(
    this.chartWidth / 2 - this.series[0].label.width + offsetLeft,
    this.plotHeight / 2 - this.series[0].points[0].shapeArgs.innerR -
    (this.series[0].points[0].shapeArgs.r - this.series[0].points[0].shapeArgs.innerR) / 2 + offsetTop
  );


  if (!this.series[1].label) {
    if(this.series[1].points[0].y != null){
      this.series[1].label = this.renderer
      .label(this.series[1].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[1].group);
    }
    else{
      this.series[1].label = this.renderer
      .label(' ', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[1].group);
    }
  }

  this.series[1].label.translate(
    this.chartWidth / 2 - this.series[1].label.width + offsetLeft,
    this.plotHeight / 2 - this.series[1].points[0].shapeArgs.innerR -
    (this.series[1].points[0].shapeArgs.r - this.series[1].points[0].shapeArgs.innerR) / 2 + offsetTop
  );

  if (!this.series[2].label) {
    if(this.series[2].points[0].y != null){
      this.series[2].label = this.renderer
      .label(this.series[2].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[2].group);
    }
    else{
      this.series[2].label = this.renderer
      .label(' ', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[2].group);
    }
  }

  this.series[2].label.translate(
    this.chartWidth / 2 - this.series[2].label.width + offsetLeft,
    this.plotHeight / 2 - this.series[2].points[0].shapeArgs.innerR -
    (this.series[2].points[0].shapeArgs.r - this.series[2].points[0].shapeArgs.innerR) / 2 + offsetTop
  );

  if (!this.series[3].label) {
    if(this.series[3].points[0].y != null){
      this.series[3].label = this.renderer
      .label(this.series[3].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[3].group);
    }
    else{
      this.series[3].label = this.renderer
      .label(' ', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center'
      })
      .add(this.series[3].group);
    }
  }

  this.series[3].label.translate(
    this.chartWidth / 2 - this.series[3].label.width + offsetLeft,
    this.plotHeight / 2 - this.series[3].points[0].shapeArgs.innerR -
    (this.series[3].points[0].shapeArgs.r - this.series[3].points[0].shapeArgs.innerR) / 2 + offsetTop
  );
}

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function drawChart(){
    var week_date = $('#week_date').val();
    var data = {
      week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportByLeader/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          // console.table(result.leaderrr);
          for(var i=1; i< result.datas.length;i++){
            for(var j=0; j< result.datas[i].length;j++){
              var result_monthly;
              var result_weekly;
              var result_daily;
              var cur_day = parseInt(result.datas[i][j].persen_cur_day);
              var cur_week = parseInt(result.datas[i][j].persen_cur_week);

              if(parseInt(result.datas[i][j].persen_monthly) == 0){
                result_monthly = null;
                outerRadiusMonthly= '0%';
                innerRadiusMonthly= '0%';
              }
              else{
                result_monthly = parseInt(result.datas[i][j].persen_monthly);
                outerRadiusMonthly= '84%';
                innerRadiusMonthly= '68%';
              }

              if(parseInt(result.datas[i][j].persen_weekly) == 0){
                result_weekly = null;
                outerRadiusWeekly= '0%';
                innerRadiusWeekly= '0%';
              }
              else{
                result_weekly = parseInt(result.datas[i][j].persen_weekly);
                outerRadiusWeekly= '68%';
                innerRadiusWeekly= '52%';
              }

              if(parseInt(result.datas[i][j].persen_daily) == 0){
                result_daily = null;
                outerRadiusDaily= '0%';
                innerRadiusDaily= '0%';
              }
              else{
                result_daily = parseInt(result.datas[i][j].persen_daily);
                outerRadiusDaily= '52%';
                innerRadiusDaily= '36%';
              }

              // if(parseInt(result.datas[i][j].persen_monthly) == 0){
              //   result_monthly = null;
              // }
              // else{
              //   result_monthly = parseInt(result.datas[i][j].persen_monthly);
              // }

              // if(parseInt(result.datas[i][j].persen_weekly) == 0){
              //   result_weekly = null;
              // }
              // else{
              //   result_weekly = parseInt(result.datas[i][j].persen_weekly);
              // }

              // if(parseInt(result.datas[i][j].persen_daily) == 0){
              //   result_daily = null;
              // }
              // else{
              //   result_daily = parseInt(result.datas[i][j].persen_daily);
              // }
              var leader_name = result.datas[i][j].leader_name;
              // console.log(result_monthly);

              Highcharts.chart('container'+i, {
                chart: {
                  type: 'solidgauge',
                  height: '105%',
                  events: {
                    render: renderLabels
                  }
                },
                title: {
                  text: leader_name,
                  style: {
                    fontSize: '14px'
                  }
                },
                tooltip: {
                  enabled:false
                  // borderWidth: 0,
                  // backgroundColor: 'none',
                  // shadow: false,
                  // style: {
                  //   fontSize: '6px'
                  // },
                  // valueSuffix: '%',
                  // pointFormat: '{series.name}<br><span style="font-size:2em; color: {point.color}; font-weight: bold">{point.y}</span>',
                  // positioner: function (labelWidth) {
                  //   return {
                  //     x: (this.chart.chartWidth - labelWidth)/2,
                  //     y: (this.chart.plotHeight / 2) + 25
                  //   };
                  // }
                },

                pane: {
                  startAngle: 0,
                  endAngle: 360,
                  center: ['50%', '50%'],
                  size: '100%',
                      background: [{ // Track for Move
                        outerRadius: outerRadiusMonthly,
                        innerRadius: innerRadiusMonthly,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[1])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }, { // Track for Exercise
                        outerRadius: outerRadiusWeekly,
                        innerRadius: innerRadiusWeekly,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }, { // Track for Stand
                        outerRadius: outerRadiusDaily,
                        innerRadius: innerRadiusDaily,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[3])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }]
                      // background: [{ // Track for Move
                      //   outerRadius: '100%',
                      //   innerRadius: '80%',
                      //   backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[0])
                      //   .setOpacity(0.3)
                      //   .get(),
                      //   borderWidth: 0
                      // },{ // Track for Move
                      //   outerRadius: '80%',
                      //   innerRadius: '60%',
                      //   backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[1])
                      //   .setOpacity(0.3)
                      //   .get(),
                      //   borderWidth: 0
                      // }, { // Track for Exercise
                      //   outerRadius: '60%',
                      //   innerRadius: '40%',
                      //   backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2])
                      //   .setOpacity(0.3)
                      //   .get(),
                      //   borderWidth: 0
                      // }, { // Track for Stand
                      //   outerRadius: '40%',
                      //   innerRadius: '20%',
                      //   backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[3])
                      //   .setOpacity(0.3)
                      //   .get(),
                      //   borderWidth: 0
                      // }]
                    },
                    yAxis: {
                      min: 0,
                      max: 100,
                      lineWidth: 0,
                      tickPositions: []
                    },
                    legend: {
                      // borderColor: 'white',
                      // borderWidth: 1,

                      itemStyle:{
                        color: "white",
                        fontSize: "12px",
                        fontWeight: "bold",
                      },
                      itemHover: {
                        enabled : false
                      },
                      itemHiddenStyle: {
                        color: '#606063'
                      },
                      // shadow: false,
                      labelFormatter: function(e) {
                        return '<span style="text-weight:bold;color:' + this.userOptions.color + ';">' + this.name + '</span>';
                      },
                      symbolWidth: 0,
                      squareSymbol: false
                    },
                    plotOptions: {
                      solidgauge: {
                        dataLabels: {
                          enabled: false
                        },
                        linecap: 'round',
                        stickyTracking: false,
                        rounded: true
                      },
                      series:{
                        cursor: 'pointer',
                        point: {
                            events: {
                              click: function(e) {
                                    ShowModalChart(this.options.key,e.point.series.name);
                              }
                            },
                        },
                        // dataLabels:{
                        //   // color: 'white',
                        //   enabled: true,
                        //   format : '{point.y}'
                        // }
                      }
                    },
                    credits: {
                      enabled: false
                    },
                    series: [
                    {
                      name: 'Prev Month',
                      color: Highcharts.getOptions().colors[0],
                      data: [{
                        color: Highcharts.getOptions().colors[0],
                        outerRadius: '100%',
                        innerRadius: '84%',
                        y: 50,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true,
                    }, {
                      name: 'Monthly',
                      color: Highcharts.getOptions().colors[1],
                      data: [{
                        color: Highcharts.getOptions().colors[1],
                        radius: '84%',
                        innerRadius: '68%',
                        y: result_monthly,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    }, {
                      name: 'Weekly',
                      color: Highcharts.getOptions().colors[2],
                      data: [{
                        color: Highcharts.getOptions().colors[2],
                        radius: '68%',
                        innerRadius: '52%',
                        y: result_weekly,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    }, 
                    {
                      name: 'Daily',
                      color: Highcharts.getOptions().colors[3],
                      data: [{
                        color: Highcharts.getOptions().colors[3],
                        radius: '52%',
                        innerRadius: '36%',
                        y: result_daily,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    },
                    {
                      name: 'Current Day',
                      color: '#ff70f8',
                      data: [{
                        color: '#ff70f8',
                        radius: '36%',
                        innerRadius: '20%',
                        y: cur_day,
                        key: result.datas[i][j].leader_name
                      }],
                      stacking: 'normal',
                      showInLegend: true
                    }]
                  });
              }
          }
      } else{
        alert('Attempt to retrieve data failed');
      }
    }
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
              [0, '#2a2a2b']
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
            // itemStyle: {
            //   color: '#E0E0E3'
            // },
            // itemHoverStyle: {
            //   color: '#FFF'
            // },
            // itemHiddenStyle: {
            //   color: '#606063'
            // }
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
        }

        function ShowModalChart(leader_name,frequency) {
          $('#myModal').modal('show');
          var week_date = $('#week_date').val();
          var data = {
            leader_name:leader_name,
            frequency:frequency,
            week_date:week_date
          }
          $('#data-activity').append().empty();
          $('#judul').append().empty();
          $('#leader_name').append().empty();
          $.get('{{ url("index/production_report/fetchDetailReport/".$id) }}', data, function(result, status, xhr) {
            if(result.status){
              console.log(result.detail);

              $('#judul').append('<b>'+frequency+' Report of '+leader_name+' on '+result.monthTitle+'</b>');
              $('#leader_name').append('<b>'+leader_name+'</b>');

              //Middle log
              var total_plan = 0;
              var total_aktual = 0;
              var presentase = 0;
              var body = '';
              var url = '{{ url("") }}';
              for (var i = 0; i < result.detail.length; i++) {
                body += '<tr>';
                body += '<td>'+result.detail[i].activity_name+'</td>';
                body += '<td>'+result.detail[i].activity_type+'</td>';
                body += '<td>'+result.detail[i].plan+'</td>';
                body += '<td>'+result.detail[i].jumlah_aktual+'</td>';
                body += '<td>'+parseInt(result.detail[i].persen)+' %</td>';
                body += '<td><a target="_blank" class="btn btn-primary btn-sm" href="'+url+'/'+result.detail[i].link+'">Details</a></td>';
                // body += '<td>'+result.good[i].quantity+'</td>';
                body += '</tr>';

                total_plan += parseInt(result.detail[i].plan);
                total_aktual += parseInt(result.detail[i].jumlah_aktual);
              }
              presentase = (total_aktual / total_plan)*100;
              body += '<tr>';
              body += '<td colspan="2"><b>Total</b></td>';
              body += '<td><b>'+total_plan+'</b></td>';
              body += '<td><b>'+total_aktual+'</b></td>';
              body += '<td><b>'+parseInt(presentase)+'%</b></td>';
              body += '<td></td>';
              body += '</tr>';
              $('#data-activity').append(body);

            }

          });
        }

</script>