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

.loading {
  margin: 0;
  position: absolute;
  left: 50%;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Total Meeting <span class="text-purple"> トータルミーティング </span>
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
        <div id="wait" class="loading">
          <div>
            <center>
              <i class="fa fa-spinner fa-5x fa-spin"></i><br>
              <h2 style="margin: 0px">Loading . . .</h2>
            </center>
          </div>
        </div>
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
      <div id="wait2" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
        </div>
      </div>
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
      <div id="wait3" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
        </div>
      </div>
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
      <div id="wait4" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
        </div>
      </div>
      <div id="serikat_chart" style="width: 100%; height: 550px;"></div>
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
      <div id="wait5" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
        </div>
      </div>
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
          <input type="text" class="form-control datepicker" id="date2" onchange="drawChartOvertimeOver()" placeholder="Select Month" style="border-color: #00a65a">
        </div>
      </div>
      <div id="wait6" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
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
          <input type="text" class="form-control datepicker" id="tgl" onchange="drawChartOvertimeControl()" placeholder="Select Date" style="border-color: #00a65a">
        </div>
      </div>
      <div id="wait7" class="loading">
        <div>
          <center>
            <i class="fa fa-spinner fa-5x fa-spin"></i><br>
            <h2 style="margin: 0px">Loading . . .</h2>
          </center>
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


<div class="box box-solid">
  <div class="box-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-4">
            <div class="description-block border-right" style="color: #02ff17">
              <h5 class="description-header" style="font-size: 60px;">
                <span class="description-percentage" id="tot_budget"></span>
              </h5>      
              <span class="description-text" style="font-size: 35px;">Total Forecast<br><span >フォーキャスト累計</span></span>   
            </div>
          </div>
          <div class="col-md-4">
            <div class="description-block border-right" style="color: #7300ab" >
              <h5 class="description-header" style="font-size: 60px; ">
                <span class="description-percentage" id="tot_act"></span>
              </h5>      
              <span class="description-text" style="font-size: 35px;">Total Actual<br><span >総実績</span></span>   
            </div>
          </div>
          <div class="col-md-4">
            <div class="description-block border-right text-green" id="diff_text">
              <h5 class="description-header" style="font-size: 60px;">
                <span class="description-percentage" id="tot_diff"></span>
              </h5>      
              <span class="description-text" style="font-size: 35px;">Difference<br><span >差異</span></span>   
            </div>
          </div>
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
                  <th width="5%">Action</th>
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
  var detail = new Array();
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
    Highcharts.setOptions({
      colors: ['#8fafc4','#336a86', '#293132', '#88d958', '#ff410d', '#b95436', '#6fb98f', '#cb58e8', '#ea2a4a', '#ffba00', '#1c8704', '#f3cc6f', '#2971e5','#c89d0f', '#ffccab']
    });
    drawChartGender();
    drawChartStatusStacked();
    drawChartSerikat();
    drawChart();
    drawChartOvertimeOver();
    drawChartOvertimeControl();
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
    $("#wait3").show();
    $.get('{{ url("fetch/report/gender") }}', function(result, status, xhr){
      $("#wait3").hide();
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
              seriesLaki.push(parseInt(result.manpower_by_gender[i].tot_karyawan));
            else
              seriesPerempuan.push(parseInt(result.manpower_by_gender[i].tot_karyawan));

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
    $('#wait').show();
    $('#wait2').show();
    $.get('{{ url("fetch/report/status1") }}', function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          $('#wait').hide();

          //  ------------- CHART STATUS STACKED ---------------

          var xCategories = [];
          var seriesPKWT = [];
          var seriesPKWTT1 = [];
          var seriesPKWTT2 = [];
          var seriesOutsource = [];
          var maxMP = [];
          var cat, cat2, MP = 0;

          for(var i = 0; i < result.manpower_by_status_stack.length; i++){
            cat = result.manpower_by_status_stack[i].mon;

            var date = new Date(cat+'-01');

            cat2 = month[date.getMonth()]+" "+date.getFullYear();

            if(result.manpower_by_status_stack[i].status == 'Tetap' || 
              result.manpower_by_status_stack[i].status == 'Percobaan')
              seriesPKWT.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'Kontrak 1')
              seriesPKWTT1.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'Kontrak 2')
              seriesPKWTT2.push(result.manpower_by_status_stack[i].emp);

            else if(result.manpower_by_status_stack[i].status == 'OUTSOURCES')
              seriesOutsource.push(result.manpower_by_status_stack[i].emp);

            // else if(result.manpower_by_status_stack[i].status == 'Percobaan')
            //   seriesPercobaan.push(result.manpower_by_status_stack[i].emp);

            if(xCategories.indexOf(cat2) === -1){
              xCategories[xCategories.length] = cat2;
            }
          }

          for (var i = 0; i < xCategories.length; i++) {
            MP = MP + seriesPKWT[i] + seriesPKWTT1[i] + seriesPKWTT2[i] + seriesOutsource[i];
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
                  fontSize: '15px',
                  textOutline: 0
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
                  fontSize: '12px',
                  textOutline: 0
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
            name: 'OutSources',
            data: seriesOutsource

          },
          {
            name: 'Contract 1',
            data: seriesPKWTT1

          }, {
            name: 'Contract 2',
            data: seriesPKWTT2

          }, {
            name: 'Permanent',
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

          $('#wait2').hide();

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
                enabled: true,
                textOutline: 0
              }
            },
            series: {
              minPointLength: 5
            }
          },
          series: [
          {
            name: 'OutSources',
            data: seriesOutsource

          }, {
            name: 'Contract 1',
            data: seriesPKWTT1

          }, {
            name: 'Contract 2',
            data: seriesPKWTT2

          }, {
            name: 'Permanent',
            data: seriesPKWT

          }]
        });
        }
      }
    });
}

function drawChartSerikat() {
  $("#wait4").show();
  $.get('{{ url("fetch/report/serikat") }}', function(result, status, xhr){
    $("#wait4").hide();
    if(xhr.status == 200){

      if(result.status){
        var xCategories = [];
        var seriesData = [];
        var cat, cat2;

        for(var i = 0; i < result.manpower_by_serikat.length; i++){
          cat = result.manpower_by_serikat[i].mon;

          var date = new Date(cat+'-01');

          cat2 = month[date.getMonth()]+" "+date.getFullYear();

          if(xCategories.indexOf(cat2) === -1){
            xCategories[xCategories.length] = cat2;
          }
        }


        for(i = 0; i < result.manpower_by_serikat.length; i++){
          if(seriesData){
           var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == result.manpower_by_serikat[i].serikat;});
           if(currSeries.length === 0){
            seriesData[seriesData.length] = currSeries = {name: result.manpower_by_serikat[i].serikat, data: []};
          } else {
            currSeries = currSeries[0];
          }
          var index = currSeries.data.length;
          currSeries.data[index] = result.manpower_by_serikat[i].emp_tot;
        } else {
         seriesData[0] = {name: result.manpower_by_serikat[i].serikat, data: [intVal(result.manpower_by_serikat[i].emp_tot)]}
       }
     }

     Highcharts.chart('serikat_chart', {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Total Manpower by Labor Union <br> Fiscal 196'
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
            fontSize: '12px',
            textOutline: 0
          }
        },
        borderWidth: 0
      },
      series: {
        minPointLength: 3
      }
    },
    series: seriesData
  });

   }
   else{
    alert('Attempt to retrieve data failed');
  }
}
})
}

function drawChart() {
  $("#wait5").show();
  $.get('{{ url("fetch/overtime_report") }}', function(result) {
    $("#wait5").hide();
   // ------------  Chart Overtime by Department
   var categories;
   var xCategories = [];
   var xTotal = [];
   var xOTHour = [];
   var xKar = [];
   var seriesData = [];
   var cats, total = 0, kar, ot = 0;

   for (i = 0; i < result.report_by_dep.length; i++){
    total += result.report_by_dep[i].avg;
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

// var map = result.report_by_dep.reduce(function(map, invoice) {
//   var name = invoice.mon
//   var price = +invoice.ot_hour
//   map[name] = (map[name] || 0) + price
//   return map
// }, {})

// xOTHour = map;

// // console.log(xOTHour);

// var map2 = result.report_by_dep.reduce(function(map2, invoice) {
//   var name2 = invoice.mon
//   var price = +invoice.kar
//   map2[name2] = (map2[name2] || 0) + price
//   return map2
// }, {})

// xKar = map2;

// // console.log(xKar);

// $.each(xOTHour, function(key, value) {
//   var hasil = xOTHour[key] / xKar[key];
//   xTotal.push(Math.round(hasil * 100) / 100);
// });

// console.log(xTotal);

// seriesData.push({name: 'avg', data: xTotal, visible: false})

console.log(seriesData);


Highcharts.chart('over_by_dep', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Overtime Every Person Every Department Every Month'
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
      text: 'Overtime (hour)',
      style: {
        fontSize: '17px'
      }
    },
    stackLabels: {
      enabled: true,
      style: {
        fontWeight: 'bold',
        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray',
        fontSize: '15px',
        textOutline: 0
      },
      formatter: function() {
        // return seriesData[15].data;
      }
    }
  },
  legend: {
    enabled: true,
    itemStyle: {
      fontSize: '12px'
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
          fontSize: '12px',
          textOutline: 0
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
});
}

function drawChartOvertimeOver() {
  var tanggal = $('#date2').val();
  var cat = new Array();
  var tiga_jam = new Array();
  var per_minggu = new Array();
  var per_bulan = new Array();
  var manam_bulan = new Array();

  var data = {
   tanggal:tanggal
 }

 $("#over").css("visibility","hidden");
 $("#wait6").show();

 $.get('{{ url("fetch/overtime_report_over") }}', data, function(result) {

  $("#wait6").hide();
  $("#over").css("visibility","visible");

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
      type: 'column'     
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
      fontSize: '15px',
      textOutline: 0
    }
  }
},
plotOptions: {
  column: {
    dataLabels: {
      enabled: true,
      color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
      style: {
        fontSize: '12px',
        textOutline: 0
      }
    },
    borderWidth: 0
  },
  series: {
   cursor: 'pointer',
   minPointLength: 5,
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
  data: tiga_jam
}, {
  name: '14 hour(s) / week',
  color: '#f78a1d',
  data: per_minggu
},
{
  name: '3 & 14 hour(s) / week',
  color: '#f90031',
  data: per_bulan
},
{
  name: '56 hour(s) / month',
  color: '#d756f7',
  data: manam_bulan
}]

});
  });
}

function drawChartOvertimeControl() {
  var tanggal = $('#tgl').val();

  var data = {
    tgl:tanggal
  }

  // $("#over_control").css("visibility","hidden");
  $("#wait7").show();
  $.get('{{ url("fetch/report/overtime_report_control") }}', data, function(result) {

    $("#wait7").hide();
    // $("#over_control").css("visibility","visible");

    // -------------- CHART OVERTIME REPORT CONTROL ----------------------

    var xCategories2 = [];
    var seriesDataBudget = [];
    var seriesDataAktual = [];
    var budgetHarian = [];
    var ctg, tot_act = 0, avg = 0;
    var tot_day_budget = 0, tot_diff;

    for(var i = 0; i < result.report_control.length; i++){
      ctg = result.report_control[i].cost_center_name;
      tot_act += result.report_control[i].act;
      tot_day_budget += result.report_control[i].jam_harian;

      seriesDataBudget.push(Math.round(result.report_control[i].tot * 100) / 100);
      seriesDataAktual.push(Math.round(result.report_control[i].act * 100) / 100);
      budgetHarian.push(Math.round(result.report_control[i].jam_harian * 100) / 100);
      if(xCategories2.indexOf(ctg) === -1){
       xCategories2[xCategories2.length] = ctg;
     }
   }

   tot_diff = tot_day_budget - tot_act;

   tot_day_budget = Math.round(tot_day_budget * 100) / 100;
   tot_act = Math.round(tot_act * 100) / 100;
   tot_diff = Math.round(tot_diff * 100) / 100;

   var tot_day_budget2 = tot_day_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
   var tot_act2 = tot_act.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
   var tot_diff2 = tot_diff.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

   $("#tot_budget").text(tot_day_budget2);
   $("#tot_act").text(tot_act2);

   if (tot_diff > 0) {
    $('#diff_text').removeClass('text-red').addClass('text-green');
    $("#tot_diff").html(tot_diff2);
  }
  else {
    $('#diff_text').removeClass('text-green').addClass('text-red');
    $("#tot_diff").html(tot_diff2);
  }

  avg = tot_act / data[1];
  avg = Math.round(avg * 100) / 100;
  // $("#avg").html(avg);

  Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
    return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
  };

  Highcharts.chart('over_control', {
    chart: {
      spacingTop: 10,
      type: 'column'
    },
    title: {
      text: '<span style="font-size: 18pt;">Overtime Control</span><br><center><span style="color: rgba(96, 92, 168);">'+ result.report_control[0].tanggal +'</center></span>',
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
      categories: xCategories2
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
      name: 'Forecast Production',
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

function show2(tgl, department, ctg) {
  tabel = $('#example3').DataTable();
  tabel.destroy();

  $('#myModal2').modal('show');

  var data = {
    tanggal : tgl,
    department : department,
    category: ctg
  }

  $("#details").empty();

  $("#details").append('<tr><td colspan="7">Loading . . .</td></tr>');
  $.get('{{ url("fetch/overtime_report_detail") }}', data, function(result){
    $("#details").empty();
    $("#head").html('Overtime of More than '+result.head);
    $.each(result.datas, function(key, value) {

     if(result.head == '3 hour(s) / day')
     {
      button = "<td><button id='expand"+value.nik+"' class='btn btn-primary btn-xs' onclick='expand(this)'>expand</button>"+
      "<button id='collapse"+value.nik+"' class='btn btn-primary btn-xs' onclick='collapse(this,\""+value.nik+"\")' style='display:none'>collapse</button></td>";
    }
    else {
      button = "<td></td>";
    }
    
    $("#details").append(
      "<tr id='"+value.nik+"'><td>"+value.nik+"</td><td>"+value.name+"</td><td>"+value.department+"</td><td>"+value.section+"</td><td>"+value.group+"</td><td>"+value.avg+"</td>"+
      button
      +"</tr>"
      );
  });

    detail = result.detail;
    console.log(detail);

  })
}

function expand(element) {
  var tr_id =  $(element).closest('tr').attr('id');
  console.log(tr_id);
  var isi = '';
  for (var i = 0; i < detail.length; i++) {
    if(detail[i].nik == tr_id) {
      var tmp = '<tr><td width="15%"><i class="fa fa-minus"></i>&nbsp; '+detail[i].tanggal+'</td><td width="30%">'+detail[i].keperluan+'</td><td width="10%">'+detail[i].jam+' Jam</td></tr>';

      isi = isi+tmp;
    }
  }
  
  $(element).hide();
  $('#'+tr_id).after('<tr id="col'+tr_id+'"><td colspan="7"><table style="margin: 5px 0 5px 0" width="80%" align="center">'+isi+
    '</table></td></tr>');
  $('#collapse'+tr_id).show();
}

function collapse(element, nik) {
  $(element).hide();
  var tr_id =  $(element).closest('tr').attr('id');
  $("#col"+tr_id).remove();
  $('#expand'+nik).show();
}

$('#date2').datepicker({
  autoclose: true,
  format: "mm-yyyy",
  viewMode: "months", 
  minViewMode: "months"
});

$('#tgl').datepicker({
  <?php $tgl_max = date('d-m-Y') ?>
  autoclose: true,
  format: "dd-mm-yyyy",
  endDate: '<?php echo $tgl_max ?>',
});

</script>

@stop
