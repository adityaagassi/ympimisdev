@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
 thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
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
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Total Meeting <span class="text-purple"> jepang </span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection


@section('content')

<section class="content container-fluid">
 <div class="row">
  <div class="col-md-12">
   <!-- Custom Tabs -->
   <div class="box">
    <div class="box-body">
     <div class="col-md-12">
      <div class="row">
       <div class="col-md-12">
        <div id="status_stacked_chart" style="width: 100%; height: 500px;"></div>   
      </div>
    </div>
  </div>
</div>
</div>


<div class="box">
  <div class="box-body">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-12">
      <div id="status_chart" style="width: 100%; height: 550px;"></div>
    </div>
  </div>
</div>
</div>
</div>


<div class="box">
  <div class="box-body">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-12">
      <div id="gender_chart" style="width: 100%; height: 550px;"></div>
    </div>
  </div>
</div>
</div>
</div>


<div class="box">
  <div class="box-body">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-12">
      <div id="over_by_dep" style="width: 100%; height: 550px;"></div>
    </div>
  </div>
</div>
</div>
</div>


<div class="box">
  <div class="box-body">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-12">
      <div class="col-md-2 pull-right">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border-color: #00a65a">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker" id="date2" onchange="drawChart()" placeholder="Select Month" style="border-color: #00a65a">
        </div>
      </div>
      <div id="over" style="width: 100%; height: 550px;"></div>
      <br>
      <br>
    </div>
  </div>
</div>
</div>
</div>


<div class="box">
  <div class="box-body">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-12">
      <small style="font-size: 15px; color: #88898c"><i class="fa fa-history"></i> Last updated : <?php echo date('d M Y') ?> </small>
      <div class="col-md-2 pull-right">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border-color: #00a65a">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
        </div>
      </div>
      <div id="over_control" style="width: 100%; height: 550px;"></div>
      <br>
      <br>
    </div>
  </div>
</div>
</div>
</div>



</div>
</div>

<div class="modal fade" id="myModal2">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b id="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;"> 
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>NIK</th>
                  <th>Nama karyawan</th>
                  <th>Departemen</th>
                  <th>Section</th>
                  <th>Kode</th>
                  <th>Avg (jam)</th>
                </tr>
              </thead>
              <tbody id="details">
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

</section>


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

  var month = new Array();
  month[0] = "January";
  month[1] = "February";
  month[2] = "March";
  month[3] = "April";
  month[4] = "May";
  month[5] = "June";
  month[6] = "July";
  month[7] = "August";
  month[8] = "September";
  month[9] = "October";
  month[10] = "November";
  month[11] = "December";

  $(function () {
    drawChartGender();
    drawChartStatusStacked();
    // drawChartStatus();
    drawChart();
  })


  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

  });

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '2000'
    });
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '2000'
    });
  }

  function drawChartGender(){
    $.get('{{ url("fetch/report/gender") }}', function(result, status, xhr){
      if(xhr.status == 200){

        if(result.status){
          var xCategories = [];
          var seriesLaki = [];
          var seriesPerempuan = [];
          var cat, cat2;

          for(var i = 0; i < result.manpower_by_gender.length; i++){
            cat = result.manpower_by_gender[i].mon;

            var date = new Date(cat+'-01');

            cat2 = month[date.getMonth()]+" "+date.getFullYear();

            if(result.manpower_by_gender[i].gender == 'L')
              seriesLaki.push(result.manpower_by_gender[i].tot_karyawan);
            else
              seriesPerempuan.push(result.manpower_by_gender[i].tot_karyawan);

            if(xCategories.indexOf(cat2) === -1){
              xCategories[xCategories.length] = cat2;
            }
          }

          Highcharts.chart('gender_chart', {
            chart: {
              type: 'column'
            },
            title: {
              text: 'Total Manpower by Gender <br> Fiscal 196'
            },
            xAxis: {
              categories: xCategories,
              labels: {
                style: {
                  fontSize: '17px'
                }
              }
            },
            legend: {
              enabled:true,
              itemStyle: {
               fontSize:'15px'
             },
           },
           yAxis: {
            min: 0,
            title: {
              text: 'Total Manpower',
              style: {
                fontSize: '17px'
              }
            }
          },
          tooltip: {
            useHTML: true
          },
          credits: {
            enabled: false
          },
          plotOptions: {
            column: {
              dataLabels: {
                enabled: true,
                crop: false,
                overflow: 'none',
                style: {
                  fontSize: '12px'
                }
              },
              borderWidth: 0
            },
            series: {
              minPointLength: 3
            }
          },
          series: [{
            name: 'Male',
            data: seriesLaki

          }, {
            name: 'Female',
            data: seriesPerempuan

          }]
        });

        }
        else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChartStatusStacked() {
    $.get('{{ url("fetch/report/status1") }}', function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){

          //  ------------- CHART STATUS STACKED ---------------

          var xCategories = [];
          var seriesPKWT = [];
          var seriesPKWTT1 = [];
          var seriesPKWTT2 = [];
          var seriesPercobaan = [];
          var maxMP = [];
          var cat, cat2, MP = 0;

          for(var i = 0; i < result.manpower_by_status_stack.length; i++){
            cat = result.manpower_by_status_stack[i].mon;

            var date = new Date(cat+'-01');

            cat2 = month[date.getMonth()]+" "+date.getFullYear();

            if(result.manpower_by_status_stack[i].status == 'PKWT')
              seriesPKWT.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'PKWTT1')
              seriesPKWTT1.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'PKWTT2')
              seriesPKWTT2.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'Percobaan')
              seriesPercobaan.push(result.manpower_by_status_stack[i].emp);

            if(xCategories.indexOf(cat2) === -1){
              xCategories[xCategories.length] = cat2;
            }

          }

          for (var i = 0; i < xCategories.length; i++) {
            MP = MP + seriesPKWT[i] + seriesPKWTT1[i] + seriesPKWTT2[i] + seriesPercobaan[i];
            maxMP.push(MP);
            MP = 0;
          }

          Highcharts.chart('status_stacked_chart', {
            chart: {
              type: 'column'
            },
            title: {
              text: 'Total Manpower'
            },
            xAxis: {
              categories: xCategories,
              labels: {
                style: {
                  fontSize: '17px'
                }
              }
            },
            tooltip: {
              useHTML: true
            },
            yAxis: {
              min: 0,
              title: {
                text: 'Total Manpower',
                style: {
                  fontSize: '17px'
                }
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray',
                  fontSize: '15px'
                }
              }
            },
            legend: {
              enabled:true,
              itemStyle: {
               fontSize:'15px'
             },
           },
           tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
          },
          plotOptions: {
            column: {
              stacking: 'normal',
              dataLabels: {
                enabled: true,
                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                style: {
                  fontSize: '12px'
                }
              },
              borderWidth: 0
            },
            series: {
              minPointLength: 5
            }
          },
          credits:{
            enabled:false
          },
          series: [{
            name: 'Kontrak 1',
            data: seriesPKWTT1

          }, {
            name: 'Kontrak 2',
            data: seriesPKWTT2

          }, {
            name: 'Percobaan',
            data: seriesPercobaan

          }, {
            name: 'Tetap',
            data: seriesPKWT
          },
          {
            name: 'Trendline',
            type: 'line',
            data: maxMP,
            color: '#f21a25'
          }
          ]
        });

          //  ------------- CHART STATUS NOT STACKED ---------------

          Highcharts.chart('status_chart', {
            chart: {
              type: 'column'
            },
            title: {
              text: 'Total Manpower by Status'
            },
            xAxis: {
              categories: xCategories,
              labels: {
                style: {
                  fontSize: '17px'
                }
              }
            },
            legend: {
              enabled:true,
              itemStyle: {
               fontSize:'15px'
             },
           },
           yAxis: {
            min: 0,
            title: {
              text: 'Total Manpower',
              style: {
                fontSize: '17px'
              }
            }
          },
          tooltip: {
            useHTML: true
          },
          credits:{
            enabled:false
          },
          plotOptions: {
            column: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                enabled: true
              }
            },
            series: {
              minPointLength: 5
            }
          },
          series: [{
            name: 'Kontrak 1',
            data: seriesPKWTT1

          }, {
            name: 'Kontrak 2',
            data: seriesPKWTT2

          }, {
            name: 'Percobaan',
            data: seriesPercobaan

          }, {
            name: 'Tetap',
            data: seriesPKWT

          }]
        });
        }
      }
    });
}

function drawChart() {
  var tanggal = $('#date2').val();
  var cat = new Array();
  var tiga_jam = new Array();
  var per_minggu = new Array();
  var per_bulan = new Array();
  var manam_bulan = new Array();

  var data = {
   tanggal:tanggal
 }

 $.get('{{ url("fetch/overtime_report") }}', data, function(result) {

   for (i = 0; i < result.report.length; i++){
     cat.push(result.report[i].department);
     tiga_jam.push(parseInt(result.report[i].tiga_jam));
     per_minggu.push(parseInt(result.report[i].emptblas_jam));
     per_bulan.push(parseInt(result.report[i].tiga_patblas_jam));
     manam_bulan.push(parseInt(result.report[i].limanam_jam));
   }

   tgl = result.report[0].month_name;

   var date = new Date(tgl+'-01');

   title = month[date.getMonth()]+" "+date.getFullYear();


   // ------ Chart Overtime over 3 hour -----------


   $('#over').highcharts({
     chart: {
       type: 'line'
     },
     legend: {
       enabled: true,
       itemStyle: {
        fontSize: '15px'
      }
    },
    exporting : {
     enabled : true,
     buttons: {
       contextButton: {
         align: 'right',
         x: -25
       }
     }
   },
   title: {
    text: 'Overtime <br><span style="font-size:12pt">'+title+'</span>',
    style: {
      fontSize: '30px'
    }
  },
  xAxis: {
    categories: cat,
    labels: {
      rotation: -60,
      style: {
        fontSize: '17px'
      }
    }
  },
  yAxis: {
    min:0,
    title: {
      text: 'Total Manpower',
      style: {
        fontSize: '17px'
      }
    },
    labels: {
      style: {
        fontSize: '15px'
      }
    }
  },
  plotOptions: {
    line: {
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '15px'
        }
      },
      enableMouseTracking: true
    },
    series: {
     cursor: 'pointer',
     point: {
       events: {
         click: function(e) {  
           show2(tgl, this.category, this.series.name);
         }
       }
     }
   }
 },
 credits: {
  enabled: false
},
series: [{
  name: '3 hour(s) / day',
  color: '#2598db',
  shadow: {
    color: '#2598db',
    width: 7,
    offsetX: 0,
    offsetY: 0
  },
  data: tiga_jam
}, {
  name: '14 hour(s) / week',
  color: '#f78a1d',
  shadow: {
    color: '#f78a1d',
    width: 7,
    offsetX: 0,
    offsetY: 0
  },
  data: per_minggu
},
{
  name: '3 & 14 hour(s) / week',
  color: '#f90031',
  shadow: {
    color: '#f90031',
    width: 7,
    offsetX: 0,
    offsetY: 0
  },
  data: per_bulan
},
{
  name: '56 hour(s) / month',
  color: '#d756f7',
  shadow: {
    color: '#d756f7',
    width: 7,
    offsetX: 0,
    offsetY: 0
  },
  data: manam_bulan
}]

});

   // ------------  Chart Overtime by Department

   var categories;
   var xCategories = [];
   var seriesData = [];
   var cats;

   for (i = 0; i < result.report_by_dep.length; i++){
    categories = result.report_by_dep[i].mon;

    var date = new Date(categories+'-01');

    cats = month[date.getMonth()]+" "+date.getFullYear();

    if(xCategories.indexOf(cats) === -1){
      xCategories[xCategories.length] = cats;
    }
  }

  for(i = 0; i < result.report_by_dep.length; i++){
    if(seriesData){
     var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == result.report_by_dep[i].department;});
     if(currSeries.length === 0){
      seriesData[seriesData.length] = currSeries = {name: result.report_by_dep[i].department, data: []};
    } else {
      currSeries = currSeries[0];
    }
    var index = currSeries.data.length;
    currSeries.data[index] = result.report_by_dep[i].avg;
  } else {
   seriesData[0] = {name: result.report_by_dep[i].department, data: [intVal(result.report_by_dep[i].avg)]}
 }
}


Highcharts.chart('over_by_dep', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Overtime by Department'
  },
  xAxis: {
    categories: xCategories,
    labels: {
      style: {
        fontSize: '17px'
      }
    }
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Total Overtime (hour)',
      style: {
        fontSize: '17px'
      }
    },
    stackLabels: {
      enabled: true,
      style: {
        fontWeight: 'bold',
        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray',
        fontSize: '15px'
      }
    }
  },
  legend: {
    enabled: true,
    itemStyle: {
      fontSize: '15px'
    }
  },
  tooltip: {
    enabled:true
  },
  credits:{
    enabled: false
  },
  plotOptions: {
    column: {
      stacking: 'normal',
      dataLabels: {
        enabled: true,
        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
        style: {
          fontSize: '12px'
        }
      },
      borderWidth: 0
    },
    series: {
      minPointLength: 5
    }
  },
  series: seriesData
});


// -------------- CHART OVERTIME REPORT CONTROL ----------------------

var xCategories2 = [];
var seriesDataBudget = [];
var seriesDataAktual = [];
var budgetHarian = [];
var ctg;

for(var i = 0; i < result.report_control.length; i++){
  ctg = result.report_control[i].name;
  seriesDataBudget.push(Math.round(result.report_control[i].tot * 100) / 100);
  seriesDataAktual.push(Math.round(result.report_control[i].act * 100) / 100);
  budgetHarian.push(Math.round(result.report_control[i].jam_harian * 100) / 100);
  if(xCategories.indexOf(ctg) === -1){
   xCategories[xCategories.length] = ctg;
 }
}

Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
  return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
};

Highcharts.chart('over_control', {
  chart: {
    spacingTop: 10,
    type: 'column'
  },
  title: {
    text: '<span style="font-size: 30pt;">Overtime</span><br><center><span style="color: rgba(96, 92, 168);">'+ result.report_control[0].tanggal +'</center></span>',
    useHTML: true
  },
  credits:{
    enabled:false
  },
  legend: {
    itemStyle: {
      color: '#000000',
      fontWeight: 'bold',
      fontSize: '20px'
    }
  },
  yAxis: {
    tickInterval: 10,
    min:0,
    allowDecimals: false,
    title: {
      text: 'Amount of Overtime (hours)'
    }
  },
  xAxis: {
    labels: {
      style: {
        color: 'rgba(75, 30, 120)',
        fontSize: '12px',
        fontWeight: 'bold'
      }
    },
    categories: xCategories
  },
  tooltip: {
    formatter: function () {
      return '<b>' + this.series.name + '</b><br/>' +
      this.point.y + ' ' + this.series.name.toLowerCase();
    }
  },
  plotOptions: {
    column: {
      pointPadding: 0.93,
      cursor: 'pointer',
      point: {
        events: {
          click: function () {
            modalTampil(this.category, result.report_control[0].tanggal);
          }
        }
      },
      minPointLength: 3,
      dataLabels: {
        allowOverlap: true,
        enabled: true,
        y: -25,
        style: {
          color: 'black',
          fontSize: '13px',
          textOutline: false,
          fontWeight: 'bold',
        },
        rotation: -90
      },
      pointWidth: 15,
      pointPadding: 0,
      borderWidth: 0,
      groupPadding: 0.1,
      animation: false,
      opacity: 0.2
    },
    scatter : {
      dataLabels: {
        enabled: false
      },
      animation: false
    }
  },
  series: [{
    name: 'Budget Accumulative',
    data: seriesDataBudget,
    color: "#f76111"
  }, {
    name: 'Actual Accumulative',
    data: seriesDataAktual,
    color: "#7300ab"
  },
  {
    name: 'Day Budget',
    marker: {
      symbol: 'c-rect',
      lineWidth:4,
      lineColor: '#02ff17',
      radius: 10,
    },
    type: 'scatter',
    data: budgetHarian
  }]
});
});
}

function show2(tgl, code, ctg) {
  tabel = $('#example3').DataTable();
  tabel.destroy();

  $('#myModal2').modal('show');

  var data = {
    tanggal : tgl,
    code : code,
    category: ctg
  }

  $.get('{{ url("fetch/overtime_report_detail") }}', data, function(result){
    $("#details").empty();
    $("#head").html('Overtime of More than '+result.head);
    $.each(result.datas, function(key, value) {
     $("#details").append(
      "<tr><td>"+value.nik+"</td><td>"+value.name+"</td><td>"+value.department+"</td><td>"+value.section+"</td><td>"+value.code+"</td><td>"+value.avg+"</td></tr>"
      );
   })
  })
}

$('.datepicker').datepicker({
  autoclose: true,
  format: "mm-yyyy",
  viewMode: "months", 
  minViewMode: "months"
});

</script>

@stop
