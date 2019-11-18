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
    Production Report <span class="text-purple">{{ $departments }}</span><br>
    <small>Berdasarkan Jenis Aktivitas<span class="text-purple"> </span></small>
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-md-12">
      {{-- <div class="col-md-12">
        <div class="col-md-3 pull-right">
          <select class="form-control select2" name="activity_type" style="width: 100%;" data-placeholder="Choose an Activity Type..." required id='activity_type' onchange="drawChart()">
              <option value=""></option>
              <option value="Semua">Semua</option>
              @foreach($data as $data)
                <option value="{{ $data->activity_type }}">{{ $data->activity_type }}</option>
              @endforeach
          </select>
          <br>
          <br>
        </div>
      </div> --}}
      <div class="col-md-12">
              <div class="col-md-2">
                <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border-color: #00a65a">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker2" id="week_date" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
                </div>
                <br>
              </div>
            </div>
      <div class="col-md-12">
        <div class="box">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div id="chart" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
              <div class="col-md-2">
                <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border-color: #00a65a">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker2" id="week_date2" onchange="drawChart2()" placeholder="Select Date" style="border-color: #00a65a">
                </div>
                <br>
              </div>
            </div>
      <div class="col-md-12">
        <div class="box">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_2">
                <div id="chart2" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
              <div class="col-md-2">
                <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border-color: #00a65a">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker" id="year" placeholder="Select Date" style="border-color: #00a65a" onchange="drawChart3()">
                </div>
                <br>
              </div>
            </div>
      <div class="col-md-12">
        <div class="box">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_3">
                <div id="chart3" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
              <div class="col-md-2">
                <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border-color: #00a65a">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker2" id="week_date3" onchange="drawChart4()" placeholder="Select Date" style="border-color: #00a65a">
                </div>
                <br>
              </div>
            </div>
      <div class="col-md-12">
        <div class="box">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_4">
                <div id="chart4" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_chart">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table2"></h4>
          {{-- <a id="link_details" class="btn btn-primary btn-xs pull-right" href="">Activity Chart</a> --}}
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="nav-tabs-custom">
                  <div class="tab-content">
                    <div class="tab-pane active" id="tab_5">
                      <div id="chart_by_frequency" style="width: 99%;"></div>
                    </div>
                  </div>
                </div>
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
  <div class="modal fade" id="modal_chart2">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table2"></h4>
          {{-- <a id="link_details" class="btn btn-primary btn-xs pull-right" href="">Activity Chart</a> --}}
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="nav-tabs-custom">
                  <div class="tab-content">
                    <div class="tab-pane active" id="tab_5">
                      <div id="chart_by_frequency2" style="width: 99%;"></div>
                    </div>
                  </div>
                </div>
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

  <div class="modal fade" id="myModalAudit">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_audit"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Audit Date</th>
                    <th>Product</th>
                    <th>Process</th>
                    <th>Kondisi</th>
                    <th>PIC</th>
                    <th>Auditor</th>
                    <th>Foreman</th>
                    <th>Details</th>
                  </tr>
                </thead>
                <tbody>
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

  <div class="modal fade" id="myModalTraining">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_training"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12" style="overflow:auto;">
              <table id="example3" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Section</th>
                    <th>Product</th>
                    <th>Periode</th>
                    <th>Date</th>
                    <th>Trainer</th>
                    <th>Details</th>
                  </tr>
                </thead>
                <tbody>
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

  <div class="modal fade" id="myModalSampling">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_sampling"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example4" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Section</th>
                    <th>Sub Section</th>
                    <th>Month</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>No. Seri / Part</th>
                    <th>Details</th>
                  </tr>
                </thead>
                <tbody>
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

  <div class="modal fade" id="myModalLaporanAktivitas">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_laporan_aktivitas"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example5" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Section</th>
                    <th>Sub Section</th>
                    <th>Date</th>
                    <th>Nama Dokumen</th>
                    <th>Nomor Dokumen</th>
                    <th>Kesesuaian QC Kouteihyo</th>
                    <th>Details</th>
                  </tr>
                </thead>
                <tbody>
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

  <div class="modal fade" id="myModalPlan">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_plan"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example6" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Activity Alias</th>
                    <th>Frequency</th>
                    <th>Activity Type</th>
                    <th>Leader</th>
                    <th>Foreman</th>
                  </tr>
                </thead>
                <tbody>
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
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
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
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    drawChart();
    drawChart2();
    drawChart3();
    drawChart4();

    $('.datepicker').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy",
      startView: "years", 
      minViewMode: "years",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });
  });

  $(function () {
      $('.select2').select2()
    });

  // $(function(){
  //   $('.datepicker2').datepicker({
  //     autoclose: true,
  //     format: "yyyy",
  //     startView: "years", 
  //     minViewMode: "years",

  //   });
  // });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  

  function drawChart() {
    var week_date = $('#week_date').val();
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportDaily/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var date = [], jml_plan = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            date.push(value.week_date);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_all);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart').highcharts({
            colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',   
             '#FF9655', '#FFF263', '#6AF9C4'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Daily of {{ $departments }}'
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: date,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function (e) {
                      var seriesName = e.point.series.name;
                      if(seriesName == "Plan") {
                        ShowModalPlan('Daily');
                      }
                      else if(seriesName == "Actual") {
                        // alert("Clicked Sea-Level Line");
                        ShowModalChart(this.category,'Daily');
                      }
                      else if(seriesName == "Not Good") {
                        ShowModalAudit(this.category,seriesName);
                        // ShowModalChart(this.category,'Monthly');
                      }
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChart2() {
    var week_date = $('#week_date2').val();
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportWeekly/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          
          var activity_type_weekly = [], jml_plan = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_weekly.push(value.week);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_all);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart2').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Weekly of {{ $departments }}'
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_weekly,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function (e) {
                      var seriesName = e.point.series.name;
                      if(seriesName == "Plan") {
                        ShowModalPlan('Weekly');
                      }
                      else if(seriesName == "Actual") {
                        // alert("Clicked Sea-Level Line");
                        ShowModalChartWeekly(this.category,'Weekly');
                      }
                      else if(seriesName == "Not Good") {
                        ShowModalAudit(this.category,seriesName);
                        // ShowModalChart(this.category,'Monthly');
                      }
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChart3() {
    var week_date = $('#year').val();
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportMonthly/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          
          var activity_type_monthly = [], jml_plan = [],jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_monthly.push(value.month);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_all);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart3').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Monthly of {{ $departments }}'
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_monthly,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function (e) {
                      // ShowModalChart(this.category,'Monthly');
                      var seriesName = e.point.series.name;
                      if(seriesName == "Plan") {
                        ShowModalPlan('Monthly');
                      }
                      else if(seriesName == "Actual") {
                        // alert("Clicked Sea-Level Line");
                        ShowModalChartMonthly(this.category,'Monthly');
                      }
                      else if(seriesName == "Not Good") {
                        ShowModalAudit(this.category,seriesName);
                        // ShowModalChart(this.category,'Monthly');
                      }
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                },
                grouping: false,
                shadow: false
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.25
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChart4() {
    var week_date = $('#week_date3').val();
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportConditional/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var activity_type_conditional = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_conditional.push(value.week_date);
            jml_aktual.push(value.jumlah_all);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart4').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Conditional of {{ $departments }}'
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_conditional,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalChartConditional(this.category,'Conditional');
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.05
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModalChart(activity_type,frequency,week_date) {


    // var week_date = $('#week_date').val();

    if(activity_type == 'Audit'){
      $("#modal_chart2").modal("show");
      var tgl = week_date;
      var data = {
        tgl: tgl,
        frequency: frequency
      };
      $.get('{{ url("index/production_report/fetchReportAudit/".$id) }}', data, function(result, status, xhr) {
        if(xhr.status == 200){
          if(result.status){

            var month = result.monthTitle;
            
            var week_date = [], jml = [], jumlahgood = [], jumlahnotgood = [];
            $.each(result.datas, function(key, value) {
              week_date.push(value.week_date);
              jml.push(value.jumlah_semua);
              jumlahgood.push(parseInt(value.jumlah_good));
              jumlahnotgood.push(parseInt(value.jumlah_not_good));
            })
            $('#chart_by_frequency2').highcharts({
              title: {
                text: 'Report Audit of '+month+' by Point Check Item'
              },
              xAxis: {
                type: 'category',
                categories: week_date,
              },
              yAxis: [{
                title: {
                  text: 'Total Report'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }
              },
                { // Secondary yAxis
                  title: {
                      text: 'Report',
                      style: {
                          color: Highcharts.getOptions().colors[0]
                      }
                  },
                  labels: {
                      format: '{value}',
                      style: {
                          color: Highcharts.getOptions().colors[0]
                      }
                  },
                  opposite: true
                }
              ],
              legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
              },
              plotOptions: {
                series: {
                  cursor: 'pointer',
                  point: {
                    events: {
                      click: function () {
                        ShowModalAudit(this.category,this.series.name);
                      }
                    }
                  },
                  borderWidth: 0,
                  dataLabels: {
                    enabled: false,
                    format: '{point.y}'
                  }
                },
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true
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
                  type: 'column',
                  name: 'Good',
                  color: '#a9ff97',
                  data: jumlahgood
              }, {
                 type: 'column',
                  name: 'Not Good',
                  data: jumlahnotgood,
                  color : '#ff7474'
              },
              {
                  type: 'spline',
                  name: 'Good',
                  color: '#69d453',
                  data: jumlahgood
              }, {
                 type: 'spline',
                  name: 'Not Good',
                  data: jumlahnotgood,
                  color : '#e85858'
              }
              ]
            })
          } else{
            alert('Attempt to retrieve data failed');
          }
        }
      })

      $('#judul_table2').append().empty();
      $('#judul_table2').append('<center> '+ frequency +' Report of '+ activity_type +'<center>');
    }
    else if(activity_type == 'Training'){
      // ShowModalTraining(week_date,frequency);
      tabel = $('#example3').DataTable();
      tabel.destroy();

      $("#myModalTraining").modal("show");

      var table = $('#example3').DataTable({
        'dom': 'Bfrtip',
        'responsive': true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
            // text: '<i class="fa fa-print"></i> Show',
          },
          {
            extend: 'copy',
            className: 'btn btn-success',
            text: '<i class="fa fa-copy"></i> Copy',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'excel',
            className: 'btn btn-info',
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            className: 'btn btn-warning',
            text: '<i class="fa fa-print"></i> Print',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'order': [],
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
          "type" : "get",
          "url" : "{{ url("fetch/production_report/detail_training/".$id) }}",
          "data" : {
            week_date : week_date,
            frequency : frequency
          }
        },
        "columns": [
        { "data": "activity_name" },
        { "data": "section" },
        { "data": "product" },
        { "data": "periode" },
        { "data": "date" },
        { "data": "trainer" },
        { "data": "training_id",
          "render": function ( data ) {
            return '<a target="_blank" class="btn btn-info btn-xs" href="../../../index/training_report/print/' + data + '">Details</a>';
          } 
        }
        ]
      });
      $('#judul_table_training').append().empty();
      $('#judul_table_training').append('<center> Training Report of '+ week_date +'<center>');
    }
    else if(activity_type == 'Sampling Check'){
      tabel = $('#example4').DataTable();
      tabel.destroy();

      $("#myModalSampling").modal("show");

      var table = $('#example4').DataTable({
        'dom': 'Bfrtip',
        'responsive': true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
            // text: '<i class="fa fa-print"></i> Show',
          },
          {
            extend: 'copy',
            className: 'btn btn-success',
            text: '<i class="fa fa-copy"></i> Copy',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'excel',
            className: 'btn btn-info',
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            className: 'btn btn-warning',
            text: '<i class="fa fa-print"></i> Print',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'order': [],
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
          "type" : "get",
          "url" : "{{ url("fetch/production_report/detail_sampling_check/".$id) }}",
          "data" : {
            week_date : week_date
          }
        },
        "columns": [
        { "data": "activity_name" },
        { "data": "section" },
        { "data": "subsection" },
        { "data": "month" },
        { "data": "date" },
        { "data": "product" },
        { "data": "no_seri_part" },
        { "data": "linkurl",
          "render": function ( data ) {
            return '<a target="_blank" class="btn btn-info btn-xs" href="../../../index/sampling_check/print_sampling_chart/' + data + '">Details</a>';
          } 
        }
        ]
      });
      $('#judul_table_sampling').append().empty();
      $('#judul_table_sampling').append('<center> Sampling Check Report of '+ week_date +'<center>');
    }
    else if(activity_type == 'Laporan Aktivitas'){
      $("#modal_chart2").modal("show");
      var week_date = week_date;
      var data = {
        week_date: week_date,
        frequency: frequency
      };
      $.get('{{ url("index/production_report/fetchReportLaporanAktivitas/".$id) }}', data, function(result, status, xhr) {
        if(xhr.status == 200){
          if(result.status){

            // var xAxis = [], productionCount = [], inTransitCount = [], fstkCount = []
            // for (i = 0; i < data.length; i++) {
            //   xAxis.push(data[i].destination);
            //   productionCount.push(data[i].production);
            //   inTransitCount.push(data[i].intransit);
            //   fstkCount.push(data[i].fstk);
            // }
            var month = result.monthTitle;
            
            var week_date = [], jumlah_laporan = [];

            $.each(result.datas, function(key, value) {
              week_date.push(value.week_date);
              jumlah_laporan.push(value.jumlah_laporan);
            })

            $('#chart_by_frequency2').highcharts({
              title: {
                text: 'Laporan Aktivitas Audit of '+month
              },
              xAxis: {
                type: 'category',
                categories: week_date
              },
              yAxis: [{
                title: {
                  text: 'Total Laporan Aktivitas'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }
              },
              { // Secondary yAxis
                  title: {
                      text: 'Report',
                      style: {
                          color: Highcharts.getOptions().colors[0]
                      }
                  },
                  labels: {
                      format: '{value}',
                      style: {
                          color: Highcharts.getOptions().colors[0]
                      }
                  },
                  opposite: true
                }
              ],
              legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
              },
              plotOptions: {
                series: {
                  cursor: 'pointer',
                  point: {
                    events: {
                      click: function () {
                        ShowModalLaporanAktivitas(this.category);
                      }
                    }
                  },
                  borderWidth: 0,
                  dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                  }
                }
              },
              credits: {
                enabled: false
              },

              tooltip: {
                formatter:function(){
                  return this.series.name+' Laporan Aktivitas Audit <br> Tanggal '+this.key + ' : ' + '<br><b>'+this.y+'</b>';
                }
              },
              series: [
              {
                type: 'column',
                name: 'Jumlah',
                color : '#a9ff97',
                data: jumlah_laporan
              },
              {
                type: 'spline',
                name: 'Jumlah',
                color : '#69d453',
                data: jumlah_laporan
              }
              ]
            })
          } else{
            alert('Attempt to retrieve data failed');
          }
        }
      })

      $('#judul_table2').append().empty();
      $('#judul_table2').append('<center> '+ frequency +' Report of '+ activity_type +'<center>');
    }
  }

  function ShowModalChartMonthly(week_date,frequency) {
    // var week_date = $('#week_date').val();
    // if(activity_type != 'Training'){
      $("#modal_chart").modal("show");
    // }
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportDetailMonthly/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var activity_type_monthly = [], jml_plan = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_monthly.push(value.activity_type);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_aktual);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart_by_frequency').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Monthly of {{ $departments }} on '+week_date
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_monthly,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalChart(this.category,'Monthly',week_date);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModalChartConditional(week_date,frequency) {
    // var week_date = $('#week_date').val();
    // if(activity_type != 'Training'){
      $("#modal_chart").modal("show");
    // }
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportDetailConditional/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var activity_type_conditional = [], jml_plan = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_conditional.push(value.activity_type);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_aktual);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart_by_frequency').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Conditional of {{ $departments }} on '+week_date
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_conditional,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalChart(this.category,'Conditional',week_date);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModalChartWeekly(week_date,frequency) {
    // var week_date = $('#week_date').val();
    // if(activity_type != 'Training'){
      $("#modal_chart").modal("show");
    // }
    var data = {
        week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportDetailWeekly/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var activity_type_conditional = [], jml_plan = [], jml_aktual = [],jml_good = [],jml_not_good = [];

          $.each(result.datas, function(key, value) {
            activity_type_conditional.push(value.activity_type);
            jml_plan.push(value.jumlah_plan);
            jml_aktual.push(value.jumlah_aktual);
            jml_good.push(parseInt(value.jumlah_good));
            jml_not_good.push(parseInt(value.jumlah_not_good));
          })
          var i;
          var j = 0;
          for (i = 0; i < jml_not_good.length; i++) {
            if(jml_not_good[i] == 0){
              jml_not_good[i] = null;
            }
          }

          $('#chart_by_frequency').highcharts({
            colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
            chart: {
                type: 'column',
                backgroundColor: null
            },
            title: {
              text: 'Report Weekly of {{ $departments }} on '+week_date
            },
            xAxis: {
                tickInterval:  1,
                overflow: true,
                categories: activity_type_conditional,
                labels:{
                  rotation: -45,
                },
                min: 0          
              },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalChart(this.category,'Weekly',week_date);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  grouping: false,
                  shadow: false,
                  borderWidth: 0,
                }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            series: [
              {
                type: 'column',
                name: 'Plan',
                color: '#8061a0',
                data: jml_plan,
                pointPadding: 0.05
              },
              {
                type: 'column',
                name: 'Actual',
                color: '#a9ff97',
                data: jml_aktual,
                pointPadding: 0.2
              },
              // {
              //   type: 'column',
              //   name: 'Good',
              //   stacking: 'normal',
              //   color: '#c9c9c9',
              //   data: jml_good,
              //   pointPadding: 0.2
              // },
              {
                type: 'column',
                name: 'Not Good',
                color: '#ff7474',
                stacking: 'normal',
                data: jml_not_good,
                pointPadding: 0.2
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }



  function ShowModalAudit(week_date,kondisi) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModalAudit").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("fetch/production_audit/detail_stat/".$id) }}",
        "data" : {
          week_date : week_date,
          kondisi : kondisi,
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "date" },
      { "data": "product" },
      { "data": "proses" },
      { "data": "kondisi" },
      { "data": "pic_name" },
      { "data": "auditor_name" },
      { "data": "foreman" },
      { "data": "urllink",
        "render": function ( data ) {
          return '<a target="_blank" class="btn btn-info btn-xs" href="../../../index/production_audit/print_audit_chart/'+ data + '">Details</a>';
        } 
      },
      ]
    });
    $('#judul_table_audit').append().empty();
    $('#judul_table_audit').append('<center> Report on '+ week_date + ' in ' + kondisi +' Condition<center>');
    
  }

  function ShowModalLaporanAktivitas(week_date) {
    tabel = $('#example5').DataTable();
    tabel.destroy();

    $("#myModalLaporanAktivitas").modal("show");

    var table = $('#example5').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("fetch/audit_report_activity/detail_laporan_aktivitas/".$id) }}",
        "data" : {
          week_date : week_date
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "section" },
      { "data": "subsection" },
      { "data": "date" },
      { "data": "nama_dokumen" },
      { "data": "no_dokumen" },
      { "data": "kesesuaian_qc_kouteihyo" },
      { "data": "linkurl",
        "render": function ( data ) {
            return '<a target="_blank" class="btn btn-info btn-xs" href="../../../index/audit_report_activity/print_audit_report_chart/' + data + '">Details</a>';
          } 
      }
      ]
    });
    $('#judul_laporan_aktivitas').append().empty();
    $('#judul_laporan_aktivitas').append('<center> Laporan Aktivitas Audit of '+ week_date +'<center>');
    
  }

  function ShowModalPlan(frequency) {
    tabel = $('#example6').DataTable();
    tabel.destroy();

    $("#myModalPlan").modal("show");

    var table = $('#example6').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("index/production_report/fetchPlanReport/".$id) }}",
        "data" : {
          frequency : frequency
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "activity_alias" },
      { "data": "frequency" },
      { "data": "activity_type" },
      { "data": "leader_dept" },
      { "data": "foreman_dept" }
      ]
    });
    $('#judul_plan').append().empty();
    $('#judul_plan').append('<center> '+ frequency +' Activity Plan<center>');
    
  }

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