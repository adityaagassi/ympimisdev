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
  #loading, #error { display: none; }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <div class="row" style="padding-left: 20px;padding-right: 20px;padding-top: 0px;margin-top: 0px">
    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10" style="background-color: rgb(126,86,134);text-align: center;height: 40px;padding-right: 5px">
      <span style="color: white;font-size: 25px;font-weight: bold;" id="title_periode">
      </span>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px">
      <div class="input-group date">
        <div class="input-group-addon" style="border-color: rgb(126,86,134);background-color: rgb(126,86,134);color: white">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker2" id="week_date" onchange="drawChart()" placeholder="Select Month" style="border-color: #00a65a;height: 40px">
      </div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container" style="height: 500px"></div>
    </div>
  </div>
    <div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
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
      month: week_date
    };
    $.get('{{ url("fetch/audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          $('#title_periode').html('Periode On '+result.monthTitle);
          var categories = [];
          var plan = [];
          var done = [];
          var not_yet = [];

          for(var i = 0; i< result.audit_ik.length;i++){
            categories.push(result.audit_ik[i].department_shortname+' - '+result.audit_ik[i].leader_dept);
            plan.push(parseInt(result.audit_ik[i].plan));
            done.push(parseInt(result.audit_ik[i].done));
            not_yet.push(parseInt(result.audit_ik[i].not_yet));
          }

          Highcharts.chart('container', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: ""
            },
            xAxis: {
              type: 'category',
              categories: categories,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"13px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                pointPadding: 0.93,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      showModal(this.category,this.series.name);
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Belum Dikerjakan',
              data: not_yet,
              color:'#a60000',
            },
            {
              name: 'Sudah Dikerjakan',
              data: done,
              color:'#00a65a',
            }]
          });
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    });
  }

  function showModal(leader,kondisi) {
    $('#loading').show();
    var week_date = $('#week_date').val();
    var data = {
      leader:leader,
      month:week_date,
      kondisi:kondisi,
    }

    $.get('{{ url("fetch/detail_audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(result.status){

        $('#data-activity').html('');
        var datatable = "";

        if (kondisi === 'Belum Dikerjakan') {
          datatable += '<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">';
          datatable += '<thead>'
          datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
          datatable += '<th>No. Dokumen</th>';
          datatable += '<th>Nama Dokumen</th>';
          datatable += '<th>Target</th>';
          datatable += '</tr>';
          datatable += '</thead>';

          for(var j = 0; j < result.datas.length;j++){
              datatable += '<tbody style="border:1px solid black">';
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td>'+result.datas[j].no_dokumen+'</td>';
              datatable += '<td>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td>'+result.datas[j].month+'</td>';
              datatable += '</tr>';
              datatable += '</tbody>';
          }
          datatable += '</table>';
          datatable += '<hr style="border:2px solid black">';
        }else{
          for(var j = 0; j < result.datas.length;j++){
            datatable += '<table id="data-log" class="table table-bordered table-striped" style="width: 100%;">';
            datatable += '<thead>'
            datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686">';
            datatable += '<th colspan="2" style="color:white;">'+result.datass[j][0].no_dokumen+'</th>';
            datatable += '</tr>';
            datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
            datatable += '<th style="width:1%">Target : '+result.datass[j][0].month+'</th>';
            datatable += '<th style="width:3%">Nama Dokumen : '+result.datass[j][0].nama_dokumen+'</th>';
            datatable += '</tr>';
            datatable += '</thead>';

            datatable += '<tbody style="border:1px solid black">';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Dept</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].department+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Section</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].section+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Group</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].subsection+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Audit Date</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].date+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Kesesuaian Aktual</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].kesesuaian_aktual_proses+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Kesesuaian QC Kouteihyo</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].kesesuaian_qc_kouteihyo+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Kelengkapan Point Safety</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].kelengkapan_point_safety+'</td>';
            datatable += '</tr>';
            datatable += '<tr>';
            datatable += '<td style="border:1px solid black">Tindakan Perbaikan</td>';
            datatable += '<td style="border:1px solid black">'+result.datass[j][0].tindakan_perbaikan+'</td>';
            datatable += '</tr>';
            if (result.datass[j][0].tindakan_perbaikan == '-') {
              datatable += '<tr>';
              datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
              datatable += '<td style="border:1px solid black"></td>';
              datatable += '</tr>';
            }else{
              datatable += '<tr>';
              datatable += '<td style="border:1px solid black">Target Perbaikan</td>';
              datatable += '<td style="border:1px solid black">'+result.datass[j][0].target+'</td>';
              datatable += '</tr>';
            }
            datatable += '<tr>';
            datatable += '<td>PIC</td>';
            datatable += '<td>'+result.datass[j][0].operator+'</td>';
            datatable += '</tr>';
            datatable += '</tbody>';

            datatable += '</table>';
            datatable += '<hr style="border:2px solid black">';
          }
        }

        $('#data-activity').append(datatable);

        var ldr = leader.split(' - ');

        $('#judul_weekly').html('<b>Audit IK Oleh '+ldr[1]+' ('+ldr[0]+') <br>Bulan '+result.monthTitle+' <br>yang '+kondisi+'<b>');

        $('#loading').hide();
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