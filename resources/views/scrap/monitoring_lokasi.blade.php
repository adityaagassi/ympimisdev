@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

table.table-bordered{
  border:1px solid rgba(150, 150, 150, 0);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56);
  text-align: center;
  background-color: #f0f0ff;  
  color:black;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse !important;
  border:1px solid rgb(54, 59, 56);
  background-color: #f0f0ff;
  color: black;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:1px solid #f4f4f4;
  color: white;
}
#tabelmonitor{
  font-size: 1vw;
}

.zoom{
   -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-animation: zoomin 5s ease-in infinite;
  animation: zoomin 5s ease-in infinite;
  transition: all .5s ease-in-out;
  overflow: hidden;
}
@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
} /*End of Zoom in Keyframes */

/* Zoom out Keyframes */
@-webkit-keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}
@keyframes zoomout {
    0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}/*End of Zoom out Keyframes */


#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <br>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <input type="hidden" id="location">
  <div class="row">
    <div class="col-md-12" style="padding: 1px !important">
         <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To" onchange="refreshTable()">
          </div>
        </div>  
    </div>

      <div class="col-md-12">
        <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
            <div id="chart" style="width: 100%; height: 650px"></div>
        </div>
      </div>
    </div>
</section>

  <div class="modal fade" id="modalLocation">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <center><h3 style="background-color: #00a65a;">Pilih Lokasi Anda</h3></center>
          <div class="modal-body table-responsive no-padding">
            <div class="form-group">
              <select class="form-control select2" onchange="fetchScrapList(value)" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
                <option></option>
                @foreach($storage_locations as $storage_location)
                <option value="{{ $storage_location }}">{{ $storage_location }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  jQuery(document).ready(function() {
    // setInterval(function(){
    //   drawChart();
    // }, 15000);

    $('body').toggleClass("sidebar-collapse");
    // drawChart();
    $('#modalLocation').modal({
      backdrop: 'static',
      keyboard: false
    });
  });

  function refreshTable() {
    drawChart();
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm-dd",
    todayHighlight: true,
    // startView: "months", 
    // minViewMode: "months",
    autoclose: true,
   });
  $('#tanggal').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

   

  function drawChart() {
    var dateto = $('#dateto').val();

    var data = {
      dateto: dateto
    };

    $.get('{{ url("fetch/scrap/monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], jml = [], dept = [], jml_dept = [], not_sign = [], sign = [];
          var category = [];

          $.each(result.datas, function(key, value) {
            bulan.push(value.bulan);
            category.push(value.issue_location);
            not_sign.push(parseInt(value.NotSigned));
            sign.push(parseInt(value.Signed));
          });

          var date = new Date();

          $('#chart').highcharts({

          //   chart: {
          //   type: 'column'
          //   },

          //   title: {
          //       text: 'Monitoring Scrap'
          //   },

          //   xAxis: {
          //       categories: category
          //   },

          //   credits: {
          //       enabled: false
          //   },

          //   yAxis: {
          //       allowDecimals: false,
          //       min: 0,
          //       title: {
          //           text: 'Jumlah Scrap'
          //       }
          //   },

          //   plotOptions: {
          //     series: {
          //     cursor: 'pointer',
          //     dataLabels: {
          //       enabled: true,
          //       format: '{point.y}'
          //     }
          //   },
          //       column: {
          //           stacking: 'normal'
          //       }
          //   },

          //   series: [{
          //       name: 'Diterima',
          //       data: sign
          //   }, {
          //       name: 'Belum Diterima',
          //       data: not_sign
          //   }]
          // })
          chart: {
            type: 'column'
          },
          title: {
            text: 'Monitoring Scrap'
          },
          accessibility: {
            announceNewData: {
              enabled: true
            }
          },
          xAxis: {
            categories: category
          },
          yAxis: {
            title: {
              text: 'Total Percent'
            }

          },
          legend: {
            enabled: false
          },
          plotOptions: {
            series: {
              borderWidth: 0,
              dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
              }
            }
          },

          tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
          },

          series: [{
                name: 'Scrap Dibuat',
                data: not_sign 
          }]
        })
        } else{
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
@stop