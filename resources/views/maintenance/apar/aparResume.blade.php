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
    <div class="col-xs-2 pull-right">
      <div class="input-group date">
        <div class="input-group-addon bg-purple" style="border: none;">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker" id="bulan" onchange="drawChartWeek()" placeholder="Pilih Bulan">
      </div>
    </div>

    <div class="col-xs-12">
      <h2 style="color: white; text-align: center" id="judul"></h2>
    </div>
    <div class="col-xs-12">
      <!-- <div id="resume_chart"></div> -->
      <div id="resume_chart_weekly"></div>
      <br>
      <div id="resume_progress_weekly"></div>
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
                            <th>No.</th>
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
                            <th>No.</th>
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
                            <th>No.</th>
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

    // RESUME APAR PER BULAN
    // drawChart();

    // drawChartWeek();
    drawChartAtas();
  });


  function drawChartAtas() {
   var data = {
    mon : $("#bulan").val()
  }

  $.get('{{ url("fetch/maintenance/apar/resumeWeek") }}', data, function(result, status, xhr) {
    var ctg2 = [];
    var progress = [];
    var total = [];

    var s_total = result.apar_total[0].total;
    var val = 0;
    var mondate;

    if ($("#bulan").val() == "") {
      mondate = new Date();
    } else {
      mon = $("#bulan").val()+"-01";
      mon = mon.split('-');
      mon = mon.join('/');

      mondate = new Date(mon);
    }

    $.each(result.apar_progres, function(index, value){
      val += value.jml;
      progress.push(val);
      ctg2.push("Week "+value.wek);

      total.push(s_total - value.jml);
    })

    // ctg2.push("Total");
    // progress.push(val);
    // total.push(s_total - val);

    console.log(total);

    Highcharts.chart('resume_chart_weekly', {
      chart: {
        type: 'column'
      },

      title: {
        text: 'APAR Resume Weekly <br />'+months[mondate.getMonth()]+" "+mondate.getFullYear(),
        html: true
      },

      xAxis: {
        categories: ctg2,
        labels: {
          style: {
            fontSize : '18px'
          }
        }
      },

      yAxis: {
        allowDecimals: false,
        title: {
          text: 'Number of Fire Extinguisher'
        },
        stackLabels: {
          enabled: true,
          align: 'center',
          style : {
            fontSize : '15px'
          }
        }
      },

      tooltip: {
        formatter: function () {
          return '<b>' + this.x + '</b><br/>' +
          this.series.name + ': ' + this.y ;
        }
      },

      credits: {
        enabled: false
      },

      plotOptions: {
        column: {
          point: {
            events: {
              click: function () {
                detail_week(this.category, months[mondate.getMonth()]+" "+mondate.getFullYear());
              }
            }
          },
          dataLabels: {
            enabled: true,
            style : {
              fontSize : '18px'
            }
          }
        }
      },

      series: [{
        name: 'Total Check',
        data: progress
      }]
    });

      //-------------- PROGRESS ---------------
      Highcharts.chart('resume_progress_weekly', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'APAR RESUME PROGRESS'
        },
        xAxis: {
          categories: ctg2,
          labels: {
            style: {
              fontSize : '18px'
            }
          }
        },
        yAxis: {
          min: 0,
          visible: false,
          title: {
            text: ''
          }
        },
        tooltip: {
          enabled: false
        },
        plotOptions: {
          column: {
            stacking: 'percent',
            dataLabels: {
              enabled: true,
              style : {
                fontSize : '18px'
              },
              format : '{point.percentage:.0f}%<br/>'
            }
          }
        },
        credits: {
          enabled: false
        },
        series: [{
          name: '',
          data: total,
          showInLegend: false,
          dataLabels: false,
        }, {
          name: 'Progress',
          data: progress,
          dataLabels: false
        }]
      });
    })
}

// ----------------------------------------------------------------------------------------------------------------

function drawChartWeek() {
  var data = {
    mon : $("#bulan").val()
  }

  $.get('{{ url("fetch/maintenance/apar/resumeWeek") }}', data, function(result, status, xhr) {

    var ctg = [];
    var ctg2 = [];
    var all_check = [];
    var checked = [];
    var exp = [];
    var replace = [];
    var mon = "";
    var mondate;

    var progress = [];
    var total = [];

    $.each(result.cek_week, function(index, value){
      mon = value.mon+"-01";
      mon = mon.split('-');
      mon = mon.join('/');

      mondate = new Date(mon);
        // var nowdate = new Date('2020/'+value.mon.split('-')[1]+'/01');
        
        ctg.push("Week "+value.wek);


        all_check.push(parseInt(value.uncek) - parseInt(value.cek));
        checked.push(parseInt(value.cek));
      })

    $.each(result.replace_week, function(index, value){
      exp.push(value.exp);
      replace.push(value.entry);
    })

    var s_total = result.apar_total[0].total;
    var val = 0;

    $.each(result.apar_progres, function(index, value){
      val += value.jml;
      progress.push(value.jml);
      ctg2.push("Week "+value.wek);
      total.push(s_total - value.jml);
    })

    ctg2.push("Total");
    progress.push(val);
    total.push(s_total - val);

    console.log(total);


    Highcharts.chart('resume_chart_weekly', {
      chart: {
        type: 'column'
      },

      title: {
        text: 'APAR Resume Weekly <br />'+months[mondate.getMonth()]+" "+mondate.getFullYear(),
        html: true
      },

      xAxis: {
        categories: ctg,
        labels: {
          style: {
            fontSize : '18px'
          }
        }
      },

      yAxis: {
        allowDecimals: false,
        title: {
          text: 'Number of Fire Extinguisher'
        },
        stackLabels: {
          enabled: true,
          align: 'center',
          style : {
            fontSize : '15px'
          }
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
      },

      plotOptions: {
        column: {
          stacking: 'normal',
          point: {
            events: {
              click: function () {
                detail_week(this.category, months[mondate.getMonth()]+" "+mondate.getFullYear());
              }
            }
          },
          dataLabels: {
            enabled: true,
            style : {
              fontSize : '18px'
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

      //-------------- PROGRESS ---------------
      Highcharts.chart('resume_progress_weekly', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'APAR RESUME PROGRESS'
        },
        xAxis: {
          categories: ctg2,
          labels: {
            style: {
              fontSize : '18px'
            }
          }
        },
        yAxis: {
          min: 0,
          visible: false,
          title: {
            text: 'Total fruit consumption'
          }
        },
        tooltip: {
          enabled: false
        },
        plotOptions: {
          column: {
            stacking: 'percent',
            dataLabels: {
              enabled: true,
              style : {
                fontSize : '18px'
              },
              format : '{point.percentage:.0f}%<br/>'
            }
          }
        },
        credits: {
          enabled: false
        },
        series: [{
          name: '',
          data: total,
          showInLegend: false,
        }, {
          name: 'Progress',
          data: progress
        }]
      });
    })
}


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

function detail_week(week, title) {

  var mon = $("#bulan").val();
  var wek = week.split(' ')[1];
  console.log(wek);

  $("#judul_modal").html("<b>"+title+"</b> "+week);
  $("#modal_detail").modal('show');

  var data = {
    mon: mon,
    week: wek
  }

  $.get('{{ url("fetch/maintenance/apar/resume/detail/week") }}', data, function(result, status, xhr) {
   $("#body_check").empty();
   $("#body_expired").empty();
   $("#body_replace").empty();

   body_check_detail = "";
   body_expired = "";
   body_replace = "";

   num_cek = 1;
   num_exp = 1;
   num_new = 1;

   var arr_cek_all = result.check_detail_list;

   $.each(arr_cek_all, function(index, value){
    $.each(result.check_detail_list, function(index2, value2){
      if (value.utility_code == value2.utility_code && value2.cek == 1 && value.cek == 0) {
        arr_cek_all[index] = "kosong";
      }

    })
  })

   $.each(result.check_detail_list, function(index, value){

    if (value.cek == 1) {
      bg = "style='background-color:#54f775'";
    } else {
      bg = "style='background-color:#f45b5b; color:white'";
    }


    body_check_detail += "<tr>";
    body_check_detail += "<td "+bg+">"+num_cek+"</td>";
    body_check_detail += "<td "+bg+">"+value.utility_code+"</td>";
    body_check_detail += "<td "+bg+">"+value.utility_name+"</td>";
    body_check_detail += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
    body_check_detail += "</tr>";

    num_cek++;
  })

   $("#body_check").append(body_check_detail);

   $.each(result.replace_list, function(index, value){
    if (value.exp == 1) {
      bg = "style='background-color:#f45b5b; color:white'";

      body_expired += "<tr>";
      body_expired += "<td "+bg+">"+num_exp+"</td>";
      body_expired += "<td "+bg+">"+value.utility_code+"</td>";
      body_expired += "<td "+bg+">"+value.utility_name+"</td>";
      body_expired += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
      body_expired += "<td "+bg+">"+value.dt+"</td>";
      body_expired += "</tr>";

      num_exp++;
    } else {
      bg = "style='background-color:#54f775'";

      body_replace += "<tr>";
      body_replace += "<td "+bg+">"+num_new+"</td>";
      body_replace += "<td "+bg+">"+value.utility_code+"</td>";
      body_replace += "<td "+bg+">"+value.utility_name+"</td>";
      body_replace += "<td "+bg+">"+value.location+" - "+value.group+"</td>";
      body_replace += "<td "+bg+">"+value.dt+"</td>";
      body_replace += "</tr>";

      num_new++;
    }
  })

   $("#body_expired").append(body_expired);
   $("#body_replace").append(body_replace);

 })

}

$(".datepicker").datepicker( {
  autoclose: true,
  format: "yyyy-mm",
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