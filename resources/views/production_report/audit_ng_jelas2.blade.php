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
  .content-wrapper{
    padding-top: 0px;
    margin-top: 0px
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
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
    border:1px solid black;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ecf0f5;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #e8e8e8;
  }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div class="row" style="padding-left: 20px;padding-right: 20px;padding-top: 0px;margin-top: 0px">
    <h1 style="color:white">
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border-color: #00a65a">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker2" id="week_date" onchange="drawChart()" placeholder="Select Month"  style="border-color: #00a65a">
        </div>
      </div>
    </h1>
    <div class="col-xs-12" style="padding-top: 20px;padding-bottom: 20px">
      <!-- <div class="row" id="containerchart" style="padding-bottom: 20px"> -->
        <div id="container" style="height: 450px"></div>
        <!-- </div> -->
      </div>
    </div>
    <div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h3>
            <h5 class="modal-title" style="text-align: center;" id="sub_judul_weekly"></h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="data-activity">
              <!-- <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-activity-head-weekly" style="background-color: rgba(126,86,134,.7);">
                </thead>
                <tbody id="data-activity-weekly">
                </tbody>
              </table> -->
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
      },5000)
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
    $.get('{{ url("fetch/audit_ng_jelas_monitoring2") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var categories = [];
          var date_format = [];
          var ok = [];
          var ng = [];

          for(var i = 0; i< result.ng_jelas.length;i++){
            ok.push([Date.parse(result.ng_jelas[i].date), (parseFloat(result.ng_jelas[i].count_ok) || 0)]);
            ng.push([Date.parse(result.ng_jelas[i].date), (parseFloat(result.ng_jelas[i].count_ng) || 0)]);
          }

          Highcharts.chart('container', {
            chart: {
              type: 'column'
            },
            title: {
              floating: false,
              text: ""
            },
            xAxis: {
              type: 'datetime',
              gridLineWidth: 0,
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
                text: 'Total Audit',
                style: {
                  color: '#eee',
                  fontSize: '15px',
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
            legend: {
              layout: 'horizontal',
              align: 'right',
              verticalAlign: 'top',
              x: -40,
              y: 10,
              floating: true,
              borderWidth: 1,
              backgroundColor:
              Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
              shadow: true,
              itemStyle: {
                fontSize:'16px',
                color:'#fff'
              },
            },
            tooltip: {
            },
            plotOptions: {
              column: {
                stacking: 'normal',
                dataLabels: {
                  enabled: true,
                },
                animation: false,
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      showModal(Highcharts.dateFormat('%Y-%m-%d', this.x),this.series.name);
                    }
                  }
                },
              },
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'NG',
              data: ng,
              color:'rgba(255, 0, 0, 0.25)',
            },{
              name: 'OK',
              data: ok,
              color: '#00a65a',
            }]
          });
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    });
  }

  function showModal(date,kondisi) {
    var week_date = $('#week_date').val();
    var data = {
      date:date,
      week_date:week_date,
      kondisi:kondisi,
    }

    $.get('{{ url("fetch/detail_audit_ng_jelas_monitoring2") }}', data, function(result, status, xhr) {
      if(result.status){

        $('#data-activity').html('');
        var datatable = "";

        var leader = [];
        var department = [];

        for(var i = 0; i < result.actlist.length;i++){

          datatable += '<h3 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly'+i+'"><b></b></h3>';

          datatable += '<table id="data-log'+i+'" class="table table-striped table-bordered" style="width: 100%;">';
          datatable += '<thead>'
          datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
          datatable += '<th>Point Check</th>';
          datatable += '<th>Cara Cek</th>';
          datatable += '<th>Foto Kondisi Aktual</th>';
          datatable += '<th>Kondisi (Good / Not Good)</th>';
          datatable += '<th>PIC</th>';
          datatable += '<th>Auditor</th>';
          datatable += '</tr>';
          datatable += '</thead>';

          var leader_name = "";
          var dept_name = "";
          for(var j = 0; j < result.detail[i].length;j++){
            if (result.detail[i][j].activity_list_id == result.actlist[i].activity_list_id) {
              datatable += '<tbody style="border:1px solid black">';
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td>'+result.detail[i][j].point_check+'</td>';
              datatable += '<td>'+result.detail[i][j].cara_cek+'</td>';
              datatable += '<td><img width="200px" src="{{ url("/data_file/") }}/'+result.detail[i][j].foto_kondisi_aktual+'"></td>';
              datatable += '<td>'+result.detail[i][j].kondisi+'</td>';
              datatable += '<td>'+result.detail[i][j].pic_name+'</td>';
              datatable += '<td>'+result.detail[i][j].auditor_name+'</td>';
              datatable += '</tr>';
              datatable += '</tbody>';

              leader_name = result.detail[i][j].leader_dept;
              dept_name = result.detail[i][j].department;
            }
          }
          datatable += '</table>';
          datatable += '<hr style="border:2px solid black">';

          leader.push(leader_name);
          department.push(dept_name);
        }

        $('#data-activity').append(datatable);

        for(var k = 0; k < result.actlist.length;k++){
          $('#judul_weekly'+k).html('');
          $('#judul_weekly'+k).html('<b>Audit NG Jelas of '+leader[k]+' ('+department[k]+') on '+date+'<b>');
        }

        $('#modalDetail').modal('show');
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