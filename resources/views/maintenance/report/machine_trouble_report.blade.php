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

  table.table-bordered > tbody > tr > th{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    color: white;
    text-align: center;
    background-color: #605ca8;
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

  .inprogress {
    background-color: #6ddb5e !important;
  }

  .pending {
    background-color: #e83e27 !important;
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
      <div class="col-xs-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border: none;">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control monpicker" id="tanggal_from" placeholder="Select Month From">
        </div>
      </div>
      <div class="col-xs-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border: none;">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control monpicker" id="tanggal_to" placeholder="Select Month To">
        </div>
      </div>
      <div class="col-xs-2">
        <button class="btn btn-success" onclick="getData()"><i class="fa fa-refresh"></i> Update Chart</button>
      </div>
      <div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;">tes</div>
    </div>
    <div class="col-xs-12">
      <div class="col-xs-12">
        <br>
        <div id="machine_group_trouble"></div>
        <br><br>
        <div id="machine_trouble_div" style="display: none"></div>
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

  var trouble_all = [];
  var mon_from = '';
  var mon_to = '';

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('.monpicker').datepicker({
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
    getData();
  });

  function getData() {
    $("#machine_trouble_div").hide();
    var data = {
      'tanggal_from' : $("#tanggal_from").val(),
      'tanggal_to' : $("#tanggal_to").val()
    }

    $.get('{{ url("fetch/maintenance/machine_report/report") }}', data, function(result, status, xhr) {
      var series = [];
      var cat = [];

      $.each(result.machine_groups, function(index, value){
        series.push({'name': value.machine_group, 'data': [value.jml_rusak]});
        cat.push(value.machine_group);
      })

      mon_from = result.mon_from;
      mon_to = result.mon_to;

      trouble_all = result.trouble_list;

      Highcharts.chart('machine_group_trouble', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'Resume Machine Trouble By Group Machine',
          style: {
            fontSize: '30px',
            fontWeight: 'bold'
          }
        },
        subtitle: {
          text: result.mon_from+' - '+result.mon_to,
          style: {
            fontSize: '1vw',
            fontWeight: 'bold'
          }
        },
        xAxis: {
          type: 'category',
          visible: false
        },
        yAxis: {
          min: 0,
          title: {
            text: 'Total Trouble(s)'
          },
          stackLabels: {
            enabled: true,
            style: {
              fontWeight: 'bold',
                color: ( // theme
                  Highcharts.defaultOptions.title.style &&
                  Highcharts.defaultOptions.title.style.color
                  ) || 'gray'
              }
            }
          },
          legend: {
            borderWidth: 1,
            backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
            shadow: true
          },
          tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: '{point.y} Trouble'
          },
          plotOptions: {
            column: {
              dataLabels: {
                enabled: true
              }
            },
            series:{
              cursor: 'pointer',
              point: {
                events: {
                  click: function () {
                    drawDetail(this.series.name);
                  }
                }
              }
            }
          },
          credits : {
            enabled : false
          },
          series: series
        });


      // ----------------------------------------------------------------------

      // var cat = [];
      // var series2 = [];
      // $.each(result.by_machine, function(index, value){
      //   if(cat.indexOf(value.machine_name) === -1){
      //     cat[cat.length] = value.machine_name;
      //   }
      // })

      // $.each(result.trouble_list, function(index, value){
      //   var stat = 0;
      //   $.each(result.by_machine, function(index2, value2){
      //     // if (value.machine_name == value2.machine_name) {
      //       if (value.machine_name == value2.machine_name && value.trouble_part == value2.trouble_part) {
      //         series2.push({'machine_name' : value.machine_name, 'trouble_part' : value.trouble_part, 'jml' : value2.jml_ng});

      //         stat = 1;
      //       }
      //     // }
      //   })

      //   if (stat == 0) {
      //     series2.push({'machine_name' : value.machine_name, 'trouble_part' : value.trouble_part, 'jml' : 0});
      //   }
      // })

      // console.log(series2);

      // arr_series2 = [];
      // arr_temp = [];
      // $.each(series2, function(index, value){
      //   arr_temp.push(value.jml);

      //   if (typeof series2[index+1] !== 'undefined') {
      //     if (value.trouble_part != series2[index+1].trouble_part) {
      //       arr_series2.push({'name' : value.trouble_part, data: arr_temp});
      //       arr_temp = [];
      //     }
      //   } else {
      //     arr_series2.push({'name' : value.trouble_part, data: arr_temp});
      //     arr_temp = [];
      //   }
      // });

      // console.log(arr_series2);
      // console.log(cat);

      // Highcharts.chart('machine_trouble_div', {
      //   chart: {
      //     type: 'column'
      //   },
      //   title: {
      //     text: '10 Highest Trouble Part Machine',
      //     style: {
      //       fontSize: '30px',
      //       fontWeight: 'bold'
      //     }
      //   },
      //   subtitle: {
      //     text: 'on 2021-Mei',
      //     style: {
      //       fontSize: '1vw',
      //       fontWeight: 'bold'
      //     }
      //   },
      //   xAxis: {
      //     categories: cat
      //   },
      //   yAxis: {
      //     min: 0,
      //     title: {
      //       text: 'Total fruit consumption'
      //     },
      //     stackLabels: {
      //       enabled: true,
      //       style: {
      //         fontWeight: 'bold',
      //           color: ( // theme
      //             Highcharts.defaultOptions.title.style &&
      //             Highcharts.defaultOptions.title.style.color
      //             ) || 'gray'
      //         }
      //       }
      //     },
      //     legend: {
      //       layout: 'vertical',
      //       align: 'right',
      //       verticalAlign: 'top',
      //       x: 1,
      //       y: 0,
      //       floating: true,
      //       borderWidth: 1,
      //       backgroundColor:
      //       Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
      //       shadow: true
      //     },
      //     tooltip: {
      //       headerFormat: '<b>{series.name}</b><br/>',
      //       pointFormat: '{point.y} Trouble'
      //     },
      //     plotOptions: {
      //       column: {
      //         dataLabels: {
      //           enabled: true
      //         }
      //       }
      //     },
      //     credits : {
      //       enabled : false
      //     },
      //     series: arr_series2
      //   });

    })
}

function drawDetail(category) {
  $("#machine_trouble_div").show();

  var series_detail = [];
  var cat_detail = [];


  function comparator(a, b) {    
    if (a.jml_trouble > b.jml_trouble) return -1
      if (a.jml_trouble < b.jml_trouble) return 1
        return 0
    }

    trouble_all = trouble_all.sort(comparator);

    console.log(trouble_all);


    $.each(trouble_all, function(index, value){
      if (value.machine_group == category) {
        series_detail.push({'name': value.trouble_part, 'data': [value.jml_trouble]});
        cat_detail.push(value.trouble_part);
      }
    })

    Highcharts.chart('machine_trouble_div', {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Detail Trouble By Group Machine '+category ,
        style: {
          fontSize: '20px',
          fontWeight: 'bold'
        }
      },
      subtitle: {
        text: mon_from+' - '+mon_to,
        style: {
          fontSize: '1vw',
          fontWeight: 'bold'
        }
      },
      xAxis: {
        type: 'category',
        visible: false
      },
      yAxis: {
        min: 0,
        title: {
          text: 'Total Trouble(s)'
        },
        stackLabels: {
          enabled: true,
          style: {
            fontWeight: 'bold',
                color: ( // theme
                  Highcharts.defaultOptions.title.style &&
                  Highcharts.defaultOptions.title.style.color
                  ) || 'gray'
              }
            }
          },
          legend: {
            borderWidth: 1,
            backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
            shadow: true
          },
          tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: '{point.y} Trouble'
          },
          plotOptions: {
            column: {
              dataLabels: {
                enabled: true
              }
            },
            series:{
              cursor: 'pointer',
              point: {
                events: {
                  click: function () {

                  }
                }
              }
            }
          },
          credits : {
            enabled : false
          },
          series: series_detail
        });

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
    colors: ['#ff0066', '#DF5353', '#000', '#7798BF', '#aaeeee', '#2b908f',
    '#eeaaee', '#55BF3B', '#90ee7e', '#7798BF', '#aaeeee'],
    chart: {
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
        stops: [
        [0, '#2a2a2b'],
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