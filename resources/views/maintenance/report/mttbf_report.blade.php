@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
  tfoot>tr>td{
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

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div class="col-xs-2">
      <div class="input-group">
        <div class="input-group-addon bg-blue">
          <i class="fa fa-search"></i>
        </div>
        <select class="form-control select2" onchange="drawChart()" id="fiscal_year" data-placeholder="Select Fiscal" style="border-color: #605ca8" >
          <option value=""></option>
          <option value="FY197">FY197</option>
          <option value="FY198">FY198</option>
        </select>
      </div>
    </div>

    <div class="col-xs-12">
      <div id="chart_mtbf"></div>
      <div id="chart_mttr"></div>
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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2();

    drawChart()
  });

  function drawChart() {
    var data = {
      fiscal : $("#fiscal_year").val()
    }

    $.get('{{ url("fetch/maintenance/machine_report/graph") }}', data, function(result) {
      var xCategories = [];
      var series = [];
      var mtbf = [];
      var mttr = [];

      // $.each(result.chart_data,function(index, value){
      //   if(xCategories.indexOf(value.mon2) === -1){
      //     xCategories[xCategories.length] = value.mon2;
      //   }
      // })

      // grouped = result.chart_data.reduce(function (r, o) {
      //   (r[o.machine_group])? r[o.machine_group].push(o.avg_mttbf) : r[o.machine_group] = [o.avg_mttbf];
      //   return r;
      // }, {});

      // // console.log(grouped);
      // $.each(grouped,function(index, value){
      //   series.push({name : index, data : value});
      // })

      var all_datas = [];
      $.each(result.load_hour, function(index, value){
        var dt_num = 0;
        var dt_min = 0;
        var re_min = 0;
        var mtbf = 0;


        $.each(result.chart_data, function(index2, value2){
          // console.log(value.machine_id+" : "+value.mon2+" : "+value2.machine_name+" : "+value2.mon);
          if (value.machine_id == value2.machine_name && value.mon2 == value2.mon) {
            dt_num = value2.down_time_count;
            dt_min = parseInt(value2.down_time_min);
            re_min = parseInt(value2.repair_time);
          }
        })

        if ((value.load_hour / dt_num).toFixed(0) == 'Infinity') {
          mtbf = 0;
        } else {
          mtbf = parseInt((value.load_hour / dt_num).toFixed(0));
        }

        all_datas.push({'mon' : value.mon2, 'machine_name' : value.description, 'mtbf' : mtbf, 'mttr' : ((re_min / dt_num) | 0)});

        // mtbf.push((value.load_hour / dt_num).toFixed(0));
        // mttr.push(((re_min / dt_num) | 0));
      })

      // var all_mtbf = [];
      var hasil = [];
      all_datas.reduce(function(res, value) {
        if (!res[value.mon]) {
          res[value.mon] = { mon: value.mon, mttr: 0, mtbf : 0 };
          hasil.push(res[value.mon])
        }
        res[value.mon].mttr += value.mttr;
        res[value.mon].mtbf += parseInt(value.mtbf);
        return res;
      }, {});

      $.each(hasil, function(index, value){
        value.mttr = (value.mttr / all_datas.length).toFixed(2);
        value.mtbf = (value.mtbf / all_datas.length).toFixed(2);
      })

      var series1 = [];
      var series2 = [];
      var categories = [];

      $.each(hasil, function(index, value){
        series1.push(parseFloat(value.mttr));
        series2.push(parseFloat(value.mtbf));
        categories.push(value.mon);
      })

      Highcharts.chart('chart_mtbf', {
        title: {
          text: ''
        },

        yAxis: {
          title: {
            text: 'MTBF (AVG)'
          }
        },

        xAxis: {
          labels: {
            style: {
              fontSize: '12px',
              fontWeight: 'bold'
            }
          },
          categories: categories
        },

        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
        },

        plotOptions: {
          series: {
            label: {
              connectorAllowed: false
            },
          }
        },

        series: [{
          name : 'MTBF',
          data : series1
        }],

        responsive: {
          rules: [{
            condition: {
              maxWidth: 500
            },
            chartOptions: {
              legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
              }
            }
          }]
        }

      });


      Highcharts.chart('chart_mttr', {
        title: {
          text: ''
        },

        yAxis: {
          title: {
            text: 'MTTR (AVG)'
          }
        },

        xAxis: {
          labels: {
            style: {
              fontSize: '12px',
              fontWeight: 'bold'
            }
          },
          categories: categories
        },

        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
        },

        plotOptions: {
          series: {
            label: {
              connectorAllowed: false
            },
          }
        },

        series: [{
          name : 'MTTR',
          data : series2
        }],

        responsive: {
          rules: [{
            condition: {
              maxWidth: 500
            },
            chartOptions: {
              legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
              }
            }
          }]
        }

      });
    })


}

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

Highcharts.createElement('link', {
  href: '{{ url("fonts/UnicaOne.css")}}',
  rel: 'stylesheet',
  type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
  colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
  '#eeaaee', '#55BF3B', '#DF5353', '#c39bd3', '#fdfefe', '#ba4a00', '#ffeb3b', '#b0bec5', '#0288d1', '#ec407a', '#a1887f'],
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
</script>
@endsection