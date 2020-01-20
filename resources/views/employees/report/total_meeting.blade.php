@extends('layouts.display')
@section('stylesheets')
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
     #loading, #progressbar2 { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
     <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
          <p style="position: absolute; color: White; top: 45%; left: 35%;">
               <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-spinner"></i></span>
          </p>
     </div>
     <div class="row">
          <div class="col-xs-12">
               <div id="chartTotalManpower" style="width: 100%; height: 500px;"></div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
               <div id="chartTotalManpowerByGender" style="width: 100%; height: 500px;"></div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
               <div id="chartTotalManpowerByUnion" style="width: 100%; height: 500px;"></div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
               <div id="chartTotalOvertime" style="width: 100%; height: 500px;"></div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
               <div id="chartOvertimeViolation" style="width: 100%; height: 500px;"></div>
          </div>
     </div>
</section>

<div class="modal fade" id="modalPeriod">
     <div class="modal-dialog modal-sm">
          <div class="modal-content">
               <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                         <div class="form-group">
                              <label for="exampleInputEmail1">Pilih Periode</label>
                              <div class="input-group date">
                                   <div class="input-group-addon bg-purple" style="border: none;">
                                        <i class="fa fa-calendar"></i>
                                   </div>
                                   <input type="text" class="form-control datepicker" id="month_from" placeholder="Bulan Sampai" onchange="fetchChart(value)">
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

<div class="modal fade" id="modalDetail">
     <div class="modal-dialog modal-lg">
          <div class="modal-content">
               <div class="modal-header">
                    <h3 style="margin-top: 0;" id="titleDetail"></h3>
                    <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="example2">
                         <thead style="background-color: rgba(126,86,134,.7);">
                              <tr>
                                   <th>Period</th>
                                   <th>ID</th>
                                   <th>Name</th>
                                   <th>Section</th>
                                   <th>OT</th>
                              </tr>
                         </thead>
                         <tbody id="tableDetailBody"></tbody>
                         <tfoot>
                         </tfoot>
                    </table>
                    <div id="progressbar2">
                         <center>
                              <i class="fa fa-spinner fa-spin" style="font-size: 6em;"></i> 
                              <br><h4>Loading ...</h4>
                         </center>
                    </div>
               </div>
               <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
               </div>
          </div>
     </div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function(){
          $('#modalPeriod').modal({
               backdrop: 'static',
               keyboard: false
          });
          // fetchChart();
     });

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

     function addZero(i) {
          if (i < 10) {
               i = "0" + i;
          }
          return i;
     }

     function getActualFullDate() {
          var d = new Date();
          var day = addZero(d.getDate());
          var month = addZero(d.getMonth()+1);
          var year = addZero(d.getFullYear());
          var h = addZero(d.getHours());
          var m = addZero(d.getMinutes());
          var s = addZero(d.getSeconds());
          return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
     }

     $('.datepicker').datepicker({
          autoclose: true,
          format: "yyyy-mm",
          startView: "months", 
          minViewMode: "months",
          autoclose: true,
     });

     function fetchChart(period){
          $('#loading').show();
          $('#modalPeriod').modal('hide');
          var data = {
               period:period,
          }
          $.get('{{ url("fetch/report/total_meeting") }}', data, function(result, status, xhr) {
               if(result.status){
                    xDate = [];
                    sTotal = [];
                    sOutsource = [];
                    sContract1 = [];
                    sContract2 = [];
                    sPermanent = [];
                    sProbation = [];
                    sMale = [];
                    sFemale = [];
                    sNon = [];
                    sSPSI = [];
                    sSBM = [];
                    sSPMI = [];

                    $.each(result.employees, function(key, value) {
                         var catDate = value.period;
                         if(xDate.indexOf(catDate) === -1){
                              xDate[xDate.length] = catDate;
                         }

                         sTotal.push(value.total);
                         sOutsource.push(parseInt(value.outsource));
                         sContract1.push(parseInt(value.contract1));
                         sContract2.push(parseInt(value.contract2));
                         sPermanent.push(parseInt(value.permanent));
                         sProbation.push(parseInt(value.probation));
                         sMale.push(parseInt(value.male));
                         sFemale.push(parseInt(value.female));
                         sNon.push(parseInt(value.no_union));
                         sSPSI.push(parseInt(value.spsi));
                         sSBM.push(parseInt(value.sbm));
                         sSPMI.push(parseInt(value.spmi));
                    });

                    Highcharts.chart('chartTotalManpower', {
                         title: {
                              text: 'Total Manpower',
                              style: {
                                   fontSize: '30px',
                                   fontWeight: 'bold'
                              }
                         },
                         yAxis:{
                              title:{
                                   text: null
                              }
                         },
                         xAxis: {
                              categories: xDate
                         },
                         credits:{
                              enabled:false
                         },
                         series: [{
                              type: 'column',
                              name: 'Outsource',
                              data: sOutsource,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }, {
                              type: 'column',
                              name: 'Contract 1',
                              data: sContract1,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }, {
                              type: 'column',
                              name: 'Contract 2',
                              data: sContract2,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }, {
                              type: 'column',
                              name: 'Permanent',
                              data: sPermanent,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }, {
                              type: 'column',
                              name: 'Probation',
                              data: sProbation,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }, {
                              type: 'spline',
                              name: 'Total Employee',
                              data: sTotal,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }]
                    });

Highcharts.chart('chartTotalManpowerByGender', {
     title: {
          text: 'Total Manpower By Gender',
          style: {
               fontSize: '30px',
               fontWeight: 'bold'
          }
     },
     yAxis:{
          title:{
               text: null
          }
     },
     xAxis: {
          categories: xDate
     },
     credits:{
          enabled:false
     },
     series: [{
          type: 'column',
          name: 'Male',
          data: sMale,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: 'Female',
          data: sFemale,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }]
});

Highcharts.chart('chartTotalManpowerByUnion', {
     title: {
          text: 'Total Manpower By Union',
          style: {
               fontSize: '30px',
               fontWeight: 'bold'
          }
     },
     yAxis:{
          title:{
               text: null
          }
     },
     xAxis: {
          categories: xDate
     },
     credits:{
          enabled:false
     },
     series: [{
          type: 'column',
          name: 'No Union',
          data: sNon,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: 'SPSI',
          data: sSPSI,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: 'SPMI',
          data: sSPMI,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: 'SBM',
          data: sSBM,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }]
});

var data = result.overtimes1;
var seriesData = [];
var xCategories = [];
var i, cat;
var intVal = function ( i ) {
     return typeof i === 'string' ?
     i.replace(/[\$,]/g, '')*1 :
     typeof i === 'number' ?
     i : 0;
};

for(i = 0; i < data.length; i++){
     cat = data[i].period;
     if(xCategories.indexOf(cat) === -1){
          xCategories[xCategories.length] = cat;
     }
}
for(i = 0; i < data.length; i++){
     var ot = parseFloat(data[i].ot_person);
     if(seriesData){
          var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].Department.toUpperCase();});
          if(currSeries.length === 0){
               seriesData[seriesData.length] = currSeries = {name: data[i].Department.toUpperCase(), data: []};
          } else {
               currSeries = currSeries[0];
          }
          var index = currSeries.data.length;
          currSeries.data[index] = parseFloat(ot.toFixed(1));
     } else {
          seriesData[0] = {name: data[i].Department.toUpperCase(), data: [parseFloat(ot.toFixed(1))]}
     }
}

Highcharts.chart('chartTotalOvertime', {
     chart: {
          type: 'column'
     },
     title: {
          text: 'Overtime By Person',
          style: {
               fontSize: '30px',
               fontWeight: 'bold'
          }
     },
     xAxis: {
          categories: xCategories
     },
     credits:{
          enabled:false
     },
     yAxis: {
          min: 0,
          title: {
               text: null
          }
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
                    style: {
                         textOutline: 'black',
                         fontWeight: 'bold',
                         fontSize: '16px'
                    }
               }
          }
     },
     series: seriesData
});

var xCat = [];
var s3 = [];
var s14 = [];
var s3_14 = [];
var s56 = [];

var tgl = result.overtimes2[0].orderer;

$.each(result.overtimes2, function(key, value) {
     var cat = value.Department.toUpperCase();
     if(xCat.indexOf(cat) === -1){
          xCat[xCat.length] = cat;
     }

     s3.push(parseInt(value.ot_3));
     s14.push(parseInt(value.ot_14));
     s3_14.push(parseInt(value.ot_3_14));
     s56.push(parseInt(value.ot_56));

});

Highcharts.chart('chartOvertimeViolation', {
     title: {
          text: 'Overtime Violation',
          style: {
               fontSize: '30px',
               fontWeight: 'bold'
          }
     },
     yAxis:{
          title:{
               text: null
          }
     },
     xAxis: {
          categories: xCat
     },
     credits:{
          enabled:false
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
                              if(this.y > 0){
                                   details(tgl, this.category, this.series.name);
                              }
                         }
                    }
               }
          }
     },
     series: [{
          type: 'column',
          name: '3Hours/Day',
          data: s3,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: '14Hours/Week',
          data: s14,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: 'Both',
          data: s3_14,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }, {
          type: 'column',
          name: '56Hours/Month',
          data: s56,
          dataLabels: {
               enabled: true,
               style: {
                    textOutline: false,
                    fontWeight: 'bold',
                    fontSize: '20px'
               }
          }
     }]
});
}
else{
     alert(result.message);
}
$('#loading').hide();
});
}

function details(date, cat, name){
     $('#tableDetailBody').html("");
     $('#titleDetail').text("");
     $('#modalDetail').modal('show');
     $('#progressbar2').show();
     var data = {
          period:date,
          department:cat,
          category:name
     }
     $.get('{{ url("fetch/overtime_report_detail") }}', data, function(result){
          if(result.status){
               tableData = "";

               $.each(result.violations, function(key, value){
                    tableData += '<tr>';
                    tableData += '<td>'+value.period+'</td>';
                    tableData += '<td>'+value.Emp_no+'</td>';
                    tableData += '<td>'+value.Full_name+'</td>';
                    tableData += '<td>'+value.Section+'</td>';
                    tableData += '<td>'+value.ot+'</td>';
                    tableData += '</tr>';
               });
               $('#titleDetail').text(cat+" More than "+name);
               $('#tableDetailBody').append(tableData);
          }
          else{
               alert(result.message);
          }
          $('#progressbar2').hide();
     });
}
</script>
@endsection