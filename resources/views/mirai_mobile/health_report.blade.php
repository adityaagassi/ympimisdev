@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  table.table-bordered{
    border:1px solid rgb(150,150,150);
  }
  table.table-bordered > thead > tr > th{
    border:1px solid rgb(54, 59, 56) !important;
    text-align: center;
    background-color: #212121;  
    color:white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(54, 59, 56);
    background-color: #212121;
    color: white;
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
    border:1px solid black !important;
    text-align: center;
    background-color: rgba(126,86,134,.7) !important;  
  }

  table.table-striped > tbody > tr > td{
    border: 1px solid #eeeeee !important;
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
    border:2px solid #f4f4f4;
    color: white;
  }
  #tabelmonitor{
    font-size: 0.83vw;
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
    <h1>
      Form Ketidaksesuaian <span class="text-purple">Grafik</span>
      <small>Berdasarkan Tanggal<span class="text-purple"> </span></small>
    </h1>
    <ol class="breadcrumb" id="last_update">
    </ol>
  </section>
  @endsection

  @section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <section class="content" style="padding-top: 0; padding-bottom: 0">
    <div class="row">
      <div class="col-md-12" style="padding: 1px !important">

        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
          </div>
        </div>
        <div class="col-xs-1">
          <button class="btn btn-success" onclick="drawChart()">Update Chart</button>
        </div>
        <div class="col-xs-7">
          <a class="btn btn-success pull-right" href="{{url('index/mirai_mobile/report')}}">See Report</a>
        </div>
      </div>
      <div class="col-md-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
        <div id="chart" style="width: 99%; height: 400px;"></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableResult" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Tanggal</th>
                    <th>NIK</th> 
                    <th>Nama</th>
                    <th>Department</th>
                    <th>Section</th>
                    <th>Group</th>
                  </tr>
                </thead>
                <tbody id="tableBodyResult">
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/drilldown.js")}}"></script>

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
    $('.select2').select2();
    drawChart();
    setInterval(drawChart, 300000);
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  function drawChart() {    

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto
    };

    $.get('{{ url("fetch/mirai_mobile/healthy_report") }}', data, function(result, status, xhr) {
      if(result.status){

        var employeeall = []; 
        var employeensubmit = [];
        var employeenotsubmit = []; 
        var tgl = [];

        $.each(result.datas, function(key, value) {
          tgl.push(value.answer_date);
          employeensubmit.push(value.mengisi);
          employeenotsubmit.push(value.belum);
        })

        $('#chart').highcharts({
          chart: {
            type: 'column'
          },
          title: {
            text: 'Laporan Kesehatan Karyawan YMPI',
            style: {
              fontSize: '25px',
              fontWeight: 'bold'
            }
          },
          subtitle: {
            text: 'By Date',
            style: {
              fontSize: '1vw',
              fontWeight: 'bold'
            }
          },
          xAxis: {
            type: 'category',
            categories: tgl,
            lineWidth:2,
            lineColor:'#9e9e9e',
            gridLineWidth: 1,
            labels: {
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
          },
          yAxis: {
            lineWidth:2,
            lineColor:'#fff',
            type: 'linear',
            title: {
              text: 'Total Karyawan',
              style: {
                color: '#eee',
                fontSize: '25px',
                fontWeight: 'bold',
                fill: '#6d869f'
              }
            },
              // tickInterval: 1,  
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black',
                  fontSize: '2vw'
                }
              }
            },
            legend: {
              align: 'right',
              x: -30,
              verticalAlign: 'top',
              y: 20,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "14px",
                fontWeight: "bold",

              },
              floating: true,
              shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      // ShowModal(this.category,this.series.name);
                      ShowModal(this.category);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: false,
                  format: '{point.y}',
                  style:{
                    fontSize: '1.5vw'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                borderRadius: 1,
                dataLabels: {
                  enabled: true,
                }
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' : ' + this.y;
              }
            },
            series: [
            {
              name: 'Employee Not Submit',
              data: employeenotsubmit,
              color : '#ff6666'
            },
            {
              name: 'Employee Submit',
              data: employeensubmit,
                  color : '#5cb85c' //00f57f
                }
                ]
              })
      } else{
        alert('Attempt to retrieve data failed');
      }
    })
  }

  // function ShowModal(tgl,status) {
  function ShowModal(tgl) {

    $("#myModal").modal("show");

    var data = {
      tgl:tgl
    }

    $.get('{{ url("index/mirai_mobile/detail") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tableResult').DataTable().clear();
        $('#tableResult').DataTable().destroy();
        $('#tableBodyResult').html("");
        var tableData = "";
        var count = 1;

        $.each(result.lists, function(key, value) {

          var d = new Date(tgl);
          var day = d.getDate();
          var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
          var month = months[d.getMonth()];
          var year = d.getFullYear();      

          tableData += '<tr>';
          tableData += '<td>'+ day +' '+month+' '+year +'</td>';
          tableData += '<td>'+ value.employee_id +'</td>';
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td>'+ value.department +'</td>';
          tableData += '<td>'+ value.section +'</td>';
          tableData += '<td>'+ value.group +'</td>';
          tableData += '</tr>';
          count += 1;
        });

        $('#tableBodyResult').append(tableData);

        $('#tableResult tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
        } );
        var table = $('#tableResult').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 5, 10, 25, -1 ],
          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            },
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 15,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });
      }
      else{
        alert('Attempt to retrieve data failed');
      }
      table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#tableResult tfoot tr').appendTo('#tableResult thead');

    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>List yang Tidak Mengisi Tanggal '+tgl+'</b></center>'); 
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