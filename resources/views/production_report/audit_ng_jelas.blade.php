@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .gambar {
    width: 400px;
    height: 420px;
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
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      </div>
      <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <center>
          <b style="font-size: 30px">{{$title}} ({{$title_jp}})</b>
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
    <div class="col-xs-12">
      <div class="row" id="containerchart" style="padding-bottom: 20px">
        <!-- <div id="container1" class="gambar"></div> -->
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalDetail" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h3>
          <h5 class="modal-title" style="text-align: center;" id="sub_judul_weekly"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-activity-head-weekly" style="background-color: rgba(126,86,134,.7);">
                </thead>
                <tbody id="data-activity-weekly">
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
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>

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

    var interval;
    var statusx = "idle";

    $(document).on('mousemove keyup keypress',function(){
      clearTimeout(interval);
      settimeout();
      statusx = "active";
    })

    function settimeout(){
      interval=setTimeout(function(){
        statusx = "idle";
        drawChart()
      },30000)
    }
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
  });

  $(function () {
    $('.select2').select2()
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function drawChart(){
    var week_date = $('#week_date').val();
    var data = {
      week_date: week_date
    };
    $.get('{{ url("fetch/audit_ng_jelas_monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var dept = [];
          var dept_id = [];

          $('#containerchart').empty();

          divcontainer = "";

          for(var l=0;l<result.department.length;l++){
            dept.push(result.department[l].department_name);
            dept_id.push(result.department[l].department_id);
            var idcon = l+1;
            var con = 'container'+idcon;
            divcontainer += '<div id="'+con+'" class="gambar"></div>';
          }
          $('#containerchart').append(divcontainer);

          for(var k = 0; k < result.department.length;k++){
            var series = [];
            var background = [];
            var render = [];

            var panjang = result.ng_jelas[k].length;
            var radius = 100;
            var divider = 90/panjang;

            for(var j=0;j<result.ng_jelas[k].length;j++){
              var y = parseInt((parseInt(result.ng_jelas[k][j].week_actual) / parseInt(result.ng_jelas[k][j].week_required)) * 100);

              if (j == 0) {
                rad = ""+radius.toFixed(0)+"%";
                radius = radius-divider;
                inrad = ""+radius.toFixed(0)+"%";
              }else{
                rad = ""+radius.toFixed(0)+"%";
                radius = radius-divider;
                var inrad = ""+radius.toFixed(0)+"%";
              }

              series.push({
                name: result.ng_jelas[k][j].leader_dept,
                color: Highcharts.getOptions().colors[j],
                data: [{
                  color: Highcharts.getOptions().colors[j],
                  radius: rad,
                  innerRadius: inrad,
                  y: y,
                  key:dept_id[k]
                }],
                showInLegend: true
              });

              background.push({
                outerRadius: rad,
                innerRadius: inrad,
                backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[j])
                .setOpacity(0.3)
                .get(),
                borderWidth: 0
              });

              rad = rad;
            }

            var idcon = k+1;

            chart = new Highcharts.Chart({
              chart: {
                type: 'solidgauge',
                height: '105%',
                renderTo: 'container'+idcon
              },
              title: {
                text: '<b>'+dept[k]+'</b>',
                style: {
                  fontSize: '14px'
                }
              },
              tooltip: {
                enabled:false
              },

              pane: {
                startAngle: 0,
                endAngle: 360,
                center: ['50%', '50%'],
                size: '100%',
                background: background
                  },
                  yAxis: {
                    min: 0,
                    max: 100,
                    lineWidth: 0,
                    tickPositions: []
                  },
                  legend: {
                    itemStyle:{
                      color: "white",
                      fontSize: "12px",
                      fontWeight: "bold",
                    },
                    itemHover: {
                      enabled : false
                    },
                    itemHiddenStyle: {
                      color: '#000'
                    },
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
                          showModalDetail(this.options.key,e.point.series.name);
                            }
                          },
                        },
                    }
                  },
                  credits: {
                    enabled: false
                  },
                  series: series
                },
                function callback() {
                  var offsetTop = result.ng_jelas[k].length,
                  offsetLeft = result.ng_jelas[k].length;
                  for(var j=0;j<result.ng_jelas[k].length;j++){
                    if (!this.series[j].label) {
                      if(this.series[j].points[0].y != null){
                        this.series[j].label = this.renderer
                        .label(this.series[j].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
                        .css({
                          'color': '#FFFFFF',
                          'fontWeight':'bold',
                          'textAlign': 'center'
                        })
                        .add(this.series[j].group);
                      }
                      else{
                        this.series[j].label = this.renderer
                        .label(' ', 0, 0, 'rect', 0, 0, true, true)
                        .css({
                          'color': '#FFFFFF',
                          'fontWeight':'bold',
                          'textAlign': 'center'
                        })
                        .add(this.series[j].group);
                      }
                    }

                    this.series[j].label.translate(
                      this.chartWidth / 2 - this.series[j].label.width + offsetLeft,
                      this.plotHeight / 2 - this.series[j].points[0].shapeArgs.innerR -
                      (this.series[j].points[0].shapeArgs.r - this.series[j].points[0].shapeArgs.innerR) / 2 + offsetTop
                      ); 
                  }
                }

                );
          }
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    });
  }

  function showModalDetail(dept_id,leader) {
      var week_date = $('#week_date').val();
      var data = {
        leader_name:leader,
        week_date:week_date,
        dept_id:dept_id,
      }

      $('#data-activity-weekly').append().empty();
      $('#data-activity-head-weekly').append().empty();
      $('#sub_judul_weekly').append().empty();
      $('#judul_weekly').append().empty();

      $('#modalDetail').modal('show');
      $.get('{{ url("fetch/detail_audit_ng_jelas_monitoring") }}', data, function(result, status, xhr) {
        if(result.status){

          // $('#sub_judul_weekly').append('<b>Audit NG Jelas of '+leader_name+' on '+result.monthTitle+'</b>');
          $('#judul_weekly').append('<b>Audit NG Jelas of '+leader+' on '+result.monthTitle+'</b>');

          var total_plan = 0;
          var presentase = 0;
          var body = '';
          var head = '';
          var jj = [];
          var no = 1;
          var aa = 1;
          var bb = 0;
          var url = '{{ url("") }}';
          head += '<tr>';
          head += '<th rowspan="2" style="vertical-align: middle;">No.</th>';
          head += '<th rowspan="2" style="vertical-align: middle;"><center>Activity Name</center></th>';
          for(var a = 0; a < result.date.length; a++){
            head += '<th>'+result.date[a].week_name+'</th>';
            jj.push(result.date[a].week_name);
          }
          head += '</tr>';
          $('#data-activity-head-weekly').append(head);

          var dds = [];
          var dd = "";
          var activity = [];
          var activity_length = 0;
          $.each(result.detail[1], function(index, value){

            for (var i = 0; i < result.detail[1].length; i++) {
              if(i == 0){
                activity.push(result.detail[1][index].activity_name);
                activity_length++;
              }else if(i > 0){
                if(!activity.includes(result.detail[1][index].activity_name)){
                  activity.push(result.detail[1][index].activity_name);
                  activity_length++;
                }
              }
              }
          })
          var nomer = 0;
          var aktual = 0;
          var total_aktual = 0;
          var plan = 1;
          var total_plan =  plan * activity.length;
          for (var i = 0; i < activity.length; i++) {
            dd += "<tr>";
            dd += "<td>"+ (++nomer) +"</td>";
            dd += "<td>"+activity[i]+"</td>";
            
            for (var j = 0; j < result.detail[1].length; j++) {
              if(activity[i] == result.detail[1][j].activity_name){
                for (var k = 1; k < result.detail.length; k++) {
                  for (var l = 0; l < result.detail[k].length; l++) {
                    if(result.detail[k][l].activity_name == activity[i]){
                      if(result.detail[k][l].jumlah_aktual > 0){
                        aktual = aktual + 1;
                        dd += "<td style='background-color: #4aff77'>1</td>";
                      }else{
                        dd += "<td style='background-color: #f7ff59'>0</td>";
                      }
                    }
                  }                      
                }
              }
            }
            dd += "<tr>";
          }
          total_aktual = aktual;
          total_plan_item = total_plan * 4;
          presentase = (total_aktual/total_plan_item)*100;
          dd += '<tr>';
          dd += '<td colspan="2"><b>Total Plan Activity</b></td>';
          dd += '<td colspan="5"><center><b>'+total_plan+'</b></center></td>';
          dd += '</tr>';
          dd += '<tr>';
          dd += '<td colspan="2"><b>Total Plan Item</b></td>';
          dd += '<td colspan="5"><center><b>'+total_plan_item+'</b></center></td>';
          dd += '</tr>';
          dd += '<tr>';
          dd += '<tr>';
          dd += '<td colspan="2"><b>Total Aktual</b></td>';
          dd += '<td colspan="5"><center><b>'+total_aktual+'</b></center></td>';
          dd += '</tr>';
          dd += '<tr>';
          dd += '<td colspan="2"><b>Presentase</b></td>';
          dd += '<td colspan="5"><center><b>'+parseInt(presentase)+'%</b></center></td>';
          dd += '</tr>';
          sd = [];
          $('#data-activity-weekly').append(dd);
        }
      });
  }
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