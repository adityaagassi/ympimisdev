@extends('layouts.master')
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
  background-color: #eeeeee;
    /*color: white;*/
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
  font-size: 0.9vw;
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

hr { background-color: red; height: 1px; border: 0; }
#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    <span class="text-purple">Investment Control & Monitoring</span>
  </h1>
  <br>
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
        @if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "ACC-SPL")
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" onchange="drawChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8;width: 100%">
                  @foreach($department as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                  @endforeach
                </select>
            </div>
        </div>
        @else
           <select class="form-control select2 hideselect" multiple="multiple" onchange="drawChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8;width: 100%">
             <option value="{{$emp_dept->department}}" selected="">{{$emp_dept->department}}</option>
           </select>
        @endif
      </div>

      <div class="col-md-12">
          <div class="col-md-12" style="margin-top: 5px; padding:0">
              <div id="chart" style="width: 99%"></div>
          </div>

          <div class="col-md-12" style="padding:0">
              <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
                <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
                  <tr>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px" rowspan="2">No Investment</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px" rowspan="2">Submission Date</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px" rowspan="2">Department</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">User</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Cek Budget</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Cek Pajak</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Manager</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">DGM</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">GM</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Manager Acc</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Direktur</th>
                    <th style="width: 6%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #3f51b5;padding: 3px">Presdir</th>
                  </tr>
                </thead>
                <tbody id="tabelisi">
                </tbody>
                <tfoot>
                </tfoot>
              </table>
          </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
                <div id="chartundone" style="width: 99%"></div>
            </div>

<!--             <div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;">
                <span style="font-size: 24px;font-weight: bold;color: white">Outstanding Investment Belum PO (Per Item)</span>
            </div> -->

            <div class="col-md-12" style="padding:0;">
              <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
                <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
                  <tr>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black;background-color: #3f51b5;color: white">Investment No</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Submission Date</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Department</th>
                    <th style="width: 20%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Description</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Qty</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Status</th>
                  </tr>
                </thead>
                <tbody id="tabelisipo_undone">
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="col-md-6">
            <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
                <div id="chartActual" style="width: 99%"></div>
            </div>

<!--             <div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;">
                <span style="font-size: 24px;font-weight: bold;color: white">Outstanding Investment Belum PO (Per Item)</span>
            </div> -->

            <div class="col-md-12" style="padding:0;">
              <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
                <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
                  <tr>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black;background-color: #3f51b5;color: white">Investment No</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Department</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">No PO</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Tanggal PO</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Supplier</th>
                    <th style="width: 20%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Deskripsi Item</th>
                    <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;color: black; background-color: #3f51b5;color: white">Status PO</th>
                  </tr>
                </thead>
                <tbody id="tabelisiactual">
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>
          </div>
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
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Reff Number</th>
                    <th>Submission Date</th>
                    <th>Department</th>
                    <th>Applicant</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Vendor</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Action</th>
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

  <div class="modal fade" id="modalInv">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table2"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tabelInv" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Nomor Investment</th>
                    <th>Tanggal Pengajuan</th>
                    <th>No Item</th>
                    <th>Detail</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Dollar</th> 
                    <th>Status</th>
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

  <div class="modal fade" id="modalActual">
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
              <table id="tabelActual" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Reff Number</th>
                    <th>Nomor PO</th>
                    <th>Tanggal PO</th>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Budget No</th>
                    <th>Qty</th>
                    <th>Total Receive</th>
                    <th>Status</th>
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
    $('body').toggleClass("sidebar-collapse");
    
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    $('.select2').select2();

    $('.hideselect').next(".select2-container").hide();

    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  function drawChart() {
    fetchTable();
    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();
    var department = $('#department').val();

    var data = {
      tglfrom: tglfrom,
      tglto: tglto,
      department: department,
    };

    $.get('{{ url("fetch/investment/control") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var week = [], week_date = [], not_sign = [], sign = [],gg = [],gg2 = [], reff_number = [], belum_po = [], sudah_po = [], inv_close = [], belum_close = [], sudah_close = [];

          $.each(result.datas, function(key, value) {
            week.push(value.week_name);
            week_date.push(value.week_date);
            not_sign.push(parseInt(value.undone));
            sign.push(parseInt(value.done));
            // gg.push({y:parseInt(value.undone),key:value.week_date});
            // gg2.push({y:parseInt(value.done),key:value.week_date});
          })

          $.each(result.data_investment_belum_po, function(key, value) {
            reff_number.push(value.reff_number);
            belum_po.push(parseInt(value.belum_po));
            sudah_po.push(parseInt(value.sudah_po));
          })

          $.each(result.data_investment_belum_receive, function(key, value) {
            if (value.belum_close != 0) {
              inv_close.push(value.reff_number);
              belum_close.push(parseInt(value.belum_close));
              sudah_close.push(parseInt(value.sudah_close));              
            }
          })

          $('#chart').highcharts({
            chart: {
              type: 'column',
              height: 275,
            },
            title: {
              text: 'Investment Monitoring & Control',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'On '+result.year+' Last 30 Days',
              style: {
                fontSize: '0.8vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: week,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return ''+ this.value +' Start from '+week_date[(this.pos)];
                }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  text: 'Total Investment'
                },
              tickInterval: 1,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalInv(this.category,this.series.name,result.tglfrom,result.tglto,result.department);
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
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
                  borderRadius: 1,
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
                name: 'Investment Incompleted',
                color: '#ff6666',
                data: not_sign
              },
              {
                name: 'Investment Completed',
                color: '#00a65a',
                data: sign
              }
            ]
          })

          $('#chartundone').highcharts({
            chart: {
              type: 'column',
              height: 250
            },
            title: {
              text: 'Outstanding Investment Belum PO',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: reff_number,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                enabled:false
              },
              tickInterval: 3,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:true,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalTableInv(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
                  borderRadius: 1,
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
                name: 'Belum PO',
                color: '#ff6666', //ff6666
                data: belum_po
              },
              {
                name: 'Sudah PO',
                color: '#00a65a',
                data: sudah_po
              }
            ]
          })

          $('#chartActual').highcharts({
            chart: {
              type: 'column',
              height: 250
            },
            title: {
              text: 'Outstanding Investment Sudah PO (Belum Receive)',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: inv_close,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                enabled:false
              },
              tickInterval: 3,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:true,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalActual(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
                  borderRadius: 1,
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
                name: 'Belum Datang',
                color: '#ff6666', //ff6666
                data: belum_close
              },
              {
                name: 'Sudah Datang',
                color: '#00a65a',
                data: sudah_close
              }
            ]
          })

        } else{
          alert('Attempt to retrieve data failed');
        }

      }
    })
  }

  

  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var department = $('#department').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      department: department,
    };

    $.get('{{ url("investment/table") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){

          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          
          var table = "";
          var user = "";
          var acc_budget = "";
          var acc_pajak = "";
          var manager = "";
          var dgm = "";
          var gm = "";
          var manager_acc = "";
          var direktur_acc = "";
          var presdir = "";

          $.each(result.datas, function(key, value) {

            var applicant = value.applicant_name;
            var applicantname = applicant.split(' ').slice(0,2).join(' ');
            var colorapplicant = "";
            var coloraccbudget = "";
            var coloraccpajak = "";


            if (value.approval_manager != null) {
              var manager_approval = value.approval_manager;
              var manager_name = manager_approval.split("/");
              var managername = manager_name[1].split(' ').slice(0,2).join(' ');
            }
            var colormanager = "";

            if (value.approval_dgm != null) {
              var dgm_approval = value.approval_dgm;
              var dgm_name = dgm_approval.split("/");
              var dgmname = dgm_name[1].split(' ').slice(0,2).join(' ');
            }
            var colordgm = "";

            if (value.approval_gm != null) {
              var gm_approval = value.approval_gm;
              var gm_name = gm_approval.split("/");
              var gmname = gm_name[1].split(' ').slice(0,2).join(' ');
            }
            var colorgm = "";

            var manager_acc_approval = value.approval_manager_acc;
            var manager_acc_name = manager_acc_approval.split("/");
            var manageraccname = manager_acc_name[1].split(' ').slice(0,2).join(' ');
            var colormanageracc = "";

            var direktur_acc_approval = value.approval_dir_acc;
            var direktur_acc_name = direktur_acc_approval.split("/");
            var direkturaccname = direktur_acc_name[1].split(' ').slice(0,2).join(' ');
            var colordirekturacc = "";

            var presdir_approval = value.approval_presdir;
            var presdir_name = presdir_approval.split("/");
            var presdirname = presdir_name[1].split(' ').slice(0,2).join(' ');
            var colorpresdir = "";

            var d = 0;

            var urldetail = '{{ url("investment/detail/") }}';
            var urlreport = '{{ url("investment/report/") }}';
            var urlverifikasi = '{{ url("investment/verifikasi/") }}';
            var urlcheck = '{{ url("investment/check/") }}';

            //User / Applicant

              if (value.posisi != "user") {
                user = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+applicantname+'</span></a>';
                colorapplicant = 'style="background-color:#00a65a"';
              }
              else {
                if (d == 0) {  
                    user = '<a href="'+urldetail+'/'+value.id+'"><span class="label label-danger zoom">'+applicantname+'</span></a>';
                    colorapplicant = 'style="background-color:#dd4b39"';                    
                    d = 1;
                  } else {
                    user = '';
                  }
              }

              //Acc Budget

              if (value.posisi != "acc_budget") {
                if (d == 0) {  
                    acc_budget = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">Lailatul Chusnah</span></a>';
                    coloraccbudget = 'style="background-color:#00a65a"';
                } 
                else {
                    acc_budget = '';
                }
              }
              else {
                if (d == 0) {  
                    acc_budget = '<a href="'+urlcheck+'/'+value.id+'"><span class="label label-danger zoom">Lailatul Chusnah</span></a>';
                    coloraccbudget = 'style="background-color:#dd4b39"';                    
                    d = 1;
                  } else {
                    acc_budget = '';
                  }
              }

              //Acc Pajak

              if (value.posisi != "acc_pajak") {
                if (d == 0) {  
                    acc_pajak = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">Yeny Arisanty</span></a>';
                    coloraccpajak = 'style="background-color:#00a65a"';
                } 
                else {
                    acc_pajak = '';
                }
              }
              else {
                if (d == 0) {  
                    acc_pajak = '<a href="'+urlcheck+'/'+value.id+'"><span class="label label-danger zoom">Yeny Arisanty</span></a>';
                    coloraccpajak = 'style="background-color:#dd4b39"';                    
                    d = 1;
                  } else {
                    acc_pajak = '';
                  }
              }

              //Manager
              if (value.approval_manager != null) {
                if (manager_name.length == 4) {
                    if (value.posisi == "manager") {
                        if (d == 0) {  
                            manager = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+managername+'</span></a>';   
                            colormanager = 'style="background-color:#dd4b39"';                  
                            d = 1;
                          } else {
                            manager = '';
                          }
                    }
                    else{
                        manager = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+managername+'</span></a>';
                        colormanager = 'style="background-color:#00a65a"'; 
                    }
                }
                else{
                  if (d == 0) {  
                    manager = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+managername+'</span></a>'; 
                    colormanager = 'style="background-color:#dd4b39"';                  
                    d = 1;
                  } else {
                    manager = '';
                  }
                }
              }
              else{
                manager = '<span style="color:white">None</span>'; 
                colormanager = 'style="background-color:#424242"';
              }

              //DGM
              if (value.approval_dgm != null) {
                if (dgm_name.length == 4) {
                    if (value.posisi == "dgm") {
                      if (d == 0) {  
                          dgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+dgmname+'</span></a>';
                          colordgm = 'style="background-color:#dd4b39"';              
                          d = 1;
                        } else {
                          dgm = '';
                        }
                    }
                    else {
                      dgm = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+dgmname+'</span></a>';
                      colordgm = 'style="background-color:#00a65a"'; 
                    } 
                }
                else {
                  if (d == 0) {  
                    dgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+dgmname+'</span></a>';
                    colordgm = 'style="background-color:#dd4b39"';                   
                    d = 1;
                  } else {
                    dgm = '';
                  }
                }
              }
              else{
                dgm = '<span style="color:white">None</span>'; 
                colordgm = 'style="background-color:#424242"';
              }

              //GM
              if (value.approval_gm != null) {
                if (gm_name.length == 4) {
                    if (value.posisi == "gm") {
                        if (d == 0) {  
                          gm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+gmname+'</span></a>';
                          colorgm = 'style="background-color:#dd4b39"'; 
                          d = 1;
                        } else {
                          gm = '';
                        }
                    } else {
                      gm = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+gmname+'</span></a>'; 
                      colorgm = 'style="background-color:#00a65a"'; 
                    }
                } 

                else {
                  if (d == 0) {  
                    gm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+gmname+'</span></a>';
                    colorgm = 'style="background-color:#dd4b39"'; 
                    d = 1;
                  } else {
                    gm = '';
                  }
                }
              }else{
                gm = '<span style="color:white">None</span>';
                colorgm = 'style="background-color:#424242"';
              }

              //Manager ACC

              if (manager_acc_name.length == 4) {
                  if (value.posisi == "manager_acc") {
                      if (d == 0) {  
                        manager_acc = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+manageraccname+'</span></a>';
                        colormanageracc = 'style="background-color:#dd4b39"'; 
                        d = 1;
                      } else {
                        manager_acc = '';
                      }
                  } else {
                    manager_acc = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+manageraccname+'</span></a>'; 
                    colormanageracc = 'style="background-color:#00a65a"'; 
                  }
              } 

              else {
                if (d == 0) {  
                  manager_acc = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+manageraccname+'</span></a>';
                  colormanageracc = 'style="background-color:#dd4b39"'; 
                  d = 1;
                } else {
                  manager_acc = '';
                }
              }

              //Direktur ACC

              if (direktur_acc_name.length == 4) {
                  if (value.posisi == "direktur_acc") {
                      if (d == 0) {  
                        direktur_acc = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+direkturaccname+'</span></a>';
                        colordirekturacc = 'style="background-color:#dd4b39"'; 
                        d = 1;
                      } else {
                        direktur_acc = '';
                      }
                  } else {
                    direktur_acc = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+direkturaccname+'</span></a>'; 
                    colordirekturacc = 'style="background-color:#00a65a"'; 
                  }
              } 

              else {
                if (d == 0) {  
                  direktur_acc = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+direkturaccname+'</span></a>';
                  colordirekturacc = 'style="background-color:#dd4b39"'; 
                  d = 1;
                } else {
                  direktur_acc = '';
                }
              }

              //Presdir

              if (presdir_name.length == 4) {
                  if (value.posisi == "presdir") {
                      if (d == 0) {  
                        presdir = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+presdirname+'</span></a>';
                        colorpresdir = 'style="background-color:#dd4b39"'; 
                        d = 1;
                      } else {
                        presdir = '';
                      }
                  } else {
                    presdir = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+presdirname+'</span></a>'; 
                    colorpresdir = 'style="background-color:#00a65a"'; 
                  }
              } 

              else {
                if (d == 0) {  
                  presdir = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+presdirname+'</span></a>';
                  colorpresdir = 'style="background-color:#dd4b39"'; 
                  d = 1;
                } else {
                  presdir = '';
                }
              }
              

              table += '<tr style="font-size:16px">';
              table += '<td>'+value.reff_number+'</td>';
              table += '<td>'+value.submission_date+'</td>';
              table += '<td>'+value.department_shortname+'</td>';
              table += '<td '+colorapplicant+'>'+user+'</td>';
              table += '<td '+coloraccbudget+'>'+acc_budget+'</td>';
              table += '<td '+coloraccpajak+'>'+acc_pajak+'</td>';  
              table += '<td '+colormanager+'>'+manager+'</td>';
              table += '<td '+colordgm+'>'+dgm+'</td>';
              table += '<td '+colorgm+'>'+gm+'</td>';
              table += '<td '+colormanageracc+'>'+manager_acc+'</td>';
              table += '<td '+colordirekturacc+'>'+direktur_acc+'</td>';
              table += '<td '+colorpresdir+'>'+presdir+'</td>';

              
          })

          $('#tabelisi').append(table);

          $("#tabelisipo_undone").find("td").remove();  
          $('#tabelisipo_undone').html("");
          
          var table_belum_po = "";

          $.each(result.data_investment_belum_po, function(key, value) {
            table_belum_po += '<tr>';
            table_belum_po += '<td>'+value.reff_number+'</td>';
            table_belum_po += '<td>'+value.submission_date+'</td>';
            table_belum_po += '<td>'+value.department_shortname+'</td>';
            table_belum_po += '<td>'+value.detail+'</td>';
            table_belum_po += '<td>'+value.qty+'</td>';
            table_belum_po += '<td style="background-color:#dd4b39;color:white">Belum PO</td>';
            table_belum_po += '</tr>';
          })

          $('#tabelisipo_undone').append(table_belum_po);

          $("#tabelisiactual").find("td").remove();  
          $('#tabelisiactual').html("");
          
          var table_belum_actual = "";

          $.each(result.data_po_belum_receive, function(key, value) {
            table_belum_actual += '<tr>';
            table_belum_actual += '<td>'+value.reff_number+'</td>';
            table_belum_actual += '<td>'+value.department_shortname+'</td>';
            table_belum_actual += '<td>'+value.no_po+'</td>';
            table_belum_actual += '<td>'+value.tgl_po+'</td>';
            table_belum_actual += '<td>'+value.supplier_name+'</td>';
            table_belum_actual += '<td>'+value.nama_item+'</td>';

            if (value.status_po == 'PO Terkirim') {
              table_belum_actual += '<td><span class="label label-success"> '+value.status_po+' </span></td>';              
            }else if(value.status_po == 'PO Approval'){
              table_belum_actual += '<td><span class="label label-warning">'+value.status_po+' </span></td>';   
            }
            
            table_belum_actual += '</tr>';
          })

          $('#tabelisiactual').append(table_belum_actual);

        }
      }
    })
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function ShowModalInv(week, status, tglfrom, tglto, department) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

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
          "url" : "{{ url("investment/detail") }}",
          "data" : {
            week : week,
            status : status,
            tglfrom : tglfrom,
            tglto : tglto,
            department : department
          }
        },
      "columns": [
          { "data": "reff_number" },
          { "data": "submission_date" },
          { "data": "applicant_department" },
          { "data": "applicant_name" },
          { "data": "category" },
          { "data": "subject" },
          { "data": "type" },
          { "data": "supplier_code" },
          { "data": "file" },
          { "data": "status" },
          { "data": "action" },
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' week '+week+'</center></b>');
    
  }

  function ShowModalTableInv(reff, status, datefrom, dateto, department) {
    tabel = $('#tabelInv').DataTable();
    tabel.destroy();

    $("#modalInv").modal("show");

    var table = $('#tabelInv').DataTable({
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
          "url" : "{{ url("investment/detailInv") }}",
          "data" : {
            reff : reff,
            status : status,
            department : department,
            datefrom : datefrom,
            dateto : dateto
          }
        },
      "columns": [
          { "data": "reff_number" },
          { "data": "submission_date" },
          { "data": "no_item" },
          { "data": "detail" },
          { "data": "qty" },
          { "data": "price" },
          { "data": "amount" },
          { "data": "dollar" },
          { "data": "status", "width": "15%"}
        ]    });

    $('#judul_table2').append().empty();
    $('#judul_table2').append('<center><b>'+status+' No Investment '+reff+'</center></b>');
    
  }


  function ShowModalActual(reff, status, datefrom, dateto, department) {
    tabel = $('#tabelActual').DataTable();
    tabel.destroy();

    $("#modalActual").modal("show");

    var table = $('#tabelActual').DataTable({
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
          "url" : "{{ url("investment/detailActual") }}",
          "data" : {
            reff : reff,
            status : status,
            department : department,
            datefrom : datefrom,
            dateto : dateto
          }
        },
      "columns": [
          { "data": "no_pr" },
          { "data": "no_po" },
          { "data": "tgl_po" },
          { "data": "nama_item" },
          { "data": "supplier_name" },
          { "data": "delivery_date" },
          { "data": "budget_item" },
          { "data": "qty" },
          { "data": "qty_receive" },
          { "data": "status" }
        ]    });

    $('#judul_table2').append().empty();
    $('#judul_table2').append('<center><b>'+status+' No Investment '+reff+'</center></b>');
    
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