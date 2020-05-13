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
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
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
  .description-block {
    margin-top: 0px
  }

  .panel {
    margin-bottom: 0px !important;
    border-top-color: #605ca8;
  }
  .box-header:hover {
    cursor: pointer;
    /*background-color: #3c3c3c;*/
  }

  .alert {
    /*width: 50px;
    height: 50px;*/
    -webkit-animation: alert 1s infinite;  /* Safari 4+ */
    -moz-animation: alert 1s infinite;  /* Fx 5+ */
    -o-animation: alert 1s infinite;  /* Opera 12+ */
    animation: alert 1s infinite;  /* IE 10+, Fx 29+ */
  }
  
  @-webkit-keyframes alert {
    0%, 49% {
      /*background: rgba(0, 0, 0, 0);*/
      background: #fffcb7; 
      /*opacity: 0;*/
    }
    50%, 100% {
      background-color: #f55359;
    }
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">   
    <div class="col-xs-12">
      <h2 style="color: white; text-align: center" id="judul"></h2>
    </div>
    <div class="col-xs-12">
      <div id="resume_chart"></div>
    </div>

    <div class="modal fade" id="modal_detail">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="judul_modal"></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                    <li class="active" style="width: 49%;"><a href="#tab_1" data-toggle="tab" style="text-align: center;"><b>Checked Data</b></a></li>
                    <li style="width: 49%;"><a href="#tab_2" data-toggle="tab" style="text-align: center;"><b>Replacement Data</b></a></li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                      <center><b>Checked Data</b></center><br>
                      <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detail_check">
                        <thead style="background-color: rgba(126,86,134,.7);">
                          <tr>
                            <th>APAR Code</th>
                            <th>APAR Name</th>
                            <th>Location</th>
                          </tr>
                        </thead>
                        <tbody id="body_check"></tbody>
                      </table>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                      <center><b>Expired Data</b></center><br>
                      <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detail_expired">
                        <thead style="background-color: rgba(126,86,134,.7);">
                          <tr>
                            <th>APAR Code</th>
                            <th>APAR Name</th>
                            <th>Location</th>
                            <th>Expired Date</th>
                          </tr>
                        </thead>
                        <tbody id="body_expired"></tbody>
                      </table>

                      <center><b>Replace/New Data</b></center><br>
                      <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detail_replace">
                        <thead style="background-color: rgba(126,86,134,.7);">
                          <tr>
                            <th>APAR Code</th>
                            <th>APAR Name</th>
                            <th>Location</th>
                            <th>Entry Date</th>
                          </tr>
                        </thead>
                        <tbody id="body_replace"></tbody>
                      </table>
                    </div>
                    <!-- /.tab-pane -->
                  </div>
                  <!-- /.tab-content -->
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          </div>
        </div>
      </div>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    drawChart();
  });

  function drawChart() {
    $.get('{{ url("fetch/maintenance/apar/resume") }}', function(result, status, xhr) {

      var ctg = [];
      var all_check = [];
      var checked = [];
      var exp = [];
      var replace = [];

      $.each(result.check_list, function(index, value){
        var nowdate = new Date('2020/'+value.mon.split('-')[1]+'/01');
        
        ctg.push(months[nowdate.getMonth()]+" "+nowdate.getFullYear());


        all_check.push(value.jml_tot);
        checked.push(value.jml);
      })

      $.each(result.replace_list, function(index, value){
        exp.push(value.exp);
        replace.push(value.new);
      })

      Highcharts.chart('resume_chart', {

        chart: {
          type: 'column'
        },

        title: {
          text: 'APAR Resume'
        },

        xAxis: {
          categories: ctg
        },

        yAxis: {
          allowDecimals: false,
          min: 0,
          title: {
            text: 'Number of Fire Extinguisher'
          }
        },

        tooltip: {
          formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
            this.series.name + ': ' + this.y + '<br/>' +
            'Total: ' + this.point.stackTotal;
          }
        },

        credits: {
          enabled: false
        }
        ,

        plotOptions: {
          column: {
            stacking: 'normal',
            point: {
              events: {
                click: function () {
                  detail(this.category);
                }
              }
            }
          }
        },

        series: [{
          name: 'Total Check',
          data: all_check,
          stack: 'check'
        }, {
          name: 'Checked',
          data: checked,
          stack: 'check'
        }, {
          name: 'Replaced / New',
          data: replace,
          stack: 'exp'
        }, {
          name: 'Expired',
          data: exp,
          stack: 'exp'
        }]
      });

    })
  }

  function detail(mon) {
    console.log(mon);
    $("#judul_modal").html("<b>"+mon+"</b>");
    $("#modal_detail").modal('show');

    dt = months.indexOf(mon.split(' ')[0])+1;

    mon2 = mon.split(' ')[1]+"-"+('0' + dt).slice(-2);

    var data = {
      mon: mon,
      mon2: mon2
    }

    $.get('{{ url("fetch/maintenance/apar/resume/detail") }}', data, function(result, status, xhr) {

      $("#body_check").empty();
      $("#body_expired").empty();
      $("#body_replace").empty();

      body_check_detail = "";
      body_expired = "";
      body_replace = "";

      $.each(result.check_detail_list, function(index, value){

        if (value.cek == 1) {
          bg = "style='background-color:#54f775'";
        } else {
          bg = "style='background-color:#f45b5b; color:white'";
        }

        body_check_detail += "<tr>";
        body_check_detail += "<td "+bg+">"+value.utility_code+"</td>";
        body_check_detail += "<td "+bg+">"+value.utility_name+"</td>";
        body_check_detail += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
        body_check_detail += "</tr>";
      })

      $("#body_check").append(body_check_detail);

      $.each(result.replace_list, function(index, value){
       if (value.stat == "Expired") {
        bg = "style='background-color:#f45b5b; color:white'";

        body_expired += "<tr>";
        body_expired += "<td "+bg+">"+value.utility_code+"</td>";
        body_expired += "<td "+bg+">"+value.utility_name+"</td>";
        body_expired += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
        body_expired += "<td "+bg+">"+value.dt+"</td>";
        body_expired += "</tr>";
      } else {
        bg = "style='background-color:#54f775'";

        body_replace += "<tr>";
        body_replace += "<td "+bg+">"+value.utility_code+"</td>";
        body_replace += "<td "+bg+">"+value.utility_name+"</td>";
        body_replace += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
        body_replace += "<td "+bg+">"+value.dt+"</td>";
        body_replace += "</tr>";
      }
    })

      $("#body_expired").append(body_expired);
      $("#body_replace").append(body_replace);

    })

  }



  $(".datepicker").datepicker( {
    autoclose: true,
    format: "mm-yyyy",
    viewMode: "months", 
    minViewMode: "months"
  });

  Highcharts.createElement('link', {
    href: '{{ url("fonts/UnicaOne.css")}}',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);

  Highcharts.theme = {
    colors: ['#f45b5b', '#90ee7e', '#2b908f', '#7798BF', '#aaeeee', '#ff0066',
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

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '3000'
    });
  }	
</script>
@endsection